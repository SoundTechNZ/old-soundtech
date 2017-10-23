<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Scheckout
 */
class Amasty_Scheckout_Block_Adminhtml_Settings_Tab_Fields extends
    Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    const FIELD_CHECKOUT = 0;
    const FIELD_ORDERATTR = 1;
    const FIELD_CUSTOMERATTR = 2;

    protected function _construct()
    {
        $this->setTemplate('amscheckout/fields.phtml');
    }

    public function getTabLabel()
    {
        return Mage::helper('amscheckout')->__('Fields Configuration');
    }

    public function getTabTitle()
    {
        return Mage::helper('amscheckout')->__('Fields Configuration');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    public function getAreas()
    {
        $storeId = $this->getData("store_id");
        return Mage::getModel("amscheckout/area")->getAreas($storeId);
    }

    public function getFields()
    {
        $storeId = $this->getData("store_id");
        return Mage::getModel("amscheckout/field")->getFields($storeId);
    }

    public function getExternalField($field = array())
    {
        $externalField = array();

        if ($this->isExternalField($field['field_db_key'])) {
            $attribute     = $this->_getAttributeByCode($field['field_db_key']);
            $externalField = (!is_null($attribute) && $attribute->getId()) ? $attribute->getData() : array();
            if ($this->getStoreId()) {
                $externalField['frontend_label'] = $attribute->getStoreLabel($this->getStoreId());
            }
        }

        return $externalField;
    }

    public function isDraggableField($fieldKey = false)
    {
        $notDragFields = array('billing:use_for_shipping_no', 'billing:use_for_shipping_yes');

        if ($fieldKey) {
            if (in_array($fieldKey, $notDragFields) || strstr($fieldKey, 'oa_')) {
                return false;
            }
        }

        return true;
    }

    public function isExternalField($fieldKey = false)
    {
        $typeField = $this->getTypeExternalField($fieldKey);
        if ($typeField == self::FIELD_ORDERATTR || $typeField == self::FIELD_CUSTOMERATTR) {
            return true;
        }

        return false;
    }

    public function isRequiredExternalField($extField = array())
    {
        if ((isset($extField['is_required']) && $extField['is_required']) ||
            (isset($extField['required_on_front_only']) && $extField['required_on_front_only'])) {
            return true;
        }

        return false;
    }

    protected function getTypeExternalField($fieldKey = false)
    {
        $typeField = self::FIELD_CHECKOUT;

        if (!$fieldKey) {
            return $typeField;
        }

        if (strstr($fieldKey, 'oa_')) {
            $typeField = self::FIELD_ORDERATTR;
        } else if (strstr($fieldKey, 'ca_')) {
            $typeField = self::FIELD_CUSTOMERATTR;
        }

        return $typeField;
    }

    protected function _getAttributeByCode($code = '')
    {
        $attribute         = null;
        $eavEntityType     = $this->_getEavEntityTypeByCode($code);
        if ($eavEntityType) {
            $attributeCode = $this->_delExternalMarkFromCode($code);
            $attribute     = Mage::getModel('eav/entity_attribute')->loadByCode($eavEntityType, $attributeCode);
        }

        return $attribute;
    }

    protected function _getEavEntityTypeByCode($code = '')
    {
        $eavEntityType     = false;
        $typeExternalField = $this->getTypeExternalField($code);
        if ($typeExternalField == self::FIELD_ORDERATTR) {
            $eavEntityType = "order";
        } elseif ($typeExternalField == self::FIELD_CUSTOMERATTR) {
            $eavEntityType = "customer";
        }

        return $eavEntityType;
    }

    protected function _delExternalMarkFromCode($code = '')
    {
        $typeField      = $this->getTypeExternalField($code);
        if ($typeField == self::FIELD_ORDERATTR) {
            $code = str_replace('oa_', '', $code);
        } elseif ($typeField == self::FIELD_CUSTOMERATTR) {
            $code = str_replace('ca_', '', $code);
        }

        return $code;
    }
}