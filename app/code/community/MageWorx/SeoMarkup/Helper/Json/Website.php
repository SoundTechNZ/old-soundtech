<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoMarkup_Helper_Json_Website extends MageWorx_SeoMarkup_Helper_Abstract
{

    public function getJsonWebSiteData()
    {
        if (!$this->_helperConfig->isWebsiteRichsnippetEnabled())
        {
            return false;
        }

        if ($this->_helperConfig->isWebsiteRichsnippetAddOnlyForMainPage()) {

            /** @var MageWorx_SeoAll_Helper_Request $helper */
            $helper = Mage::helper('mageworx_seoall/request');

            if ($helper->getCurrentFullActionName() !== 'cms_index_index') {
                return false;
            }
        }

        $data = array();
        $data['@context']  = 'http://schema.org';
        $data['@type']     = 'WebSite';
        $data['url']       = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

        $siteName = $this->_helperConfig->getWebsiteName();
        if ($siteName) {
            $data['name'] = $siteName;
        }

        $siteAbout = $this->_helperConfig->getWebsiteAboutInfo();
        if ($siteAbout) {
            $data['about'] = $siteAbout;
        }

        $potentialActionData = $this->_getPotentialActionData();
        if ($potentialActionData) {
            $data['potentialAction'] = $potentialActionData;
        }

        return $data;
    }

    protected function _getPotentialActionData()
    {
        if (!$this->_helper->isHomePage()) {
            return false;
        }

        if (!$this->_helperConfig->isAddWebsiteSearchAction()) {
            return false;
        }

        $storeBaseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

        $data = array();
        $data['@type']       = 'SearchAction';
        $data['target']      = $storeBaseUrl . 'catalogsearch/result/?q={search_term_string}';
        $data['query-input'] = 'required name=search_term_string';

        return $data;
    }
}
