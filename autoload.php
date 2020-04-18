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
use PHPStan\Cache\Cache;

// This autoloader implementation supersedes the former \bitExpert\PHPStan\Magento\Autoload\Autoload implementation
(function () {
    $cache = new Cache(new FileCacheStorage(sys_get_temp_dir() . '/phpstan/cache/PHPStan'));

    $mockAutoloader = new MockAutoloader();
    $factoryAutoloader = new FactoryAutoloader($cache);
    $proxyAutoloader = new ProxyAutoloader($cache);

    \spl_autoload_register([$mockAutoloader, 'autoload'], true, true);
    \spl_autoload_register([$factoryAutoloader, 'autoload'], true, false);
    \spl_autoload_register([$proxyAutoloader, 'autoload'], true, false);
})();
