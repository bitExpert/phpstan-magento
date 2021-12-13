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

use bitExpert\PHPStan\Magento\Autoload\Autoloader;
use PHPStan\DependencyInjection\Container;

if (!isset($container) || !$container instanceof Container) {
    echo 'No container found, or container not of expected type' . PHP_EOL;
    return;
}

foreach ($container->getServicesByTag('phpstan.magento.autoloader') as $autoloader) {
    /** @var Autoloader $autoloader */
    $autoloader->register();
}
