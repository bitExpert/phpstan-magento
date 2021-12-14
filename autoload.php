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
    die(-1);
}

foreach ($container->getServicesByTag('phpstan.magento.autoloader') as $autoloader) {
    /** @var Autoloader|object $autoloader */
    if (!$autoloader instanceof Autoloader) {
        echo 'Services tagged with \'phpstan.magento.autoloader\' must extend ' .
            'bitExpert\PHPStan\Magento\Autoload\Autoloader!' . PHP_EOL .
            get_class($autoloader) . ' does not.' . PHP_EOL;
        die(-1);
    }

    $autoloader->register();
}
