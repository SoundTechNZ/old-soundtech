<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Block_Adminhtml_Template_CategoryFilter_Grid extends MageWorx_SeoXTemplates_Block_Adminhtml_Template_Grid
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function _addCustomColumn()
    {
        $this->addColumn(
            'attribute_id',
            array(
                'header'    => Mage::helper('mageworx_seoextended')->__('Product Attribute'),
                'width' => '250px',
                'type'  => 'options',
                'options' => Mage::getSingleton('mageworx_seoall/source_product_attribute')->toArray(),
                'index'  => 'attribute_id'
            )
        );
        return $this;
    }

}