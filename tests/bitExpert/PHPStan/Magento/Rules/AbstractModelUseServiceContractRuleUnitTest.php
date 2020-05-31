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
 * @extends \PHPStan\Testing\RuleTestCase<AbstractModelUseServiceContractRule>
 */
class AbstractModelUseServiceContractRuleUnitTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new AbstractModelUseServiceContractRule();
    }

    /**
     * @test
     */
    public function checkCaughtExceptions(): void
    {
        $this->analyse([__DIR__ . '/Helper/service_contract.php'], [
            [
                'Use service contracts to persist entities in favour of ' . SampleModel::class . '::save() method',
                04,
            ],
            [
                'Use service contracts to persist entities in favour of ' . SampleModel::class . '::delete() method',
                05,
            ],
            [
                'Use service contracts to persist entities in favour of ' . SampleModel::class . '::load() method',
                06,
            ],
        ]);
    }
}
