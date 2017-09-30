<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoRedirects_Model_Source_Custom_RequestEntity extends MageWorx_SeoAll_Model_Source
{
    /**
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => MageWorx_SeoRedirects_Model_Redirect_Custom::CUSTOM_URL_ENTITY_TYPE_ID,
                'label' => Mage::helper('mageworx_seoredirects')->__('Custom URL')
            ),
            array(
                'value' => MageWorx_SeoRedirects_Model_Redirect_Custom::PRODUCT_ENTITY_TYPE_ID,
                'label' => Mage::helper('adminhtml')->__('Product'),
            ),
            array(
                'value' => MageWorx_SeoRedirects_Model_Redirect_Custom::CATEGORY_ENTITY_TYPE_ID,
                'label' => Mage::helper('catalog')->__('Category'),
            ),
            array(
                'value' => MageWorx_SeoRedirects_Model_Redirect_Custom::CMS_PAGE_ENTITY_TYPE_ID,
                'label' => Mage::helper('mageworx_seoredirects')->__('CMS Page')
            ),
        );
    }

    /**
     *
     * @return array
     */
    public function toRequestOptionArray()
    {
        return array(
            array(
                'value' => 'request_' . MageWorx_SeoRedirects_Model_Redirect_Custom::CUSTOM_URL_ENTITY_TYPE_ID,
                'label' => Mage::helper('mageworx_seoredirects')->__('Custom URL')
            ),
            array(
                'value' => 'request_' . MageWorx_SeoRedirects_Model_Redirect_Custom::PRODUCT_ENTITY_TYPE_ID,
                'label' => Mage::helper('adminhtml')->__('Product'),
            ),
            array(
                'value' => 'request_' . MageWorx_SeoRedirects_Model_Redirect_Custom::CATEGORY_ENTITY_TYPE_ID,
                'label' => Mage::helper('catalog')->__('Category'),
            ),
            array(
                'value' => 'request_' . MageWorx_SeoRedirects_Model_Redirect_Custom::CMS_PAGE_ENTITY_TYPE_ID,
                'label' => Mage::helper('mageworx_seoredirects')->__('CMS Page')
            ),
        );
    }
}