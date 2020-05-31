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
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends \PHPStan\Testing\RuleTestCase<AbstractModelRetrieveCollectionViaFactoryRule>
 */
class AbstractModelRetrieveCollectionViaFactoryRuleUnitTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new AbstractModelRetrieveCollectionViaFactoryRule(true);
    }

    /**
     * @test
     */
    public function checkCaughtException(): void
    {
        $this->analyse([__DIR__ . '/Helper/collection.php'], [
            [
                'Collections should be used directly via factory, not via ' .
                    SampleModel::class . '::getCollection() method',
                04
            ]
        ]);
    }
}
