<?php
/**
 * MageWorx
 * MageWorx SeoBreadcrumbs Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBreadcrumbs
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBreadcrumbs_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * current breadcrumbs block
     */
    private $_breadcrumbsBlock;

    /**
     * Hide block to avoid adding default breadcrumbs
     * @param $observer
     */
    public function hideBreadcrumbs($observer)
    {

        $block = $observer->getBlock();
        $crumbsBlockName = Mage::helper('mageworx_seobreadcrumbs')->getCrumbsBlockName();
        if($block instanceof Mage_Catalog_Block_Breadcrumbs && $this->isUpdatable()){
           $this->_breadcrumbsBlock = $block->getLayout()->getBlock($crumbsBlockName);
           $block->getLayout()->unsetBlock($crumbsBlockName);
        }
    }

    /**
     * Show breadcrumbs block
     * @param $observer
     */
    public function showBreadcrumbs($observer)
    {
        $block = $observer->getBlock();
        $crumbsBlockName = Mage::helper('mageworx_seobreadcrumbs')->getCrumbsBlockName();
        if($block instanceof Mage_Catalog_Block_Breadcrumbs && $this->isUpdatable()){
            $block->getLayout()->setBlock($crumbsBlockName, $this->_breadcrumbsBlock);
        }
    }

    /**
     * Add custom breadcrums
     * @param $observer
     * @return boolean
     */
    public function modifyBreadcrumbs($observer)
    {
        $block = $observer->getBlock();
        if($block instanceof Mage_Page_Block_Html_Breadcrumbs) {
//            exit('breadcrumbs');

            if (!$this->isUpdatable()) {
                return false;
            }

            $product = Mage::registry('current_product');
            $breadcrumbs = Mage::getModel('mageworx_seobreadcrumbs/breadcrumbs');
            $targetCategory = $breadcrumbs->getTargetCategory($product);
            $crumbs = $breadcrumbs->getNewBreadcrumbPath($product, $targetCategory);
            foreach ($crumbs as $crumbName => $crumbInfo){
                $block->addCrumb($crumbName, $crumbInfo);
            }

            return true;
        }
    }

    /**
     * Checking using custom breadcrumbs
     * @return boolean
     */
    public function isUpdatable()
    {
        $product = Mage::registry('current_product');
        if (!$product) {
            return false;
        }

        if ($product->getId() != Mage::app()->getRequest()->getParam('id')) {
            return false;
        }

        $helper = Mage::helper('mageworx_seobreadcrumbs');
        if (!$helper->isSeoBreadcrumbsEnabled()) {
            return false;
        }

        if ($helper->getSeoBreadcrumbsType() == MageWorx_SeoBreadcrumbs_Model_System_Config_Source_Type::BREADCRUMBS_TYPE_DEFAULT
            && !$helper->isUseCategoryBreadcrumbsPriority()
        ) {
            return false;
        }

        $breadcrumbs = Mage::getModel('mageworx_seobreadcrumbs/breadcrumbs');
        $targetCategory = $breadcrumbs->getTargetCategory($product);
        if (!$targetCategory) {
            return false;
        }

        return true;
    }
}