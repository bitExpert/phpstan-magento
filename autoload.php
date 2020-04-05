<?php

/*
 * This file is part of the phpstan-magento package.
 *
 * (c) bitExpert AG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// Include this file if you are using the phar version of PHPStan. Since the phar version make use of dynamic namespaces
// the hack in registration.php to overload spl_autoload_register() and spl_autoload_unregister() does not work any more.
\bitExpert\PHPStan\Magento\Autoload\Autoloader::register();
