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

namespace Cytracon\Core\Block\Adminhtml;

class Head extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
    	\Magento\Framework\View\Element\Template\Context $context,
        \Magento\Backend\Model\UrlInterface $backendUrl,
    	array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_backendUrl = $backendUrl;
    }

	public function getMediaUrl()
	{
		return $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
	}

	public function getFilesBrowserWindowUrl()
	{
		return $this->_backendUrl->getUrl('mgzcore/wysiwyg_images/index');
	}
}
