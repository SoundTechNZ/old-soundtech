<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Scheckout
 */


$installer = $this;
$installer->startSetup();

$this->run("
    SET @billingAreaId  = (SELECT area_id FROM `{$this->getTable('amscheckout/area')}` WHERE area_key = 'billing');
    
    INSERT INTO `{$this->getTable('amscheckout/field')}` (`field_key`, `field_label`, `area_id`, `field_order`, `field_required`, `column_position`, `is_eav_attribute`, `field_disabled`) VALUES
    ('billing:cf_billing1', 'Custom field1', @billingAreaId, 2400, FALSE, 50, FALSE, TRUE),
    ('billing:cf_billing2', 'Custom field2', @billingAreaId, 2500, FALSE, 50, FALSE, TRUE),
    ('billing:cf_billing3', 'Custom field3', @billingAreaId, 2600, FALSE, 50, FALSE, TRUE);
    
    SET @shippingAreaId = (SELECT area_id FROM `{$this->getTable('amscheckout/area')}` WHERE area_key = 'shipping');
    
    INSERT INTO `{$this->getTable('amscheckout/field')}` (`field_key`, `field_label`, `area_id`, `field_order`, `field_required`, `column_position`, `is_eav_attribute`, `field_disabled`) VALUES
    ('shipping:cf_shipping1', 'Custom field1', @shippingAreaId, 1700, FALSE, 50, FALSE, TRUE),
    ('shipping:cf_shipping2', 'Custom field2', @shippingAreaId, 1800, FALSE, 50, FALSE, TRUE),
    ('shipping:cf_shipping3', 'Custom field3', @shippingAreaId, 1900, FALSE, 50, FALSE, TRUE);
    
    UPDATE `{$this->getTable('amscheckout/field')}` SET
    `default_field_label` = field_label,
    `default_field_order` = field_order,
    `default_field_required` = field_required,
    `default_column_position` = column_position
    WHERE field_key IN ('billing:cf_billing1', 'billing:cf_billing2', 'billing:cf_billing3', 
    'shipping:cf_shipping1', 'shipping:cf_shipping2', 'shipping:cf_shipping3');
   
");

$tableQuote = $this->getTable('sales/quote_address');
$tableOrder = $this->getTable('sales/order_address');
for ($i = 0; $i < 3; $i++) {
    $attrCode = 'cf_billing' . ($i + 1);
    $label    = 'Custom field' . ($i + 1);
    $this->addAttribute('customer_address', $attrCode, array(
        'type' => 'varchar',
        'label' => $label,
        'global' => 1,
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'visible_on_front' => 1
    ));

    Mage::getSingleton('eav/config')
        ->getAttribute('customer_address', $attrCode)
        ->setData('used_in_forms', array('customer_address_edit', 'adminhtml_customer_address'))
        ->save();

    $installer->run("
        ALTER TABLE  $tableQuote ADD  `$attrCode` varchar(255) NOT NULL
    ");

    $installer->run("
        ALTER TABLE  $tableOrder ADD  `$attrCode` varchar(255) NOT NULL
    ");

    $attrCode = 'cf_shipping' . ($i + 1);
    $label    = 'Custom field' . ($i + 1);
    $this->addAttribute('customer_address', $attrCode, array(
        'type' => 'varchar',
        'label' => $label,
        'global' => 1,
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'visible_on_front' => 1
    ));

    Mage::getSingleton('eav/config')
        ->getAttribute('customer_address', $attrCode)
        ->setData('used_in_forms', array('customer_address_edit', 'adminhtml_customer_address'))
        ->save();

    $installer->run("
        ALTER TABLE  $tableQuote ADD  `$attrCode` varchar(255) NOT NULL
    ");

    $installer->run("
        ALTER TABLE  $tableOrder ADD  `$attrCode` varchar(255) NOT NULL
    ");
}

$installer->endSetup();