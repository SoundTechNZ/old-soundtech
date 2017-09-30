<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Model_MetaUpdater_Category extends MageWorx_SeoExtended_Model_MetaUpdater
{
    const DEFAULT_LIST_SEPARATOR = ', ';
    const DEFAULT_PAIR_SEPARATOR = ': ';

    /**
     * @var string
     */
    protected $_pairSeparator;

    /**
     *
     */
    protected $_listSeparator;

    /**
     * {@inheritdoc}
     */
    public function update($block)
    {
        $this->_pairSeparator = $this->_getPairSeparator();
        $this->_listSeparator = $this->_getListSeparator();

        $stopLnFiltersInTitle = false;

        //Title:
        if ($this->_callSeoFiltersForTitle($block)) {
            $stopLnFiltersInTitle = true;
        }

        $title = $this->_updateTitle($this->_getTitle($block), $stopLnFiltersInTitle);
        $this->_setTitle($block, $title, true);

        //Meta Description:
        $stopLnFiltersInMetaDescription = false;

        if ($this->_callSeoFiltersForMetaDescription($block)) {
            $stopLnFiltersInMetaDescription = true;
        }

        $metaDescription = $this->_updateMetaDescription($block->getDescription(), $stopLnFiltersInMetaDescription);
        $this->_setMetaDescription($block, $metaDescription);

        //Meta Keywords:
        $stopLnFiltersInMetaKeywords = false;

        if ($this->_callSeoFiltersForMetaKeywords($block)) {
            $stopLnFiltersInMetaKeywords = true;
        }

        $metaKeywords = $this->_updateMetaKeywords($block->getKeywords(), $stopLnFiltersInMetaKeywords);
        $this->_setMetaKeywords($block, $metaKeywords);

        return true;
    }

    /**
     * @param Mage_Page_Block_Html_Head $block
     * @return bool
     */
    protected function _callXTemplatesTitleRenderer($block)
    {
        if (Mage::helper('mageworx_seoextended')->isModuleEnabled('MageWorx_XTemplates')) {
            return false;
        }

        $category = Mage::registry('current_category');
        if ($this->_getXTemplatesDynamicRenderer()->modifyCategoryTitle($category, $block)) {
            return true;
        }

        return false;
    }

    /**
     * @param Mage_Page_Block_Html_Head $block
     * @return bool
     */
    protected function _callSeoFiltersForTitle($block)
    {
        $category = Mage::registry('current_category');
        $storeId  = Mage::app()->getStore()->getId();

        /** @var MageWorx_SeoExtended_Helper_SeoFilterProvider $seoFilterProvider */
        $seoFilterProvider = Mage::helper('mageworx_seoextended/seoFilterProvider');

        $seoForFilterModel = $seoFilterProvider->getSeoFilter($category, $storeId);

        if (!$seoForFilterModel) {
            return false;
        }

        if (!trim($seoForFilterModel->getMetaTitle())) {
            return false;
        }

        $block->setTitle(trim($seoForFilterModel->getMetaTitle()));
        return true;
    }

    /**
     * @param Mage_Page_Block_Html_Head $block
     * @return bool
     */
    protected function _callSeoFiltersForMetaDescription($block)
    {
        $category = Mage::registry('current_category');
        $storeId  = Mage::app()->getStore()->getId();

        /** @var MageWorx_SeoExtended_Helper_SeoFilterProvider $seoFilterProvider */
        $seoFilterProvider = Mage::helper('mageworx_seoextended/seoFilterProvider');

        $seoForFilterModel = $seoFilterProvider->getSeoFilter($category, $storeId);

        if (!$seoForFilterModel) {
            return false;
        }

        if (!trim($seoForFilterModel->getMetaDescription())) {
            return false;
        }

        $block->setDescription(trim($seoForFilterModel->getMetaDescription()));
        return true;
    }

    /**
     * @param Mage_Page_Block_Html_Head $block
     * @return bool
     */
    protected function _callXTemplatesMetaDescriptionRenderer($block)
    {
        if (!Mage::helper('mageworx_seoextended/adapter')->isAvailableSeoTemplates()) {
            return false;
        }

        $category = Mage::registry('current_category');
        if ($this->_getXTemplatesDynamicRenderer()->modifyCategoryMetaDescription($category, $block)) {
            return true;
        }

        return false;
    }

    /**
     * @param Mage_Page_Block_Html_Head $block
     * @return bool
     */
    protected function _callXTemplatesMetaKeywordsRenderer($block)
    {
        if (!Mage::helper('mageworx_seoextended/adapter')->isAvailableSeoTemplates()) {
            return false;
        }

        $category = Mage::registry('current_category');
        if ($this->_getXTemplatesDynamicRenderer()->modifyCategoryMetaKeywords($category, $block)) {
            return true;
        }

        return false;
    }

    /**
     * @param Mage_Page_Block_Html_Head $block
     * @return bool
     */
    protected function _callSeoFiltersForMetaKeywords($block)
    {
        $category = Mage::registry('current_category');
        $storeId  = Mage::app()->getStore()->getId();

        /** @var MageWorx_SeoExtended_Helper_SeoFilterProvider $seoFilterProvider */
        $seoFilterProvider = Mage::helper('mageworx_seoextended/seoFilterProvider');

        $seoForFilterModel = $seoFilterProvider->getSeoFilter($category, $storeId);

        if (!$seoForFilterModel) {
            return false;
        }

        if (!trim($seoForFilterModel->getMetaKeywords())) {
            return false;
        }

        $block->setKeywords(trim($seoForFilterModel->getMetaKeywords()));
        return true;
    }

    /**
     * @return MageWorx_SeoXTemplates_Model_DynamicRenderer_Category
     */
    protected function _getXTemplatesDynamicRenderer()
    {
        return Mage::getSingleton('mageworx_seoxtemplates/dynamicRenderer_category');
    }

    /**
     * @param string $title
     * @param bool $stopLnFilters
     * @return string
     */
    protected function _updateTitle($title, $stopLnFilters)
    {
        $title = trim($title);

        if (!$stopLnFilters) {
            $filtersPartForMeta = $this->_getLayerFiltersMetaPart('title');
        }

        if (!empty($filtersPartForMeta)) {
            $title = rtrim($title) . $this->_listSeparator . $filtersPartForMeta;
        }

        $this->_addPageNumToMeta('title', $title);

        return $title;
    }

    /**
     * @param string $description
     * @param bool $stopLnFilters
     * @return string
     */
    protected function _updateMetaDescription($description, $stopLnFilters)
    {
        $description = trim($description);

        if (!$stopLnFilters) {
            $filtersPartForMeta = $this->_getLayerFiltersMetaPart('description');
        }

        if (!empty($filtersPartForMeta)) {
            $description = rtrim($description) . $this->_listSeparator . $filtersPartForMeta;
        }

        $this->_addPageNumToMeta('description', $description);

        return $description;
    }

    /**
     * @param string $description
     * @param bool $stopLnFilters
     * @return string
     */
    protected function _updateMetaKeywords($keywords, $stopLnFilters)
    {
        $keywords = trim($keywords);

        if (!$stopLnFilters) {
            $filtersPartForMeta = $this->_getLayerFiltersMetaPart('keywords');
        }

        if (!empty($filtersPartForMeta)) {
            $keywords = rtrim($keywords) . $this->_listSeparator . $filtersPartForMeta;
        }

        $this->_addPageNumToMeta('keywords', $keywords);

        return $keywords;
    }

    /**
     * @param string $metaType
     * @return string
     */
    protected function _getLayerFiltersMetaPart($metaType)
    {
        $metaPart = '';

        /** @var MageWorx_SeoExtended_Helper_Config $helperConfig */
        $helperConfig = Mage::helper('mageworx_seoextended/config');

        if (($metaType == 'title' && $helperConfig->isExtendedMetaTitleForLNEnabled())
            || ($metaType == 'description' && $helperConfig->isExtendedMetaDescriptionForLNEnabled())
        ) {
            /** @var MageWorx_SeoAll_Helper_LayeredFilter $helperLayer */
            $helperLayer = Mage::helper('mageworx_seoall/layeredFilter');

            $currentFiltersData = $helperLayer->getLayeredNavigationFiltersData();
            if (is_array($currentFiltersData) && count($currentFiltersData) > 0) {
                foreach ($currentFiltersData as $filter) {
                    $metaPart .= $filter['name'] . $this->_pairSeparator . strip_tags($filter['label'] . $this->_listSeparator);
                }
            }
        }

        return trim(trim($metaPart), trim($this->_listSeparator));
    }

    /**
     * @return string
     */
    protected function _getPairSeparator()
    {
        /** @var MageWorx_SeoExtended_Helper_Data $helperAdapter */
        $helperAdapter = Mage::helper('mageworx_seoextended/adapter');

        if ($helperAdapter->isAvailableSeoTemplates()) {
            /** @var MageWorx_SeoXTemplates_Helper_Config $templateHelperConfig */
            $templateHelperConfig = Mage::helper('mageworx_seoxtemplates/config');
            $templateHelperConfig->getSeparatorForPair();
        }

        return self::DEFAULT_PAIR_SEPARATOR;
    }

    /**
     * @return string
     */
    protected function _getListSeparator()
    {
        /** @var MageWorx_SeoExtended_Helper_Data $helperAdapter */
        $helperAdapter = Mage::helper('mageworx_seoextended/adapter');

        if ($helperAdapter->isAvailableSeoTemplates()) {
            /** @var MageWorx_SeoXTemplates_Helper_Config $templateHelperConfig */
            $templateHelperConfig = Mage::helper('mageworx_seoxtemplates/config');
            return $templateHelperConfig->getSeparatorForList();
        }

        return self::DEFAULT_LIST_SEPARATOR;
    }
}