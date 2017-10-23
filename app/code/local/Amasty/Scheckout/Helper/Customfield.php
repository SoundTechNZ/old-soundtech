<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Scheckout
 */


class Amasty_Scheckout_Helper_Customfield extends Amasty_Scheckout_Helper_Data
{
    protected  $_fields = array();

    public function getCustomField($field, $area = 'billing', $addressObj = null)
    {
        $html = '';
        $key = $field['field_key'];
        $label = $field['field_label'];
        $disabled = $field['field_disabled'];
        $position = $field['column_position'];
        $required = $field['field_required'];

        if ($this->_checkCustomFieldKey($key, $area) && !$disabled) {
            $html = $this->getBeforeControlHtml($field);
            $fieldName = $this->getPartCustomFieldsName($key, $area, 'name');
            $fieldClassName = $this->getPartCustomFieldsName($key, $area, 'className');
            $defValue = (!is_null($addressObj) && $addressObj->getData($fieldClassName)) ? $addressObj->getData($fieldClassName) : '';
            $html .= '<input id="' . $key . '" type="text" name="' . $fieldName . '" value="' . $defValue . '" class="input-text ' . $this->getAttributeValidationClass($fieldClassName, $required) . '" title="' . Mage::helper('core')->escapeHtml($label) . '"/>';
            $html .= $this->getAfterControlHtml($field);
        }
        return $html;
    }

    public function getPartCustomFieldsName($key, $area = 'billing', $partType = 'area')
    {
        $partName = '';
        $keyChunks = ($partType != 'area') ? $this->_getKeyChunks($key, $area) : $this->_getKeyChunks($key, $area, true);
        switch ($partType) {
            case 'name':
                $partName = (!empty($keyChunks)) ? $area . '[' . $keyChunks[1] . ']' : '';
                break;
            case 'className':
                $partName = (!empty($keyChunks)) ? $keyChunks[1] : '';
                break;
            case 'area':
                $partName = (!empty($keyChunks)) ? $keyChunks[0] : '';
                break;
        }
        return $partName;
    }

    public function getEnabledCustomFields()
    {
        $enabledCustomFields = array();
        $fieldsCollection = Mage::getModel('amscheckout/field')->getCollection();
        foreach ($fieldsCollection as $field) {
            $area = $this->getPartCustomFieldsName($field->getFieldKey());
            if ($this->_checkCustomFieldKey($field->getFieldKey(), $area) && !$field->getFieldDisabled()) {
                $enabledCustomFields[] = $this->getPartCustomFieldsName($field->getFieldKey(), $area, 'className');
            }
        }
        return $enabledCustomFields;
    }

    public function getDisabledCustomFields($area = 'billing')
    {
        $disabledCustomFields = array();
        $fields = $this->_getFieldsByStore();
        foreach ($fields as $field) {
            if ($this->_checkCustomFieldKey($field['field_key'], $area) && $field['field_disabled']) {
                $disabledCustomFields[] = $this->getPartCustomFieldsName($field['field_key'], $area, 'className');
            }
        }
        return $disabledCustomFields;
    }

    public function getSeparate($type)
    {
        return ($type == 'html') ? '<br/>' : (($type == 'pdf') ? '|' : '');
    }

    public function setValueForCustomField($data = array())
    {
        $preparedBillingData = $this->_prepareCustomFields($data, 'billing');
        $preparedShipingData = $this->_prepareCustomFields($data, 'shipping');
        $preparedData = array_merge($preparedBillingData, $preparedShipingData);
        return $preparedData;
    }

    public function isCustomField($key, $area = 'billing')
    {
        return (bool)$this->_checkCustomFieldKey($key, $area);
    }

    public function isFieldEnabled($key, $area = 'billing')
    {
        $isEnabled = false;
        $area    = (!is_null($area)) ? $area : $this->_getAreaFromKey($key);
        $fieldItems = $this->_getFieldsByStore();
        foreach ($fieldItems as $field) {
            if ($this->_checkCustomFieldKey($field['field_key'], $area) && !$field['field_disabled']
                && $this->getPartCustomFieldsName($field['field_key'], $area, 'className') == $key
            ) {
                $isEnabled = true;
                break;
            }
        }
        return $isEnabled;
    }

    public function prepareDisabledCustomFields($origArea = 'all')
    {
        $disCustomFields = array();
        if ($origArea == 'billing' || $origArea == 'shipping') {
            $disArea = ($origArea == 'billing') ? 'shipping' : 'billing';
            $disCustomFields = $this->getDisabledCustomFields($origArea);
            $disCustomFieldsFromArea = $this->_getCustomFieldKeysWithoutNS($disArea);
            $disCustomFields = array_merge($disCustomFields, $disCustomFieldsFromArea);
        } else if ($origArea == 'all') {
            $disBillingCustomFields  = $this->getDisabledCustomFields('billing');
            $disShippingCustomFields = $this->getDisabledCustomFields('shipping');
            $disCustomFields = array_merge($disBillingCustomFields, $disShippingCustomFields);
        }
        return $disCustomFields;
    }

    public function prepareFormat($data = array(), $area = 'billing', $separate = '<br/>')
    {
        $preparedFormat = '';
        $preparedData = $this->_prepareCustomFields($data, $area);
        if (!empty($preparedData)) {
            foreach ($preparedData as $key => $item) {
                if ($this->isFieldEnabled($key, $area)) {
                    $area = (!is_null($area)) ? $area : $this->_getAreaFromKey($key);
                    $preparedFormat .= '{{depend ' . $key . '}}';
                    $preparedFormat .= '{{if ' . $key . '}}';
                    $preparedFormat .= $separate;
                    $preparedFormat .= $this->_getLabelByKey($key, $area) . ': {{var ' . $key . '}}';
                    $preparedFormat .= '{{/if}}';
                    $preparedFormat .= '{{/depend}}';
                }
            }
        }
        return $preparedFormat;
    }

    public function clearPreviousChanged($format = '', $data = array(), $typeFormat = 'html')
    {
        if ($typeFormat == 'html' || $typeFormat == 'pdf') {
            $separate = $this->getSeparate($typeFormat);
            $prevPreparedBillingHtml = $this->prepareFormat($data, 'billing', $separate);
            $format = $this->_removePrevCustomFieldValues($format, $prevPreparedBillingHtml);
            $separate = $this->getSeparate($typeFormat);
            $prevPreparedShippingHtml = $this->prepareFormat($data, 'shipping', $separate);
            $format = $this->_removePrevCustomFieldValues($format, $prevPreparedShippingHtml);
        }
        return $format;
    }

    protected function _getLabelByKey($key, $area = 'billing')
    {
        $labelByKey = '';
        $fieldItems = $this->_getFieldsByStore();
        foreach ($fieldItems as $item) {
            if ($this->_checkCustomFieldKey($item['field_key'], $area) && !$item['field_disabled']
                && $this->getPartCustomFieldsName($item['field_key'], $area, 'className') == $key
            ) {
                $labelByKey = (!is_null($item['st_field_label'])) ? $item['st_field_label'] : $item['field_label'];
                break;
            }
        }
        return $labelByKey;
    }

    protected function _getKeyChunks($key, $area = 'billing', $withoutCheck = false)
    {
        $keyChunks = array();
        if ($this->_checkCustomFieldKey($key, $area) || $withoutCheck) {
            $keyChunks = explode(':', $key);
        }
        return $keyChunks;
    }

    protected function _getAreaFromKey($key)
    {
        $area   = '';
        $pattern = '/cf_(shipping|billing)\d+/';
        if (preg_match($pattern, $key, $matches)) {
            $area  = (!empty($matches) && isset($matches[1])) ? $matches[1] : '';
        }
        return $area;
    }

    protected function _getCustomFieldKeysWithoutNS($area = 'billing')
    {
        $cfKeys  = array();
        $fields = $this->_getFieldsByStore();
        foreach ($fields as $field) {
            if ($this->_checkCustomFieldKey($field['field_key'], $area)) {
                $cfKeys[] = $this->getPartCustomFieldsName($field['field_key'], $area, 'className');
            }
        }
        return $cfKeys;
    }

    protected function _getFieldsByStore($storeId = null)
    {
        if (!$storeId) {
            $storeId = Mage::app()->getStore()->getStoreId();
        }

        if ((!empty($this->_fields) && !isset($this->_fields[$storeId])) || empty($this->_fields)) {
            $this->_fields[$storeId] = Mage::getModel('amscheckout/field')->getCollection()->byStore($storeId)->getData();
        }
        return $this->_fields[$storeId];
    }

    protected function _checkCustomFieldKey($key, $area = 'billing')
    {
        $isCustomFieldKey = false;
        $keyRegex  = '/' . $area . ':cf_' . $area . '\d+/';
        if (preg_match($keyRegex, $key)) {
            $isCustomFieldKey = true;
        }
        return $isCustomFieldKey;
    }

    protected function _checkKeyWithoutNS($key , $area = 'billing')
    {
        $isTrueKey = false;
        $areaPart  = (!is_null($area)) ? $area : $this->_getAreaFromKey($key);
        $keyRegex = '/cf_' . $areaPart . '\d+/';
        if (preg_match($keyRegex, $key)) {
            $isTrueKey = true;
        }
        return $isTrueKey;
    }

    protected function _prepareCustomFields($data = array(), $area = 'billing')
    {
        $preparedData = array();
        $areaData = (isset($data[$area])) ? $data[$area] : $data;
        foreach ($areaData as $key => $value) {
            if ($this->_checkKeyWithoutNS($key, $area))
                $preparedData[$key] = $value;
        }
        return $preparedData;
    }

    protected function _removePrevCustomFieldValues($format = '', $removedStr = '')
    {
        $newFormat = $format;
        if ($removedStr && strpos($format, $removedStr) !== false) {
            $newFormat = str_replace($removedStr, '', $format);
        }
        return $newFormat;
    }
}