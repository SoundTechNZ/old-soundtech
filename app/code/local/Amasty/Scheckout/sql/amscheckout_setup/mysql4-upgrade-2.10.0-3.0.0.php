<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Scheckout
 */

/** @var  Mage_Core_Model_Resource_Setup $this */
$installer = $this;
$installer->startSetup();

$connection = $installer->getConnection();

$tableName = $installer->getTable('amscheckout/field');

$connection->delete($tableName, 'field_key = "billing:email"');

$installer->endSetup();