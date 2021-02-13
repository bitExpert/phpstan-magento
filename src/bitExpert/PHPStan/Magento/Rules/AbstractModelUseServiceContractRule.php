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
 * Since 100.1.0 entities must not be responsible for their own loading, service contracts should persist entities.
 *
 * @implements Rule<MethodCall>
 */
class AbstractModelUseServiceContractRule implements Rule
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

        if (!in_array($node->name->name, ['save', 'load', 'delete'], true)) {
            return [];
        }

        $type = $scope->getType($node->var);
        $isAbstractModelType = (new ObjectType('Magento\Framework\Model\AbstractModel'))->isSuperTypeOf($type);
        if (!$isAbstractModelType->yes()) {
            return [];
        }

        return [
            sprintf(
                'Use service contracts to persist entities in favour of %s::%s() method',
                $type->describe(VerbosityLevel::typeOnly()),
                $node->name->name
            )
        ];
    }
}
