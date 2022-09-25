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
use Laminas\Code\Generator\ClassGenerator;
use Laminas\Code\Generator\DocBlock\Tag\ParamTag;
use Laminas\Code\Generator\DocBlock\Tag\ReturnTag;
use Laminas\Code\Generator\DocBlockGenerator;
use Laminas\Code\Generator\MethodGenerator;
use Laminas\Code\Generator\ParameterGenerator;
use PHPStan\Cache\Cache;

class ExtensionAutoloader implements Autoloader
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
     * ExtensionAutoloader constructor.
     *
     * @param Cache $cache
     * @param ExtensionAttributeDataProvider $attributeDataProvider
     */
    public function __construct(
        Cache $cache,
        ExtensionAttributeDataProvider $attributeDataProvider
    ) {
        $this->cache = $cache;
        $this->attributeDataProvider = $attributeDataProvider;
    }

    public function autoload(string $class): void
    {
        if (preg_match('#Extension$#', $class) !== 1) {
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
    public function getFileContents(string $className): string
    {
        /** @var class-string $sourceInterface */
        $sourceInterface = rtrim(substr($className, 0, -1 * strlen('Extension')), '\\') . 'ExtensionInterface';
        /** @var class-string $attrInterface */
        $attrInterface = rtrim(substr($sourceInterface, 0, -1 * strlen('ExtensionInterface')), '\\') . 'Interface';

        $generator = new ClassGenerator();
        $generator
            ->setName($className)
            ->setExtendedClass('\Magento\Framework\Api\AbstractSimpleObject')
            ->setImplementedInterfaces([$sourceInterface]);

        $attrs = $this->attributeDataProvider->getAttributesForInterface($attrInterface);
        foreach ($attrs as $propertyName => $type) {
            /**
             * Generate getters and setters for each extension attribute
             *
             * @see \Magento\Framework\Api\Code\Generator\ExtensionAttributesGenerator::_getClassMethods
             */

            // treat array types properly in the generated code. Similar to Magento core MyInterface[] type gets
            // converted to just an array
            $paramType = $type;
            if (strpos($type, '[]') !== false) {
                $paramType = null;
            }

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
                    'parameters' => [new ParameterGenerator($propertyName, $paramType)],
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
