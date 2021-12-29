<?php

namespace bitExpert\PHPStan\Magento\Autoload;

use bitExpert\PHPStan\Magento\Autoload\DataProvider\ExtensionAttributeDataProvider;
use PHPStan\Cache\Cache;
use PHPStan\Cache\CacheStorage;
use PHPUnit\Framework\TestCase;

class ExtensionInterfaceAutoloaderUnitTest extends TestCase
{
    /**
     * @var CacheStorage|\PHPUnit\Framework\MockObject\MockObject
     */
    private $storage;
    /**
     * @var ExtensionAttributeDataProvider|\PHPUnit\Framework\MockObject\MockObject
     */
    private $dataProvider;
    /**
     * @var ExtensionInterfaceAutoloader
     */
    private $autoloader;

    protected function setUp(): void
    {
        $this->storage = $this->createMock(CacheStorage::class);
        $this->dataProvider = $this->createMock(ExtensionAttributeDataProvider::class);
        $this->autoloader = new ExtensionInterfaceAutoloader(new Cache($this->storage), $this->dataProvider);
    }

    /**
     * @test
     */
    public function autoloaderIgnoresClassesWithoutExtensionInterfacePostfix(): void
    {
        $this->storage->expects(self::never())
            ->method('load');

        $this->autoloader->autoload('SomeClass');
    }

    /**
     * @test
     */
    public function autoloaderUsesCachedFileWhenFound(): void
    {
        $this->storage->expects(self::once())
            ->method('load')
            ->willReturn(__DIR__ . '/HelperExtensionInterface.php');

        $this->autoloader->autoload(HelperExtensionInterface::class);

        self::assertTrue(interface_exists(HelperExtensionInterface::class, false));
    }
}
