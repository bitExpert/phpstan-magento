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

use bitExpert\PHPStan\Magento\Type\TestFrameworkObjectManagerDynamicReturnTypeExtension;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\ErrorType;
use PHPStan\Type\ObjectType;

/**
 * \Magento\Framework\TestFramework\Unit\Helper\ObjectManager::getCollectionMock() needs first parameter to extend
 * \Magento\Framework\Data\Collection
 *
 * @implements Rule<MethodCall>
 */
class GetCollectionMockMethodNeedsCollectionSubclassRule implements Rule
{
    /**
     * @return class-string<\PhpParser\Node\Expr\MethodCall>
     */
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param Node $node
     * @param Scope $scope
     * @return (string|\PHPStan\Rules\RuleError)[] errors
     * @throws ShouldNotHappenException
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node instanceof MethodCall) {
            throw new ShouldNotHappenException();
        }

        if (!$node->name instanceof Node\Identifier) {
            return [];
        }

        if ($node->name->name !== 'getCollectionMock') {
            return [];
        }

        $dynReturnTypeExt = new TestFrameworkObjectManagerDynamicReturnTypeExtension();

        $type = $scope->getType($node->var);
        $isAbstractModelType = (new ObjectType($dynReturnTypeExt->getClass()))->isSuperTypeOf($type);
        if (!$isAbstractModelType->yes()) {
            return [];
        }

        // the return type check is done in TestFrameworkObjectManagerDynamicReturnTypeExtension. When an ErrorType
        // is returned, it's an indication that the type check failed. That's why we only need to check for the
        // ErrorType here
        $returnType = $scope->getType($node);
        if (!$returnType->equals(new ErrorType())) {
            return [];
        }

        /** @var \PhpParser\Node\Arg[] $args */
        $args = $node->args;
        /** @var ConstantStringType $argType */
        $argType = $scope->getType($args[0]->value);
        return [
            sprintf(
                '%s does not extend \Magento\Framework\Data\Collection as required!',
                $argType->getValue()
            )
        ];
    }
}
