<?php

/*
 * This file is part of the phpstan-magento package.
 *
 * (c) bitExpert AG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Magento\Backend\Block\Widget\Button;

/**
 * @api
 * @since 100.0.2
 */
class ButtonList
{
    /**
     * @param \Magento\Backend\Block\Widget\ItemFactory $itemFactory
     */
    public function __construct(\Magento\Backend\Block\Widget\ItemFactory $itemFactory)
    {
    }

    /**
     * Add a button
     *
     * @param string $buttonId
     * @param array $data
     * @param integer $level
     * @param integer $sortOrder
     * @param string|null $region That button should be displayed in ('toolbar', 'header', 'footer', null)
     * @return void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function add($buttonId, $data, $level = 0, $sortOrder = 0, $region = 'toolbar')
    {
    }

    /**
     * Remove existing button
     *
     * @param string $buttonId
     * @return void
     */
    public function remove($buttonId)
    {
    }

    /**
     * Update specified button property
     *
     * @param string $buttonId
     * @param string|null $key
     * @param mixed $data
     * @return void
     */
    public function update($buttonId, $key, $data)
    {
    }

    /**
     * Get all buttons
     *
     * @return array
     */
    public function getItems()
    {
    }

    /**
     * Sort buttons by sort order
     *
     * @param Item $itemA
     * @param Item $itemB
     * @return int
     */
    public function sortButtons(Item $itemA, Item $itemB)
    {
    }
}
