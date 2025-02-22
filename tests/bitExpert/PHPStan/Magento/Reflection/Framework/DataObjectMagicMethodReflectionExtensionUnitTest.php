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

namespace bitExpert\PHPStan\Magento\Reflection\Framework;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\PHPStanTestCase;
use PHPStan\Type\BooleanType;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\UnionType;

class DataObjectMagicMethodReflectionExtensionUnitTest extends PHPStanTestCase
{
    /**
     * @var DataObjectMagicMethodReflectionExtension
     */
    private $extension;

    /**
     * @var ClassReflection
     */
    private $classReflection;

    protected function setUp(): void
    {
        /** @var ReflectionProvider $reflectionProvider */
        $reflectionProvider = $this->getContainer()->getService('reflectionProvider');
        $this->classReflection = $reflectionProvider->getClass(DataObjectHelper::class);

        $this->extension = new DataObjectMagicMethodReflectionExtension();
    }

    /**
     * @test
     */
    public function returnMagicMethodReflectionForGetDataMethod(): void
    {
        $methodReflection = $this->extension->getMethod($this->classReflection, 'getData');

        $variants = $methodReflection->getVariants();
        $params = $variants[0]->getParameters();

        self::assertCount(1, $variants);
        self::assertInstanceOf(MixedType::class, $variants[0]->getReturnType());
        self::assertCount(2, $params);
        self::assertInstanceOf(StringType::class, $params[0]->getType());
        self::assertInstanceOf(UnionType::class, $params[1]->getType());
    }

    /**
     * @test
     */
    public function returnMagicMethodReflectionForGetMethod(): void
    {
        $methodReflection = $this->extension->getMethod($this->classReflection, 'getTest');

        $variants = $methodReflection->getVariants();
        $params = $variants[0]->getParameters();

        self::assertCount(1, $variants);
        self::assertInstanceOf(MixedType::class, $variants[0]->getReturnType());
        self::assertCount(1, $params);
        self::assertInstanceOf(MixedType::class, $params[0]->getType());
    }

    /**
     * @test
     */
    public function returnMagicMethodReflectionForSetDataMethod(): void
    {
        $methodReflection = $this->extension->getMethod($this->classReflection, 'setData');

        $variants = $methodReflection->getVariants();
        $params = $variants[0]->getParameters();

        self::assertCount(1, $variants);
        self::assertInstanceOf(ObjectType::class, $variants[0]->getReturnType());
        self::assertCount(2, $params);
        self::assertInstanceOf(UnionType::class, $params[0]->getType());
        self::assertInstanceOf(MixedType::class, $params[1]->getType());
    }

    /**
     * @test
     */
    public function returnMagicMethodReflectionForSetMethod(): void
    {
        $methodReflection = $this->extension->getMethod($this->classReflection, 'setTest');

        $variants = $methodReflection->getVariants();
        $params = $variants[0]->getParameters();

        self::assertCount(1, $variants);
        self::assertInstanceOf(ObjectType::class, $variants[0]->getReturnType());
        self::assertCount(1, $params);
        self::assertInstanceOf(MixedType::class, $params[0]->getType());
    }

    /**
     * @test
     */
    public function returnMagicMethodReflectionForUnsetMethod(): void
    {
        $methodReflection = $this->extension->getMethod($this->classReflection, 'unsetTest');

        $variants = $methodReflection->getVariants();
        $params = $variants[0]->getParameters();

        self::assertCount(1, $variants);
        self::assertInstanceOf(ObjectType::class, $variants[0]->getReturnType());
        self::assertCount(1, $params);
        self::assertInstanceOf(UnionType::class, $params[0]->getType());
    }

    /**
     * @test
     */
    public function returnMagicMethodReflectionForHasDataMethod(): void
    {
        $methodReflection = $this->extension->getMethod($this->classReflection, 'hasData');

        $variants = $methodReflection->getVariants();
        $params = $variants[0]->getParameters();

        self::assertCount(1, $variants);
        self::assertInstanceOf(BooleanType::class, $variants[0]->getReturnType());
        self::assertCount(1, $params);
        self::assertInstanceOf(StringType::class, $params[0]->getType());
    }

    /**
     * @test
     */
    public function returnMagicMethodReflectionForHasMethod(): void
    {
        $methodReflection = $this->extension->getMethod($this->classReflection, 'hasTest');

        $variants = $methodReflection->getVariants();
        $params = $variants[0]->getParameters();

        self::assertCount(1, $variants);
        self::assertInstanceOf(BooleanType::class, $variants[0]->getReturnType());
        self::assertCount(0, $params);
    }

    /**
     * @test
     */
    public function throwsExceptionForUnknownMethodNames(): void
    {
        $this->expectException(ShouldNotHappenException::class);

        $this->extension->getMethod($this->classReflection, 'someOtherMethod');
    }

    /**
     * @test
     * @dataProvider isMethodSupportedDataprovider
     * @param string $method
     * @param bool $expectedResult
     */
    public function hasMethodDetectsDataObjectClass(string $method, bool $expectedResult): void
    {
        self::assertSame($expectedResult, $this->extension->hasMethod($this->classReflection, $method));
    }

    /**
     * @test
     * @dataProvider isMethodSupportedDataprovider
     * @param string $method
     * @param bool $expectedResult
     */
    public function hasMethodDetectsDataObjectParentClass(string $method, bool $expectedResult): void
    {
        self::assertSame($expectedResult, $this->extension->hasMethod($this->classReflection, $method));
    }

    /**
     * @return mixed[]
     */
    public function isMethodSupportedDataprovider(): array
    {
        return [
            ['getTest', true],
            ['setTest', true],
            ['unsetTest', true],
            ['hasText', true],
            ['someOtherMethod', false],
        ];
    }
}
