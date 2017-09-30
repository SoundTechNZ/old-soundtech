<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Block_Adminhtml_Catalog_Seocategory_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('seocategory_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle($this->__('Category SEO Mass Edit'));
    }

    /**
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'massedit_tab',
            array(
                'label' => Mage::helper('catalog')->__('Mass Edit Grid'),
                'title' => Mage::helper('catalog')->__('Mass Edit Grid'),
                'content' => $this->getLayout()->createBlock("mageworx_seoextended/adminhtml_catalog_seocategory_edit_tab_massedit")->toHtml(),
                'active' => true,
            )
        );

        return parent::_beforeToHtml();
    }
}