<?php
/**
 * MageWorx
 * MageWorx SeoFriendlyLN Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoFriendlyLN
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoFriendlyLN_Block_Catalog_Product_List_Toolbar extends MageWorx_SeoFriendlyLN_Block_Catalog_Product_List_Toolbar_Abstract
{

    /**
     * @param array $params
     * @return string
     */
    public function getPagerUrl($params = array())
    {
        if($this->_out()){
            return parent::getPagerUrl($params);
        }

        $urlParams                 = array();
        $urlParams['_current']     = true;
        $urlParams['_escape']      = true;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_query']       = $params;
        return Mage::helper('mageworx_seofriendlyln')->getLayerFilterUrl($urlParams);
    }

    protected function _out()
    {
        if (!Mage::helper('mageworx_seofriendlyln/config')->isLNFriendlyUrlsEnabled()) {
            return true;
        }

        if ((string)Mage::helper('mageworx_seofriendlyln')->isModuleOutputEnabled('Amasty_Shopby')) {
            return true;
        }

        if (Mage::helper('mageworx_seofriendlyln')->isIndividualLNFriendlyUrlsDisable()) {
            return true;
        }

        return false;
    }

}
