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
use PHPStan\Cache\CacheStorage;
use PHPUnit\Framework\TestCase;

class FactoryAutoloaderUnitTest extends TestCase
{
    /**
     * @var CacheStorage|\PHPUnit\Framework\MockObject\MockObject
     */
    private $storage;
    /**
     * @var FactoryAutoloader
     */
    private $autoloader;

    public function setUp(): void
    {
        $this->storage = $this->createMock(CacheStorage::class);
        $this->autoloader = new FactoryAutoloader(new Cache($this->storage));
    }

    /**
     * @test
     */
    public function autoloaderIgnoresClassesWithoutFactoryPostfix(): void
    {
        $this->storage->expects($this->never())
            ->method('load');

        $this->autoloader->autoload('SomeClass');
    }

    /**
     * @test
     */
    public function autoloaderUsesCachedFileWhenFound(): void
    {
        $this->storage->expects($this->once())
            ->method('load')
            ->willReturn(__DIR__ . '/HelperFactory.php');

        $this->autoloader->autoload(HelperFactory::class);

        self::assertTrue(class_exists(HelperFactory::class, false));
    }

    /**
     * @test
     */
    public function autoloaderGeneratesCacheFileWhenNotFoundInCache(): void
    {
        $this->storage->expects($this->atMost(2))
            ->method('load')
            ->willReturnOnConsecutiveCalls(null, __DIR__ . '/HelperFactory.php');
        $this->storage->expects($this->once())
            ->method('save');

        $this->autoloader->autoload(HelperFactory::class);

        self::assertTrue(class_exists(HelperFactory::class, false));
    }
}
