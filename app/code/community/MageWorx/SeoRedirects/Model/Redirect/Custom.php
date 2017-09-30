<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoRedirects_Model_Redirect_Custom extends Mage_Core_Model_Abstract
{
    const CUSTOM_URL_ENTITY_TYPE_ID = 1;
    const PRODUCT_ENTITY_TYPE_ID    = 2;
    const CATEGORY_ENTITY_TYPE_ID   = 3;
    const CMS_PAGE_ENTITY_TYPE_ID   = 4;

    const PERMANENT_REDIRECT_TYPE_ID = 301;
    const TEMPORARY_REDIRECT_TYPE_ID = 302;

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('mageworx_seoredirects/redirect_custom');
        $this->setIdFieldName('custom_redirect_id');
    }

    /**
     * Implement logic of custom rewrites
     *
     */
    public function rewrite()
    {
        if (!Mage::isInstalled()) {
            return false;
        }
        $request = Mage::app()->getFrontController()->getRequest();

        $requestUrlRaw = $request->getPathInfo();
        $this->loadRewrite($requestUrlRaw);

        if ($this->getId() && $this->getStatus() != '0') {
            $this->makeRedirect();
        }
    }

    /**
     * @param $requestUrlRaw
     * @return $this
     */
    public function loadRewrite($requestUrlRaw){
        $requestCases = $this->_prepareRequestCases($requestUrlRaw);
        $requestPath = $this->_prepareRequestPath($requestUrlRaw);

        if (null === $this->getStoreId() || false === $this->getStoreId()) {
            $this->setStoreId(Mage::app()->getStore()->getId());
        }

        $this->loadByCustomRequestPath($requestCases);

        if (!$this->getId()) {
            $rewrite = Mage::getModel('core/url_rewrite')
                ->setStoreId($this->getStoreId())
                ->loadByRequestPath($requestCases);
            if($rewrite->getUrlRewriteId()){
                if($rewrite->getData('product_id')) {
                    $this->loadByEntity(self::PRODUCT_ENTITY_TYPE_ID, $rewrite->getData('product_id'));
                } elseif($rewrite->getData('category_id')) {
                    $this->loadByEntity(self::CATEGORY_ENTITY_TYPE_ID, $rewrite->getData('category_id'));
                }
            } elseif ($pos = strstr($requestPath, 'catalog/product/view/id/')) {
                $path = explode('/', $requestPath);
                $productId = $path[4];
                $this->loadByEntity(self::PRODUCT_ENTITY_TYPE_ID, $productId);
            } elseif (strstr($requestPath, 'catalog/category/view/')) {
                $path = explode('/', $requestPath);
                $categoryId = end($path);
                $this->loadByEntity(self::CATEGORY_ENTITY_TYPE_ID, $categoryId);
            } else {
                $cmsPageId = Mage::helper('mageworx_seoredirects')->getCmsIdByIdentifier($requestPath, $this->getStoreId());
                if($cmsPageId) {
                    $this->loadByEntity(self::CMS_PAGE_ENTITY_TYPE_ID, $cmsPageId);
                }
            }
        }
        return $this;
    }

    /**
     * Load rewrite by custom request path
     *
     * @param string $path
     * @return MageWorx_SeoRedirects_Model_Redirect_Custom
     */
    public function loadByCustomRequestPath($path)
    {
        $this->setId(null);
        $this->_getResource()->loadByCustomRequestPath($this, $path);
        return $this;
    }

    /**
     * Load rewrite by entity type and id
     *
     * @param int $entityTypeId
     * @param int $entityId
     * @return MageWorx_SeoRedirects_Model_Redirect_Custom
     */
    public function loadByEntity($entityTypeId, $entityId)
    {
        $this->setId(null);
        $this->_getResource()->loadByEntity($this, $entityTypeId, $entityId);
        return $this;
    }

    /**
     * Make redirect
     *
     */
    public function makeRedirect()
    {
        if ($this->getTargetEntityTypeId() && $this->getTargetEntityId()) {
            switch ($this->getTargetEntityTypeId()) {
                case self::CUSTOM_URL_ENTITY_TYPE_ID:
                    $url = $this->getTargetEntityId();
                    if(strpos($url, 'http://') === false
                        && (strpos($url, 'https://')) === false) {
                            $url = Mage::app()->getStore($this->getStoreId())
                                    ->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . $url;
                    }
                    break;
                case self::PRODUCT_ENTITY_TYPE_ID:
                    /*$url = Mage::getResourceSingleton('catalog/product')
                        ->getAttributeRawValue($this->getTargetEntityId(), 'url_path', $this->getStoreId());*/
                    $product = Mage::getModel('catalog/product')->setStore($this->getStoreId())->load($this->getTargetEntityId());
                    $url = $product->getProductUrl();
                    break;
                case self::CATEGORY_ENTITY_TYPE_ID:
                    /*$url = Mage::getResourceSingleton('catalog/category')
                        ->getAttributeRawValue($this->getTargetEntityId(), 'url_key', $this->getStoreId());*/
                    $category = Mage::getModel('catalog/category')->setStore($this->getStoreId())->load($this->getTargetEntityId());
                    $url = $category->getUrl();
                    break;
                case self::CMS_PAGE_ENTITY_TYPE_ID:
                    $url = Mage::helper('cms/page')->getPageUrl($this->getTargetEntityId());
                    break;
                default:
                    $url = '';
                    break;
            }
            if($url) {
                $response = Mage::app()->getResponse();
                $response->setRedirect($url, (int)$this->getRedirectType());
                $response->sendResponse();
                exit;
            }
        }
    }

    /**
     * Load rewrite by entity type and id
     *
     * @param int $entityTypeId
     * @param int $entityId
     * @return MageWorx_SeoRedirects_Model_Redirect_Custom
     */
    public function loadByTargetEntity($entityTypeId, $entityId)
    {
        $this->setId(null);
        $this->_getResource()->loadByTargetEntity($this, $entityTypeId, $entityId);
        return $this;
    }

    /**
     * @param $requestUrlRaw
     * @return array
     */
    protected function _prepareRequestCases($requestUrlRaw)
    {
        $requestCases = array();
        $requestPath = $this->_prepareRequestPath($requestUrlRaw);

        $origSlash = (substr($requestUrlRaw, -1) == '/') ? '/' : '';
        $altSlash = $origSlash ? '' : '/';

        $requestCases[] = $requestPath . $origSlash;
        $requestCases[] = $requestPath . $altSlash;
        $requestCases[] = Mage::app()->getStore($this->getStoreId())
                ->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . $requestPath . $origSlash;
        $requestCases[] = Mage::app()->getStore($this->getStoreId())
                ->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . $requestPath . $altSlash;
        if (Mage::getStoreConfigFlag('web/url/use_store')) {
            $requestCases[] = Mage::app()->getStore($this->getStoreId())->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK)
                . Mage::app()->getStore()->getCode() . $requestUrlRaw . $origSlash;
            $requestCases[] = Mage::app()->getStore($this->getStoreId())->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK)
                . Mage::app()->getStore()->getCode() . $requestUrlRaw . $altSlash;
        }
        return $requestCases;
    }

    protected function _prepareRequestPath($requestUrlRaw)
    {
        $stringHelper = Mage::helper('mageworx_seoall/string');
        $pathInfo = $stringHelper->cropFirstPart(
            $requestUrlRaw,
            array('/', 'index.php/', Mage::app()->getStore()->getCode(), '/'),
            true
        );
        return trim($pathInfo, '/');
    }
}