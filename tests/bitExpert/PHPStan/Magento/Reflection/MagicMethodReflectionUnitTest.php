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

use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Testing\PHPStanTestCase;
use PHPStan\TrinaryLogic;

class MagicMethodReflectionUnitTest extends PHPStanTestCase
{
    /**
     * @test
     */
    public function magicMethodReflectionCreation(): void
    {
        /** @var ReflectionProvider $reflectionProvider */
        $reflectionProvider = $this->getContainer()->getService('reflectionProvider');
        $classReflection = $reflectionProvider->getClass('Magento\Framework\App\RequestInterface');
        $methodName = 'myTestMethod';
        $variants = [];

        $reflection = new MagicMethodReflection($methodName, $classReflection, $variants);

        self::assertSame($classReflection, $reflection->getDeclaringClass());
        self::assertFalse($reflection->isStatic());
        self::assertFalse($reflection->isPrivate());
        self::assertTrue($reflection->isPublic());
        self::assertSame($methodName, $reflection->getName());
        self::assertSame($reflection, $reflection->getPrototype());
        self::assertSame($variants, $reflection->getVariants());
        self::assertNull($reflection->getDocComment());
        self::assertSame(TrinaryLogic::createNo(), $reflection->isDeprecated());
        self::assertSame('', $reflection->getDeprecatedDescription());
        self::assertSame(TrinaryLogic::createNo(), $reflection->isFinal());
        self::assertSame(TrinaryLogic::createNo(), $reflection->isInternal());
        self::assertNull($reflection->getThrowType());
    }

    /**
     * @test
     * @dataProvider sideeffectsDataprovider
     * @param string $methodName
     * @param TrinaryLogic $expectedResult
     */
    public function magicMethodReflectionCreationSideeffects(
        string $methodName,
        TrinaryLogic $expectedResult
    ): void {
        /** @var ReflectionProvider $reflectionProvider */
        $reflectionProvider = $this->getContainer()->getService('reflectionProvider');
        $classReflection = $reflectionProvider->getClass('Magento\Framework\App\RequestInterface');
        $variants = [];

        $reflection = new MagicMethodReflection($methodName, $classReflection, $variants);
        self::assertSame($expectedResult, $reflection->hasSideEffects());
    }

    /**
     * @return mixed[]
     */
    public function sideeffectsDataprovider(): array
    {
        return [
            ['getTest', TrinaryLogic::createNo()],
            ['setTest', TrinaryLogic::createYes()],
            ['unsetTest', TrinaryLogic::createYes()],
            ['hasText', TrinaryLogic::createNo()],
            ['someOtherMethod', TrinaryLogic::createNo()],
        ];
    }
}
