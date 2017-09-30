<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Block_Adminhtml_Catalog_Category_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('category_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle($this->__('SEO for Category Filters'));
    }

    /**
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        /** @var MageWorx_SeoExtended_Helper_Data $helper */
        $helper = Mage::helper('mageworx_seoextended');

        if ($helper->getStep() == 'new_step_1') {
            $this->addTab(
                'general_tab',
                array(
                    'label' => $helper->__('Settings'),
                    'title' => $helper->__('Settings'),
                    'content' => $this->getLayout()->createBlock("mageworx_seoextended/adminhtml_catalog_category_edit_tab_prepare")->toHtml(),
                    'active' => true,
                )
            );
        } else {
            $this->addTab(
                'general_tab',
                array(
                    'label' => Mage::helper('catalog')->__('Settings'),
                    'title' => Mage::helper('catalog')->__('Settings'),
                    'content' => $this->getLayout()->createBlock("mageworx_seoextended/adminhtml_catalog_category_edit_tab_general")->toHtml(),
                    'active' => true,
                )
            );
        }

        return parent::_beforeToHtml();
    }
}