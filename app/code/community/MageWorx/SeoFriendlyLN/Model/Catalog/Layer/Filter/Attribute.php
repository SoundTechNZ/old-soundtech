<?php
/**
 * MageWorx
 * MageWorx SeoFriendlyLN Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoFriendlyLN
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoFriendlyLN_Model_Catalog_Layer_Filter_Attribute extends MageWorx_SeoFriendlyLN_Model_Catalog_Layer_Filter_Attribute_Abstract
{
    /**
     * @param Zend_Controller_Request_Abstract $request
     * @param Varien_Object $filterBlock
     * @return $this|Mage_Catalog_Model_Layer_Filter_Attribute
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        if($this->_out()){
            return parent::apply($request, $filterBlock);
        }

        $text = $request->getParam($this->_requestVar);
        if (is_array($text)) {
            return $this;
        }

        $filter = $this->_getOptionId($text);

        if ($filter && $text) {
            if (method_exists($this, '_getResource')) {
                $this->_getResource()->applyFilterToCollection($this, $filter);
            }
            else {
                Mage::getSingleton('catalogindex/attribute')->applyFilterToCollection(
                    $this->getLayer()->getProductCollection(), $this->getAttributeModel(), $filter
                );
            }

            $this->getLayer()->getState()->addFilter($this->_createItem($text, $filter));
            $this->_items = array();
        }

        return $this;
    }

    /**
     * @param string $label
     * @return bool|null
     */
    protected function _getOptionId($label)
    {
        if ($source = $this->getAttributeModel()->getSource()) {
            return $source->getOptionId($label);
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function _out()
    {
        if (!Mage::helper('mageworx_seofriendlyln/config')->isLNFriendlyUrlsEnabled()){
            return true;
        }

        if ((string)Mage::helper('mageworx_seofriendlyln')->isModuleOutputEnabled('Amasty_Shopby')) {
            return true;
        }

        if(Mage::helper('mageworx_seofriendlyln')->isIndividualLNFriendlyUrlsDisable()){
            return true;
        }

        return false;
    }

}
