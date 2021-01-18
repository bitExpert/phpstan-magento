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

namespace bitExpert\PHPStan\Magento\Reflection;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\FunctionVariant;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Reflection\Php\DummyParameter;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\ArrayType;
use PHPStan\Type\BooleanType;
use PHPStan\Type\Generic\TemplateTypeMap;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\NullType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\UnionType;

abstract class AbstractMagicMethodReflectionExtension implements MethodsClassReflectionExtension
{
    /**
     * @param ClassReflection $classReflection
     * @param string $methodName
     * @return MethodReflection
     * @throws ShouldNotHappenException
     */
    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        $methodPrefix = substr($methodName, 0, 3);
        switch ($methodPrefix) {
            case 'get':
                return $this->returnGetMagicMethod($classReflection, $methodName);
            case 'set':
                return $this->returnSetMagicMethod($classReflection, $methodName);
            case 'uns':
                return $this->returnUnsetMagicMethod($classReflection, $methodName);
            case 'has':
                return $this->returnHasMagicMethod($classReflection, $methodName);
            default:
                throw new ShouldNotHappenException();
        }
    }

    /**
     * Helper method to create magic method reflection for get() calls.
     *
     * @param ClassReflection $classReflection
     * @param string $methodName
     * @return MethodReflection
     * @throws ShouldNotHappenException
     */
    protected function returnGetMagicMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        $params = [];
        if ($methodName === 'getData') {
            $params = [
                new DummyParameter(
                    'key',
                    new StringType(),
                    true,
                    null,
                    false,
                    null
                ),
                new DummyParameter(
                    'index',
                    new UnionType([new StringType(), new IntegerType(), new NullType()]),
                    true,
                    null,
                    false,
                    null
                )
            ];
        } else {
            $params = [
                new DummyParameter(
                    'value',
                    new MixedType(),
                    true,
                    null,
                    false,
                    null
                )
            ];
        }

        $returnType = new MixedType();

        $variants = new FunctionVariant(
            TemplateTypeMap::createEmpty(),
            null,
            $params,
            false,
            $returnType
        );

        return new MagicMethodReflection($methodName, $classReflection, [$variants]);
    }

    /**
     * Helper method to create magic method reflection for set() calls.
     *
     * @param ClassReflection $classReflection
     * @param string $methodName
     * @return MethodReflection
     * @throws ShouldNotHappenException
     */
    protected function returnSetMagicMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        $params = [];
        if ($methodName === 'setData') {
            $params = [
                new DummyParameter(
                    'key',
                    new UnionType([new StringType(), new ArrayType(new MixedType(), new MixedType())]),
                    true,
                    null,
                    false,
                    null
                ),
                new DummyParameter(
                    'value',
                    new MixedType(),
                    true,
                    null,
                    false,
                    null
                )
            ];
        } else {
            $params = [
                new DummyParameter(
                    'value',
                    new MixedType(),
                    true,
                    null,
                    false,
                    null
                )
            ];
        }

        $returnType = new ObjectType($classReflection->getName());

        $variants = new FunctionVariant(
            TemplateTypeMap::createEmpty(),
            null,
            $params,
            false,
            $returnType
        );

        return new MagicMethodReflection($methodName, $classReflection, [$variants]);
    }

    /**
     * Helper method to create magic method reflection for unset() calls.
     *
     * @param ClassReflection $classReflection
     * @param string $methodName
     * @return MethodReflection
     * @throws ShouldNotHappenException
     */
    protected function returnUnsetMagicMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        $params = [
            new DummyParameter(
                'key',
                new UnionType([new NullType(), new StringType(), new ArrayType(new MixedType(), new MixedType())]),
                true,
                null,
                false,
                null
            )
        ];

        $returnType = new ObjectType($classReflection->getName());

        $variants = new FunctionVariant(
            TemplateTypeMap::createEmpty(),
            null,
            $params,
            false,
            $returnType
        );

        return new MagicMethodReflection($methodName, $classReflection, [$variants]);
    }

    /**
     * Helper method to create magic method reflection for has() calls.
     *
     * @param ClassReflection $classReflection
     * @param string $methodName
     * @return MethodReflection
     */
    protected function returnHasMagicMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        $params = [];
        if ($methodName === 'hasData') {
            $params = [
                new DummyParameter(
                    'key',
                    new StringType(),
                    true,
                    null,
                    false,
                    null
                )
            ];
        }

        $returnType = new BooleanType();

        $variants = new FunctionVariant(
            TemplateTypeMap::createEmpty(),
            null,
            $params,
            false,
            $returnType
        );

        return new MagicMethodReflection($methodName, $classReflection, [$variants]);
    }
}
