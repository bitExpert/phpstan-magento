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

use PHPStan\Reflection\ClassMemberReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptor;

class MagicMethodReflection implements MethodReflection
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var ClassReflection
     */
    private $declaringClass;
    /**
     * @var array
     */
    private $variants;

    /**
     * MagicMethodReflection constructor.
     *
     * @param string $name
     * @param ClassReflection $declaringClass
     * @param ParametersAcceptor[] $variants
     */
    public function __construct(string $name, ClassReflection $declaringClass, array $variants = [])
    {
        $this->name = $name;
        $this->declaringClass = $declaringClass;
        $this->variants = $variants;
    }

    public function getDeclaringClass(): ClassReflection
    {
        return $this->declaringClass;
    }

    public function isStatic(): bool
    {
        return false;
    }

    public function isPrivate(): bool
    {
        return false;
    }

    public function isPublic(): bool
    {
        return true;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrototype(): ClassMemberReflection
    {
        return $this;
    }

    /**
     * @return ParametersAcceptor[]
     */
    public function getVariants(): array
    {
        return $this->variants;
    }
}
