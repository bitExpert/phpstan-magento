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

class FactoryAutoloader
{
    public function autoload(string $class): void
    {
        if (!preg_match('#Factory$#', $class)) {
            return;
        }

        $namespace = explode('\\', ltrim($class, '\\'));
        $factoryClassname = array_pop($namespace);
        $originalClassname = str_replace('Factory', '', $factoryClassname);
        $namespace = implode('\\', $namespace);

        $template = "<?php\n";
        $template .= "namespace {NAMESPACE};\n\n";
        $template .= "/**\n";
        $template .= " * Factory class for @see \{NAMESPACE}\{CLASSNAME}\n";
        $template .= " */\n";
        $template .= "class {FACTORY_CLASSNAME}\n";
        $template .= "{\n";
        $template .= "    /**\n";
        $template .= "     * Create class instance with specified parameters\n";
        $template .= "     *\n";
        $template .= "     * @param array \$data\n";
        $template .= "     * @return {CLASSNAME}\n";
        $template .= "     */\n";
        $template .= "    public function create(array \$data = array()) {}\n";
        $template .= "}\n";

        $template = str_replace(
            ['{NAMESPACE}', '{CLASSNAME}', '{FACTORY_CLASSNAME}'],
            [$namespace, $originalClassname, $factoryClassname],
            $template
        );

        // we need to store the generated file on disk as PHPStan will try to access the file in several ways. Just
        // eval'ing the php code to make the class available won't work!
        $tmpFilename = tempnam(sys_get_temp_dir(), 'PSMF');
        file_put_contents($tmpFilename, $template, LOCK_EX);
        include($tmpFilename);
    }
}
