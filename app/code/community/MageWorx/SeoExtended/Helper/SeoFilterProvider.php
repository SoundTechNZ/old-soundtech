<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Helper_SeoFilterProvider extends Mage_Core_Helper_Abstract
{
    /**
     * @var MageWorx_SeoExtended_Model_Catalog_Category $seoFilter
     */
    protected $seoFilter;

    /**
     * @param Mage_Catalog_Model_Category $category
     * @return MageWorx_SeoExtended_Model_Catalog_Category|false
     */
    public function getSeoFilter($category, $storeId)
    {
        if ($this->seoFilter === null) {
            /** @var MageWorx_SeoExtended_Helper_Config $helperConfig */
            $helperConfig = Mage::helper('mageworx_seoextended/config');

            if (!$helperConfig->isUseSeoForCategoryFilters()) {
                $this->seoFilter = false;
                return $this->seoFilter;
            }

            /** @var MageWorx_SeoAll_Helper_LayeredFilter $layeredFilter */
            $helperLayeredFilter = Mage::helper('mageworx_seoall/layeredFilter');

            $currentFiltersData  = $helperLayeredFilter->getLayeredNavigationFiltersData();

            if (empty($currentFiltersData)) {
                $this->seoFilter = false;
                return $this->seoFilter;
            }

            if (count($currentFiltersData) > 1 && $helperConfig->isUseOnSingleFilterOnly()) {
                $this->seoFilter = false;
                return $this->seoFilter;
            }

            $filterData = $this->_getFilterBySortOrder($currentFiltersData);
            $filterId   = $filterData['attribute_id'];

            /** @var MageWorx_SeoExtended_Model_Resource_Catalog_Category_Collection $collection */
            $collection = Mage::getModel('mageworx_seoextended/catalog_category')->getCollection();
            $collection->getFilteredCollection($filterId, $category->getId(), $storeId);

            $this->seoFilter = $collection->count() ? $collection->getFirstItem() : false;
        }

        return $this->seoFilter;
    }

    /**
     * @param $currentFiltersData
     * @return mixed
     */
    protected function _getFilterBySortOrder($currentFiltersData)
    {
        return array_shift($currentFiltersData);
    }
}