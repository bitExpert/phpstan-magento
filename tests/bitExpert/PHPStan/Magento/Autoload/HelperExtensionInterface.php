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
 * Dummy attribute extension interface that can be loaded via the Autoloader in the test cases.
 */
interface HelperExtensionInterface extends \Magento\Framework\Api\ExtensionAttributesInterface
{
}
