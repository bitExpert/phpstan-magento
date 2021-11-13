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

use Magento\Framework\View\Design\Theme\ThemeList;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\PHPStanTestCase;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\ErrorType;
use PHPStan\Type\IntersectionType;
use PHPStan\Type\MixedType;
use PHPStan\Type\NullType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\UnionType;

class TestFrameworkObjectManagerDynamicReturnTypeExtensionUnitTest extends PHPStanTestCase
{
    /**
     * @var TestFrameworkObjectManagerDynamicReturnTypeExtension
     */
    private $extension;

    protected function setUp(): void
    {
        $this->extension = new TestFrameworkObjectManagerDynamicReturnTypeExtension();
    }

    /**
     * @return mixed[]
     */
    public function isMethodSupportedDataprovider(): array
    {
        return [
            ['getObject', true],
            ['getConstructArguments', true],
            ['getCollectionMock', true],
            ['bar', false],
        ];
    }

    /**
     * @test
     * @dataProvider isMethodSupportedDataprovider
     * @param string $method
     * @param bool $expectedResult
     */
    public function checkIfMethodIsSupported(string $method, bool $expectedResult): void
    {
        $methodReflection = $this->createMock(MethodReflection::class);
        $methodReflection->method('getName')->willReturn($method);

        self::assertSame($expectedResult, $this->extension->isMethodSupported($methodReflection));
    }

    /**
     * @test
     */
    public function returnsErrorTypeForUnkownMethodCall(): void
    {
        $methodReflection = $this->createMock(MethodReflection::class);
        $scope = $this->createMock(Scope::class);
        $methodCall = $this->createMock(MethodCall::class);
        $methodCall->args = [];

        $resultType = $this->extension->getTypeFromMethodCall($methodReflection, $methodCall, $scope);

        self::assertInstanceOf(ErrorType::class, $resultType);
    }

    /**
     * @test
     */
    public function returnsMixedTypeForGetObjectCallWithoutParameter(): void
    {
        $methodReflection = $this->createMock(MethodReflection::class);
        $scope = $this->createMock(Scope::class);
        $methodCall = $this->createMock(MethodCall::class);
        $methodCall->args = [];
        $methodCall->name = new \PhpParser\Node\Identifier('getObject');

        $resultType = $this->extension->getTypeFromMethodCall($methodReflection, $methodCall, $scope);

        self::assertInstanceOf(MixedType::class, $resultType);
    }

    /**
     * @test
     */
    public function returnsMixedTypeForGetObjectCallWithNonStringParameter(): void
    {
        $methodReflection = $this->createMock(MethodReflection::class);
        $scope = $this->createMock(Scope::class);
        $methodCall = $this->createMock(MethodCall::class);
        $methodCall->args = [new Arg(new LNumber(1))];
        $methodCall->name = new \PhpParser\Node\Identifier('getObject');

        $resultType = $this->extension->getTypeFromMethodCall($methodReflection, $methodCall, $scope);

        self::assertInstanceOf(MixedType::class, $resultType);
    }

    /**
     * @test
     */
    public function returnsUnionTypeForGetObjectCallWithStringParameter(): void
    {
        $methodReflection = $this->createMock(MethodReflection::class);
        $scope = $this->createMock(Scope::class);
        $scope->expects(self::once())
            ->method('getType')
            ->willReturn(new ConstantStringType('someType'));

        $methodCall = $this->createMock(MethodCall::class);
        $methodCall->args = [new Arg(new String_('someArg'))];
        $methodCall->name = new \PhpParser\Node\Identifier('getObject');

        $resultType = $this->extension->getTypeFromMethodCall($methodReflection, $methodCall, $scope);

        self::assertInstanceOf(UnionType::class, $resultType);
    }

    /**
     * @test
     */
    public function returnsUnionTypeForGetConstructArgumentsCall(): void
    {
        $methodReflection = $this->createMock(MethodReflection::class);
        $scope = $this->createMock(Scope::class);
        $methodCall = $this->createMock(MethodCall::class);
        $methodCall->args = [];
        $methodCall->name = new \PhpParser\Node\Identifier('getConstructArguments');

        /** @var UnionType $resultType */
        $resultType = $this->extension->getTypeFromMethodCall($methodReflection, $methodCall, $scope);
        $resultTypes = $resultType->getTypes();

        self::assertInstanceOf(UnionType::class, $resultType);
        self::assertInstanceOf(ArrayType::class, $resultTypes[0]);
        self::assertInstanceOf(StringType::class, $resultTypes[0]->getKeyType());
        self::assertInstanceOf(MixedType::class, $resultTypes[0]->getItemType());
        self::assertInstanceOf(NullType::class, $resultTypes[1]);
    }

    /**
     * @test
     */
    public function returnsErrorTypeForGetCollectionMockMethod(): void
    {
        $methodReflection = $this->createMock(MethodReflection::class);
        $scope = $this->createMock(Scope::class);
        $scope->expects(self::once())
            ->method('getType')
            ->willReturn(new ConstantStringType(self::class));

        $methodCall = $this->createMock(MethodCall::class);
        $methodCall->args = [new Arg(new String_(self::class))];
        $methodCall->name = new \PhpParser\Node\Identifier('getCollectionMock');

        $resultType = $this->extension->getTypeFromMethodCall($methodReflection, $methodCall, $scope);

        self::assertInstanceOf(ErrorType::class, $resultType);
    }


    /**
     * @test
     */
    public function returnsUnionTypeForGetCollectionMockMethod(): void
    {
        $methodReflection = $this->createMock(MethodReflection::class);
        $scope = $this->createMock(Scope::class);
        $scope->expects(self::once())
            ->method('getType')
            ->willReturn(new ConstantStringType(ThemeList::class));

        $methodCall = $this->createMock(MethodCall::class);
        $methodCall->args = [new Arg(new String_(ThemeList::class))];
        $methodCall->name = new \PhpParser\Node\Identifier('getCollectionMock');

        /** @var IntersectionType $resultType */
        $resultType = $this->extension->getTypeFromMethodCall($methodReflection, $methodCall, $scope);
        $resultTypes = $resultType->getTypes();

        self::assertInstanceOf(IntersectionType::class, $resultType);
        self::assertInstanceOf(ObjectType::class, $resultTypes[0]);
        self::assertEquals(ThemeList::class, $resultTypes[0]->getClassName());
        self::assertInstanceOf(ObjectType::class, $resultTypes[1]);
        self::assertEquals(\PHPUnit\Framework\MockObject\MockObject::class, $resultTypes[1]->getClassName());
    }
}
