<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Scheckout
 */


class Amasty_Scheckout_Model_Order extends Mage_Sales_Model_Order
{
    public function getCustomFields($area = 'billing')
    {
        $orderId = $this->getId();
        $csValues = $this->_prepareCustomFieldValues($orderId, $area);
        $enFields = $this->_excludeDisableField($csValues);
        return $enFields;
    }

    protected function _prepareCustomFieldLabel($area = 'billing')
    {
        $customFieldLabel = array();
        $fields = Mage::helper('amscheckout')->getFields($area);
        foreach ($fields as $field) {
            if ($field['field_db_key'] == $area.':first_cs_field' || $field['field_db_key'] == $area.':second_cs_field' || $field['field_db_key'] == $area.':third_cs_field') {
                $customFieldLabel[$field['field_db_key']]['label'] = $field['field_label'];
                $customFieldLabel[$field['field_db_key']]['field_disabled'] = $field['field_disabled'];
            }
        }
        return $customFieldLabel;
    }

    protected function _prepareCustomFieldValues($orderId, $area = 'billing')
    {
        $orderCsFieldModel = Mage::getModel('amscheckout/ordercustomfield')->loadByOrderId($orderId);
        $customFieldValues  = $this->_prepareCustomFieldLabel($area);
        $customFieldValues[$area.':first_cs_field']['value']  = $orderCsFieldModel->getData('first_' . $area . '_field');
        $customFieldValues[$area.':second_cs_field']['value'] = $orderCsFieldModel->getData('second_' . $area . '_field');
        $customFieldValues[$area.':third_cs_field']['value']  = $orderCsFieldModel->getData('third_' . $area . '_field');
        return $customFieldValues;
    }

    protected function _excludeDisableField($csFields)
    {
        $enCustomField = array();
        foreach ($csFields as $csField) {
            if (!(bool)$csField['field_disabled'])
                $enCustomField[] = $csField;
        }
        return $enCustomField;
    }
}