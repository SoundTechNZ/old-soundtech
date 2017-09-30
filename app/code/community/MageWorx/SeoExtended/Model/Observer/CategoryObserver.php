<?php

/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Model_Observer_CategoryObserver extends Mage_Core_Model_Abstract
{
    /**
     * Convert properties of the product that contain [category] and [categories]
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function updateCategoryProperties($observer)
    {
        /** @var Mage_Page_Block_Html_Head $block */
        $block = $observer->getBlock();

        if ($block->getNameInLayout() != 'head') {
            return false;
        }

        /** @var MageWorx_SeoExtended_Helper_Config $helperConfig */
        $helperConfig = Mage::helper('mageworx_seoextended/config');

        if (!$helperConfig->isUseSeoForCategoryFilters()) {
            return false;
        }

        if ('catalog_category_view' != Mage::helper('mageworx_seoall/request')->getCurrentFullActionName()) {
            return false;
        }

        $category = Mage::registry('current_category');

        if (!is_object($category)) {
            return false;
        }

        if (Mage::app()->getRequest()->getParam('id') != $category->getId()) {
            return false;
        }

        /** @var MageWorx_SeoExtended_Helper_SeoFilterProvider $seoFilterProvider */
        $seoFilterProvider = Mage::helper('mageworx_seoextended/seoFilterProvider');

        $storeId = Mage::app()->getStore()->getId();

        /** @var MageWorx_SeoExtended_Model_Catalog_Category $seoForFilterModel */
        $seoForFilterModel = $seoFilterProvider->getSeoFilter($category, $storeId);

        if (!$seoForFilterModel) {
            return false;
        }

        if (!trim($seoForFilterModel->getDescription())) {
            return false;
        }

        $category->setDescription(trim($seoForFilterModel->getDescription()));
    }
}