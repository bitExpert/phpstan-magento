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
use PHPStan\Cache\Cache;
use PHPStan\Cache\CacheStorage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class InterceptorAutoloaderUnitTest extends TestCase
{
    private CacheStorage&MockObject $storage;
    private ClassLoaderProvider&MockObject $classLoader;
    private InterceptorAutoloader $autoloader;

    public function setUp(): void
    {
        $this->storage = $this->createMock(CacheStorage::class);
        $this->classLoader = $this->createMock(ClassLoaderProvider::class);

        $this->autoloader = new InterceptorAutoloader(new Cache($this->storage), $this->classLoader);
    }

    /**
     * @test
     */
    public function autoloaderIgnoresClassesWithoutInterceptorPostfix(): void
    {
        $this->classLoader->expects(self::never())
            ->method('findFile');
        $this->storage->expects(self::never())
            ->method('load');

        $this->autoloader->autoload('SomeClass');
    }

    /**
     * @test
     */
    public function autoloaderPrefersLocalFile(): void
    {
        $this->classLoader->expects(self::once())
            ->method('findFile')
            ->willReturn(__DIR__ . '/HelperInterceptor.php');
        $this->storage->expects(self::never())
            ->method('load');

        $this->autoloader->autoload('\bitExpert\PHPStan\Magento\Autoload\Helper\Interceptor');

        self::assertTrue(class_exists(HelperInterceptor::class, false));
    }

    /**
     * @test
     */
    public function autoloaderUsesCachedFileWhenFound(): void
    {
        $this->classLoader->expects(self::once())
            ->method('findFile')
            ->willReturn(false);
        $this->storage->expects(self::once())
            ->method('load')
            ->willReturn(__DIR__ . '/HelperInterceptor.php');

        $this->autoloader->autoload('\bitExpert\PHPStan\Magento\Autoload\Helper\Interceptor');

        self::assertTrue(class_exists(HelperInterceptor::class, false));
    }

    /**
     * @test
     */
    public function autoloaderGeneratesCacheFileWhenNotFoundInCache(): void
    {
        $this->classLoader->expects(self::once())
            ->method('findFile')
            ->willReturn(false);
        $this->storage->expects(self::atMost(2))
            ->method('load')
            ->willReturnOnConsecutiveCalls(null, __DIR__ . '/HelperInterceptor.php');
        $this->storage->expects(self::once())
            ->method('save');

        $this->autoloader->autoload('\bitExpert\PHPStan\Magento\Autoload\Helper\Interceptor');

        self::assertTrue(class_exists(HelperInterceptor::class, false));
    }
}