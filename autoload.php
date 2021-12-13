<?php

/*
 * This file is part of the phpstan-magento package.
 *
 * (c) bitExpert AG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// This autoloader implementation supersedes the former \bitExpert\PHPStan\Magento\Autoload\Autoload implementation

use PHPStan\DependencyInjection\Container;

if (!isset($container) || !$container instanceof Container) {
    echo 'No container found, or container not of expected type' . PHP_EOL;
    return;
}

foreach ($container->getParameter('magento')['autoloaders'] as $autoloaderConfig) {
    // see structure for magento.autoloaders in extension.neon
    ['serviceName' => $serviceName, 'method' => $method, 'throw' => $throw, 'prepend' => $prepend] = $autoloaderConfig;

    if (!$container->hasService($serviceName)) {
        // warn about this
        echo "cannot find autoloader {$serviceName}, please ensure it's configured as a service" . PHP_EOL;
        continue;
    }

    $autoloader = $container->getService($serviceName);
    \spl_autoload_register([$autoloader, $method], $throw, $prepend);
}
