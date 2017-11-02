<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Scheckout
 */


class Amasty_Scheckout_Block_Customer_Address_Amform extends Mage_Core_Block_Template
{

    const AM_BONTH_AREA     = 0;

    const AM_BILLING_AREA   = 1;

    const AM_SHIPPING_AREA  = 2;

    protected $_areaCFPart  = 'billing';

    protected $_addressData = array();

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('amasty/amscheckout/customer/address/amform.phtml');
    }

    public function setAddressData($addressData = array())
    {
        $this->_addressData = $addressData;
        return $this;
    }

    public function getAddressData()
    {
        return $this->_addressData;
    }

    public function setCFArea($area = 'billing')
    {
        $this->_areaCFPart = $area;
        return $this;
    }

    public function getCFArea()
    {
        return $this->_areaCFPart;
    }

    public function getCFAreaId()
    {
        $areaId = ($this->_areaCFPart == 'billing') ? self::AM_BILLING_AREA : (($this->_areaCFPart == 'shipping') ? self::AM_SHIPPING_AREA : self::AM_BONTH_AREA);
        return $areaId;
    }

    public function getFields()
    {
        $collection = $this->_getFieldCollection();
        foreach ($collection as $item) {
            $stFieldLabel = $item->getData('st_field_label');
            if (!is_null($stFieldLabel)) {
                $item->setData('field_label', $stFieldLabel);
            }
        }
        return $collection;
    }

    public function getDefaultValueField($key)
    {
        $keyWOArea  = $this->helper('amscheckout/customfield')->getPartCustomFieldsName($key, $this->getCFArea(), 'className');
        $defValue   = (isset($this->_addressData[$keyWOArea])) ? $this->_addressData[$keyWOArea] : '';
        return $defValue;
    }

    public function getAreaFieldsLabel()
    {
        $areaId = $this->getCFAreaId();
        return ($areaId == self::AM_SHIPPING_AREA) ? $this->__('Shipping') : $this->__('Billing');
    }

    protected function _getFieldCollection()
    {
        $area       = $this->getCFArea();
        $areaId     = $this->getCFAreaId();
        $storeId    = Mage::app()->getStore()->getStoreId();
        $collection = Mage::getModel('amscheckout/field')->getCollection();
        $collection->addFieldToFilter('area_id', $areaId);
        $collection->addFieldToFilter('field_key', array('like' => $area . ':cf_' . $area . '%'));
        $collection->byStore($storeId);
        return $collection;
    }

}