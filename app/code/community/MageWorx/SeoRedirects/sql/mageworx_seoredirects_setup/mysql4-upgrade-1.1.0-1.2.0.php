<?php
/**
 * MageWorx
 * MageWorx SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

$installer = $this;
$installer->startSetup();

if (!$installer->getConnection()->isTableExists($installer->getTable('mageworx_seoredirects/redirect_custom'))) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_seoredirects/redirect_custom'))
        ->addColumn(
            'custom_redirect_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary'  => true,
        ), 'Custom Redirect ID'
        )
        ->addColumn(
            'request_entity_type_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
        ), 'Request From(Type)'
        )
        ->addColumn(
            'target_entity_type_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
        ), 'Request To(Type)'
        )
        ->addColumn(
            'request_entity_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => false,
            ), 'Request From'
        )
        ->addColumn(
            'target_entity_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => false,
            ), 'Request To'
        )
        ->addColumn(
            'redirect_type', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '301',
        ), 'Redirect Type'
        )
        ->addColumn(
            'store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '0',
        ), 'Store Id'
        )
        ->addColumn(
            'date_created', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            'nullable' => false,
            'default' => '0000-00-00 00:00:00',
        ), 'Date Created'
        )
        ->addColumn(
            'date_modified', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            'nullable' => false,
            'default' => '0000-00-00 00:00:00',
        ), 'Date Modified'
        )
        ->addColumn(
            'status', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default' => 1,
        ), 'Status'
        );

    $installer->getConnection()->createTable($table);

    $installer->getConnection()->addKey(
        $installer->getTable('mageworx_seoredirects/redirect_custom'),
        'UNQ_request_entity_type_id_request_entity_id_store_id',
        array('request_entity_type_id', 'request_entity_id', 'store_id'),
        'unique'
    );
}

$installer->endSetup();
