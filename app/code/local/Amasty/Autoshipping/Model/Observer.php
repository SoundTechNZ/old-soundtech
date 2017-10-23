<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Autoshipping
 */
class Amasty_Autoshipping_Model_Observer
{
    public function handleCollect($observer)
    {
        if (Mage::getStoreConfig('amautoshipping/general/enable')
            && $this->_needAutoshipping()
        )
        {
            $quote = $observer->getEvent()->getQuote();
            $shippingAddress = $quote->getShippingAddress();
            $notAutoFields = explode(',', Mage::getStoreConfig('amautoshipping/general/not_auto_fill'));
            //for compatibility with region select and region text field
            if (in_array('region', $notAutoFields)) {
                $notAutoFields[] = 'region_id';
            }

            if (!$shippingAddress->getCountryId()
                || $this->_isClassFunction('Amasty_Scheckout_Model_Cart', '_initShipping')) {
                $customerShippingAddress = Mage::getSingleton('customer/session')->getCustomer()->getDefaultShippingAddress();
                if ($customerShippingAddress) {
                    $settings['country_id'] = $customerShippingAddress->getCountryId();
                    $settings['region'] = $customerShippingAddress->getRegion();
                    $settings['region_id'] = $customerShippingAddress->getRegionId();
                    $settings['postcode'] = $customerShippingAddress->getPostcode();
                    $settings['city'] = $customerShippingAddress->getCity();
                } else {
                    $settings = Mage::getStoreConfig('amautoshipping/address');
                }

                foreach ($settings as $k => $v) {
                    if (!in_array($k, $notAutoFields)) {
                        $shippingAddress->setData($k, $v);
                    }
                }
                if (Mage::helper('core')->isModuleEnabled('Amasty_Geoip')
                    && (Mage::getStoreConfig('amautoshipping/geoip/use') == 1)
                    && !$shippingAddress->getShippingMethod()
                    && !$customerShippingAddress
                ) {
                    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                    } else {
                        $ip = $_SERVER['REMOTE_ADDR'];
                    }
                    $geoIpModel = Mage::getModel('amgeoip/geolocation');
//                $ip = '72.229.28.185';//NY
//                $ip = '50.46.132.0';//Lynnwood

                    $location = $geoIpModel->locate($ip);
                    $country = $location->getCountry();
                    if (!empty($country)) {
                        if ($country != $shippingAddress['county_id']) {
                            foreach ($settings as $k => $v) {
                                $shippingAddress->setData($k, '');
                            }
                        }
                        $shippingAddress->setCountryId($country);

                        if (!in_array('city', $notAutoFields)) {
                            $city = $location->getCity();
                            if (!empty($city)) {
                                $shippingAddress->setCity($city);
                            }
                        }

                        if (!in_array('region', $notAutoFields)) {
                            $region = $location->getRegion();
                            if (!empty($region)) {
                                $shippingAddress->setRegion($region);
                            }
                        }

                        if (!in_array('postcode', $notAutoFields)) {
                            $postcode = $location->getPostalCode();
                            if (!empty($postcode)) {
                                $shippingAddress->setPostcode($postcode);
                            }
                        }
                    }
                }
            }

            if (!$shippingAddress->getCountryId()) {
                $defaultCountryId = Mage::getStoreConfig('general/country/default');
                $shippingAddress->setCountryId($defaultCountryId);
            }

            $isNoAutoMethod = !Mage::getStoreConfig('amautoshipping/general/shipping_method')
                && Mage::getStoreConfig('amautoshipping/general/select_method') == 'not_autoselect'
                && Mage::getStoreConfig('amautoshipping/address/country_id') === ''
            ;

            if (!$isNoAutoMethod) {
                $this->_addShippingMethod($quote);
            }
        }

        return $this;
    }

    public function beforeSaveSettings($observer)
    {
        $countryId = Mage::getStoreConfig('amautoshipping/address/country_id');
        if ($countryId) {
            $regionName = Mage::getStoreConfig('amautoshipping/address/region');
            $collectionRegions = Mage::getModel('directory/region_api')->items($countryId);
            if (is_array($collectionRegions) && !empty($collectionRegions)) {
                foreach ($collectionRegions as $region) {
                    if ($regionName == $region['name']) {
                        $regionId = $region['region_id'];
                        break;
                    }
                }
                if (isset($regionId)) {
                    Mage::getModel('core/config')->saveConfig('amautoshipping/address/region_id', $regionId);
                }
            }
        }

    }

    protected function _addShippingMethod($quote)
    {
        if ($this->_isClassFunction('Mage_Customer_Model_Customer', 'authenticate')) {
            return;
        }
        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->collectTotals();
        if (!$shippingAddress->getShippingMethod()) {
            $allShippingRates = $shippingAddress->getAllShippingRates();
            $method = $this->_applyIfOneMethod($allShippingRates);
            if (!$method) {
                $method = Mage::getStoreConfig('amautoshipping/general/shipping_method');
            }
            $shippingAddress->setShippingMethod($method)
                ->setCollectShippingRates(true)
            ;

            $shippingAddress->collectTotals();

            if (!$shippingAddress->getShippingMethod()) {
                $method = Mage::getModel('amautoshipping/selectMethods')->applyAutoShipping($allShippingRates);
            }

            $shippingAddress
                ->setShippingMethod($method)
            ;
            $shippingAddress->save();
            $quote->save();
        }
    }

    protected function _isClassFunction($class, $function)
    {
        $isClassFunction = false;
        $backtrace = debug_backtrace();
        foreach ($backtrace as $step) {
            if (isset($step['object'])
                && is_a($step['object'], $class)
                && isset($step['function'])
                && $step['function'] == $function) {
                $isClassFunction = true;
                break;
            }
        }
        $backtrace = NULL;
        return $isClassFunction;
    }

    protected function _needAutoshipping()
    {
        $needAutoshipping = false;
        $backtrace = debug_backtrace();
        foreach ($backtrace as $step) {
            if ((isset($step['object']) && isset($step['function']))) {
                if ($step['object'] instanceof Mage_Checkout_CartController && ($step['function'] == 'indexAction' || $step['function'] == 'addAction')) {
                    $needAutoshipping = true;
                    break;
                }
                if (Mage::helper('core')->isModuleEnabled('Amasty_Scheckout')) {
                    if ($step['object'] instanceof Mage_Checkout_OnepageController &&
                        ($step['function'] == 'updateAction'
                            || $step['function'] == 'initAmscheckout'
                            || $step['function'] == 'indexAction'
                            || $step['function'] == 'cartAction'
                        )) {
                        $needAutoshipping = true;
                        break;
                    }
                }
                if (Mage::helper('core')->isModuleEnabled('Amasty_Cart')) {
                    if ($step['object'] instanceof Amasty_Cart_AjaxController && $step['function'] == 'indexAction') {
                        $needAutoshipping = true;
                        break;
                    }
                }
            }
        }
        $backtrace = NULL;

        return $needAutoshipping;
    }

    protected function _applyIfOneMethod($allShippingRates)
    {
        $method = '';

        if (count($allShippingRates) == 1) {
            $method = $allShippingRates[0]->getCode();
        }

        return $method;
    }
}
