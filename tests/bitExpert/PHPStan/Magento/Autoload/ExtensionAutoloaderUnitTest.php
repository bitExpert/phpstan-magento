<?php

namespace bitExpert\PHPStan\Magento\Autoload;

use bitExpert\PHPStan\Magento\Autoload\Cache\FileCacheStorage;
use bitExpert\PHPStan\Magento\Autoload\DataProvider\ClassLoaderProvider;
use bitExpert\PHPStan\Magento\Autoload\DataProvider\ExtensionAttributeDataProvider;
use org\bovigo\vfs\vfsStream;
use PHPStan\Cache\Cache;
use PHPStan\Cache\CacheStorage;
use PHPUnit\Framework\TestCase;

class ExtensionAutoloaderUnitTest extends TestCase
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
     * @var ClassLoaderProvider&\PHPUnit\Framework\MockObject\MockObject
     */
    private $classLoader;
    /**
     * @var ExtensionAttributeDataProvider&\PHPUnit\Framework\MockObject\MockObject
     */
    private $extAttrDataProvider;
    /**
     * @var ExtensionAutoloader
     */
    private $autoloader;

    protected function setUp(): void
    {
        $this->cacheStorage = $this->createMock(CacheStorage::class);
        $this->cache = new Cache($this->cacheStorage);
        $this->classLoader = $this->createMock(ClassLoaderProvider::class);
        $this->extAttrDataProvider = $this->createMock(ExtensionAttributeDataProvider::class);
        $this->autoloader = new ExtensionAutoloader(
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
            ->willReturn(__DIR__ . '/HelperExtension.php');
        $this->cacheStorage->expects(self::never())
            ->method('load');

        $this->autoloader->autoload(HelperExtension::class);

        self::assertTrue(class_exists(HelperExtension::class, false));
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
            ->willReturn(__DIR__ . '/HelperExtension.php');

        $this->cacheStorage->expects(self::never())
            ->method('save');

        $this->autoloader->autoload(HelperExtension::class);

        self::assertTrue(class_exists(HelperExtension::class, false));
    }

    /**
     * @test
     */
    public function autoloadGeneratesInterfaceWhenNotCached(): void
    {
        $this->classLoader->expects(self::once())
            ->method('findFile')
            ->willReturn(false);
        $this->extAttrDataProvider->expects(self::once())
            ->method('getAttributesForInterface')
            ->willReturn(['attr' => 'string']);

        $className = 'MyUncachedExtension';
        // since the generated class implements an interface, we need to make it available here, otherwise
        // the autoloader will fail with an exception that the interface can't be found!
        class_alias(HelperExtensionInterface::class, 'MyUncachedExtensionInterface');

        $root = vfsStream::setup('test');
        $cache = new Cache(new FileCacheStorage($root->url() . '/tmp/cache/PHPStan'));
        $autoloader = new ExtensionAutoloader($cache, $this->classLoader, $this->extAttrDataProvider);

        $autoloader->autoload($className);
        static::assertTrue(class_exists($className));
        $classReflection = new \ReflectionClass($className);
        try {
            $getAttrReflection = $classReflection->getMethod('getAttr');
            $docComment = $getAttrReflection->getDocComment();
            if (!is_string($docComment)) {
                throw new \ReflectionException();
            }
            static::assertStringContainsString('@return string|null', $docComment);
        } catch (\ReflectionException $e) {
            static::fail('Could not find expected method getAttr on generated class');
        }

        try {
            $setAttrReflection = $classReflection->getMethod('setAttr');
            $docComment = $setAttrReflection->getDocComment();
            if (!is_string($docComment)) {
                throw new \ReflectionException();
            }
            static::assertStringContainsString('@param string $attr', $docComment);
        } catch (\ReflectionException $e) {
            static::fail('Could not find expected generated method setAttr on generated class');
        }
    }
}
