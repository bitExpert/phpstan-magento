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

namespace bitExpert\PHPStan\Magento\Rules\Helper;

use Magento\Framework\Model\AbstractModel;

class SampleModel extends AbstractModel
{
    public function __construct()
    {
        // We do not call the parent constructor here on purpose as we do not want do to deal with creating
        // not needed dependencies just for the testcase!
    }
}
