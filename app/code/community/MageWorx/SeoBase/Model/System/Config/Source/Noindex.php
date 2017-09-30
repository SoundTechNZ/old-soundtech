<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_System_Config_Source_Noindex
{
    /**
     * @var array
     */
    protected $_options;

    /**
     * @param bool $isMultiselect
     * @return array
     */
    public function toOptionArray($isMultiselect = false)
    {
        $helper = Mage::helper('mageworx_seobase');

        if (!$this->_options) {
            $this->_options = array(
                array('value' => '^checkout_.+', 'label' => $helper->__('Checkout Pages')),
                array('value' => '^contacts_.+', 'label' => $helper->__('Contact Us Page')),
                array('value' => '^customer_.+', 'label' => $helper->__('Customer Account Pages')),
                array('value' => '^catalog_product_compare_.+', 'label' => $helper->__('Product Compare Pages')),
                //array('value'=>'^review.+', 'label'=> $helper->__('Product Review Pages')),
                array('value' => '^rss_.+', 'label' => $helper->__('RSS Feeds')),
                array('value' => '^catalogsearch_.+', 'label' => $helper->__('Search Pages')),
                array('value' => '.*?_product_send$', 'label' => $helper->__('Send Product Pages')),
                array('value' => '^tag_.+', 'label' => $helper->__('Tag Pages')),
                array('value' => '^wishlist_.+', 'label' => $helper->__('Wishlist Pages')),
            );
        }

        $options = $this->_options;

        if (!$isMultiselect) {
            array_unshift($options, array('value' => '', 'label' => Mage::helper('adminhtml')->__('--Please Select--')));
        }

        return $options;
    }

}