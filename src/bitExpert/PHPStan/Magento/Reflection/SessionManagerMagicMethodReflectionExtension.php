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
use PHPStan\Reflection\TrivialParametersAcceptor;
use PHPStan\Type\MixedType;

class SessionManagerMagicMethodReflectionExtension implements MethodsClassReflectionExtension
{
    /**
     * @param ClassReflection $classReflection
     * @param string $methodName
     * @return bool
     */
    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        return $classReflection->isSubclassOf('Magento\Framework\Session\SessionManager') &&
            in_array(substr($methodName, 0, 3), ['get', 'set', 'uns', 'has']);
    }

    /**
     * @param ClassReflection $classReflection
     * @param string $methodName
     * @return MethodReflection
     */
    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        $variants = null;
        if (strpos($methodName, 'get') === 0) {
            // get call does not have any parameters
            $variants = new TrivialParametersAcceptor();
        } else {
            // set, unset and has call does have one parameter and returning mixed type
            $variants = new FunctionVariant(
                [new SessionManagerMagicMethodParameterReflection()],
                false,
                new MixedType()
            );
        }

        return new SessionManagerMagicMethodReflection($methodName, $classReflection, [$variants]);
    }
}
