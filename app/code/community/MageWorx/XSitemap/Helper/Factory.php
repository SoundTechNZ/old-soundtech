<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_XSitemap_Helper_Factory extends Mage_Core_Helper_Abstract
{
    public function getProductXmlResource()
    {
        if(Mage::helper('mageworx_seoall/version')->isEeRewriteActive()) {
            return Mage::getResourceModel('mageworx_xsitemap/catalog_product_ee_xml');
        }

        return Mage::getResourceModel('mageworx_xsitemap/catalog_product_ce_xml');
    }

     public function getCategoryXmlResource()
     {
        if(Mage::helper('mageworx_seoall/version')->isEeRewriteActive()) {
            return Mage::getResourceModel('mageworx_xsitemap/catalog_category_ee_xml');
        }

        return Mage::getResourceModel('mageworx_xsitemap/catalog_category_ce_xml');
     }
}