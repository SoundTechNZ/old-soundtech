<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoMarkup_Helper_Json_Seller extends MageWorx_SeoMarkup_Helper_Abstract
{

    public function getJsonOrganizationData()
    {
        if (!$this->_helperConfig->isSellerRichsnippetEnabled()) {
            return false;
        }

        $name = $this->_helperConfig->getSellerName();
        $image = $this->getSellerImageUrl();

        if (!$name || !$image) { // Name and Image are required fields
            return false;
        }

        $data = array();
        $data['@context']    = 'http://schema.org';
        $data['@type']       = $this->_helperConfig->getSellerType();

        if ($name) {
            $data['name'] = $name;
        }

        $description = $this->_helperConfig->getSellerDescription();
        if ($description) {
            $data['description'] = $description;
        }

        $phone = $this->_helperConfig->getSellerPhone();
        if ($phone) {
            $data['telephone'] = $phone;
        }

        $email = $this->_helperConfig->getSellerEmail();
        if ($email) {
            $data['email'] = $email;
        }

        $fax = $this->_helperConfig->getSellerFax();
        if ($fax) {
            $data['faxNumber'] = $fax;
        }

        $address = $this->_getAddress();
        if ($address && count($address) > 1) {
            $data['address'] = $address;
        }

        $socialLinks = $this->_helperConfig->getSameAsLinks();

        if(is_array($socialLinks) && !empty($socialLinks)){
            $data['sameAs'] = array();
            $data['sameAs'][] = $socialLinks;
        }

        $data['url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

        if ($image) {
            $data['image'] = $image;
        }

        $priceRange = $this->_helperConfig->getSellerPriceRange();
        if ($priceRange) {
            $data['priceRange'] =  $priceRange;
        }

        return $data;
    }

    protected function _getAddress()
    {
        $data = array();
        $data['@type']           = 'PostalAddress';
        $data['addressLocality'] = $this->_helperConfig->getSellerLocation();
        $data['addressRegion']   = $this->_helperConfig->getSellerRegionAddress();
        $data['streetAddress']   = $this->_helperConfig->getSellerStreetAddress();
        $data['postalCode']      = $this->_helperConfig->getSellerPostCode();
        return $data;
    }

    public function getSellerImageUrl()
    {
        $folderName = MageWorx_SeoMarkup_Model_System_Config_Backend_SellerImage::UPLOAD_DIR;
        $storeConfig = $this->_helperConfig->getSellerImage();
        $faviconFile = Mage::getBaseUrl('media') . $folderName . '/' . $storeConfig;
        $absolutePath = Mage::getBaseDir('media') . '/' . $folderName . '/' . $storeConfig;

        if(!is_null($storeConfig) &&  $this->_helper->isFile($absolutePath)) {
            return $faviconFile;
        }

        return false;
    }
}