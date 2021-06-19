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

namespace bitExpert\PHPStan\Magento\Reflection\Framework\Session;

use bitExpert\PHPStan\Magento\Reflection\AbstractMagicMethodReflectionExtension;
use bitExpert\PHPStan\Magento\Reflection\MagicMethodReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\FunctionVariant;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\Php\DummyParameter;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\BooleanType;
use PHPStan\Type\Generic\TemplateTypeMap;
use PHPStan\Type\MixedType;
use PHPStan\Type\StringType;

class SessionManagerMagicMethodReflectionExtension extends AbstractMagicMethodReflectionExtension
{
    /**
     * @param ClassReflection $classReflection
     * @param string $methodName
     * @return bool
     */
    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        return $classReflection->isSubclassOf('Magento\Framework\Session\SessionManager') &&
            in_array(substr($methodName, 0, 3), ['get', 'set', 'uns', 'has'], true);
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
                    'cache',
                    new BooleanType(),
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
}
