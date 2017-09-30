<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_XSitemap_Block_Adminhtml_Cache_Notice extends Mage_Adminhtml_Block_Template
{
    protected function _toHtml()
    {
        if (Mage::helper('mageworx_xsitemap')->isProductImages() && Mage::helper('mageworx_xsitemap')->isUseImageCache()) {
            return parent::_toHtml();
        }

        return '';
    }
}
