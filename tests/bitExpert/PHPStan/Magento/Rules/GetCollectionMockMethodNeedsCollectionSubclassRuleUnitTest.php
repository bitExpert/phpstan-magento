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

namespace bitExpert\PHPStan\Magento\Rules;

use bitExpert\PHPStan\Magento\Rules\Helper\SampleModel;
use bitExpert\PHPStan\Magento\Type\TestFrameworkObjectManagerDynamicReturnTypeExtension;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends \PHPStan\Testing\RuleTestCase<GetCollectionMockMethodNeedsCollectionSubclassRule>
 */
class GetCollectionMockMethodNeedsCollectionSubclassRuleUnitTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new GetCollectionMockMethodNeedsCollectionSubclassRule();
    }

    /**
     * @return \PHPStan\Type\DynamicMethodReturnTypeExtension[]
     */
    public function getDynamicMethodReturnTypeExtensions() : array
    {
        return [
            new TestFrameworkObjectManagerDynamicReturnTypeExtension(),
        ];
    }

    /**
     * @test
     */
    public function checkCaughtExceptions(): void
    {
        $this->analyse([__DIR__ . '/Helper/objectmanager_collectionmock.php'], [
            [
                'DateTime does not extend \Magento\Framework\Data\Collection as required!',
                7,
            ],
        ]);
    }
}
