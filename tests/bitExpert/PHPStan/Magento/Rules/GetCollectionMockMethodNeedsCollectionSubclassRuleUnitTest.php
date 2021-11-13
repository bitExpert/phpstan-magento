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
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
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

    public static function getAdditionalConfigFiles(): array
    {
        // make sure to load \bitExpert\PHPStan\Magento\Type\TestFrameworkObjectManagerDynamicReturnTypeExtension
        // which is needed for the integration test
        return array_merge(
            parent::getAdditionalConfigFiles(),
            [__DIR__ . '/Helper/dynamic_method_returntype_extension.neon']
        );
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

    /**
     * @test
     */
    public function getNodeTypeMethodReturnsMethodCall(): void
    {
        $rule = new GetCollectionMockMethodNeedsCollectionSubclassRule();

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

        $rule = new GetCollectionMockMethodNeedsCollectionSubclassRule();
        $rule->processNode($node, $scope);
    }

    /**
     * @test
     */
    public function processNodeReturnsEarlyWhenNodeNameIsWrongType(): void
    {
        $node = new MethodCall(new Variable('var'), new Variable('wrong_node'));
        $scope = $this->createMock(Scope::class);

        $rule = new GetCollectionMockMethodNeedsCollectionSubclassRule();
        $return = $rule->processNode($node, $scope);

        self::assertCount(0, $return);
    }

    /**
     * @test
     */
    public function processNodeReturnsEarlyWhenNodeNameIsNotGetCollectionMock(): void
    {
        $node = new MethodCall(new Variable('var'), 'wrong_node_name');
        $scope = $this->createMock(Scope::class);

        $rule = new GetCollectionMockMethodNeedsCollectionSubclassRule();
        $return = $rule->processNode($node, $scope);

        self::assertCount(0, $return);
    }
}
