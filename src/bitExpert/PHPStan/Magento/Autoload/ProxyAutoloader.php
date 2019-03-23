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
        $originalClassname = implode('\\', $namespace);
        $namespace = implode('\\', $namespace);
        $markerInterface = '\Magento\Framework\ObjectManager\NoninterceptableInterface';

        $template = "<?php\n";
        $template .= "namespace {NAMESPACE};\n";
        $template .= "/**\n";
        $template .= " * Proxy class for @see {CLASSNAME}\n";
        $template .= " */\n";
        $template .= "class {PROXY_CLASSNAME} extends {CLASSNAME} implements {MARKER_INTERFACE}\n";
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
        $template .= "}\n";

        $template = str_replace(
            ['{NAMESPACE}', '{CLASSNAME}', '{PROXY_CLASSNAME}', '{MARKER_INTERFACE}'],
            [$namespace, $originalClassname, $proxyClassname, $markerInterface],
            $template
        );

        // we need to store the generated file on disk as PHPStan will try to access the file in several ways. Just
        // eval'ing the php code to make the class available won't work!
        $tmpFilename = tempnam(sys_get_temp_dir(), 'PSMP');
        file_put_contents($tmpFilename, $template, LOCK_EX);
        include($tmpFilename);
    }
}
