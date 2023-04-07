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
 * Do not use setTemplate in Block classes, see
 * https://github.com/extdn/extdn-phpcs/blob/master/Extdn/Sniffs/Blocks/SetTemplateInBlockSniff.md
 *
 * @implements Rule<MethodCall>
 */
class SetTemplateDisallowedForBlockRule implements Rule
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

        if ($node->name->name !== 'setTemplate') {
            return [];
        }

        $type = $scope->getType($node->var);
        $isAbstractModelType = (new ObjectType('Magento\Framework\View\Element\Template'))->isSuperTypeOf($type);
        if (!$isAbstractModelType->yes()) {
            return [];
        }

        return [
            sprintf(
                'Setter methods like %s::%s() are deprecated in Block classes, use constructor arguments instead',
                $type->describe(VerbosityLevel::typeOnly()),
                $node->name->name
            )
        ];
    }
}
