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

namespace bitExpert\PHPStan\Magento\Autoload\DataProvider;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class ExtensionAttributeDataProviderUnitTest extends TestCase
{
    /**
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    private $root;

    protected function setUp(): void
    {
        $this->root = vfsStream::setup('test');
    }

    /**
     * @test
     */
    public function returnsArrayWhenAttrsForInterfaceExist(): void
    {
        vfsStream::create([
            'etc' => [
                'extension_attributes.xml' => <<<'XML'
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:framework:Api/etc/extension_attributes.xsd">
    <extension_attributes for="Magento\Sales\Api\Data\OrderInterface">
        <attribute code="attr" type="string"/>
    </extension_attributes>
</config>
XML
            ]
        ], $this->root);

        $dataprovider = new ExtensionAttributeDataProvider($this->root->url());
        $attrs = $dataprovider->getAttributesForInterface('Magento\Sales\Api\Data\OrderInterface');

        static::assertCount(1, $attrs);
        static::assertSame('string', $attrs['attr']);
    }

    /**
     * @test
     */
    public function returnsEmptyArrayWhenNoAttrsForInterfaceExist(): void
    {
        vfsStream::create([
            'etc' => [
                'extension_attributes.xml' => <<<'XML'
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:framework:Api/etc/extension_attributes.xsd">
    <extension_attributes for="Magento\Sales\Api\Data\OrderInterface">
        <attribute code="attr" type="string"/>
    </extension_attributes>
</config>
XML
            ]
        ], $this->root);

        $dataprovider = new ExtensionAttributeDataProvider($this->root->url());
        $attrs = $dataprovider->getAttributesForInterface('Some\Random\Api\Data\SampleInterface');

        static::assertCount(0, $attrs);
    }

    /**
     * @test
     */
    public function loadsAndMergesAttributesFromDifferentSourceFiles(): void
    {
        vfsStream::create([
            'etc' => [
                'extension_attributes.xml' => <<<'XML'
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:framework:Api/etc/extension_attributes.xsd">
    <extension_attributes for="Magento\Sales\Api\Data\OrderInterface">
        <attribute code="attr" type="string"/>
    </extension_attributes>
</config>
XML
            ],
            'vendor' => [
                'vendor1' => [
                    'package1' => [
                        'etc' => [
                            'extension_attributes.xml' => <<<'XML'
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="urn:magento:framework:Api/etc/extension_attributes.xsd">
<extension_attributes for="Magento\Sales\Api\Data\OrderInterface">
    <attribute code="attr2" type="string"/>
</extension_attributes>
</config>
XML
                        ]
                    ]
                ]
            ]
        ], $this->root);

        $dataprovider = new ExtensionAttributeDataProvider($this->root->url());
        $attrs = $dataprovider->getAttributesForInterface('Magento\Sales\Api\Data\OrderInterface');

        static::assertCount(2, $attrs);
        static::assertSame('string', $attrs['attr']);
        static::assertSame('string', $attrs['attr2']);
    }

    /**
     * @test
     */
    public function ignoresFilesInNonEtcDir(): void
    {
        vfsStream::create([
            'test' => [
                'My' => [
                    'Namespace' => [
                        'extension_attributes.xml' => <<<'XML'
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="urn:magento:framework:Api/etc/extension_attributes.xsd">
<extension_attributes for="Magento\Sales\Api\Data\OrderInterface">
    <attribute code="attr2" type="string"/>
</extension_attributes>
</config>
XML
                    ]
                ]
            ]
        ], $this->root);

        $dataprovider = new ExtensionAttributeDataProvider($this->root->url());
        $attrs = $dataprovider->getAttributesForInterface('Magento\Sales\Api\Data\OrderInterface');

        static::assertCount(0, $attrs);
    }

    /**
     * @test
     */
    public function primitiveTypesAreReturnedAsIs(): void
    {
        vfsStream::create([
            'etc' => [
                'extension_attributes.xml' => <<<'XML'
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:framework:Api/etc/extension_attributes.xsd">
    <extension_attributes for="Magento\Sales\Api\Data\OrderInterface">
        <attribute code="attr" type="float"/>
        <attribute code="attr2" type="int"/>
        <attribute code="attr3" type="string"/>
        <attribute code="attr4" type="bool"/>
        <attribute code="attr5" type="boolean"/>
    </extension_attributes>
</config>
XML
            ]
        ], $this->root);

        $dataprovider = new ExtensionAttributeDataProvider($this->root->url());
        $attrs = $dataprovider->getAttributesForInterface('Magento\Sales\Api\Data\OrderInterface');

        static::assertCount(5, $attrs);
        static::assertSame('float', $attrs['attr']);
        static::assertSame('int', $attrs['attr2']);
        static::assertSame('string', $attrs['attr3']);
        static::assertSame('bool', $attrs['attr4']);
        static::assertSame('boolean', $attrs['attr5']);
    }

    /**
     * @test
     */
    public function classTypeIsReturnedWithValidNamespace(): void
    {
        vfsStream::create([
            'etc' => [
                'extension_attributes.xml' => <<<'XML'
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:framework:Api/etc/extension_attributes.xsd">
    <extension_attributes for="Magento\Sales\Api\Data\OrderInterface">
        <attribute code="attr" type="Magento\Quote\Api\Data\CartInterface"/>
    </extension_attributes>
</config>
XML
            ]
        ], $this->root);

        $dataprovider = new ExtensionAttributeDataProvider($this->root->url());
        $attrs = $dataprovider->getAttributesForInterface('Magento\Sales\Api\Data\OrderInterface');

        static::assertCount(1, $attrs);
        static::assertSame('\Magento\Quote\Api\Data\CartInterface', $attrs['attr']);
    }
}
