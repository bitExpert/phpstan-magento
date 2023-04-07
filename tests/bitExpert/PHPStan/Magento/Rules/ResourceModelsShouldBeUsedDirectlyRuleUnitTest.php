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
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends \PHPStan\Testing\RuleTestCase<ResourceModelsShouldBeUsedDirectlyRule>
 */
class ResourceModelsShouldBeUsedDirectlyRuleUnitTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new ResourceModelsShouldBeUsedDirectlyRule();
    }

    /**
     * @test
     */
    public function checkCaughtExceptions(): void
    {
        $this->analyse([__DIR__ . '/Helper/resource_model.php'], [
            [
                SampleModel::class . '::getResource() is deprecated. Use Resource Models directly',
                4,
            ],
        ]);
    }

    /**
     * @test
     */
    public function getNodeTypeMethodReturnsMethodCall(): void
    {
        $rule = new ResourceModelsShouldBeUsedDirectlyRule();

        self::assertSame(MethodCall::class, $rule->getNodeType());
    }

    /**
     * @test
     */
    public function processNodeThrowsExceptionForNonMethodCallNodes(): void
    {
        $this->expectException(ShouldNotHappenException::class);

        $node = new Variable('var');
        $scope = $this->createMock(Scope::class);

        $rule = new ResourceModelsShouldBeUsedDirectlyRule();
        $rule->processNode($node, $scope);
    }

    /**
     * @test
     */
    public function processNodeReturnsEarlyWhenNodeNameIsWrongType(): void
    {
        $node = new MethodCall(new Variable('var'), new Variable('wrong_node'));
        $scope = $this->createMock(Scope::class);

        $rule = new ResourceModelsShouldBeUsedDirectlyRule();
        $return = $rule->processNode($node, $scope);

        self::assertCount(0, $return);
    }

    /**
     * @test
     */
    public function processNodeReturnsEarlyWhenNodeNameIsNotSaveOrLoadOrDelete(): void
    {
        $node = new MethodCall(new Variable('var'), 'wrong_node_name');
        $scope = $this->createMock(Scope::class);

        $rule = new ResourceModelsShouldBeUsedDirectlyRule();
        $return = $rule->processNode($node, $scope);

        self::assertCount(0, $return);
    }
}
