<?php

/*
 * This file is part of the phpstan-magento package.
 *
 * (c) bitExpert AG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Magento\Framework\Api;

/**
 * Interface for entities which can be extended with extension attributes.
 *
 * @api
 * @since 100.0.2
 */
interface ExtensibleDataInterface
{
    /**
     * Key for extension attributes object
     */
    const EXTENSION_ATTRIBUTES_KEY = 'extension_attributes';
}
