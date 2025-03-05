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

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Testing\PHPStanTestCase;
use PHPStan\Type\ObjectType;

class ControllerResultFactoryReturnTypeExtensionUnitTest extends PHPStanTestCase
{
    /**
     * @var ControllerResultFactoryReturnTypeExtension
     */
    private $extension;

    protected function setUp(): void
    {
        $this->extension = new ControllerResultFactoryReturnTypeExtension();
    }

    /**
     * @return mixed[]
     */
    public function returnTypeDataProvider(): array
    {
        return [
            ['TYPE_JSON', 'Magento\Framework\Controller\Result\Json'],
            ['TYPE_PAGE', 'Magento\Framework\View\Result\Page']
        ];
    }

    /**
     * @return mixed[]
     */
    public function isMethodSupportedDataProvider(): array
    {
        return [
            ['create', true],
            ['get', false]
        ];
    }

    /**
     * @test
     * @dataProvider isMethodSupportedDataProvider
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
     * @dataProvider returnTypeDataProvider
     * @param string $param
     * @param string $expectedResult
     */
    public function returnValidResultType(string $param, string $expectedResult): void
    {
        $methodReflection = $this->createMock(MethodReflection::class);
        $scope = $this->createMock(Scope::class);

        $identifier = $this->createConfiguredMock(Identifier::class, ['toString' => $param]);

        $expr = $this->createMock(ClassConstFetch::class);
        $expr->name = $identifier;

        $arg = $this->createMock(Arg::class);
        $arg->value = $expr;

        $methodCall = $this->createConfiguredMock(MethodCall::class, ['getArgs' => [$arg]]);

        $resultType = $this->extension->getTypeFromMethodCall($methodReflection, $methodCall, $scope);

        self::assertNotNull($resultType);
        self::assertSame($expectedResult, $resultType->getClassName());
    }
}
