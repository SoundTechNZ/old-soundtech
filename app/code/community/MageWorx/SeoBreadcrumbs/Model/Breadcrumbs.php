<?php
/**
 * MageWorx
 * MageWorx SeoBreadcrumbs Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBreadcrumbs
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBreadcrumbs_Model_Breadcrumbs extends Mage_Core_Model_Abstract
{
    /**
     * filtered categories
     */
    public $filteredCategories;

    /**
     * category path
     */
    public $categoryPath;

    /**
     * Get target category
     * @param $product
     * @return null|Magento_Catalog_Model_Category
     */
    public function getTargetCategory($product)
    {
        $helper = Mage::helper('mageworx_seobreadcrumbs');
        $collection = $product->getCategoryCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('is_active')
            ->setOrder('level', 'ASC');
        if ($helper->isUseCategoryBreadcrumbsPriority()) {
            $collection->addAttributeToSelect(
                'breadcrumbs_priority',
                'left'
            );
        }

        $targetCategory = null;

        $loadedProductCategories      = array();
        $disabledProductCategoryIds   = array();

        $parentCategoryIds = array();
        $productCategories = $collection->getItems();

        foreach ($collection->getItems() as $category) {
            if (!$category->getIsActive()) {
                $disabledProductCategoryIds[] = $category->getId();
                continue;
            }

            if (in_array($category->getId(), $disabledProductCategoryIds)) {
                $disabledProductCategoryIds[] = $category->getId();
                continue;
            }

            if ($helper->isShortMode()) {
                $parentId = $category->getParentId();
                if ($parentId != Mage::app()->getStore()->getRootCategoryId()
                    && !empty($productCategories[$parentId])
                ) {
                    continue;
                }
            }

            $loadedProductCategories[$category->getId()] = $category;
            $ids = explode(',', $category->getPathInStore());
            $parentCategoryIds = array_merge($parentCategoryIds, $ids);
        }

        if (!$loadedProductCategories) {
            return null;
        }

        $parentCategoryIds = array_filter($parentCategoryIds);
        $parentCategoryIds = array_unique($parentCategoryIds);

        $loadedProductCategoryIds = array_keys($loadedProductCategories);
        $parentCategoryIdsForLoad = array_diff($parentCategoryIds, $loadedProductCategoryIds);
        $loadedParentCategories   = $this->getCategoryCollection($parentCategoryIdsForLoad)->getItems();

        $allIds = array_merge($loadedProductCategoryIds, array_keys($loadedParentCategories));

        $targetCategory = $this->chooseTargetBySettings($loadedProductCategories, $allIds);
        $this->filteredCategories = $loadedProductCategories + $loadedParentCategories;
        return $targetCategory;
    }

    /**
     * @param array $loadedProductCategories
     * @param array $allIds
     * @param string $by
     * @return null|Magento_Catalog_Model_Category
     */
    protected function chooseTargetBySettings($loadedProductCategories, $allIds)
    {
        $targetCategory      = null;
        $level               = null;
        $breadcrumbsPriority = null;

        foreach ($loadedProductCategories as $category) {
            $categoryParentIds = explode(',', $category->getPathInStore());

            if (count($categoryParentIds) != count(array_intersect($categoryParentIds, $allIds))) {
                continue;
            }

            if ($category->getId() == Mage::app()->getStore()->getRootCategoryId()) {
                continue;
            }

            if ($level === null) {
                $level = $category->getLevel();
                $breadcrumbsPriority = $category->getBreadcrumbsPriority();
                $targetCategory = $category;
            } else {
                if (Mage::helper('mageworx_seobreadcrumbs')->isUseCategoryBreadcrumbsPriority()) {
                    if (!$this->isApproveByBreadcrumbsPriority($category, $breadcrumbsPriority, $level, $targetCategory->getId())) {
                        continue;
                    }
                } else {
                    if (!$this->isApproveByDepth($category, $level, $targetCategory->getId())) {
                        continue;
                    }
                }

                $targetCategory = $category;
                $level = (int)$category->getLevel();
                $breadcrumbsPriority = (int)$category->getBreadcrumbsPriority();
            }
        }

        return $targetCategory;
    }

    /**
     * @param $category
     * @param int $breadcrumbsPriority
     * @param int $level
     * @param int $currentId
     * @return bool
     */
    protected function isApproveByBreadcrumbsPriority($category, $breadcrumbsPriority, $level, $currentId)
    {
        if ((int)$category->getBreadcrumbsPriority() > $breadcrumbsPriority) {
            return true;
        } elseif((int)$category->getBreadcrumbsPriority() == $breadcrumbsPriority) {
            return $this->isApproveByDepth($category, $level, $currentId);
        }

        return false;
    }

    /**
     * @param $category
     * @param int $level
     * @param int $currentId
     * @return bool
     */
    protected function isApproveByDepth($category, $level, $currentId)
    {
        $helper = Mage::helper('mageworx_seobreadcrumbs');
        if ($helper->isLongMode() && (int)$category->getLevel() > $level) {
            return true;
        } elseif ($helper->isShortMode() &&  (int)$category->getLevel() < $level) {
            return true;
        } elseif (!$helper->isDefaultMode() && (int)$category->getLevel() == $level) {
            if ($category->getId() < $currentId) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  $product
     * @param  $category
     * @return array|string
     */
    public function getNewBreadcrumbPath($product, $category)
    {
        $path = array();
        $pathInStore = $category->getPathInStore();

        $pathIds = array_reverse(explode(',', $pathInStore));

        $categories = $this->filteredCategories;

        $path['home'] = array(
            'label' => Mage::helper('catalog')->__('Home'),
            'title' => Mage::helper('catalog')->__('Go to Home Page'),
            'link'  => Mage::getBaseUrl()
        );
        foreach ($pathIds as $categoryId) {
            if (isset($categories[$categoryId]) && $categories[$categoryId]->getName()) {
                $path['category' . $categoryId] = array(
                    'label' => $categories[$categoryId]->getName(),
                    'link' => $categories[$categoryId]->getUrl()
                );
            }
        }

        $path['product'] = array('label' => $product->getName());

        $this->categoryPath = $path;
        return $this->categoryPath;
    }

    /**
     * @param array $categoryIds
     * @return Magento_Catalog_Model_ResourceModel_Category_Collection
     */
    protected function getCategoryCollection($categoryIds)
    {
        $collection = Mage::getResourceModel('catalog/category_collection');

        $collection->setStore(Mage::app()->getStore()->getId())
            ->addAttributeToSelect('name')
            ->addAttributeToSelect(
                'url_key'
            )
            ->addIdFilter(
                $categoryIds
            )
            ->addIsActiveFilter();

        $helper = Mage::helper('mageworx_seobreadcrumbs');
        if ($helper->isUseCategoryBreadcrumbsPriority()) {
            $collection->addAttributeToSelect(
                'breadcrumbs_priority',
                'left'
            );
        }

        return $collection;
    }
}
