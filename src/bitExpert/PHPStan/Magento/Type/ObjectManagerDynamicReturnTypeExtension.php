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
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

class ObjectManagerDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return 'Magento\Framework\App\ObjectManager';
    }

    /**
     * @param MethodReflection $methodReflection
     * @return bool
     */
    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return in_array(
            $methodReflection->getName(),
            ['create', 'get'],
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
}
