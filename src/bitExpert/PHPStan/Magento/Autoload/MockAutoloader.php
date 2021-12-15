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
 * The MockAutoloader is responsible to load custom mocked classes or interfaces instead of the original Magento classes
 * or interfaces. This is needed as not all interfaces expose all public methods that can be called on those objects.
 */
class MockAutoloader implements Autoloader
{
    public function autoload(string $class): void
    {
        $filename = realpath(__DIR__ . '/../../../../' . str_replace('\\', '/', $class) . '.php');
        if (!is_bool($filename) && file_exists($filename) && is_readable($filename)) {
            include($filename);
        }
    }

    public function register(): void
    {
        \spl_autoload_register([$this, 'autoload'], true, true);
    }

    public function unregister(): void
    {
        \spl_autoload_unregister([$this, 'autoload']);
    }
}
