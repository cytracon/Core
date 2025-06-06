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

class Pages implements OptionSourceInterface
{
	/**
	 * @var \Magento\Cms\Model\ResourceModel\Page\CollectionFactory
	 */
	protected $collectionFactory;

	/**
	 * @param \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $collectionFactory
	 */
    public function __construct(
        \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $collectionFactory
    ) {
		$this->collectionFactory = $collectionFactory;
    }

    public function getOptions()
    {
    	return $this->toOptionArray();
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
		$options    = [];
		$collection = $this->collectionFactory->create();
		foreach ($collection as $page) {
			$options[] = [
				'label' => $page->getTitle(),
				'value' => $page->getId(),
				'path'  => ''
			];
		}
        return $options;
    }
}
