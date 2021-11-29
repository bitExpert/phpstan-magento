<?php

namespace bitExpert\PHPStan\Magento\Autoload;

use Laminas\Code\Generator\DocBlock\Tag\ParamTag;
use Laminas\Code\Generator\DocBlock\Tag\ReturnTag;
use Laminas\Code\Generator\DocBlockGenerator;
use Laminas\Code\Generator\InterfaceGenerator;
use Laminas\Code\Generator\MethodGenerator;
use Magento\Framework\Api\SimpleDataObjectConverter;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\DeploymentConfig\Reader as DeploymentConfigReader;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Config\File\ConfigFilePool;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Module\Declaration\Converter\Dom as ModuleDeclarationDom;
use Magento\Framework\Module\ModuleList;
use Magento\Framework\Module\ModuleList\Loader;
use Magento\Framework\Xml\Parser as XmlParser;
use PHPStan\Cache\Cache;
use Symfony\Component\Finder\Finder;

class ExtensionInterfaceAutoloader
{
    private $moduleList;

    private $componentRegistrar;

    private $cache;

    private $xmlDocs;

    public function __construct(
        ModuleList $moduleList,
        ComponentRegistrar $componentRegistrar,
        Cache $cache
    ) {
        $this->moduleList = $moduleList;
        $this->componentRegistrar = $componentRegistrar;
        $this->cache = $cache;
    }

    public function autoload(string $class): void
    {
        if (preg_match('/ExtensionInterface$/', $class) !== 1) {
            return;
        }

        $cachedFilename = $this->cache->load($class, '');
        if ($cachedFilename === null) {
            try {
                $this->cache->save($class, '', $this->getFileContents($class));
                $cachedFilename = $this->cache->load($class, '');
            } catch (\Exception $e) {
                return;
            }
        }

        require_once $cachedFilename;
    }

    /**
     * Given an extension attributes interface name, generate that interface (if possible)
     */
    public function getFileContents(string $interfaceName): string
    {
        /**
         * Given a classname to autoload (such as Magento\Catalog\Api\Data\ProductExtensionInterface),
         * generate the entity's interface name (like Magento\Catalog\Api\Data\ProductInterface)
         *
         * @see \Magento\Framework\Code\Generator::generateClass
         * @see \Magento\Framework\Api\Code\Generator\ExtensionAttributesGenerator::__construct
         */
        $sourceInterface = rtrim(substr($interfaceName, 0, -1 * strlen('ExtensionInterface')), '\\') . 'Interface';

        // Magento only creates extension attribute interfaces for existing interfaces; retain that logic
        if (!interface_exists($sourceInterface)) {
            throw new \InvalidArgumentException("${sourceInterface} does not exist and has no extension interface");
        }

        $generator = new InterfaceGenerator();
        $generator
            ->setName($interfaceName)
            ->setImplementedInterfaces([\Magento\Framework\Api\ExtensionAttributesInterface::class]);

        foreach ($this->getExtensionAttributesXmlDocs() as $doc) {
            $xpath = new \DOMXPath($doc);
            $attrs = $xpath->query(
                "//extension_attributes[@for=\"${sourceInterface}\"]/attribute",
                $doc->documentElement
            );
            /** @var \DOMElement $attr */
            foreach ($attrs as $attr) {
                /**
                 * Generate getters and setters for each extension attribute
                 *
                 * @see \Magento\Framework\Api\Code\Generator\ExtensionAttributesGenerator::_getClassMethods
                 */
                $propertyName = SimpleDataObjectConverter::snakeCaseToCamelCase($attr->getAttribute('code'));
                $type = $this->getAttrType($attr);

                $generator->addMethodFromGenerator(
                    MethodGenerator::fromArray([
                        'name' => 'get' . ucfirst($propertyName),
                        'docblock' => DocBlockGenerator::fromArray([
                            'tags' => [
                                new ReturnTag([$type, 'null']),
                            ],
                        ]),
                    ])
                );
                $generator->addMethodFromGenerator(
                    MethodGenerator::fromArray([
                        'name' => 'set' . ucfirst($propertyName),
                        'parameters' => [$propertyName],
                        'docblock' => DocBlockGenerator::fromArray([
                            'tags' => [
                                new ParamTag(
                                    $propertyName,
                                    [
                                        $type
                                    ]
                                ),
                                new ReturnTag(
                                    '$this'
                                )
                            ]
                        ])
                    ])
                );
            }
        }

        return "<?php\n\n" . $generator->generate();
    }

    /**
     * Create a generator which creates DOM documents for every extension attributes XML file in enabled modules
     *
     * @return \DOMDocument[]
     */
    private function getExtensionAttributesXmlDocs(): array
    {
        if (is_array($this->xmlDocs)) {
            return $this->xmlDocs;
        }

        $enabledModuleDirs = array_filter(
            $this->componentRegistrar->getPaths(ComponentRegistrar::MODULE),
            function ($moduleName) {
                return $this->moduleList->has($moduleName);
            },
            ARRAY_FILTER_USE_KEY
        );

        $finder = Finder::create()
            ->files()
            ->in(array_map(function ($dir) {
                return $dir . '/etc';
            }, $enabledModuleDirs))
            ->name('extension_attributes.xml');

        $this->xmlDocs = [];
        foreach ($finder as $item) {
            $doc = new \DOMDocument();
            $doc->loadXML($item->getContents());
            $this->xmlDocs[] = $doc;
        }

        return $this->xmlDocs;
    }

    public static function create(Cache $cache): ExtensionInterfaceAutoloader
    {
        $componentRegistrar = new ComponentRegistrar();
        return new ExtensionInterfaceAutoloader(
            new ModuleList(
                new DeploymentConfig(
                    new DeploymentConfigReader(
                        new DirectoryList('.'), // todo: the path passed to directory list should be app's base dir
                        new Filesystem\DriverPool(),
                        new ConfigFilePool()
                    )
                ),
                new Loader(
                    new ModuleDeclarationDom(),
                    new XmlParser(),
                    $componentRegistrar,
                    new FileDriver()
                )
            ),
            $componentRegistrar,
            $cache
        );
    }

    /**
     * @param \DOMElement $attr
     *
     * @return string
     */
    protected function getAttrType(\DOMElement $attr): string
    {
        $type = $attr->getAttribute('type');
        $cleanType = str_replace('[]', '', $type);
        return class_exists($cleanType) || interface_exists($cleanType) || trait_exists($cleanType)
            ? '\\' . $type : $type;
    }
}
