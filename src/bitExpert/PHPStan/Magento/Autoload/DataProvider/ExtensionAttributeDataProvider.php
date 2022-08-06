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

use DOMDocument;
use DOMElement;
use DOMXPath;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ExtensionAttributeDataProvider
{
    /**
     * @var string
     */
    private $magentoRoot;
    /**
     * @var DOMDocument[]|null
     */
    private $xmlDocs;

    /**
     * ExtensionAttributeDataProvider constructor.
     *
     * @param string $magentoRoot
     */
    public function __construct(string $magentoRoot)
    {
        $this->magentoRoot = $magentoRoot;
    }

    /**
     * Returns
     * @param string $sourceInterface
     * @return array<string, string>
     */
    public function getAttributesForInterface(string $sourceInterface): array
    {
        $return = [];

        foreach ($this->getExtensionAttributesXmlDocs() as $doc) {
            $xpath = new DOMXPath($doc);
            $attrs = $xpath->query(
                "//extension_attributes[@for=\"${sourceInterface}\"]/attribute",
                $doc->documentElement
            );

            if ($attrs === false) {
                continue;
            }

            foreach ($attrs as $attr) {
                /** @var DOMElement $attr */
                $propertyName = $this->getAttrName($attr);
                $type = $this->getAttrType($attr);
                $return[$propertyName] = $type;
            }
        }

        return $return;
    }


    /**
     * Create a generator which creates DOM documents for every extension attributes XML file found.
     *
     * @return DOMDocument[]
     */
    protected function getExtensionAttributesXmlDocs(): array
    {
        if (is_array($this->xmlDocs)) {
            return $this->xmlDocs;
        }

        $finder = Finder::create()
            ->files()
            ->in($this->magentoRoot)
            ->name('extension_attributes.xml')
            ->filter(static function (SplFileInfo $file) {
                // ignore any files not located in an etc directory to exclude e.g. test data
                return $file->isFile() && (bool) preg_match('#etc/extension_attributes.xml$#', $file->getPathname());
            });

        $this->xmlDocs = [];
        foreach ($finder as $item) {
            /** @var SplFileInfo $item */
            $doc = new DOMDocument();
            $doc->loadXML($item->getContents());
            $this->xmlDocs[] = $doc;
        }

        return $this->xmlDocs;
    }

    /**
     * Extracts and formats the attribute type out of the given DOM element.
     *
     * @param DOMElement $attr
     * @return string
     */
    protected function getAttrType(DOMElement $attr): string
    {
        $type = $attr->getAttribute('type');
        $cleanType = str_replace('[]', '', $type);

        $primitiveTypes = ['float', 'int', 'string', 'bool', 'boolean'];
        return in_array(strtolower($cleanType), $primitiveTypes, true) ? $type : '\\'.$type;
    }

    /**
     * Extracts and formats the attribute name out of the given DOM element
     * @param DOMElement $attr
     * @return string
     */
    protected function getAttrName(DOMElement $attr): string
    {
        // see Magento\Framework\Api\SimpleDataObjectConverter::snakeCaseToCamelCase()
        $attrName = $attr->getAttribute('code');
        $attrName = str_replace('_', '', ucwords($attrName, '_'));
        return lcfirst($attrName);
    }
}
