<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoMarkup_Helper_Json_Event extends MageWorx_SeoMarkup_Helper_Abstract
{
    protected $_product;

    public function getJsonEventData($product)
    {
        if (!$this->_helperConfig->isEventRichsnippetEnabled()) {
            return false;
        }

        if (!in_array($product->getAttributeSetId(), $this->_helperConfig->getEventAttributeSets())) {
            return false;
        }

        $this->_product = $product;
        $data = array();
        $data['@context']    = 'http://schema.org';
        $data['@type']       = 'Event';
        $data['name']        = $product->getName();

        $startDate = $this->_helper->getEventStartDateValue($this->_product);
        if (!$startDate) {
            return false;
        }

        $data['startDate']   = $startDate;
        $data['description'] = $this->_helper->getDescriptionValue($this->_product);
        $data['image']       = $this->_helper->getProductImage($this->_product);
        $data['offers']      = $this->_getOfferData();

        if (!$data['offers']['price']) {
            unset($data['offers']);
        }

        $locationData = $this->_getLocationData();

        if (!$locationData) {
            return false;
        }

        $data['location'] = $locationData;

        if ($this->_helperConfig->isRatingEnabled()) {
            $aggregateRatingData = $this->_helper->getAggregateRatingData($this->_product, false);

            if ($this->_helper->isValidReviewData($aggregateRatingData)) {
                $data['aggregateRating'] = $aggregateRatingData;
            }
        }

        return $data;
    }

    protected function _getLocationData()
    {
        $name            = $this->_helperConfig->getEventLocationName();
        if (!$name) {
            return false;
        }

        $addressLocality = $this->_helper->getEventAddressLocalityValue($this->_product);
        if (!$addressLocality) {
            return false;
        }

        $addressStreet   = $this->_helper->getEventAddressStreetValue($this->_product);

        $data = array();
        $data['@type'] = 'Place';
        $data['name']  = $name;

        if ($addressLocality) {
            $address = array();
            $address["@type"]           = "PostalAddress";
            $address["addressLocality"] = $addressLocality;
            if ($addressStreet) {
                $address["addressStreet"] = $addressStreet;
            }

            $data['address'] = $address;
        }

        return $data;
    }

    protected function _getOfferData()
    {
        $data   = array();
        $prices = Mage::helper('mageworx_seomarkup/price')->getPricesByProductType($this->_product->getTypeId());

        if (is_array($prices) && count($prices)) {
            $data['price'] = $prices[0];
        }

        $data['priceCurrency'] = Mage::app()->getStore()->getCurrentCurrencyCode();

        $availability = $this->_helper->getAvailability($this->_product);
        if ($availability) {
            $data['availability'] = $availability;
        }

        $condition = $this->_helper->getConditionValue($this->_product);
        if ($condition) {
            $data['itemCondition'] = $condition;
        }

        $paymentMethods = $this->_helper->getPaymentMethods();
        if ($paymentMethods) {
            $data['acceptedPaymentMethod'] = $paymentMethods;
        }

        $deliveryMethods = $this->_helper->getDeliveryMethods();
        if ($deliveryMethods) {
            $data['availableDeliveryMethod'] = $deliveryMethods;
        }

        return $data;
    }
}
