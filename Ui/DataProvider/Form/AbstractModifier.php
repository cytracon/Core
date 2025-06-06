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

namespace Cytracon\Core\Ui\DataProvider\Form;

use Magento\Framework\App\ObjectManager;
use Cytracon\Core\Framework\Stdlib\ArrayManager;
use Magento\Catalog\Model\Attribute\ScopeOverriddenValue;

class AbstractModifier extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    const META_CONFIG_PATH = '/arguments/data/config';

    /**
     * Container fieldset prefix
     */
    const CONTAINER_PREFIX = 'container_';

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var Filesystem
     */
    private $fileInfo;

    /**
     * @var ScopeOverriddenValue
     */
    private $scopeOverriddenValue;

    /**
     * Retrieve array manager
     *
     * @return ArrayManager
     */
    protected function getArrayManager()
    {
        if (null === $this->arrayManager) {
            $this->arrayManager = ObjectManager::getInstance()->get(
                ArrayManager::class
            );
        }
        return $this->arrayManager;
    }

    /**
     * Get FileInfo instance
     *
     * @return FileInfo
     */
    protected function getFileInfo()
    {
        if ($this->fileInfo === null) {
            $this->fileInfo = ObjectManager::getInstance()->get(\Cytracon\Core\Model\FileInfo::class);
        }
        return $this->fileInfo;
    }

    /**
     * Retrieve scope overridden value
     *
     * @return ScopeOverriddenValue
     */
    protected function getScopeOverriddenValue()
    {
        if (null === $this->scopeOverriddenValue) {
            $this->scopeOverriddenValue = ObjectManager::getInstance()->get(
                ScopeOverriddenValue::class
            );
        }

        return $this->scopeOverriddenValue;
    }
}
