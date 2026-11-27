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

namespace bitExpert\PHPStan\Magento\Fixtures;

use Magento\Catalog\Api\Data\ProductExtensionInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\RequestInterface\Proxy as RequestProxy;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;

/**
 * Fixture analysed against a real Magento installation by the real-codebase
 * workflow. Every method resolves only when one of the extension's
 * generated-class autoloaders or type extensions works against the real
 * framework code, so an analysis of this file with zero errors proves them.
 */
class ConsumesGeneratedClasses
{
    /**
     * @var ProductFactory
     */
    private $productFactory;
    /**
     * @var RequestProxy
     */
    private $request;
    /**
     * @var ResultFactory
     */
    private $resultFactory;
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        ProductFactory $productFactory,
        RequestProxy $request,
        ResultFactory $resultFactory,
        ObjectManagerInterface $objectManager
    ) {
        $this->productFactory = $productFactory;
        $this->request = $request;
        $this->resultFactory = $resultFactory;
        $this->objectManager = $objectManager;
    }

    public function factoryGeneratedByTheFactoryAutoloader(): Product
    {
        return $this->productFactory->create();
    }

    public function proxyGeneratedByTheProxyAutoloaderKeepsDefaults(): void
    {
        $this->request->setDispatched();
        $this->request->getParam('id');
    }

    public function extensionInterfaceGeneratedByTheExtensionInterfaceAutoloader(
        ProductExtensionInterface $extensionAttributes
    ): ProductExtensionInterface {
        return $extensionAttributes;
    }

    public function objectManagerReturnTypeIsResolvedByTheTypeExtension(): DataObject
    {
        return $this->objectManager->create(DataObject::class)->setData('key', 'value');
    }

    public function resultFactoryReturnTypeIsResolvedByTheTypeExtension(): void
    {
        $this->resultFactory->create(ResultFactory::TYPE_JSON)->setHttpResponseCode(200);
    }

    /**
     * @return mixed
     */
    public function dataObjectMagicGetterIsResolvedByTheReflectionExtension(DataObject $dataObject)
    {
        return $dataObject->getKey();
    }
}
