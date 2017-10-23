<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Scheckout
 */


class Amasty_Scheckout_Helper_RegionBase extends Mage_Directory_Helper_Data
{
    protected $_app     = null;

    //protected $_factory = null;

    protected $_countryCollection;

    protected $_reflection;

    public function __construct(array $args = array())
    {
        $this->_reflection = new ReflectionClass('Mage_Directory_Helper_Data');

        if ($this->_reflection->getConstructor()) {
            parent::__construct($args);
        } else {
            $this->_app = !empty($args['app']) ? $args['app'] : Mage::app();
        }
    }

    public function getCountryCollection()
    {
        if ($this->_reflection->hasProperty('_factory')) {
            return parent::getCountryCollection();
        }

        if (!$this->_countryCollection) {
            $this->_countryCollection = Mage::getModel('directory/country')->getResourceCollection();
        }
        return $this->_countryCollection;
    }

    protected function _getRegions($storeId)
    {
        if ($this->_reflection->hasMethod('_getRegions')) {
            return parent::_getRegions($storeId);
        }

        $countryIds = array();

        $countryCollection = $this->getCountryCollection()->loadByStore($storeId);
        foreach ($countryCollection as $country) {
            $countryIds[] = $country->getCountryId();
        }

        /** @var $regionModel Mage_Directory_Model_Region */
        $regionModel = Mage::getModel('directory/region');
        /** @var $collection Mage_Directory_Model_Resource_Region_Collection */
        $collection = $regionModel->getResourceCollection()
            ->addCountryFilter($countryIds)
            ->load();

        $regions = array(
            'config' => array(
                'show_all_regions' => $this->getShowNonRequiredState(),
                'regions_required' => $this->getCountriesWithStatesRequired()
            )
        );
        foreach ($collection as $region) {
            if (!$region->getRegionId()) {
                continue;
            }
            $regions[$region->getCountryId()][$region->getRegionId()] = array(
                'code' => $region->getCode(),
                'name' => $this->__($region->getName())
            );
        }
        return $regions;
    }

    protected function _getHelper($helperClass)
    {
       if ($this->_reflection->hasProperty('_factory')) {
           return $this->_factory->getHelper($helperClass);
       }
       return Mage::helper($helperClass);
    }
}