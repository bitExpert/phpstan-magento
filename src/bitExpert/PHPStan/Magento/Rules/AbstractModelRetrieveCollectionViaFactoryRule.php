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
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use PHPStan\Type\VerbosityLevel;

/**
 * Since 101.0.0 because collections should be used directly via factory
 *
 * @implements Rule<MethodCall>
 */
class AbstractModelRetrieveCollectionViaFactoryRule implements Rule
{
    /**
     * @phpstan-return class-string<MethodCall>
     * @return string
     */
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node->name instanceof Node\Identifier) {
            return [];
        }

        if ($node->name->name !== 'getCollection') {
            return [];
        }

        $type = $scope->getType($node->var);
        $isAbstractModelType = (new ObjectType('Magento\Framework\Model\AbstractModel'))->isSuperTypeOf($type);
        if (!$isAbstractModelType->yes()) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                sprintf(
                    'Collections should be used directly via factory, not via %s::%s() method',
                    $type->describe(VerbosityLevel::typeOnly()),
                    $node->name->name
                )
            )
            ->identifier('bitExpertMagento.abstractModelRetrieveCollectionViaFactory')
            ->build()
        ];
    }
}
