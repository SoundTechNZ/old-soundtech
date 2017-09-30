<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoRedirects_Model_Source_Custom_Status extends MageWorx_SeoAll_Model_Source
{
    /**
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 1,
                'label' => Mage::helper('adminhtml')->__('Enable')
            ),
            array(
                'value' => 0,
                'label' => Mage::helper('adminhtml')->__('Disable')
            ),
        );
    }
}