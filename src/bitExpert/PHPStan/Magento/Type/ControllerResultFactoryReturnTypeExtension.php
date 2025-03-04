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

use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

/**
 * \Magento\Framework\Controller\ResultFactory returns result type based on first parameter
 */
class ControllerResultFactoryReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    /** @see \Magento\Framework\Controller\ResultFactory */
    private const TYPE_MAP = [
        'TYPE_JSON' => \Magento\Framework\Controller\Result\Json::class,
        'TYPE_RAW' => \Magento\Framework\Controller\Result\Raw::class,
        'TYPE_REDIRECT' => \Magento\Framework\Controller\Result\Redirect::class,
        'TYPE_FORWARD' => \Magento\Framework\Controller\Result\Forward::class,
        'TYPE_LAYOUT' => \Magento\Framework\View\Result\Layout::class,
        'TYPE_PAGE' => \Magento\Framework\View\Result\Page::class,
    ];

    public function getClass(): string
    {
        return \Magento\Framework\Controller\ResultFactory::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'create';
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): ?ObjectType {
        $class = null;
        if (\count($methodCall->getArgs()) > 0) {
            $arg = $methodCall->getArgs()[0];
            $expr = $arg->value;

            if ($expr instanceof ClassConstFetch && $expr->name instanceof Identifier) {
                $class = self::TYPE_MAP[$expr->name->toString()] ?? null;
            }
        }

        return $class !== null ? new ObjectType($class) : null;
    }
}
