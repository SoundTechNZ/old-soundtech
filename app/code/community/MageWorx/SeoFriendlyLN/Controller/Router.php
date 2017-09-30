<?php
/**
 * MageWorx
 * MageWorx SeoFriendlyLN Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoFriendlyLN
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoFriendlyLN_Controller_Router extends Mage_Core_Controller_Varien_Router_Standard
{
    const SEO_PARAMS_IDENTIFIER = 'seo_layered_params';

    protected $_request = null;
    protected $_urlData = null;

    public function initControllerRouters($observer)
    {
        $front  = $observer->getEvent()->getFront();
        //  Varien_Autoload::registerScope('catalog');
        $router = new MageWorx_SeoFriendlyLN_Controller_Router();
        $front->addRouter('mageworx_seofriendlyln', $router);
    }

    public function match(Zend_Controller_Request_Http $request)
    {
        $this->_setRequest($request);

        /** @var MageWorx_SeoExtended_Helper_Config $helper */
        $helper = Mage::helper('mageworx_seofriendlyln/config');

        if ($helper->isLNFriendlyUrlsEnabled() && $this->_matchCategoryPager($request)) {
            return true;
        }

        if ($helper->isLNFriendlyUrlsEnabled() && $this->_matchCategoryLayer()) {
            return true;
        }

        return false;
    }

    /**
     *  Parse and set parsed params to request
     */
    public function parseLayeredParams()
    {
        /** @var MageWorx_SeoExtended_Helper_Config $helper */
        $helper = Mage::helper('mageworx_seofriendlyln/config');

        $this->_setRequest(Mage::app()->getRequest());
        $layerParams = $this->_getRequest()->getParam(self::SEO_PARAMS_IDENTIFIER, array());
        foreach ($layerParams as $params) {
            $param = explode($helper->getAttributeParamDelimiter(), $params, 2);
            if (empty($param)) {
                continue;
            }

            if ($cat = $this->_getCategoryByParam($param)) {
                $this->_setCategoryToRequest($cat);
            }
            elseif ($this->_isHiddenAttribute($param)) {
                if ($this->_isHiddenPriceAttribute($param)) {
                    $this->_setPriceAttributeToRequest($param);
                }
                else {
                    $this->_setHiddenAttributeToRequest($param);
                }
            }
            else {
                $this->_setNotHiddenAttributeToRequest($param);
            }
        }
    }

    /**
     * @param Zend_Controller_Request_Http $request
     * @return bool
     */
    protected function _matchCategoryPager($request)
    {
        /** @var MageWorx_SeoFriendlyLN_Helper_Config $helper */
        $helper = Mage::helper('mageworx_seofriendlyln/config');

        $pagerUrlFormat = $helper->getPagerUrlFormat();

        if (!$pagerUrlFormat) {
            return false;
        }

        $url    = $request->getRequestUri();
        $suffix = $helper->getCategoryUrlSuffix();

        $pagerUrlFormatRegEx = explode($helper::PAGER_NUM_PATTERN, $pagerUrlFormat);

        foreach ($pagerUrlFormatRegEx as $key => $part) {
            $pagerUrlFormatRegEx[$key] = preg_quote($part, '/');
        }

        $pagerUrlFormatRegEx = implode('([0-9]+)', $pagerUrlFormatRegEx);
        if (preg_match('/' . $pagerUrlFormatRegEx . preg_quote($suffix, '/') . '/', $url, $match)) {
            $url = str_replace($match[0], $suffix, $url);
            $request->setRequestUri($url);

            $path = $request->getPathInfo();
            $path = str_replace($match[0], $suffix, $path);
            $request->setPathInfo($path);
            $request->setParam($helper::PAGER_VAR_NAME, $match[1]);
        }
        else {
            return false;
        }

        $identifier = $this->_getIdentifier($suffix);
        $urlSplit   = explode('/' . $helper->getLayeredNavigationIdentifier() . '/', $identifier, 2);

        if (isset($urlSplit[1])) {
            return false;
        }

        $catPath    = $identifier . $suffix;
        $urlRewrite = $this->_getUrlRewrite($catPath);

        if (!$urlRewrite->getId()) {
            $store = $request->getParam('___from_store');
            $store = Mage::app()->getStore($store)->getId();

            if (!$store) {
                return false;
            }

            $urlRewrite->setData(array())->setStoreId($store)->loadByRequestPath($catPath);

            if (!$urlRewrite->getId()) {
                return false;
            }
        }

        if ($urlRewrite && $urlRewrite->getId()) {
            $request->setPathInfo($catPath);
            $request->setModuleName('catalog')
                ->setControllerName('category')
                ->setActionName('view')
                ->setParam('id', $urlRewrite->getCategoryId())
                ->setAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS, 'catalog');

            $urlRewrite->rewrite($request);
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function _matchProductAndCategory()
    {
        list($catPath, $layerParams) = $this->_getUrlData();
        if (!isset($layerParams) || !isset($catPath)) {
            return false;
        }

        $urlRewrite = $this->_getCategoryUrlRewrite($catPath);
        if ($urlRewrite && $urlRewrite->getId()) {
            $this->_prepareRequestForUrlRewrite($urlRewrite, $catPath);

            if (count($layerParams)) {
                $this->_passLayerParamsToRequest($layerParams);
                $urlRewrite->rewrite($this->_getRequest());
                return true;
            }
        }

        return false;
    }

    protected function _matchCategoryLayer()
    {
        list($catPath, $layerParams) = $this->_getUrlData();
        if (!isset($layerParams) || !isset($catPath)) {
            return false;
        }

        $urlRewrite = $this->_getCategoryUrlRewrite($catPath);

        if ($urlRewrite && $urlRewrite->getId()) {
            $this->_prepareRequestForUrlRewrite($urlRewrite, $catPath);

            if (count($layerParams)) {
                $this->_passLayerParamsToRequest($layerParams);
                $isVersionEE13 = ('true' == (string) Mage::getConfig()->getNode('modules/Enterprise_UrlRewrite/active'));
                if ($isVersionEE13) {
                    $this->_getRequest()->setParams($layerParams)
                        ->setParam('id', $urlRewrite->getValueId())
                        ->setAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS, $catPath);
                }
                else {
                    $this->_getRequest()->setParams($layerParams);
                    $urlRewrite->rewrite($this->_getRequest());
                }

                return true;
            }
        }

        return false;
    }

    protected function _getUrlRewrite($catPath)
    {
        /** @var MageWorx_SeoAll_Helper_Version $helperVersion */
        $helperVersion = Mage::helper('mageworx_seoall/version');


        if ($helperVersion->isEeRewriteActive()) {
            $urlRewrite = Mage::getModel('enterprise_urlrewrite/url_rewrite');

            /* @var $urlRewrite Enterprise_UrlRewrite_Model_Url_Rewrite */
            $urlRewrite->setStoreId(Mage::app()->getStore()->getId())->loadByRequestPath($catPath);

            return $urlRewrite;
        }

        /* @var $urlRewrite Mage_Core_Model_Url_Rewrite */
        $urlRewrite = Mage::getModel('core/url_rewrite');

        $urlRewrite->setStoreId(Mage::app()->getStore()->getId())->loadByRequestPath($catPath);
        return $urlRewrite;

    }

    protected function _prepareRequestForUrlRewrite($urlRewrite, $catPath)
    {
        $this->_getRequest()->setPathInfo($catPath);
        $this->_getRequest()->setModuleName('catalog')
            ->setControllerName('category')
            ->setActionName('view')
            ->setParam('id', $urlRewrite->getCategoryId())
            ->setAlias(
                Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS, 'catalog'
            );
    }

    /**
     * @param $category
     * @return array
     */
    protected function _getFilterableCategoryAttributesArray($category)
    {
        $modelLayer = Mage::getModel('catalog/layer')->setData('current_category', $category);
        $attributes = $modelLayer->getFilterableAttributes();
        $attr       = array();

        foreach ($attributes as $attribute) {
            foreach ($attribute->getSource()->getAllOptions() as $option) {
                $attr[] = $option['label'];
            }
        }

        return $attr;
    }

    /**
     * @deprecated
     * @param $category
     * @return bool
     */
    protected function _isCategoryNameDuplicatesAttribute($category)
    {
        $attributes = $this->_getFilterableCategoryAttributesArray($category);
        return in_array($category->getName(), $attributes);
    }


    /**
     * @param string $catPath
     * @return mix
     */
    protected function _getCategoryUrlRewrite($catPath)
    {
        /** @var MageWorx_SeoAll_Helper_Version $helperVersion */
        $helperVersion = Mage::helper('mageworx_seoall/version');

        if ($helperVersion->isEeRewriteActive()) {
            /* @var $urlRewrite Enterprise_UrlRewrite_Model_Url_Rewrite */
            $urlRewrite = Mage::getModel('enterprise_urlrewrite/url_rewrite');

            $catArray = (!is_array($catPath)) ? $catArray = $this->_getSystemPaths($catPath) : $catPath;

            $urlRewrite->setStoreId(Mage::app()->getStore()->getId())->loadByRequestPath($catArray);

            //If category id from target_path != value_id, category id will be as in target_path
            if ($urlRewrite->getId()){
                $targetPathParts = explode('/', $urlRewrite->getData('target_path'));
                $categoryIdFromPath = array_pop($targetPathParts);
                if ($categoryIdFromPath && is_numeric($categoryIdFromPath) && $urlRewrite->getData('value_id')) {
                    if ($categoryIdFromPath != $urlRewrite->getData('value_id')) {
                        $urlRewrite->setData('value_id', $categoryIdFromPath);
                    }
                }
            }
        }
        else {
            /* @var $urlRewrite Mage_Core_Model_Url_Rewrite */
            $urlRewrite = Mage::getModel('core/url_rewrite');

            if (is_array($catPath)) {
                $catPath = array_shift($catPath);
            }

            $urlRewrite->setStoreId(Mage::app()->getStore()->getId())->loadByRequestPath($catPath);
        }

        if (!$urlRewrite->getId()) {
            $store = $this->_getRequest()->getParam('___from_store');
            $store = Mage::app()->getStore($store)->getId();

            if (!$store) {
                return false;
            }

            $urlRewrite->setData(array())->setStoreId($store)->loadByRequestPath($catPath);

            if (!$urlRewrite->getId()) {
                return false;
            }
        }

        return ($urlRewrite->getId()) ? $urlRewrite : null;
    }

    /**
     * Return request path pieces
     *
     * @param string $requestPath
     * @return array
     */
    protected function _getSystemPaths($requestPath)
    {
        if (version_compare(Mage::getConfig()->getModuleConfig("Enterprise_UrlRewrite")->version, '1.12.0.13', '<')) {
            $systemPath = explode('/', $requestPath);
            $suffixPart = array_pop($systemPath);
            if (false !== strrpos($suffixPart, '.')) {
                $suffixPart = substr($suffixPart, 0, strrpos($suffixPart, '.'));
            }

            $systemPath[] = $suffixPart;
            return $systemPath;
        }

        $parts  = explode('/', $requestPath);
        $suffix = array_pop($parts);
        if (false !== strrpos($suffix, '.')) {
            $suffix = substr($suffix, 0, strrpos($suffix, '.'));
        }

        $paths = array('request' => $requestPath, 'suffix'  => $suffix);
        if (count($parts)) {
            $paths['whole'] = implode('/', $parts) . '/' . $suffix;
        }

        return $paths;

    }

    /**
     * @return array|null
     */
    protected function _getUrlData()
    {
        /** @var MageWorx_SeoFriendlyLN_Helper_Config $helper */
        $helper = Mage::helper('mageworx_seofriendlyln/config');

        if (!$this->_urlData) {
            $suffix = $helper->getCategoryUrlSuffix();

            $request = $this->_getRequest();
            if ($request->getPathInfo() !== $request->getOriginalPathInfo()) {
                $request->setPathInfo($request->getOriginalPathInfo());
            }

            $identifier = $this->_getIdentifier($suffix);

            if ($helper->isLNFriendlyUrlsEnabled()) {
                $pagerUrlFormat = $helper->getPagerUrlFormat();
            }

            if (!empty($pagerUrlFormat)) {
                $pagerPart = str_replace($helper::PAGER_NUM_PATTERN, '', $pagerUrlFormat);
                $pos = strpos($identifier, $pagerPart);

                if ($pagerPart && $pos) {
                    $identifier = substr($identifier, 0, $pos);
                }
            }

            if ($helper->isLNFriendlyUrlsEnabled()) {
                $pagerUrlFormat = $helper->getPagerUrlFormat();
            }

            if (!empty($pagerUrlFormat)) {
                $pagerPart = str_replace($helper::PAGER_NUM_PATTERN, '', $pagerUrlFormat);
                $pos = strpos($identifier, $pagerPart);

                if ($pagerPart && $pos) {
                    $identifier = substr($identifier, 0, $pos);
                }
            }

            $urlSplit = explode('/' . $helper->getLayeredNavigationIdentifier() . '/', $identifier, 2);
            if (isset($urlSplit[1])) {
                $urlSplit[1] = explode('/', $urlSplit[1]);
            }
            else {
                $urlSplit[1] = array();
            }

            $urlSplit[0] .= $suffix;
            $this->_urlData = $urlSplit;
        }

        return $this->_urlData;
    }

    /**
     * @return mixed
     */
    protected function _getCategoryPathInArray()
    {
        $urlData = $this->_getUrlData();
        return $urlData[1];
    }

    /**
     * @deprecated
     * @param $param
     * @return bool
     */
    protected function _getCategoryByParam($param)
    {
        return false;

        if (count($param) == 1 && !$this->_getRequest()->getQuery('cat')) {
            $productUrl = Mage::getModel('catalog/product_url');

            $cat = Mage::getModel('mageworx_seofriendlyln/catalog_category')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->loadByAttribute('url_key', $productUrl->formatUrlKey($param[0]));
            if (!$cat) {
                $name = str_replace('-', ' ', $productUrl->formatUrlKey($param[0]));
                $cat  = Mage::getModel('mageworx_seofriendlyln/catalog_category')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->loadByAttribute('name', $name);
            }

            if ($cat && $cat->getId() && !in_array($cat->getUrlKey(), $this->_getCategoryPathInArray())) {
                if (!$this->_isCategoryNameDuplicatesAttribute($cat)) {
                    return $cat;
                }
            }
        }

        return false;
    }

    /**
     * @param $param
     * @return bool
     */
    protected function _isHiddenAttribute($param)
    {
        return count($param) == 1;
    }

    /**
     * @param array $param
     * @return bool
     */
    protected function _isHiddenPriceAttribute($param)
    {
        $localParam = explode(Mage::helper('mageworx_seofriendlyln/config')->getAttributeValueDelimiter(), $param[0]);
        return count($localParam) == 2 && $localParam[0] === 'price';
    }

    /**
     * @param array $param
     */
    protected function _setHiddenAttributeToRequest($param)
    {
        $attr = Mage::helper('mageworx_seofriendlyln')->_getFilterableAttributes($this->_getCategoryId());
        foreach ($attr as $attrCode => $attrData) {
            if (isset($attrData['options'][$param[0]])) {
                $this->_getRequest()->setQuery($attrCode, $attrData['options'][$param[0]]);
                break;
            }
        }
    }

    /**
     * @param array $param
     */
    protected function _setPriceAttributeToRequest($param)
    {
        $priceParam = explode(Mage::helper('mageworx_seofriendlyln/config')->getAttributeValueDelimiter(), $param[0]);
        $this->_getRequest()->setQuery($priceParam[0], $priceParam[1]);
    }

    /**
     * @deprecated
     * @param $cat
     */
    protected function _setCategoryToRequest($cat)
    {
        $this->_getRequest()->setQuery('cat', $cat->getName());
    }

    /**
     * @return int|false
     */
    protected function _getCategoryId()
    {
        $catId   = false;
        $catPath = $this->_getUrlData();

        if ($catPath) {
            $catRewriteModel = $this->_getCategoryUrlRewrite($catPath[0]);

            if ($catRewriteModel) {

                /** @var MageWorx_SeoAll_Helper_Version $helperVersion */
                $helperVersion = Mage::helper('mageworx_seoall/version');

                if ($helperVersion->isEeRewriteActive()) {
                    $catId = $catRewriteModel->getValueId();
                } else {
                    $catId = $catRewriteModel->getCategoryId();
                }
            }
        }

        return $catId;
    }

    /**
     * @param $param
     */
    protected function _setNotHiddenAttributeToRequest($param)
    {
        $code  = $param[0];
        $value = $param[1];

        if ($code == 'price') {
            if (strpos($value, '-') !== false) {
                $multipliers = explode('-', $value);
                $priceFrom   = floatval($multipliers[0]);
                $priceTo     = ($multipliers[1] ? floatval($multipliers[1]) + 0.01 : $multipliers[1]);
                $value       = $priceFrom . '-' . $priceTo;
            }

            $this->_getRequest()->setQuery($code, $value);
        }

        $attr = Mage::helper('mageworx_seofriendlyln')->_getFilterableAttributes($this->_getCategoryId());
        if (isset($attr) && !empty($attr)) {
            $code = str_replace('-', '_', $code); // attrCode is only = [a-z0-9_]
            if (isset($attr[$code]) && isset($attr[$code]['options'][$value])) {
                $this->_getRequest()->setQuery($code, $attr[$code]['options'][$value]);
            }
        }
    }

    protected function _passLayerParamsToRequest($layerParams)
    {
        if (empty($layerParams)) {
            return;
        }

        $this->_getRequest()->setParam(self::SEO_PARAMS_IDENTIFIER, $layerParams);
        return $this;
    }

    protected function _setRequest($request)
    {
        $this->_request = $request;
    }

    protected function _getRequest()
    {
        return $this->_request;
    }

    /**
     * @param $suffix
     * @return string
     */
    protected function _getIdentifier($suffix)
    {
        if ($suffix && substr($this->_getRequest()->getPathInfo(), - (strlen($suffix))) == $suffix) {
            $identifier = substr($this->_getRequest()->getPathInfo(), 0, -(strlen($suffix)));
        } else {
            $identifier = trim($this->_getRequest()->getPathInfo(), '/');
        }

        return trim($identifier, '/');
    }
}