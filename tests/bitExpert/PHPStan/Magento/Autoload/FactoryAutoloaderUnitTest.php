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

use bitExpert\PHPStan\Magento\Autoload\Cache\GeneratedFileCache;
use bitExpert\PHPStan\Magento\Autoload\DataProvider\ClassLoaderProvider;
use PHPUnit\Framework\TestCase;

class FactoryAutoloaderUnitTest extends TestCase
{
    /**
     * @var GeneratedFileCache|\PHPUnit\Framework\MockObject\MockObject
     */
    private $cache;
    /**
     * @var FactoryAutoloader
     */
    private $autoloader;
    /**
     * @var ClassLoaderProvider|\PHPUnit\Framework\MockObject\MockObject
     */
    private $classLoader;
    /**
     * @var string[]
     */
    private $tempFiles = [];

    public function setUp(): void
    {
        $this->cache = $this->createMock(GeneratedFileCache::class);
        $this->classLoader = $this->createMock(ClassLoaderProvider::class);

        $this->autoloader = new FactoryAutoloader($this->cache, $this->classLoader);
    }

    public function tearDown(): void
    {
        foreach ($this->tempFiles as $tempFile) {
            @unlink($tempFile);
        }

        $this->tempFiles = [];
    }

    /**
     * @test
     */
    public function autoloaderIgnoresClassesWithoutFactoryPostfix(): void
    {
        $this->classLoader->expects(self::never())
            ->method('findFile');
        $this->cache->expects(self::never())
            ->method('getFile');

        $this->autoloader->autoload('SomeClass');
    }

    /**
     * @test
     */
    public function autoloaderPrefersLocalFile(): void
    {
        $this->classLoader->expects(self::once())
            ->method('findFile')
            ->willReturn(__DIR__ . '/HelperFactory.php');
        $this->cache->expects(self::never())
            ->method('getFile');

        $this->autoloader->autoload(HelperFactory::class);

        self::assertTrue(class_exists(HelperFactory::class, false));
    }

    /**
     * @test
     */
    public function autoloaderUsesCachedFileWhenFound(): void
    {
        $this->classLoader->expects(self::once())
            ->method('findFile')
            ->willReturn(false);
        $this->cache->expects(self::once())
            ->method('getFile')
            ->willReturn(__DIR__ . '/HelperFactory.php');
        $this->cache->expects(self::never())
            ->method('putFile');

        $this->autoloader->autoload(HelperFactory::class);

        self::assertTrue(class_exists(HelperFactory::class, false));
    }

    /**
     * @test
     */
    public function autoloaderGeneratesCacheFileWhenNotFoundInCache(): void
    {
        $this->classLoader->expects(self::once())
            ->method('findFile')
            ->willReturn(false);
        $this->cache->expects(self::once())
            ->method('getFile')
            ->willReturn(null);
        $this->cache->expects(self::once())
            ->method('putFile')
            ->willReturn(__DIR__ . '/HelperFactory.php');

        $this->autoloader->autoload(HelperFactory::class);

        self::assertTrue(class_exists(HelperFactory::class, false));
    }

    /**
     * @test
     */
    public function autoloaderRequiresTheGeneratedFileAndNotTheGeneratedSource(): void
    {
        $className = 'bitExpert\PHPStan\Magento\Autoload\PathNotSourceThingFactory';

        $this->classLoader->expects(self::once())
            ->method('findFile')
            ->willReturn(false);
        $this->cache->expects(self::once())
            ->method('getFile')
            ->willReturn(null);
        $this->cache->expects(self::once())
            ->method('putFile')
            ->willReturnCallback(function (string $key, string $contents): string {
                $file = sys_get_temp_dir() . '/phpstan-magento-' . uniqid() . '.php';
                file_put_contents($file, $contents);
                $this->tempFiles[] = $file;

                return $file;
            });

        $this->autoloader->autoload($className);

        self::assertTrue(class_exists($className, false));
    }

    /**
     * @test
     */
    public function autoloaderGeneratesFactoryForCorrectClassname(): void
    {
        $this->classLoader->expects(self::once())
            ->method('findFile')
            ->willReturn(false);
        $this->cache->expects(self::once())
            ->method('getFile')
            ->willReturn(null);
        $this->cache->expects(self::once())
            ->method('putFile')
            ->with(
                'bitExpert\PHPStan\Magento\Autoload\FactoryThingFactory',
                static::stringContains(<<<DOC
/**
 * Factory class for @see \bitExpert\PHPStan\Magento\Autoload\FactoryThing
 */
DOC
                )
            )
            ->willReturn(__DIR__ . '/FactoryThingFactory.php');

        $this->autoloader->autoload(FactoryThingFactory::class);
    }
}
