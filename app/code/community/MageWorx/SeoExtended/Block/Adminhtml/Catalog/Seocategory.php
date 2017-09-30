<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Block_Adminhtml_Catalog_Seocategory extends Mage_Adminhtml_Block_Template
{
    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('mageworx_seoextended/adminhtml_catalog_seocategory_grid', 'seoextended.seocategory.grid')
        );
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getGridTitle()
    {
        return Mage::helper('mageworx_seoextended')->__('Category SEO Grid');
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