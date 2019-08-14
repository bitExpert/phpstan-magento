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

class ProxyAutoloader
{
    public function autoload(string $class): void
    {
        if (!preg_match('#\\\Proxy#', $class)) {
            return;
        }

        $namespace = explode('\\', ltrim($class, '\\'));
        $proxyClassname = array_pop($namespace);
        $proxyBaseClass = '';
        $originalClassname = implode('\\', $namespace);
        $namespace = implode('\\', $namespace);
        $proxyInterface = ['\Magento\Framework\ObjectManager\NoninterceptableInterface'];
        $methods = '';

        $reflectionClass = new \ReflectionClass($originalClassname);
        if ($reflectionClass->isInterface()) {
            $proxyInterface[] = '\\' . $originalClassname;
            foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                $returnType = $method->getReturnType() ?: '';
                if ($returnType instanceof \ReflectionType) {
                    $returnType = ': ' . $returnType->getName();
                }

                $params = [];
                foreach ($method->getParameters() as $parameter) {
                    $paramType = $parameter->getType() ?: '';
                    if ($paramType instanceof \ReflectionType) {
                        if ($paramType->isBuiltin()) {
                            $paramType = $paramType->getName() . ' ';
                        } else {
                            $paramType = '\\' . $paramType->getName() . ' ';
                        }
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
            $proxyBaseClass = ' extends ' . $originalClassname;
        }

        $template = "<?php\n";
        $template .= "namespace {NAMESPACE};\n";
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

        $template = str_replace(
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

        // we need to store the generated file on disk as PHPStan will try to access the file in several ways. Just
        // eval'ing the php code to make the class available won't work!
        $tmpFilename = tempnam(sys_get_temp_dir(), 'PSMP');
        file_put_contents($tmpFilename, $template, LOCK_EX);
        include($tmpFilename);
    }
}
