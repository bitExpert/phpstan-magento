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
use PHPStan\Reflection\TrivialParametersAcceptor;
use PHPStan\Type\Generic\TemplateTypeMap;
use PHPStan\Type\MixedType;

abstract class AbstractMagicMethodReflectionExtension implements MethodsClassReflectionExtension
{
    /**
     * @param ClassReflection $classReflection
     * @param string $methodName
     * @return MethodReflection
     */
    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        if (strpos($methodName, 'get') === 0) {
            return $this->returnGetMagicMethodReflection($classReflection, $methodName);
        }

        return $this->returnMagicMethodReflection($classReflection, $methodName);
    }

    /**
     * Helper method to create magic method for get calls. The method call does not accept any parameter and will return
     * mixed type.
     *
     * @param ClassReflection $classReflection
     * @param string $methodName
     * @return MethodReflection
     */
    protected function returnGetMagicMethodReflection(
        ClassReflection $classReflection,
        string $methodName
    ): MethodReflection {
        $variants = new TrivialParametersAcceptor();
        return new MagicMethodReflection($methodName, $classReflection, [$variants]);
    }

    /**
     * Helper method to create magic reflection method for set, unset and has calls. Those method calls accept one
     * mixed parameter and return mixed type.
     *
     * @param ClassReflection $classReflection
     * @param string $methodName
     * @return MethodReflection
     */
    protected function returnMagicMethodReflection(
        ClassReflection $classReflection,
        string $methodName
    ): MethodReflection {
        $variants = new FunctionVariant(
            TemplateTypeMap::createEmpty(),
            null,
            [ new DummyParameter('name', new MixedType(), false, null, false, null),],
            false,
            new MixedType()
        );

        return new MagicMethodReflection($methodName, $classReflection, [$variants]);
    }
}
