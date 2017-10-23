<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Scheckout
 */


$installer = $this;
$installer->startSetup();

$this->run("
    SET @defaultOrderUseShippingYes  = (SELECT `default_field_order` FROM `{$this->getTable('amscheckout/field')}` WHERE `field_key` = 'billing:use_for_shipping_yes') + 1000;
    SET @defaultOrderDontUseShipping = (SELECT `default_field_order` FROM `{$this->getTable('amscheckout/field')}` WHERE `field_key` = 'billing:use_for_shipping_no') + 1000;
    
    SET @defaultOrderDatebirth    = (SELECT `default_field_order` FROM `{$this->getTable('amscheckout/field')}` WHERE `field_key` = 'billing:datebirth') + 1000;
    SET @defaultOrderTaxVatNumber = (SELECT `default_field_order` FROM `{$this->getTable('amscheckout/field')}` WHERE `field_key` = 'billing:taxvat_number') + 1000;
    
    UPDATE `{$this->getTable('amscheckout/field')}` SET `default_field_order` = @defaultOrderDatebirth WHERE `field_key` = 'billing:use_for_shipping_yes';
    UPDATE `{$this->getTable('amscheckout/field')}` SET `default_field_order` = @defaultOrderTaxVatNumber WHERE `field_key` = 'billing:use_for_shipping_no';
    UPDATE `{$this->getTable('amscheckout/field')}` SET `field_order` = @defaultOrderDatebirth WHERE `field_key` = 'billing:use_for_shipping_yes';
    UPDATE `{$this->getTable('amscheckout/field')}` SET `field_order` = @defaultOrderTaxVatNumber WHERE `field_key` = 'billing:use_for_shipping_no';
    
    UPDATE `{$this->getTable('amscheckout/field')}` SET `default_field_order` = @defaultOrderUseShippingYes WHERE `field_key` = 'billing:datebirth';
    UPDATE `{$this->getTable('amscheckout/field')}` SET `default_field_order` = @defaultOrderDontUseShipping WHERE `field_key` = 'billing:taxvat_number';
    UPDATE `{$this->getTable('amscheckout/field')}` SET `field_order` = @defaultOrderUseShippingYes WHERE `field_key` = 'billing:datebirth';
    UPDATE `{$this->getTable('amscheckout/field')}` SET `field_order` = @defaultOrderDontUseShipping WHERE `field_key` = 'billing:taxvat_number';
");

$installer->endSetup();