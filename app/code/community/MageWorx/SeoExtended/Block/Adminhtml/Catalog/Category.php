<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Block_Adminhtml_Catalog_Category extends Mage_Adminhtml_Block_Template
{
    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'add_new_button', $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                'label' => Mage::helper('mageworx_seoextended')->__('New SEO for Category Filter'),
                'onclick' => "setLocation('" . $this->getUrl('*/*/new', array('store' => Mage::app()->getRequest()->getParam('store'))) . "')",
                'class' => 'add'
                )
            )
        );

        $this->setChild('grid', $this->getLayout()->createBlock('mageworx_seoextended/adminhtml_catalog_category_grid', 'seoextended.category.grid'));
        return parent::_prepareLayout();
    }

    /**
     * Retrieve HTML of add button
     *
     * @return string
     */
    public function getAddNewButtonHtml()
    {
        return $this->getChildHtml('add_new_button');
    }

    /**
     * @return string
     */
    public function getGridTitle()
    {
        return Mage::helper('mageworx_seoextended')->__('Manage SEO for Category Filters');
    }

    /**
     * Get grid HTML
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }
}