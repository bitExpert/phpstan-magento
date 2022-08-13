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

use bitExpert\PHPStan\Magento\Autoload\DataProvider\ClassLoaderProvider;
use bitExpert\PHPStan\Magento\Autoload\DataProvider\ExtensionAttributeDataProvider;
use PHPStan\Cache\Cache;
use PHPUnit\Framework\TestCase;

class RegistrationUnitTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideAutoloaders()
     */
    public function autoloadersCanRegisterAndUnregister(Autoloader $autoloader): void
    {
        /** @var array<callable> $initialAutoloadFunctions */
        $initialAutoloadFunctions = spl_autoload_functions();

        $autoloader->register();
        /** @var array<callable> $registerAutoloadFunctions */
        $registerAutoloadFunctions = spl_autoload_functions();
        static::assertCount(count($initialAutoloadFunctions) + 1, $registerAutoloadFunctions);

        $autoloader->unregister();
        /** @var array<callable> $unregisterAutoloadFunctions */
        $unregisterAutoloadFunctions = spl_autoload_functions();
        static::assertCount(count($initialAutoloadFunctions), $unregisterAutoloadFunctions);
    }

    /**
     * @return array<array<Autoloader>>
     */
    public function provideAutoloaders(): array
    {
        $cache = new Cache($this->getMockBuilder(\PHPStan\Cache\CacheStorage::class)->getMock());
        $classLoader = $this->createMock(ClassLoaderProvider::class);

        return [
            [new FactoryAutoloader($cache, $classLoader)],
            [new MockAutoloader()],
            [new ProxyAutoloader($cache, $classLoader)],
            [new TestFrameworkAutoloader(__DIR__)],
            [new ExtensionInterfaceAutoloader(
                $cache,
                new ExtensionAttributeDataProvider(__DIR__),
                new ClassLoaderProvider(__DIR__)
            )]
        ];
    }
}
