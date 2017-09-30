<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_XSitemap_Model_Adminhtml_System_Config_Source_Xsitemap_Url_Product
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'yes', 'label' => Mage::helper('mageworx_xsitemap')->__('Yes')),
            array('value' => 'no', 'label' => Mage::helper('mageworx_xsitemap')->__('No')),
            array('value' => 'canonical', 'label' => Mage::helper('mageworx_xsitemap')->__('Use Canonical URL'))
        );
    }

}
