<?php
/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoXTemplates_Block_Adminhtml_Template_CategoryFilter extends Mage_Adminhtml_Block_Template
{
    protected function _prepareLayout()
    {
        $this->setChild(
            'add_new_button', $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                'label' => Mage::helper('mageworx_seoxtemplates')->__('New Template'),
                'onclick' => "setLocation('" . $this->getUrl('*/*/new') . "')",
                'class' => 'add'
                )
            )
        );

        $this->setChild('grid', $this->getLayout()->createBlock('mageworx_seoxtemplates/adminhtml_template_categoryFilter_grid', 'template.grid'));
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
     * Get grid HTML
     *
     * @return unknown
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

    /**
     * Retrieve category template labels
     * @return array
     */
    public function getEntityNames()
    {
        return array(
            'single' => Mage::helper('catalog')->__('SEO for Category Filter'),
            'plural' => Mage::helper('catalog')->__('SEO for Category Filters')
        );
    }

}