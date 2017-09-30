<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
$installer = $this;
$installer->startSetup();

if (!$installer->getConnection()->isTableExists($installer->getTable('mageworx_seoextended/category'))) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_seoextended/category'))
        ->addColumn(
            'id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'ID'
        )

        ->addColumn(
            'attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Attribute ID'
        )

        ->addColumn(
            'category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Store ID'
        )

        ->addColumn(
            'store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Store ID'
        )

        ->addColumn(
            'meta_title', Varien_Db_Ddl_Table::TYPE_VARCHAR, '64k', array(
            'nullable' => false,
            'default'  => '',
            ), 'Value'
        )

        ->addColumn(
            'meta_description', Varien_Db_Ddl_Table::TYPE_VARCHAR, '64k', array(
            'nullable'  => false,
            'default'  => '',
            ), 'Value'
        )

        ->addColumn(
            'meta_keywords', Varien_Db_Ddl_Table::TYPE_VARCHAR, '255', array(
            'nullable'  => false,
            'default'  => '',
            ), 'Value'
        )

        ->addColumn(
            'description', Varien_Db_Ddl_Table::TYPE_VARCHAR, '64k', array(
            'nullable'  => false,
            'default'  => '',
            ), 'Value'
        )

        ->addForeignKey(
            $installer->getFkName(
                'mageworx_seoextended/category',
                'attribute_id',
                'eav/attribute',
                'attribute_id'
            ),
            'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
        )

        ->addForeignKey(
            $installer->getFkName(
                'mageworx_seoextended/category',
                'store_id',
                'core/store',
                'store_id'
            ),
            'store_id', $installer->getTable('core/store'), 'store_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
        )

        ->addForeignKey(
            $installer->getFkName(
                'mageworx_seoextended/category',
                'category_id',
                'catalog/category',
                'entity_id'
            ),
            'category_id', $installer->getTable('catalog/category'), 'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
        )

        ->setComment('Table of additional attribute data for category created by MageWorx SeoExtended extension');

    $installer->getConnection()->createTable($table);
}

$installer->endSetup();