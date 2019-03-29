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

class Autoloader
{
    /**
     * @var MockAutoloader
     */
    private static $mockAutoloader = null;
    /**
     * @var FactoryAutoloader
     */
    private static $factoryAutoloader = null;
    /**
     * @var ProxyAutoloader
     */
    private static $proxyAutoloader = null;

    /**
     * @param bool $throw
     * @param bool $prepend
     * @return bool
     */
    public static function register(bool $throw = true, bool $prepend = false): bool
    {
        if (null !== self::$mockAutoloader && null !== self::$factoryAutoloader && null !== self::$proxyAutoloader) {
            return false;
        }

        self::$mockAutoloader = new MockAutoloader();
        self::$factoryAutoloader = new FactoryAutoloader();
        self::$proxyAutoloader = new ProxyAutoloader();

        return \spl_autoload_register([self::$mockAutoloader, 'autoload'], $throw, true) &&
            \spl_autoload_register([self::$factoryAutoloader, 'autoload'], $throw, $prepend) &&
            \spl_autoload_register([self::$proxyAutoloader, 'autoload'], $throw, $prepend);
    }

    /**
     * @return bool
     */
    public static function unregister(): bool
    {
        if (null === self::$mockAutoloader && null === self::$factoryAutoloader && null === self::$proxyAutoloader) {
            return false;
        }

        $result = \spl_autoload_unregister([self::$mockAutoloader, 'autoload']) &&
            \spl_autoload_unregister([self::$factoryAutoloader, 'autoload']) &&
            \spl_autoload_unregister([self::$proxyAutoloader, 'autoload']);

        self::$mockAutoloader = null;
        self::$factoryAutoloader = null;
        self::$proxyAutoloader = null;
        return $result;
    }
}
