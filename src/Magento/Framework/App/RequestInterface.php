<?php

/*
 * This file is part of the phpstan-magento package.
 *
 * (c) bitExpert AG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Magento\Framework\App;

use Magento\Framework\HTTP\PhpEnvironment\Request;

/**
 * @api
 * @since 100.0.2
 */
interface RequestInterface
{
    /**
     * Retrieve module name
     *
     * @return string
     */
    public function getModuleName();

    /**
     * Set Module name
     *
     * @param string $name
     * @return $this
     */
    public function setModuleName($name);

    /**
     * Retrieve action name
     *
     * @return string
     */
    public function getActionName();

    /**
     * Set action name
     *
     * @param string $name
     * @return $this
     */
    public function setActionName($name);

    /**
     * Retrieve param by key
     *
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getParam($key, $defaultValue = null);

    /**
     * Set params from key value array
     *
     * @param array $params
     * @return $this
     */
    public function setParams(array $params);

    /**
     * Retrieve all params as array
     *
     * @return array
     */
    public function getParams();

    /**
     * Retrieve cookie value
     *
     * @param string|null $name
     * @param string|null $default
     * @return string|null
     */
    public function getCookie($name, $default);

    /**
     * Returns whether request was delivered over HTTPS
     *
     * @return bool
     */
    public function isSecure();

    //
    // additional interface methods below...
    //

    /**
     * Set flag indicating whether or not request has been dispatched
     *
     * @param boolean $flag
     * @return $this
     */
    public function setDispatched($flag = true);
}
