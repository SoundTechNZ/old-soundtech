<?php
/**
 * MageWorx
 * MageWorx SeoFriendlyLN Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoFriendlyLN
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */



class MageWorx_SeoFriendlyLN_Model_Catalog_Layer_Filter_Item extends MageWorx_SeoFriendlyLN_Model_Catalog_Layer_Filter_Item_Abstract
{
    /**
     * @return string
     */
    public function getUrl()
    {
        if($this->_out()){
            return parent::getUrl();
        }

        if ($this->getFilter() instanceof Mage_Catalog_Model_Layer_Filter_Category) {
            return $this->_getCategoryUrl();
        }

        return $this->_getDefaultUrl();
    }

    /**
     * @return string
     */
    public function getRemoveUrl()
    {
        if($this->_out()){
            return parent::getRemoveUrl();
        }

        $query                  = array($this->getFilter()->getRequestVar() => $this->getFilter()->getResetValue());
        $params['_current']     = true;
        $params['_use_rewrite'] = true;
        $params['_query']       = $query;
        $params['_escape']      = true;
        return Mage::helper('mageworx_seofriendlyln')->getLayerFilterUrl($params);
    }

    /**
     * @TODO  Optimize: use collection from block.
     * @param int $id
     * @return bool
     */
    private function _isCategoryAnchor($id)
    {
        if (is_object(Mage::registry('current_category')) && !is_array(Mage::registry('mageworx_category_anchor'))) {
            $collection = Mage::registry('current_category')->getChildrenCategories();

            if (is_object($collection) && is_callable(array($collection, 'toArray'))) {
                $data = $collection->toArray();
                if (is_array($data) && count($data) > 0) {
                    Mage::register('mageworx_category_anchor', $data);
                }
            }
        }

        $catData = Mage::registry('mageworx_category_anchor');
        if (is_array($catData) && !empty($catData[$id])) {
            return !empty($catData[$id]['is_anchor']);
        }

        return false;
    }

    /**
     * @return string
     */
    protected function _getCategoryUrl()
    {
        $category = Mage::getModel('catalog/category')->setId($this->getValue());
        $query = array(
            Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls
        );

        $suffix = $this->_getSuffix();

        $catpart = $category->getUrl();

        if ($suffix && substr($catpart, -(strlen($suffix))) == $suffix) {
            $catpart = substr($catpart, 0, -(strlen($suffix)));
        }

        if ($this->_isCategoryAnchor($this->getValue())) {
            $layeredNavIdentifier = Mage::helper('mageworx_seofriendlyln/config')->getLayeredNavigationIdentifier();

            if (preg_match("/\/$layeredNavIdentifier\/.+/", Mage::app()->getRequest()->getOriginalPathInfo(), $matches)) {
                $layeredpart = ($suffix && substr($matches[0], -(strlen($suffix))) == $suffix ? substr(
                    $matches[0], 0,
                    -(strlen($suffix))
                ) : $matches[0]);
            }
            else {
                $layeredpart = '';
            }
        } else {
            $layeredpart = '';
        }

        $catpart     = str_replace('?___SID=U', '', $catpart);
        $catpart     = trim($catpart);
        $layeredpart = trim($layeredpart);
        $catpart     = str_replace($suffix, '', $catpart);
        $url         = $this->_clearDoubleSlashInUrl($catpart . $layeredpart . $suffix);

        return $url;
    }

    /**
     * @return string
     */
    protected function _getDefaultUrl()
    {
        $var        = $this->getFilter()->getRequestVar();
        $labelValue = $this->getLabel();
        $attribute  = $this->getFilter()->getData('attribute_model');

        if ($attribute) {
            if ($attribute->getAttributeCode() == 'price' || $attribute->getBackendType() == 'decimal') {
                $value = $this->getValue();
            }
            else {
                $value = $labelValue;
            }
        }
        else {
            $value = $labelValue;
        }

        $query = array(
            $var => $value,
            Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls
        );
        return Mage::helper('mageworx_seofriendlyln')->getLayerFilterUrl(
            array('_current'     => true,
                '_use_rewrite' => true,
                '_query'       => $query
            )
        );
    }

    /**
     * @return string
     */
    protected function _getSuffix()
    {
        $suffix  = Mage::getStoreConfig('catalog/seo/category_url_suffix');

        if ($suffix == "/") {
            $suffix = '';
        }

        if ($suffix && strpos($suffix, '.') === false) {
            $suffix = '.' . $suffix;
        }

        if (strlen($suffix) > 0 && strpos($suffix, '.') === false){
            $suffix = $suffix . '.';
        }

        return $suffix;
    }

    /**
     * @param string $url
     * @return string
     */
    protected function _clearDoubleSlashInUrl($url)
    {
        $url = trim($url);

        $url = str_replace("//", "/", $url);
        if (strpos($url, 'http:/') === 0) {
            $url = substr_replace($url, 'http://', 0, 6);
        } elseif(strpos($url, 'https:/') === 0) {
            $url = substr_replace($url, 'https://', 0, 7);
        }

        return $url;
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

        if (Mage::helper('mageworx_seofriendlyln')->isIndividualLNFriendlyUrlsDisable()) {
            return true;
        }

        return false;
    }
}