<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
abstract class MageWorx_SeoExtended_Model_MetaUpdater
{
    /**
     * @param Mage_Page_Block_Html_Head $block
     * @return mixed
     */
    abstract public function update($block);

    /**
     * @param string $metaType
     * @param string $metaValue
     * @return string
     */
    protected function _addPageNumToMeta($metaType, &$metaValue)
    {
        $metaValue = trim($metaValue);
        $statusPagerNum = Mage::helper('mageworx_seoextended/config')->getStatusPagerNumForMeta($metaType);

        /** @var MageWorx_SeoAll_Helper_Url $helperUrl */
        $helperUrl = Mage::helper('mageworx_seoall/url');


        if ($statusPagerNum == MageWorx_SeoExtended_Model_Source_Meta_PageNum::PAGER_ADD_AFTER) {
            $pageNum = $helperUrl->getPageNumFromUrl();
            if ($pageNum) {
                $metaValue .= ' | ' . Mage::helper('mageworx_seoextended')->__('Page') . " " . $pageNum;
            }
        } elseif ($statusPagerNum == MageWorx_SeoExtended_Model_Source_Meta_PageNum::PAGER_ADD_BEFORE) {
            $pageNum = $helperUrl->getPageNumFromUrl();
            if ($pageNum) {
                $metaValue = Mage::helper('mageworx_seoextended')->__('Page') . " " . $pageNum . ' | ' . $metaValue;
            }
        }

        return $metaValue;
    }

    /**
     * @param Mage_Page_Block_Html_Head $block
     * @return string
     */
    protected function _getTitle($block)
    {
        return Mage::helper('mageworx_seoall/title')->cutPrefixSuffix($block->getTitle());
    }

    /**
     * @param Mage_Page_Block_Html_Head $block
     * @param string $title
     * @param bool $categoryProductFlag
     * @return bool
     */
    protected function _setTitle($block, $title, $categoryProductFlag = false)
    {
        if (!empty($title)) {
            $title = trim(htmlspecialchars(html_entity_decode($title, ENT_QUOTES, 'UTF-8')));

            if ($title) {

                /** @var MageWorx_SeoExtended_Helper_Config $helperConfig */
                $helperConfig = Mage::helper('mageworx_seoextended/config');

                if ($categoryProductFlag && $helperConfig->isCutPrefixSuffixFromProductAndCategoryPages()) {
                    $block->setData('title', $title);
                }
                else {
                    $block->setTitle($title);
                }

                return true;
            }
        }

        return false;
    }

    /**
     * @param Mage_Page_Block_Html_Head $block
     * @param string $metaDescription
     * @return bool
     */
    protected function _setMetaDescription($block, $metaDescription)
    {
        $stripTags       = new Zend_Filter_StripTags();

        $cropedMetaDescription = $stripTags->filter($metaDescription);

        $metaDescription = htmlspecialchars(
            html_entity_decode(
                preg_replace(array('/\r?\n/', '/[ ]{2,}/'), array(' ', ' '), $cropedMetaDescription),
                ENT_QUOTES,
                'UTF-8'
            )
        );

        if ($metaDescription) {
            $block->setDescription($metaDescription);
            return true;
        }

        return false;
    }

    /**
     * @param Mage_Page_Block_Html_Head $block
     * @param string $metaKeywords
     * @return bool
     */
    protected function _setMetaKeywords($block, $metaKeywords)
    {
        $metaKeywords = trim(htmlspecialchars(html_entity_decode($metaKeywords, ENT_QUOTES, 'UTF-8')));
        if ($metaKeywords) {
            $block->setKeywords($metaKeywords);
            return true;
        }

        return false;
    }
}