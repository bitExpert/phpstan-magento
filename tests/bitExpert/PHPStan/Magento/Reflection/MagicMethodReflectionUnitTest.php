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
use PHPUnit\Framework\TestCase;

class MagicMethodReflectionUnitTest extends TestCase
{
    /**
     * @test
     */
    public function magicMethodReflectionCreation(): void
    {
        $classReflection = $this->createMock(ClassReflection::class);
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
        self::assertSame(\PHPStan\TrinaryLogic::createNo(), $reflection->isDeprecated());
        self::assertSame('', $reflection->getDeprecatedDescription());
        self::assertSame(\PHPStan\TrinaryLogic::createNo(), $reflection->isFinal());
        self::assertSame(\PHPStan\TrinaryLogic::createNo(), $reflection->isInternal());
        self::assertNull($reflection->getThrowType());
    }

    /**
     * @test
     * @dataProvider sideeffectsDataprovider
     * @param string $methodName
     * @param \PHPStan\TrinaryLogic $expectedResult
     */
    public function magicMethodReflectionCreationSideeffects(
        string $methodName,
        \PHPStan\TrinaryLogic $expectedResult
    ): void {
        $classReflection = $this->createMock(ClassReflection::class);
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
            ['getTest', \PHPStan\TrinaryLogic::createNo()],
            ['setTest', \PHPStan\TrinaryLogic::createYes()],
            ['unsetTest', \PHPStan\TrinaryLogic::createYes()],
            ['hasText', \PHPStan\TrinaryLogic::createNo()],
            ['someOtherMethod', \PHPStan\TrinaryLogic::createNo()],
        ];
    }
}
