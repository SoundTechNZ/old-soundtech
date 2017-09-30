<?php
/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

$installer = $this;
$installer->startSetup();

///// Template Attribute

if(!$installer->getConnection()->isTableExists($installer->getTable('mageworx_seoxtemplates/template_categoryFilter'))){
    $table = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_seoxtemplates/template_categoryFilter'))
        ->addColumn(
            'template_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'Template ID'
        )

        ->addColumn(
            'attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Category Attribute ID'
        )

        ->addColumn(
            'type_id', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Template Type'
        )

        ->addColumn(
            'name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable'  => false,
            ), 'Template Name'
        )

        ->addColumn(
            'store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Store ID'
        )

        ->addColumn(
            'code', Varien_Db_Ddl_Table::TYPE_VARCHAR, '64k', array(
            'nullable'  => false,
            ), 'Template Code'
        )

        ->addColumn(
            'assign_type', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Assign Type'
        )

        ->addColumn(
            'priority', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Priority'
        )

        ->addColumn(
            'date_modified', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            'nullable'  => true,
            ), 'Last Modify Date'
        )

        ->addColumn(
            'date_apply_start', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            'nullable'  => true,
            ), 'Last Apply Start Date'
        )

        ->addColumn(
            'date_apply_finish', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            'nullable'  => true,
            ), 'Last Apply Finish Date'
        )

        ->addColumn(
            'write_for', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'  => 1,
            ), 'Write for'
        )

        ->addColumn(
            'use_cron', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'  => 2,
            ), 'Use Cron'
        )

        ->addForeignKey(
            $installer->getFkName(
                'mageworx_seoxtemplates/template_categoryFilter',
                'store_id',
                'core/store',
                'store_id'
            ),
            'store_id', $installer->getTable('core/store'), 'store_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->addForeignKey(
            $installer->getFkName(
                'mageworx_seoxtemplates/template_categoryFilter',
                'attribute_id',
                'eav/attribute',
                'attribute_id'
            ),
            'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->setComment('Template Category Attribute Table created by MageWorx SeoXTemplates extension');

    $installer->getConnection()->createTable($table);
}

if(!$installer->getConnection()->isTableExists($installer->getTable('mageworx_seoxtemplates/template_relation_categoryFilter'))){
    $table = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_seoxtemplates/template_relation_categoryFilter'))
        ->addColumn(
            'id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'ID'
        )

        ->addColumn(
            'template_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Template ID'
        )

        ->addColumn(
            'category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Category ID'
        )

        ->addForeignKey(
            $installer->getFkName(
                'mageworx_seoxtemplates/template_relation_categoryFilter',
                'template_id',
                'mageworx_seoxtemplates/template_categoryFilter',
                'template_id'
            ),
            'template_id', $installer->getTable('mageworx_seoxtemplates/template_categoryFilter'), 'template_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
        )

        ->addForeignKey(
            $installer->getFkName(
                'mageworx_seoxtemplates/template_relation_categoryFilter',
                'category_id',
                'catalog/category',
                'entity_id'
            ),
            'category_id', $installer->getTable('catalog/category'), 'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->setComment('Table of relation between Category Attribute Template and Category created by MageWorx SeoXTemplates extension');

    $installer->getConnection()->createTable($table);
}

$installer->endSetup();