<?php

namespace bitExpert\PHPStan\Magento\Autoload;

use bitExpert\PHPStan\Magento\Autoload\Cache\FileCacheStorage;
use bitExpert\PHPStan\Magento\Autoload\DataProvider\ExtensionAttributeDataProvider;
use org\bovigo\vfs\vfsStream;
use PHPStan\Cache\Cache;
use PHPUnit\Framework\TestCase;

class ExtensionAutoloaderUnitTest extends TestCase
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
     * @var ExtensionAutoloader
     */
    private $autoloader;

    protected function setUp(): void
    {
        $this->cache = $this->createMock(Cache::class);
        $this->extAttrDataProvider = $this->createMock(ExtensionAttributeDataProvider::class);
        $this->autoloader = new ExtensionAutoloader(
            $this->cache,
            $this->extAttrDataProvider
        );
    }

    /**
     * @test
     */
    public function autoloaderIgnoresClassesWithoutExtensionInterfacePostfix(): void
    {
        $this->cache->expects(self::never())
            ->method('load');

        $this->autoloader->autoload('SomeClass');
    }

    /**
     * @test
     */
    public function autoloaderUsesCachedFileWhenFound(): void
    {
        $this->cache->expects(self::once())
            ->method('load')
            ->willReturn(__DIR__ . '/HelperExtension.php');

        $this->cache->expects(self::never())
            ->method('save');

        $this->autoloader->autoload(HelperExtension::class);

        self::assertTrue(class_exists(HelperExtension::class, false));
    }

    /**
     * @test
     */
    public function autoloadGeneratesInterfaceWhenNotCached(): void
    {
        $className = 'MyUncachedExtension';
        // since the generated class implements an interface, we need to make it available here, otherwise
        // the autoloader will fail with an exception that the interface can't be found!
        class_alias(HelperExtensionInterface::class, 'MyUncachedExtensionInterface');

        $root = vfsStream::setup('test');
        $cache = new Cache(new FileCacheStorage($root->url() . '/tmp/cache/PHPStan'));
        $autoloader = new ExtensionAutoloader($cache, $this->extAttrDataProvider);

        $this->extAttrDataProvider->expects(self::once())
            ->method('getAttributesForInterface')
            ->willReturn(['attr' => 'string']);

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
