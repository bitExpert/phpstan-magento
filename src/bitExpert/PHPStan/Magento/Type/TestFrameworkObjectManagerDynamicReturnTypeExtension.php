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

namespace bitExpert\PHPStan\Magento\Type;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ErrorType;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

class TestFrameworkObjectManagerDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return 'Magento\Framework\TestFramework\Unit\Helper\ObjectManager';
    }

    /**
     * @param MethodReflection $methodReflection
     * @return bool
     */
    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return in_array(
            $methodReflection->getName(),
            ['getObject', 'getConstructArguments', 'getCollectionMock'],
            true
        );
    }

    /**
     * @param MethodReflection $methodReflection
     * @param MethodCall $methodCall
     * @param Scope $scope
     * @return Type
     */
    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type {
        $methodName = ($methodCall->name instanceof Identifier) ? $methodCall->name->toString() : $methodCall->name;
        switch ($methodName) {
            case 'getObject':
                return $this->getTypeForGetObjectMethodCall($methodReflection, $methodCall, $scope);
            case 'getConstructArguments':
                return $this->getTypeForGetConstructArgumentsMethodCall($methodReflection, $methodCall, $scope);
            case 'getCollectionMock':
                return $this->getTypeForGetCollectionMockMethodCall($methodReflection, $methodCall, $scope);
            default:
                return new ErrorType();
        }
    }

    /**
     * @param MethodReflection $methodReflection
     * @param MethodCall $methodCall
     * @param Scope $scope
     * @return Type
     */
    private function getTypeForGetObjectMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type {
        $mixedType = new MixedType();
        if (count($methodCall->args) === 0) {
            return $mixedType;
        }

        /** @var \PhpParser\Node\Arg[] $args */
        $args = $methodCall->args;
        $argType = $scope->getType($args[0]->value);
        if (!$argType instanceof ConstantStringType) {
            return $mixedType;
        }
        return TypeCombinator::addNull(new ObjectType($argType->getValue()));
    }

    /**
     * @param MethodReflection $methodReflection
     * @param MethodCall $methodCall
     * @param Scope $scope
     * @return Type
     */
    private function getTypeForGetConstructArgumentsMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type {
        return TypeCombinator::addNull(new ArrayType(new StringType(), new MixedType()));
    }

    /**
     * @param MethodReflection $methodReflection
     * @param MethodCall $methodCall
     * @param Scope $scope
     * @return Type
     */
    private function getTypeForGetCollectionMockMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type {
        /** @var \PhpParser\Node\Arg[] $args */
        $args = $methodCall->args;
        /** @var ConstantStringType $type */
        $type = $scope->getType($args[0]->value);
        /** @var string $className */
        $className = $type->getValue();
        if (!is_subclass_of($className, 'Magento\Framework\Data\Collection')) {
            return new \PHPStan\Type\ErrorType();
        }

        $type = TypeCombinator::intersect(
            new ObjectType($className),
            new ObjectType('PHPUnit\Framework\MockObject\MockObject')
        );

        return $type;
    }
}
