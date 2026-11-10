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

namespace bitExpert\PHPStan\Magento\Autoload;

/**
 * Dummy interface with parameter defaults that a generated proxy has to repeat.
 */
interface HelperWithDefaults
{
    public const SIZE = 10;

    /**
     * @param mixed $null
     * @param mixed $false
     * @param mixed $true
     * @param mixed $zero
     * @param mixed $one
     * @param mixed $quote
     * @param mixed $empty
     * @param mixed $list
     * @param mixed $constant
     * @param string|null $nullableString
     * @param \DateTimeInterface|null $nullableObject
     */
    public function call(
        $null = null,
        $false = false,
        $true = true,
        $zero = 0,
        $one = 1,
        $quote = "it's",
        $empty = [],
        $list = ['a' => 1],
        $constant = self::SIZE,
        ?string $nullableString = '',
        ?\DateTimeInterface $nullableObject = null
    ): void;
}
