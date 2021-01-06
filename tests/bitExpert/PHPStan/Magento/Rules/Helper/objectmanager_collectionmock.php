<?php

$objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager(
    new class extends PHPUnit\Framework\TestCase {
    }
);
$mock = $objectManager->getCollectionMock(\DateTime::class, []);
