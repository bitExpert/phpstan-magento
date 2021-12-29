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

use bitExpert\PHPStan\Magento\Autoload\DataProvider\ExtensionAttributeDataProvider;
use Laminas\Code\Generator\DocBlock\Tag\ParamTag;
use Laminas\Code\Generator\DocBlock\Tag\ReturnTag;
use Laminas\Code\Generator\DocBlockGenerator;
use Laminas\Code\Generator\InterfaceGenerator;
use Laminas\Code\Generator\MethodGenerator;
use PHPStan\Cache\Cache;

class ExtensionInterfaceAutoloader implements Autoloader
{
    /**
     * @var Cache
     */
    private $cache;
    /**
     * @var ExtensionAttributeDataProvider
     */
    private $attributeDataProvider;

    /**
     * ExtensionInterfaceAutoloader constructor.
     *
     * @param Cache $cache
     * @param ExtensionAttributeDataProvider $attributeDataProvider
     */
    public function __construct(Cache $cache, ExtensionAttributeDataProvider $attributeDataProvider)
    {
        $this->cache = $cache;
        $this->attributeDataProvider = $attributeDataProvider;
    }

    public function autoload(string $class): void
    {
        if (preg_match('#ExtensionInterface$#', $class) !== 1) {
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

        require_once($cachedFilename);
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

        $generator = new InterfaceGenerator();
        $generator
            ->setName($interfaceName)
            ->setImplementedInterfaces(['\Magento\Framework\Api\ExtensionAttributesInterface']);

        $attrs = $this->attributeDataProvider->getAttributesForInterface($sourceInterface);
        foreach ($attrs as $propertyName => $type) {
            /**
             * Generate getters and setters for each extension attribute
             *
             * @see \Magento\Framework\Api\Code\Generator\ExtensionAttributesGenerator::_getClassMethods
             */

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
                            new ParamTag($propertyName, [$type]),
                            new ReturnTag('$this')
                        ]
                    ])
                ])
            );
        }

        return "<?php\n\n" . $generator->generate();
    }

    public function register(): void
    {
        \spl_autoload_register([$this, 'autoload'], true, false);
    }

    public function unregister(): void
    {
        \spl_autoload_unregister([$this, 'autoload']);
    }
}
