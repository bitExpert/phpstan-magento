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

use PHPStan\Cache\Cache;
use PHPUnit\Framework\TestCase;

class RegistrationUnitTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideAutoloaders()
     */
    public function autoloadersCanRegisterAndUnregister(Autoloader $autoloader)
    {
        $autoloadFunctions = spl_autoload_functions();
        $autoloader->register();
        static::assertCount(count($autoloadFunctions) + 1, spl_autoload_functions());
        $autoloader->unregister();
        static::assertCount(count($autoloadFunctions), spl_autoload_functions());
    }

    public function provideAutoloaders(): array
    {
        $cache = new Cache($this->getMockBuilder(\PHPStan\Cache\CacheStorage::class)->getMock());

        return [
            [new FactoryAutoloader($cache)],
            [new MockAutoloader()],
            [new ProxyAutoloader($cache)],
            [new TestFrameworkAutoloader()]
        ];
    }
}
