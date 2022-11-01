<?php

namespace bitExpert\PHPStan\Magento\Autoload;

use bitExpert\PHPStan\Magento\Autoload\Cache\FileCacheStorage;
use bitExpert\PHPStan\Magento\Autoload\DataProvider\ClassLoaderProvider;
use bitExpert\PHPStan\Magento\Autoload\DataProvider\ExtensionAttributeDataProvider;
use InvalidArgumentException;
use org\bovigo\vfs\vfsStream;
use PHPStan\Cache\Cache;
use PHPUnit\Framework\TestCase;

class ExtensionInterfaceAutoloaderUnitTest extends TestCase
{
    /**
     * @var Cache|\PHPUnit\Framework\MockObject\MockObject
     */
    private $cache;
    /**
     * @var ExtensionAttributeDataProvider|\PHPUnit\Framework\MockObject\MockObject
     */
    private $extAttrDataProvider;
    /**
     * @var ClassLoaderProvider|\PHPUnit\Framework\MockObject\MockObject
     */
    private $classyDataProvider;
    /**
     * @var ExtensionInterfaceAutoloader
     */
    private $autoloader;

    protected function setUp(): void
    {
        $this->cache = $this->createMock(Cache::class);
        $this->extAttrDataProvider = $this->createMock(ExtensionAttributeDataProvider::class);
        $this->classyDataProvider = $this->createMock(ClassLoaderProvider::class);
        $this->autoloader = new ExtensionInterfaceAutoloader(
            $this->cache,
            $this->extAttrDataProvider,
            $this->classyDataProvider
        );
    }

    /**
     * @test
     */
    public function autoloaderIgnoresClassesWithoutExtensionInterfacePostfix(): void
    {
        $this->classyDataProvider->expects(self::never())
            ->method('findFile');
        $this->cache->expects(self::never())
            ->method('load');

        $this->autoloader->autoload('SomeClass');
    }

    /**
     * @test
     */
    public function autoloaderPrefersLocalFile(): void
    {
        $this->classyDataProvider->expects(self::once())
            ->method('findFile')
            ->willReturn(__DIR__ . '/HelperExtensionInterface.php');
        $this->cache->expects(self::never())
            ->method('load');

        $this->autoloader->autoload(HelperExtensionInterface::class);

        self::assertTrue(interface_exists(HelperExtensionInterface::class, false));
    }

    /**
     * @test
     */
    public function autoloaderUsesCachedFileWhenFound(): void
    {
        $this->classyDataProvider->expects(self::once())
            ->method('findFile')
            ->willReturn(false);
        $this->cache->expects(self::once())
            ->method('load')
            ->willReturn(__DIR__ . '/HelperExtensionInterface.php');

        $this->cache->expects(self::never())
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

        $interfaceName = 'NonExistentExtensionInterface';

        $this->classyDataProvider->expects(self::once())
            ->method('findFile')
            ->willReturn(false);
        $this->cache->expects(self::once())
            ->method('load')
            ->willReturn(null);

        $this->classyDataProvider->expects(self::once())
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
        $autoloader = new ExtensionInterfaceAutoloader($cache, $this->extAttrDataProvider, $this->classyDataProvider);

        $this->classyDataProvider->expects(self::once())
            ->method('findFile')
            ->willReturn(false);

        $this->classyDataProvider->expects(self::once())
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
