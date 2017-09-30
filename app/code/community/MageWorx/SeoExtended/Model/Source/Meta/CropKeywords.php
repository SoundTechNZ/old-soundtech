<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Model_Source_Meta_CropKeywords extends MageWorx_SeoAll_Model_Source
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'yes',
                'label' => Mage::helper('mageworx_seoextended')->__('Yes (Ignore List Available)')
            ),
            array(
                'value' => 'for_empty',
                'label' => Mage::helper('mageworx_seoextended')->__('Yes, if Empty Content')
            ),
            array(
                'value' => 'no',
                'label' => Mage::helper('catalog')->__('No')
            )
        );
    }
}