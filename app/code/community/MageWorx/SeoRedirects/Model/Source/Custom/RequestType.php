<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoRedirects_Model_Source_Custom_RequestType extends MageWorx_SeoAll_Model_Source
{
    /**
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => MageWorx_SeoRedirects_Model_Redirect_Custom::PERMANENT_REDIRECT_TYPE_ID,
                'label' => Mage::helper('mageworx_seoredirects')->__('Permanent redirect(301)')
            ),
            array(
                'value' => MageWorx_SeoRedirects_Model_Redirect_Custom::TEMPORARY_REDIRECT_TYPE_ID,
                'label' => Mage::helper('mageworx_seoredirects')->__('Temporary redirect(302)')
            ),
        );
    }
}