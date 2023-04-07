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

namespace bitExpert\PHPStan\Magento\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\ObjectType;
use PHPStan\Type\VerbosityLevel;

/**
 * Since 100.1.0 resource models should be used directly.
 *
 * @implements Rule<MethodCall>
 */
class ResourceModelsShouldBeUsedDirectlyRule implements Rule
{
    /**
     * @phpstan-return class-string<MethodCall>
     * @return string
     */
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param Node $node
     * @param Scope $scope
     * @return (string|\PHPStan\Rules\RuleError)[] errors
     * @throws ShouldNotHappenException
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node instanceof MethodCall) {
            throw new ShouldNotHappenException();
        }

        if (!$node->name instanceof Node\Identifier) {
            return [];
        }

        if (!in_array($node->name->name, ['getResource', '_getResource'], true)) {
            return [];
        }

        $type = $scope->getType($node->var);
        $isAbstractModelType = (new ObjectType('Magento\Framework\Model\AbstractModel'))->isSuperTypeOf($type);
        if (!$isAbstractModelType->yes()) {
            return [];
        }

        return [
            sprintf(
                '%s::%s() is deprecated. Use Resource Models directly',
                $type->describe(VerbosityLevel::typeOnly()),
                $node->name->name
            )
        ];
    }
}
