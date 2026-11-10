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

class ProxyAutoloaderUnitTest extends TestCase
{
    /**
     * @var GeneratedFileCache|\PHPUnit\Framework\MockObject\MockObject
     */
    private $cache;
    /**
     * @var ProxyAutoloader
     */
    private $autoloader;
    /**
     * @var ClassLoaderProvider|\PHPUnit\Framework\MockObject\MockObject
     */
    private $classLoader;

    public function setUp(): void
    {
        $this->cache = $this->createMock(GeneratedFileCache::class);
        $this->classLoader = $this->createMock(ClassLoaderProvider::class);

        $this->autoloader = new ProxyAutoloader($this->cache, $this->classLoader);
    }

    /**
     * @test
     */
    public function autoloaderIgnoresClassesWithoutProxyPostfix(): void
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
            ->willReturn(__DIR__ . '/HelperProxy.php');
        $this->cache->expects(self::never())
            ->method('getFile');

        $this->autoloader->autoload('\bitExpert\PHPStan\Magento\Autoload\Helper\Proxy');

        self::assertTrue(class_exists(HelperProxy::class, false));
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
            ->willReturn(__DIR__ . '/HelperProxy.php');

        $this->autoloader->autoload('\bitExpert\PHPStan\Magento\Autoload\Helper\Proxy');

        self::assertTrue(class_exists(HelperProxy::class, false));
    }

    /**
     * @test
     */
    public function autoloaderGeneratesCacheFileWhenNotFoundInCache(): void
    {
        // little hack: the proxy autoloader will use Reflection to look for a class without the \Proxy prefix,
        // to avoid having another stub class file, we define an class alias here
        class_alias('\bitExpert\PHPStan\Magento\Autoload\HelperProxy', '\bitExpert\PHPStan\Magento\Autoload\Helper');

        $this->classLoader->expects(self::once())
            ->method('findFile')
            ->willReturn(false);
        $this->cache->expects(self::once())
            ->method('getFile')
            ->willReturn(null);
        $this->cache->expects(self::once())
            ->method('putFile')
            ->willReturn(__DIR__ . '/HelperProxy.php');

        $this->autoloader->autoload('\bitExpert\PHPStan\Magento\Autoload\Helper\Proxy');

        self::assertTrue(class_exists(HelperProxy::class, false));
    }

    /**
     * @test
     */
    public function generatedProxyKeepsTheParameterDefaultsOfAnInterface(): void
    {
        $this->classLoader->expects(self::once())
            ->method('findFile')
            ->willReturn(false);
        $this->cache->expects(self::once())
            ->method('getFile')
            ->willReturn(null);
        $file = tempnam(sys_get_temp_dir(), 'phpstan-magento-proxy');
        $this->cache->expects(self::once())
            ->method('putFile')
            ->willReturnCallback(static function (string $class, string $content) use ($file): string {
                file_put_contents($file, $content);
                return $file;
            });

        $this->autoloader->autoload('\\bitExpert\\PHPStan\\Magento\\Autoload\\HelperWithDefaults\\Proxy');
        unlink($file);

        $method = new \ReflectionMethod('\\bitExpert\\PHPStan\\Magento\\Autoload\\HelperWithDefaults\\Proxy', 'call');
        $defaults = [];
        foreach ($method->getParameters() as $parameter) {
            $defaults[$parameter->getName()] = $parameter->isDefaultValueAvailable()
                ? $parameter->getDefaultValue()
                : 'none';
        }
        self::assertSame(
            [
                'null' => null,
                'false' => false,
                'true' => true,
                'zero' => 0,
                'one' => 1,
                'quote' => "it's",
                'empty' => [],
                'list' => ['a' => 1],
                'constant' => 10,
            ],
            $defaults
        );
    }
}
