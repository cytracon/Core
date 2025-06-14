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

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\DB\Helper as DbHelper;
use Magento\Catalog\Model\Category as CategoryModel;

class Categories
{
    /**#@+
     * Category tree cache id
     */
    const CATEGORY_TREE_ID = 'CYTRACON_CATEGORY_TREE';

    /**
     * @var CacheInterface
     */
    private $cacheManager;

    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var DbHelper
     */
    private $dbHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    private $catalogProductVisibility;

    /**
     * @var \Cytracon\Core\Helper\Data
     */
    private $coreHelper;

    /**
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param DbHelper $dbHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Cytracon\Core\Helper\Data $coreHelper
     */
    public function __construct(
        CategoryCollectionFactory $categoryCollectionFactory,
        DbHelper $dbHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Cytracon\Core\Helper\Data $coreHelper
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->dbHelper                  = $dbHelper;
        $this->storeManager              = $storeManager;
        $this->catalogProductVisibility  = $catalogProductVisibility;
        $this->coreHelper                = $coreHelper;
    }

    public function getConfig()
    {
        return $this->getOptions();
    }

    public function getOptions()
    {
        $categoryTree = $this->getCacheManager()->load(self::CATEGORY_TREE_ID);
        if ($categoryTree) {
            return $this->coreHelper->unserialize($categoryTree);
        }

        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect('name');
        $options    = [];
        foreach ($collection as $category) {
            $categoryLabel = $this->getSpaces($category['level']) . '(ID:' . $category->getId() . ') ' . $category->getName();
            $category['label'] = $categoryLabel;
            $options[] = [
                'value'       => $category->getId(),
                'label'       => $categoryLabel,
                'short_label' => '(ID:' . $category->getId() . ')' . $category->getName()
            ];
        }

        $this->getCacheManager()->save(
            $this->coreHelper->serialize($options),
            self::CATEGORY_TREE_ID,
            [
                \Magento\Framework\App\Cache\Type\Block::CACHE_TAG
            ]
        );

        return $options;
    }

    protected function getSpaces($number)
    {
        $s = '';
        for($i = 0; $i < $number; $i++) {
            $s .= '_ ';
        }
        return $s;
    }

    /**
     * Retrieve cache interface
     *
     * @return CacheInterface
     * @deprecated 101.0.3
     */
    private function getCacheManager()
    {
        if (!$this->cacheManager) {
            $this->cacheManager = ObjectManager::getInstance()
                ->get(CacheInterface::class);
        }
        return $this->cacheManager;
    }

    /**
     * Retrieve categories tree
     *
     * @param string|null $filter
     * @return array
     * @since 101.0.0
     */
    public function getCategoriesTree($storeId = \Magento\Store\Model\Store::DEFAULT_STORE_ID)
    {
        $categoryTree = $this->getCacheManager()->load(self::CATEGORY_TREE_ID . '_' . $storeId);
        if ($categoryTree) {
            //return $this->coreHelper->unserialize($categoryTree);
        }

        /* @var $matchingNamesCollection \Magento\Catalog\Model\ResourceModel\Category\Collection */
        $matchingNamesCollection = $this->categoryCollectionFactory->create();

        $matchingNamesCollection->addAttributeToSelect('path')
            ->addAttributeToFilter('entity_id', ['neq' => CategoryModel::TREE_ROOT_ID])
            ->addIsActiveFilter()
            ->setStoreId($storeId);

        $shownCategoriesIds = [];

        /** @var \Magento\Catalog\Model\Category $category */
        foreach ($matchingNamesCollection as $category) {
            foreach (explode('/', $category->getPath()) as $parentId) {
                $shownCategoriesIds[$parentId] = 1;
            }
        }

        /* @var $collection \Magento\Catalog\Model\ResourceModel\Category\Collection */
        $collection = $this->categoryCollectionFactory->create();

        $collection->addAttributeToFilter('entity_id', ['in' => array_keys($shownCategoriesIds)])
            ->addAttributeToSelect(['name', 'is_active', 'parent_id'])
            ->addIsActiveFilter()
            ->setStoreId($storeId);

        $categoryById = [
            CategoryModel::TREE_ROOT_ID => [
                'value' => CategoryModel::TREE_ROOT_ID,
                'optgroup' => null,
            ],
        ];

        foreach ($collection as $category) {
            foreach ([$category->getId(), $category->getParentId()] as $categoryId) {
                if (!isset($categoryById[$categoryId])) {
                    $categoryById[$categoryId] = ['value' => $categoryId];
                }
            }

            $categoryById[$category->getId()]['is_active']        = $category->getIsActive();
            $categoryById[$category->getId()]['label']            = $category->getName();
            $categoryById[$category->getId()]['name']             = $category->getName();
            $categoryById[$category->getId()]['id']               = $category->getId();
            $categoryById[$category->getId()]['url']              = $category->getUrl();
            $categoryById[$category->getId()]['product_count']    = $category->getProductCount();
            $categoryById[$category->getId()]['children_cout']    = $category->getChildrenCount();
            $categoryById[$category->getParentId()]['optgroup'][] = &$categoryById[$category->getId()];
        }

        $this->getCacheManager()->save(
            $this->coreHelper->serialize($categoryById[CategoryModel::TREE_ROOT_ID]['optgroup']),
            self::CATEGORY_TREE_ID . '_' . $storeId,
            [
                \Magento\Catalog\Model\Category::CACHE_TAG,
                \Magento\Framework\App\Cache\Type\Block::CACHE_TAG
            ]
        );

        return $categoryById[CategoryModel::TREE_ROOT_ID]['optgroup'];
    }

    private function _prepareChildren($list, $collection)
    {
        $children = [];
        foreach ($list as $item) {
            $category = $item['category'];
            if (isset($item['children'])) {
                $category->setSubCategories($this->_prepareChildren($item['children'], $collection));
            }
            $children[] = $category;
        }
        return $children;
    }

    public function getCategoriesCollection($ids, $loadCount)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $categories = $this->categoryCollectionFactory->create();
        $categories->addAttributeToSelect(['name', 'is_active', 'parent_id']);
        $categories->setStoreId($storeId);
        $categories->setOrder('position', 'ASC');
        $attributes[] = [
            'attribute' => 'entity_id', 'in' => $ids
        ];
        foreach ($ids as $k => $v) {
            $attributes[] = [
                'attribute' => 'path', 'like' => '%/' . $v . '/%'
            ];
        }
        $categories->addAttributeToFilter($attributes);
        $categories->addUrlRewriteToResult();
        $categories->addIsActiveFilter();
        if ($loadCount) {
            $categories->getSelect()
                ->joinLeft(
                    [
                        'ccpi' => $categories->getResource()->getTable('catalog_category_product_index'),
                    ],
                    "e.entity_id = ccpi.category_id",
                    [
                        'product_count' => 'COUNT(ccpi.product_id)'
                    ]
                )->where(
                    'ccpi.visibility in (?)', $this->catalogProductVisibility->getVisibleInSiteIds()                    
                )->group(
                    'e.entity_id'
                )->group(
                    'ccpi.category_id'
                )->group($categories->getResource()->getTable('url_rewrite') . '.request_path');
        }
        return $categories;
    }

    public function getCategories($ids, $loadCount)
    {
        $categories = $this->getCategoriesCollection($ids, $loadCount);
        $list = [];
        foreach ($categories as $category) {
            $list[$category->getId()]['category'] = $category;
            $list[$category->getParentId()]['children'][] = &$list[$category->getId()];
        }
        $result = [];
        foreach ($ids as $id) {
            $category = $categories->getItemById($id);
            if ($category) {
                if (isset($list[$id]['children'])) {
                    $category->setSubCategories($this->_prepareChildren($list[$id]['children'], $categories));
                }
                $result[] = $category;
            }
        }
        return $result;
    }
}
