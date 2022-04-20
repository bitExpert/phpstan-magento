<?php

/*
 * This file is part of the phpstan-magento package.
 *
 * (c) bitExpert AG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace bitExpert\PHPStan\Magento\Autoload;

use PHPStan\Cache\Cache;

class ProxyAutoloader implements Autoloader
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * ProxyAutoloader constructor.
     *
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function autoload(string $class): void
    {
        if (preg_match('#\\\Proxy#', $class) !== 1) {
            return;
        }

        $cacheFilename = $this->cache->load($class, '');
        if ($cacheFilename === null) {
            $this->cache->save($class, '', $this->getFileContents($class));
            $cacheFilename = $this->cache->load($class, '');
        }

        require_once($cacheFilename);
    }

    /**
     * Generate the proxy file content as Magento would.
     *
     * @param string $class
     * @return string
     * @throws \ReflectionException
     */
    protected function getFileContents(string $class): string
    {
        $namespace = explode('\\', ltrim($class, '\\'));
        $proxyClassname = array_pop($namespace);
        $proxyBaseClass = '';
        $originalClassname = implode('\\', $namespace);
        $namespace = implode('\\', $namespace);
        $proxyInterface = ['\Magento\Framework\ObjectManager\NoninterceptableInterface'];
        $methods = '';

        /** @var class-string $originalClassname */
        $reflectionClass = new \ReflectionClass($originalClassname);
        if ($reflectionClass->isInterface()) {
            $proxyInterface[] = '\\' . $originalClassname;
            foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                $returnType = $method->getReturnType();
                if ($returnType instanceof \ReflectionNamedType) {
                    $returnType = ': ' . $returnType->getName();
                } else {
                    $returnType = '';
                }

                $params = [];
                foreach ($method->getParameters() as $parameter) {
                    $paramType = $parameter->getType();
                    if ($paramType instanceof \ReflectionNamedType) {
                        if ($paramType->isBuiltin()) {
                            $paramType = $paramType->getName() . ' ';
                        } else {
                            $paramType = '\\' . $paramType->getName() . ' ';
                        }
                    } else {
                        $paramType = '';
                    }

                    $defaultValue = '';
                    if ($parameter->isDefaultValueAvailable()) {
                        switch ($parameter->getDefaultValue()) {
                            case null:
                                $defaultValue = ' = NULL';
                                break;
                            case false:
                                $defaultValue = ' = false';
                                break;
                            default:
                                $defaultValue = ' = ' . $parameter->getDefaultValue();
                                break;
                        }
                    }

                    $params[] = $paramType . '$' . $parameter->getName() . $defaultValue;
                }

                $methods .= '    public function ' . $method->getName() . '(' . implode(', ', $params) . ')' .
                    $returnType . " {}\n\n";
            }
        } else {
            $proxyBaseClass = ' extends \\' . $originalClassname;
        }

        $template = "<?php\n";

        if ($namespace !== '') {
            $template .= "namespace {NAMESPACE};\n\n";
        }

        $template .= "/**\n";
        $template .= " * Proxy class for @see {CLASSNAME}\n";
        $template .= " */\n";
        $template .= "class {PROXY_CLASSNAME}{PROXY_BASE_CLASSNAME} implements {PROXY_INTERFACE}\n";
        $template .= "{\n";
        $template .= "    /**\n";
        $template .= "     * @return array\n";
        $template .= "     */\n";
        $template .= "    public function __sleep() {}\n";
        $template .= "    /**\n";
        $template .= "     * Retrieve ObjectManager from global scope\n";
        $template .= "     */\n";
        $template .= "    public function __wakeup() {}\n";
        $template .= "    /**\n";
        $template .= "     * Clone proxied instance\n";
        $template .= "     */\n";
        $template .= "    public function __clone() {}\n";
        $template .= "{METHODS}";
        $template .= "}\n";

        return str_replace(
            [
                '{NAMESPACE}',
                '{CLASSNAME}',
                '{PROXY_BASE_CLASSNAME}',
                '{PROXY_CLASSNAME}',
                '{PROXY_INTERFACE}',
                '{METHODS}'
            ],
            [
                $namespace,
                $originalClassname,
                $proxyBaseClass,
                $proxyClassname,
                implode(', ', $proxyInterface),
                $methods
            ],
            $template
        );
    }

    public function register(): void
    {
        \spl_autoload_register([$this, 'autoload'], true, false);
    }

    public function unregister(): void
    {
        \spl_autoload_unregister([$this, 'autoload']);
    }
}
