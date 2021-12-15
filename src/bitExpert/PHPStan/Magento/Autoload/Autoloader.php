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

interface Autoloader
{
    /**
     * Begin autoloading
     */
    public function register(): void;

    /**
     * Stop autoloading
     */
    public function unregister(): void;
}
