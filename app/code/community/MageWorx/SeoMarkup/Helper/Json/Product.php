<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoMarkup_Helper_Json_Product extends MageWorx_SeoMarkup_Helper_Abstract
{
    protected $_product;

    public function getJsonProductData($product)
    {
        if (!$this->_helperConfig->isProductRichsnippetEnabled()) {
            return false;
        }

        if (!$this->_helperConfig->isProductJsonLdMethod()) {
            return false;
        }

        $this->_product = $product;
        $data = array();
        $data['@context']    = 'http://schema.org';
        $data['@type']       = 'Product';
        $data['name']        = $product->getName();
        $data['description'] = $this->_helper->getDescriptionValue($this->_product);
        $data['image']       = $this->_helper->getProductImage($this->_product);
        $data['offers']      = $this->_getOfferData();

        if (!$data['offers']['price']) {
            return false;
        }

        if ($this->_helperConfig->isRatingEnabled()) {
            $aggregateRatingData = $this->_helper->getAggregateRatingData($this->_product, false);

            if ($this->_helper->isValidReviewData($aggregateRatingData)) {
                $data['aggregateRating'] = $aggregateRatingData;
            }
        }

        $color = $this->_helper->getColorValue($this->_product);
        if ($color) {
            $data['color'] = $color;
        }

        $brand = $this->_helper->getBrandValue($this->_product);
        if ($brand) {
            $data['brand'] = $brand;
        }

        $manufacturer = $this->_helper->getManufacturerValue($this->_product);
        if ($manufacturer) {
            $data['manufacturer'] = $manufacturer;
        }

        $model = $this->_helper->getModelValue($this->_product);
        if ($model) {
            $data['model'] = $model;
        }

        $gtin =  $this->_helper->getGtinData($this->_product);
        if (!empty($gtin['gtinType']) && !empty($gtin['gtinValue'])) {
            $data[$gtin['gtinType']] = $gtin['gtinValue'];
        }

        $skuValue = $this->_helper->getSkuValue($this->_product);
        if ($skuValue) {
            $data['sku'] = $skuValue;
        }

        $data['url'] = $this->_helper->getProductCanonicalUrl($this->_product);

        $heightValue = $this->_helper->getHeightValue($this->_product);
        if ($heightValue) {
            $data['height'] = $heightValue;
        }

        $widthValue = $this->_helper->getWidthValue($this->_product);
        if ($widthValue) {
            $data['width'] = $widthValue;
        }

        $depthValue = $this->_helper->getDepthValue($this->_product);
        if ($depthValue) {
            $data['depth'] = $depthValue;
        }

        $weightValue = $this->_helper->getWeightValue($this->_product);
        if ($weightValue) {
            $data['weight'] = $weightValue;
        }

        $categoryName = $this->_helper->getCategoryValue($this->_product);
        if ($categoryName) {
            $data['category'] = $categoryName;
        }

        $customProperties = $this->_helperConfig->getCustomProperties();
        if ($customProperties) {
            foreach ($customProperties as $propertyName => $propertyValue) {
                if ($propertyName && $propertyValue) {
                    $value = $this->_helper->getCustomPropertyValue($product, $propertyValue);
                    if ($value) {
                        $data[$propertyName] = $value;
                    }
                }
            }
        }

        return $data;
    }

    protected function _getOfferData()
    {
        $data   = array();
        $data['@type'] = MageWorx_SeoMarkup_Helper_Data::OFFER;

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
