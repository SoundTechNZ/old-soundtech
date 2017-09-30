<?php
/**
 * MageWorx
 * MageWorx SeoReports Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoReports
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */



class MageWorx_SeoReports_Model_Observer
{
    public function productSaveAfter(Varien_Event_Observer $observer)
    {
        if (Mage::helper('mageworx_seoreports')->getProductReportStatus()) Mage::helper('mageworx_seoreports')->setProductReportStatus(0);
    }

    public function categorySaveAfter(Varien_Event_Observer $observer)
    {
        if (Mage::helper('mageworx_seoreports')->getCategoryReportStatus()) Mage::helper('mageworx_seoreports')->setCategoryReportStatus(0);
    }

    public function cmsPageSaveAfter(Varien_Event_Observer $observer)
    {
        if (Mage::helper('mageworx_seoreports')->getCmsReportStatus()) Mage::helper('mageworx_seoreports')->setCmsReportStatus(0);
    }
}