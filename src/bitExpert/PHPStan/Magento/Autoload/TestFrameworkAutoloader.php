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

/**
 * Autoloader for Magento\TestFramework classes as those are not loaded by Composer by default which makes PHPStan
 * not know about them.
 */
class TestFrameworkAutoloader implements Autoloader
{
    /**
     * @var string
     */
    private $magentoRoot;

    /**
     * TestFrameworkAutoloader constructor.
     *
     * @param string $magentoRoot
     */
    public function __construct(string $magentoRoot)
    {
        $this->magentoRoot = $magentoRoot;
    }

    public function autoload(string $class): void
    {
        $class = str_replace('\\', '/', $class);
        $testsBaseDir = $this->magentoRoot.'/dev/tests/static';

        $directories = [
            // try to find Magento\TestFramework classes...
            $testsBaseDir . '/framework/',
            $testsBaseDir . '/../integration/framework/',
            $testsBaseDir . '/../api-functional/framework/',
            // try to find Magento classes...
            $testsBaseDir . '/testsuite/',
            $testsBaseDir . '/framework/',
            $testsBaseDir . '/framework/tests/unit/testsuite/',
        ];

        foreach ($directories as $directory) {
            $filename = realpath($directory . $class . '.php');
            if (!is_bool($filename) && file_exists($filename) && is_readable($filename)) {
                include($filename);
                break;
            }
        }
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
