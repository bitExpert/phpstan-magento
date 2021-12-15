<?php

namespace bitExpert\PHPStan\Magento\Autoload;

use bitExpert\PHPStan\Magento\Autoload\Cache\FileCacheStorage;
use Magento\Framework\Component\ComponentRegistrar;
use org\bovigo\vfs\vfsStream;
use PHPStan\Cache\Cache;
use PHPStan\Cache\CacheStorage;
use PHPUnit\Framework\TestCase;

class ExtensionInterfaceAutoloaderUnitTest extends TestCase
{
    /** @var \org\bovigo\vfs\vfsStreamDirectory */
    private $root;

    protected function setUp(): void
    {
        $this->root = vfsStream::setup('test');
    }

    /**
     * @test
     */
    public function autoloadIgnoresClassesWithoutExtensionInterface(): void
    {
        $cacheStorage = $this->getMockBuilder(CacheStorage::class)->getMock();
        $autoloader = new ExtensionInterfaceAutoloader(
            new Cache($cacheStorage),
            $this->root->url()
        );
        $cacheStorage->expects(self::never())->method('load');
        $autoloader->autoload('ExtensionInterfaceNotAtEnd');
    }

    /**
     * @test
     */
    public function autoloadUsesCachedFileWhenFound(): void
    {
        $interfaceName = 'CachedExtensionInterface';
        $cache = new Cache(new FileCacheStorage($this->root->url() . '/tmp/cache/PHPStan'));
        $cache->save($interfaceName, '', "<?php interface ${interfaceName} {}");
        $autoloader = new ExtensionInterfaceAutoloader($cache, $this->root->url());

        static::assertFalse(interface_exists($interfaceName));
        $autoloader->autoload($interfaceName);
        static::assertTrue(interface_exists($interfaceName));
    }

    /**
     * @test
     */
    public function autoloadDoesNotGenerateInterfaceWhenNoAttributesExist(): void
    {
        $interfaceName = 'NonExistentExtensionInterface';
        $cache = new Cache(new FileCacheStorage($this->root->url() . '/tmp/cache/PHPStan'));
        $autoloader = new ExtensionInterfaceAutoloader($cache, $this->root->url());

        $autoloader->autoload($interfaceName);
        static::assertFalse(interface_exists($interfaceName));
    }

    /**
     * @test
     */
    public function autoloadGeneratesInterfaceWhenNotCached(): void
    {
        $interfaceName = 'UncachedExtensionInterface';
        vfsStream::create([
            'interface.php' => '<?php interface UncachedInterface {}',
            'app' => [
                'etc' => [
                    'config.php' => <<<PHP
<?php return [
    'modules' => [
        'Module_Name' => 1
    ]
];
PHP
                ],
                'code' => [
                    'Module_Name' => [
                        'etc' => [
                            'extension_attributes.xml' => <<<'XML'
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:framework:Api/etc/extension_attributes.xsd">
    <extension_attributes for="UncachedInterface">
        <attribute code="attr" type="string"/>
    </extension_attributes>
</config>
XML
                        ]
                    ]
                ]
            ],
        ], $this->root);

        ComponentRegistrar::register(
            ComponentRegistrar::MODULE,
            'Module_Name',
            $this->root->url() . '/app/code/Module_Name'
        );

        $autoloader = new ExtensionInterfaceAutoloader(
            new Cache(new FileCacheStorage($this->root->url() . '/tmp/cache/PHPStan')),
            $this->root->url()
        );

        require $this->root->url() . '/interface.php';

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
