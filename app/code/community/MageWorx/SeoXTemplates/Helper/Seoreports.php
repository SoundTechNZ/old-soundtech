<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Helper_Seoreports extends Mage_Core_Helper_Abstract
{
    /**
     * Get category templates options in "key-value" format
     *
     * @return array
     */
    public function categoryTemplatesTypeArray()
    {
        $categoryTemplateHelper = Mage::helper('mageworx_seoxtemplates/template_category');
        $types = $categoryTemplateHelper->getTypeArray();

        unset($types[$categoryTemplateHelper::CATEGORY_META_KEYWORDS]);
        unset($types[$categoryTemplateHelper::CATEGORY_DESCRIPTION]);

        return $types;
    }

    /**
     * Get product templates options in "key-value" format
     *
     * @return array
     */
    public function productTemplatesTypeArray()
    {
        $productTemplateHelper = Mage::helper('mageworx_seoxtemplates/template_product');
        $types = $productTemplateHelper->getTypeArray();

        unset($types[$productTemplateHelper::PRODUCT_META_KEYWORDS]);
        unset($types[$productTemplateHelper::PRODUCT_DESCRIPTION]);
        
        if (Mage::helper('mageworx_seoall/version')->isEeRewriteActive()) {
            unset($types[$productTemplateHelper::PRODUCT_URL_KEY]);
        }

        return $types;
    }
}