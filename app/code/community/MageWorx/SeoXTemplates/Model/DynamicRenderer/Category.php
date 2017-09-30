<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Model_DynamicRenderer_Category extends Mage_Core_Model_Abstract
{
    /**
     * @var
     */
    protected $_isConvertedTitle;

    protected $_isConvertedMetaDescription;

    protected $_isConvertedMetaKeywords;

    protected $_isConvertedDescription;

    /**
     * @param Mage_Catalog_Model_Category $category
     * @param Mage_Page_Block_Html_Head $block
     * @return boolean
     */
    public function modifyCategoryTitle($category, $block)
    {
        if ($this->_isConvertedTitle) {
            return true;
        }

        /** @var MageWorx_SeoXTemplates_Model_Converter_Category_Metatitle $metaTitleConverter */
        $metaTitleConverter = Mage::getSingleton('mageworx_seoxtemplates/converter_category_metatitle');
        $title = Mage::helper('mageworx_seoall/title')->cutPrefixSuffix($block->getTitle());

        $convertedTitle = $metaTitleConverter->convert($category, $title, true);

        if (!empty($convertedTitle) && $title != $convertedTitle) {
            $convertedTitle = trim(htmlspecialchars(html_entity_decode($convertedTitle, ENT_QUOTES, 'UTF-8')));
            if ($convertedTitle) {
                $block->setTitle($convertedTitle);
                $this->_isConvertedTitle = true;
                return true;
            }
        }

        return false;
    }

    /**
     * @param Mage_Catalog_Model_Category $category
     * @param Mage_Page_Block_Html_Head $block
     * @return boolean
     */
    public function modifyCategoryMetaDescription($category, $block)
    {
        if ($this->_isConvertedMetaDescription) {
            return true;
        }

        $metaDescriptionConverter = Mage::getSingleton('mageworx_seoxtemplates/converter_category_metadescription');
        $convertedMetaDescription = $metaDescriptionConverter->convert($category, $block->getDescription(), true);

        if (!empty($convertedMetaDescription) && $block->getDescription() != $convertedMetaDescription) {
            $stripTags       = new Zend_Filter_StripTags();
            $convertedMetaDescription = htmlspecialchars(
                html_entity_decode(
                    preg_replace(
                        array('/\r?\n/', '/[ ]{2,}/'),
                        array(' ', ' '), $stripTags->filter($convertedMetaDescription)
                    ), ENT_QUOTES, 'UTF-8'
                )
            );
            if ($convertedMetaDescription) {
                $this->_isConvertedMetaDescription = true;
                $block->setDescription($convertedMetaDescription);
                return true;
            }
        }

        return false;
    }

    /**
     * @param Mage_Catalog_Model_Category $category
     * @param Mage_Page_Block_Html_Head $block
     * @return boolean
     */
    public function modifyCategoryMetaKeywords($category, $block)
    {
        if ($this->_isConvertedMetaKeywords) {
            return true;
        }

        /** @var MageWorx_SeoXTemplates_Model_Converter_Category_Metakeywords $metaKeywordsConverter */
        $metaKeywordsConverter = Mage::getSingleton('mageworx_seoxtemplates/converter_category_metakeywords');
        $convertedMetaKeywords = $metaKeywordsConverter->convert($category, $block->getKeywords(), true);

        if (!empty($convertedMetaKeywords) && $block->getKeywords() != $convertedMetaKeywords) {
            $stripTags       = new Zend_Filter_StripTags();
            $convertedMetaKeywords = htmlspecialchars(
                html_entity_decode(
                    preg_replace(
                        array('/\r?\n/', '/[ ]{2,}/'),
                        array(' ', ' '), $stripTags->filter($convertedMetaKeywords)
                    ), ENT_QUOTES, 'UTF-8'
                )
            );
            if ($convertedMetaKeywords) {
                $this->_isConvertedMetaKeywords = true;
                $block->setKeywords($convertedMetaKeywords);
                return true;
            }
        }

        return false;
    }

    /**
     * @param Mage_Catalog_Model_Category $category
     * @param Mage_Page_Block_Html_Head $block
     * @return boolean
     */
    public function modifyCategoryDescription($category)
    {
        if ($this->_isConvertedDescription) {
            return true;
        }

        $descriptionConverter = Mage::getSingleton('mageworx_seoxtemplates/converter_category_description');
        $convertedDescription = $descriptionConverter->convert($category, $category->getDescription(), true);

        if (!empty($convertedDescription) && $convertedDescription != $category->getDescription()) {
            $this->_isConvertedDescription = true;
            $category->setDescription($convertedDescription);
            return true;
        }

        return false;
    }
}