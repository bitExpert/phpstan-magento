<?php

namespace bitExpert\PHPStan\Magento\Autoload;

use bitExpert\PHPStan\Magento\Autoload\Cache\FileCacheStorage;
use bitExpert\PHPStan\Magento\Autoload\DataProvider\ClassLoaderProvider;
use bitExpert\PHPStan\Magento\Autoload\DataProvider\ExtensionAttributeDataProvider;
use InvalidArgumentException;
use org\bovigo\vfs\vfsStream;
use PHPStan\Cache\Cache;
use PHPStan\Cache\CacheStorage;
use PHPUnit\Framework\TestCase;

class ExtensionInterfaceAutoloaderUnitTest extends TestCase
{
    /**
     * @var Cache
     */
    private $cache;
    /**
     * @var CacheStorage&\PHPUnit\Framework\MockObject\MockObject
     */
    private $cacheStorage;
    /**
     * @var ExtensionAttributeDataProvider&\PHPUnit\Framework\MockObject\MockObject
     */
    private $extAttrDataProvider;
    /**
     * @var ClassLoaderProvider&\PHPUnit\Framework\MockObject\MockObject
     */
    private $classLoader;
    /**
     * @var ExtensionInterfaceAutoloader
     */
    private $autoloader;

    protected function setUp(): void
    {
        $this->cacheStorage = $this->createMock(CacheStorage::class);
        $this->cache = new Cache($this->cacheStorage);
        $this->classLoader = $this->createMock(ClassLoaderProvider::class);
        $this->extAttrDataProvider = $this->createMock(ExtensionAttributeDataProvider::class);
        $this->autoloader = new ExtensionInterfaceAutoloader(
            $this->cache,
            $this->classLoader,
            $this->extAttrDataProvider
        );
    }

    /**
     * @test
     */
    public function autoloaderIgnoresClassesWithoutExtensionInterfacePostfix(): void
    {
        $this->classLoader->expects(self::never())
            ->method('findFile');
        $this->cacheStorage->expects(self::never())
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
            ->willReturn(__DIR__ . '/HelperExtensionInterface.php');
        $this->cacheStorage->expects(self::never())
            ->method('load');

        $this->autoloader->autoload(HelperExtensionInterface::class);

        self::assertTrue(interface_exists(HelperExtensionInterface::class, false));
    }

    /**
     * @test
     */
    public function autoloaderUsesCachedFileWhenFound(): void
    {
        $this->classLoader->expects(self::once())
            ->method('findFile')
            ->willReturn(false);
        $this->cacheStorage->expects(self::once())
            ->method('load')
            ->willReturn(__DIR__ . '/HelperExtensionInterface.php');

        $this->cacheStorage->expects(self::never())
            ->method('save');

        $this->autoloader->autoload(HelperExtensionInterface::class);

        self::assertTrue(interface_exists(HelperExtensionInterface::class, false));
    }

    /**
     * @test
     */
    public function autoloadDoesNotGenerateInterfaceWhenNoAttributesExist(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('NonExistentInterface does not exist and has no extension interface');

        $interfaceName = 'NonExistentExtensionInterface';

        $this->classLoader->expects(self::once())
            ->method('findFile')
            ->willReturn(false);
        $this->cacheStorage->expects(self::once())
            ->method('load')
            ->willReturn(null);

        $this->classLoader->expects(self::once())
            ->method('exists')
            ->willReturn(false);

        $this->autoloader->autoload($interfaceName);
    }

    /**
     * @test
     */
    public function autoloadGeneratesInterfaceWhenNotCached(): void
    {
        $interfaceName = 'UncachedExtensionInterface';

        $root = vfsStream::setup('test');
        $cache = new Cache(new FileCacheStorage($root->url() . '/tmp/cache/PHPStan'));
        $autoloader = new ExtensionInterfaceAutoloader($cache, $this->classLoader, $this->extAttrDataProvider);

        $this->classLoader->expects(self::once())
            ->method('findFile')
            ->willReturn(false);

        $this->classLoader->expects(self::once())
            ->method('exists')
            ->willReturn(true);

        $this->extAttrDataProvider->expects(self::once())
            ->method('getAttributesForInterface')
            ->willReturn(['attr' => 'string']);

        $autoloader->autoload($interfaceName);
        static::assertTrue(interface_exists($interfaceName));

        $interfaceReflection = new \ReflectionClass($interfaceName);
        try {
            $getAttrReflection = $interfaceReflection->getMethod('getAttr');
            $docComment = $getAttrReflection->getDocComment();
            if (!is_string($docComment)) {
                throw new \ReflectionException();
            }
            static::assertStringContainsString('@return string|null', $docComment);
        } catch (\ReflectionException $e) {
            static::fail('Could not find expected method getAttr on generated interface');
        }

        try {
            $setAttrReflection = $interfaceReflection->getMethod('setAttr');
            $docComment = $setAttrReflection->getDocComment();
            if (!is_string($docComment)) {
                throw new \ReflectionException();
            }
            static::assertStringContainsString('@param string $attr', $docComment);
        } catch (\ReflectionException $e) {
            static::fail('Could not find expected generated method setAttr on generated interface');
        }
    }
}
