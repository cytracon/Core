<?php
/**
 * Cytracon
 *
 * This source file is subject to the Cytracon Software License, which is available at https://www.cytracon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.cytracon.com for more information.
 *
 * @category  Cytracon
 * @package   Cytracon_Core
 * @copyright Copyright (C) 2019 Cytracon (https://www.cytracon.com)
 */

namespace Cytracon\Core\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class YesNo implements OptionSourceInterface
{
    const STATUS_ENABLED  = 1;
    const STATUS_DISABLED = 0;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
    	$options = [
            [
            	'label' => __('Yes'),
            	'value' => self::STATUS_ENABLED,
            ],
            [
            	'label' => __('No'),
            	'value' => self::STATUS_DISABLED,
            ]
        ];
        return $options;
    }
}
