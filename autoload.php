<?php

/*
 * This file is part of the phpstan-magento package.
 *
 * (c) bitExpert AG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use bitExpert\PHPStan\Magento\Autoload\Cache\FileCacheStorage;
use bitExpert\PHPStan\Magento\Autoload\FactoryAutoloader;
use bitExpert\PHPStan\Magento\Autoload\MockAutoloader;
use bitExpert\PHPStan\Magento\Autoload\ProxyAutoloader;
use bitExpert\PHPStan\Magento\Autoload\TestFrameworkAutoloader;
use Nette\Neon\Neon;
use PHPStan\Cache\Cache;

if (!isset($container)) {
    return;
}

// This autoloader implementation supersedes the former \bitExpert\PHPStan\Magento\Autoload\Autoload implementation
/** @var \PHPStan\DependencyInjection\Container $container */
(function () use ($container) {

    // Get the cache from PHPStan's container
    // see https://github.com/bitExpert/phpstan-magento/pull/163#issuecomment-990926534
    foreach ($container->findServiceNamesByType(Cache::class) as $cacheServiceName) {
        if (!$container->hasService($cacheServiceName)) {
            continue;
        }

        /** @var Cache|null $cache */
        $cache = $container->getService($cacheServiceName);
        break;
    }

    if (!isset($cache)) {
        // TODO: throw, create our own cache?
        return;
    }

    $mockAutoloader = new MockAutoloader();
    $testFrameworkAutoloader = new TestFrameworkAutoloader();
    $factoryAutoloader = new FactoryAutoloader($cache);
    $proxyAutoloader = new ProxyAutoloader($cache);

    \spl_autoload_register([$mockAutoloader, 'autoload'], true, true);
    \spl_autoload_register([$testFrameworkAutoloader, 'autoload'], true, false);
    \spl_autoload_register([$factoryAutoloader, 'autoload'], true, false);
    \spl_autoload_register([$proxyAutoloader, 'autoload'], true, false);
})();
