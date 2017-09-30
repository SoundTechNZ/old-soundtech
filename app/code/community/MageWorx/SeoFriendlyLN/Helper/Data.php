<?php
/**
 * MageWorx
 * MageWorx SeoFriendlyLN Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoFriendlyLN
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoFriendlyLN_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_layerFilterableAttributes;

    /**
     * Disable LN Friendly URLs by conditions.
     * Used in classes:
     * MageWorx_SeoFriendlyLN_Block_Page_Html_Pager
     * MageWorx_SeoFriendlyLN_Block_Catalog_Product_List_Toolbar
     * MageWorx_SeoFriendlyLN_Model_Catalog_Layer_Filter_Item
     * MageWorx_SeoFriendlyLN_Model_Catalog_Layer_Filter_Attribute
     *
     * @return boolean
     */
    public function isIndividualLNFriendlyUrlsDisable()
    {
        return false;
    }

    public function _getFilterableAttributes($catId = false)
    {
        if ($this->_layerFilterableAttributes === null) {
            $attr = array();

            $layerModel = Mage::getModel('catalog/layer');
            if ($catId) {
                $layerModel->setCurrentCategory($catId);
            }

            $attributes = $layerModel->getFilterableAttributes();

            foreach ($attributes as $attribute) {
                $attr[$attribute->getAttributeCode()]['type'] = $attribute->getBackendType();
                $options                                      = $attribute->getSource()->getAllOptions();
                foreach ($options as $option) {
                    $attr[$attribute->getAttributeCode()]['options'][$this->formatUrlKey($option['label'])] = $option['label'];
                    $attr[$attribute->getAttributeCode()]['frontend_label']                                 = $attribute->getFrontendLabel();
                }
            }

            $this->_layerFilterableAttributes = $attr;
        }

        return $this->_layerFilterableAttributes;
    }

    public function getLayerFilterUrl($params)
    {
        /** @var MageWorx_SeoFriendlyLN_Helper_Config $helper */
        $helper = Mage::helper('mageworx_seofriendlyln/config');

        if (!$helper->isLNFriendlyUrlsEnabled()) {
            return Mage::getUrl('*/*/*', $params);
        }

        $hideAttributes = $helper->getIsHideAttributeCodes();
        $urlModel       = Mage::getModel('core/url');
        $queryParams    = $urlModel->getRequest()->getQuery();

        if (isset($queryParams['price']) && is_array($queryParams['price'])) {
            $queryParams['price'] = join(' ', $queryParams['price']);
        }

        if (isset($queryParams['price']) && strpos($queryParams['price'], '-') !== false) {
            $multipliers          = explode('-', $queryParams['price']);
            $priceFrom            = floatval($multipliers[0]);
//            $priceTo              = (!$multipliers[1] ? '' : floatval($multipliers[1]) - 0.01);
            $priceTo              = (!$multipliers[1] ? '' : floatval($multipliers[1]));
            $queryParams['price'] = $priceFrom . '-' . $priceTo;
        }

        foreach ($params['_query'] as $param => $value) {
            $queryParams[$param] = $value;
        }

        $queryParams = array_filter($queryParams);
        $attr        = $this->_getFilterableAttributes();

        $layerParams = array();
        foreach ($queryParams as $param => $value) {
            if ($param == 'cat' || isset($attr[$param])) {
                switch ($hideAttributes) {
                    case true:
                        $layerParams = $this->_addHideAttributeToParams($layerParams, $param, $value, $attr);
                        break;
                    default:
                        $layerParams = $this->_addAttributeToParams($layerParams, $param, $value, $attr);
                        break;
                }

                $params['_query'][$param] = null;
            }
        }

        $layer = null;
        if (!empty($layerParams)) {
            uksort($layerParams, 'strcmp');
            $layer = implode('/', $layerParams);
        }

        $url = Mage::getUrl('*/*/*', $params);
        if (!$layer) {
            return $url;
        }

        $urlParts = explode('?', $url, 2);
        $suffix   = $helper->getCategoryUrlSuffix();

        if ($suffix && substr($urlParts[0], -(strlen($suffix))) == $suffix) {
            $url = substr($urlParts[0], 0, -(strlen($suffix)));
        }
        else {
            $url = $urlParts[0];
        }

        $navIdentifier = $helper->getLayeredNavigationIdentifier();
        return $url . '/' . $navIdentifier . '/' . $layer . $suffix . (isset($urlParts[1]) ? '?' . $urlParts[1] : '');
    }

    /**
     * @param string $str
     * @return string
     */
    public function formatUrlKey($str)
    {
        return Mage::getSingleton('catalog/category')->formatUrlKey($str);
    }

    /**
     * @param array $layerParams
     * @param string $param
     * @param string $value
     * @param array $attr
     * @return array
     */
    protected function _addHideAttributeToParams($layerParams, $param, $value, $attr)
    {
        /** @var MageWorx_SeoFriendlyLN_Helper_Config $helper */
        $helper = Mage::helper('mageworx_seofriendlyln/config');

        $key = $param == 'cat' ? 0 : $param;

        if ($attr[$param]['type'] == 'decimal') {
            $layerParams[$key] = $this->formatUrlKey($param) . $helper->getAttributeValueDelimiter() . $value;
        } else {
            $layerParams[$key] = $this->formatUrlKey($value);
        }

        return $layerParams;
    }

    /**
     * @param array $layerParams
     * @param string $param
     * @param string $value
     * @param array $attr
     * @return array
     */
    protected function _addAttributeToParams($layerParams, $param, $value, $attr)
    {
        /** @var MageWorx_SeoFriendlyLN_Helper_Config $helper */
        $helper = Mage::helper('mageworx_seofriendlyln/config');

        if ($param == 'cat') {
            $key = 0;
            $layerParams[$key] = $this->formatUrlKey($value);
        } else {
            $key = $param;
            $part = ($attr[$param]['type'] == 'decimal') ? $value : $this->formatUrlKey($value);
            $layerParams[$key] = $this->formatUrlKey($param) . $helper->getAttributeValueDelimiter() . $part;
        }

        return $layerParams;
    }

}