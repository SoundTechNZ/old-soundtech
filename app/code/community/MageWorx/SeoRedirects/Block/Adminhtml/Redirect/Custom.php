<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoRedirects_Block_Adminhtml_Redirect_Custom extends Mage_Adminhtml_Block_Template
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
                    'label' => Mage::helper('mageworx_seoredirects')->__('New Custom Redirect'),
                    'onclick' => "setLocation('" . $this->getUrl('*/*/new') . "')",
                    'class' => 'add'
                )
            )
        );
        $this->setChild('grid', $this->getLayout()->createBlock('mageworx_seoredirects/adminhtml_redirect_custom_grid', 'redirect.custom.grid'));
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
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }
}