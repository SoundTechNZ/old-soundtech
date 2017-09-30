<?php
/**
 * MageWorx
 * MageWorx SeoFriendlyLN Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoFriendlyLN
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoFriendlyLN_Helper_Config extends Mage_Core_Helper_Abstract
{
    const PAGER_VAR_NAME = 'p';
    const PAGER_NUM_PATTERN = '[page_number]';
    const DEFAULT_LN_SEPARATOR = 'l';
    const DEFAULT_ATTRIBUTE_VALUE_DELIMITER = ':';


    const XML_PATH_LN_FRIENDLY_URLS_ENABLED     = 'mageworx_seo/seofriendlyln/enable_ln_friendly_urls';
    const XML_PATH_LN_IDENTIFIER                = 'mageworx_seo/seofriendlyln/layered_identifier';
    const XML_PATH_LN_HIDE_ATTRIBUTE_NAMES      = 'mageworx_seo/seofriendlyln/layered_hide_attributes';
    const XML_PATH_LN_PAGER_URL_FORMAT          = 'mageworx_seo/seofriendlyln/pager_url_format';
    const XML_PATH_LATER_SEPARATOR              = 'mageworx_seo/seofriendlyln/layered_separatort';

    protected $_enterpriseSince113 = null;

    /**
     * @return bool
     */
    public function isLNFriendlyUrlsEnabled()
    {
        $fullActionName = Mage::helper('mageworx_seoall/request')->getCurrentFullActionName();

        if ($fullActionName && $fullActionName !== 'catalog_category_view') {
            return false;
        }

        return Mage::getStoreConfigFlag(self::XML_PATH_LN_FRIENDLY_URLS_ENABLED);
    }

    /**
     * @return string
     */
    public function getLayeredNavigationIdentifier()
    {
        $identifier = trim(Mage::getStoreConfig(self::XML_PATH_LN_IDENTIFIER));
        $identifier = strtolower(trim($identifier, '/'));
        if (preg_match('/^[a-z]+$/', $identifier)) {
            return $identifier;
        }

        return self::DEFAULT_LN_SEPARATOR;
    }

    /**
     * @return string
     */
    public function getAttributeValueDelimiter()
    {
        $delimeter = trim(Mage::getStoreConfig(self::XML_PATH_LATER_SEPARATOR));
        return $delimeter ? $delimeter : self::DEFAULT_ATTRIBUTE_VALUE_DELIMITER;
    }

    /**
     * @return string
     */
    public function getAttributeParamDelimiter()
    {
        return $this->getIsHideAttributeCodes() ? '/' : $this->getAttributeValueDelimiter();
    }

    /**
     * @return bool
     */
    public function getIsHideAttributeCodes()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_LN_HIDE_ATTRIBUTE_NAMES);
    }

    /**
     * @return bool|string
     */
    public function getPagerUrlFormat()
    {
        if ($this->isLNFriendlyUrlsEnabled()) {
            $pagerUrlFormat = trim(Mage::getStoreConfig(self::XML_PATH_LN_PAGER_URL_FORMAT));
            if (strpos($pagerUrlFormat, self::PAGER_NUM_PATTERN) !== false) {
                return $pagerUrlFormat;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getCategoryUrlSuffix()
    {
        $suffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');

        /** @var MageWorx_SeoAll_Helper_Version $helperVersion */
        $helperVersion = Mage::helper('mageworx_seoall/version');

        if ($helperVersion->isEeRewriteActive() && strlen($suffix) > 1 and strpos($suffix, '.') === false) {
            $suffix = '.' . $suffix;
        }

        return $suffix;
    }

}