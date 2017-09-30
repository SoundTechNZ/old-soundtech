<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Model_Observer_Seoreports extends Mage_Core_Model_Abstract
{
    /**
     * Add massaction to SeoReports category grid
     *
     * @param type $observer
     * @return type
     */
    public function addMassactionToCategoryGrid($observer)
    {
        $block = $observer->getBlock();
        $block->getMassactionBlock()->addItem(
            'type_id',
            array(
                'label' => Mage::helper('mageworx_seoxtemplates')->__('Create template for'),

                'url'   => $block->getUrl('*/mageworx_seoxtemplates_template_category/new/store/', array('store' => $observer->getStore())),
                'additional' => array(
                    'visibility' => array(
                        'name'     => 'type_id',
                        'type'     => 'select',
                        'class'    => 'required-entry',
                        'values'   => Mage::helper('mageworx_seoxtemplates/seoreports')->categoryTemplatesTypeArray()
                    )
                )
            )
        );

    }

    /**
    * Add massaction to SeoReports product grid
    *
    * @param type $observer
    * @return type
    */
    public function addMassactionToProductGrid($observer)
    {
        $block = $observer->getBlock();
        $block->getMassactionBlock()->addItem(
            'type_id',
            array(
                'label' =>Mage::helper('mageworx_seoxtemplates')->__('Create template for'),
                'url'   => $block->getUrl('*/mageworx_seoxtemplates_template_product/new/store/', array('store' => $observer->getStore())),
                'additional' => array(
                    'visibility' => array(
                        'name'     => 'type_id',
                        'type'     => 'select',
                        'class'    => 'required-entry',
                        'values'   => Mage::helper('mageworx_seoxtemplates/seoreports')->productTemplatesTypeArray()
                    )
                )
            )
        );
    }
}