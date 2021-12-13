<?php

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
