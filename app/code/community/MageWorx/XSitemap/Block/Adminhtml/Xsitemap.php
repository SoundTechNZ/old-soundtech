<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_XSitemap_Block_Adminhtml_Xsitemap extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup     = 'mageworx_xsitemap';
        $this->_controller     = 'adminhtml_xsitemap';
        $this->_headerText     = Mage::helper('mageworx_xsitemap')->__('Google Sitemap (Extended)');
        $this->_addButtonLabel = Mage::helper('mageworx_xsitemap')->__('Add Sitemap');
        parent::__construct();
    }

}
