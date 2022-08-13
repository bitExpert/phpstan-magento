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

namespace bitExpert\PHPStan\Magento\Autoload\DataProvider;

use Composer\Autoload\ClassLoader;

class ClassLoaderProvider
{
    /**
     * @var ClassLoader
     */
    private $composer;

    /**
     * ClassLoaderProvider constructor.
     *
     * @param string $magentoRoot
     */
    public function __construct(string $magentoRoot)
    {
        $this->composer = new ClassLoader($magentoRoot . '/vendor');
        $autoloadFile = $magentoRoot . '/vendor/composer/autoload_namespaces.php';
        if (is_file($autoloadFile)) {
            $map = require $autoloadFile;
            foreach ($map as $namespace => $path) {
                $this->composer->set($namespace, $path);
            }
        }

        $autoloadFile = $magentoRoot . '/vendor/composer/autoload_psr4.php';
        if (is_file($autoloadFile)) {
            $map = require $autoloadFile;
            foreach ($map as $namespace => $path) {
                $this->composer->setPsr4($namespace, $path);
            }
        }

        $autoloadFile = $magentoRoot . '/vendor/composer/autoload_classmap.php';
        if (is_file($autoloadFile)) {
            $classMap = require $autoloadFile;
            if (is_array($classMap)) {
                $this->composer->addClassMap($classMap);
            }
        }
    }

    /**
     * Check if the given class/interface/tait exists in the defined scope.
     *
     * @param string $classyConstructName
     * @return bool
     */
    public function exists(string $classyConstructName): bool
    {
        return $this->composer->findFile($classyConstructName) !== false;
    }

    /**
     * Finds the path to the file where the class is defined.
     *
     * @param string $class The name of the class
     * @return string|false The path if found, false otherwise
     */
    public function findFile($class)
    {
        return $this->composer->findFile($class);
    }
}
