<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Scheckout
 */


class Amasty_Scheckout_Helper_Region extends Amasty_Scheckout_Helper_RegionBase
{
    public function getRequiredRegionJson($isRegionRequired)
    {
        return $this->getRequiredRegionJsonByStore($isRegionRequired);
    }

    public function getRequiredRegionJsonByStore($isRegionRequired, $storeId = null)
    {
        Varien_Profiler::start('TEST: '.__METHOD__);
        if (!$this->_regionJson) {
            $store = $this->_app->getStore($storeId);
            $cacheKey = 'DIRECTORY_REGIONS_JSON_STORE' . (string)$store->getId();
            if ($this->_app->useCache('config')) {
                $json = $this->_app->loadCache($cacheKey);
            }
            if (empty($json)) {
                $regions = $this->_getRequiredRegions($storeId, $isRegionRequired);
                $helper = $this->_getHelper('core');
                $json = $helper->jsonEncode($regions);

                if ($this->_app->useCache('config')) {
                    $this->_app->saveCache($json, $cacheKey, array('config'));
                }
            }
            $this->_regionJson = $json;
        }

        Varien_Profiler::stop('TEST: ' . __METHOD__);
        return $this->_regionJson;
    }

    protected function _getRequiredRegions($storeId, $isRegionRequired)
    {
        $regions = parent::_getRegions($storeId);
        if ($isRegionRequired) {
            $allCountries = Mage::getModel('directory/country')->getCollection()->getData();
            foreach ($allCountries as $key => $country) {
                $allCountries[$key] = $allCountries[$key]['country_id'];
            }
            $regions['config']['regions_required'] = $allCountries;
        }

        return $regions;
    }
}
