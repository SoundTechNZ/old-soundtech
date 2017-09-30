<?php
/**
 * MageWorx
 * MageWorx SeoReports Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoReports
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$installer->startSetup();

    if ($installer->getConnection()->isTableExists($installer->getTable('seosuite_report_product'))) {
        $installer->getConnection()->dropTable($this->getTable('seosuite_report_product'));
    }

    if ($installer->getConnection()->isTableExists($installer->getTable('mageworx_seoreports/product'))) {
        $installer->getConnection()->dropTable($this->getTable('mageworx_seoreports/product'));
    }

    $productReportTable = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_seoreports/product'))

        ->addColumn(
            'entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'Entity ID'
        )

        ->addColumn(
            'product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Product ID'
        )

        ->addColumn(
            'store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
            ), 'Store ID'
        )

        ->addColumn(
            'sku', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(
            'nullable'  => false,
            ), 'Product SKU'
        )

        ->addColumn(
            'url_path', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
            'nullable'  => false,
            ), 'Product URL Path'
        )

        ->addColumn(
            'type_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 32, array(
            'nullable'  => false,
            ), 'Product Type'
        )

        ->addColumn(
            'name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
            'nullable'  => false,
            ), 'Product Name'
        )

        ->addColumn(
            'prepared_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
            'nullable'  => false,
            ), 'Product Prepared Name'
        )

        ->addColumn(
            'name_dupl', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => 0,
            ), 'Product Name Duplicate Count'
        )

        ->addColumn(
            'meta_title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
            'nullable'  => false,
            ), 'Product Meta Title'
        )

        ->addColumn(
            'prepared_meta_title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
            'nullable'  => false,
            ), 'Product Prepared Meta Title'
        )

        ->addColumn(
            'meta_title_len', Varien_Db_Ddl_Table::TYPE_SMALLINT, 3, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Product Meta Title Lenght'
        )

        ->addColumn(
            'meta_title_dupl', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => 0,
            ), 'Product Meta Title Duplicate Count'
        )

        ->addColumn(
            'meta_descr_len', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Product Meta Description Length'
        )

        ->addColumn(
            'url', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
            'nullable'  => false,
            ), 'Product URL'
        )

        ->addColumn(
            'url_dupl', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => 0,
            ), 'Product URL Duplicate Count'
        )

        ->addIndex(
            $installer->getIdxName('mageworx_seoreports/product', 'prepared_name'),
            array(
                'prepared_name' => array('name' => 'prepared_name', 'size' => 8)
            )
        )

        ->addIndex(
            $installer->getIdxName('mageworx_seoreports/product', 'prepared_meta_title'),
            array(
                'prepared_meta_title' => array('name' => 'prepared_meta_title', 'size' => 8)
            )
        )

        ->addIndex(
            $installer->getIdxName('mageworx_seoreports/product', array('entity_id', 'product_id', 'store_id')),
            array('entity_id', 'product_id', 'store_id')
        )

        ->addForeignKey(
            $installer->getFkName(
                'mageworx_seoreports/product',
                'store_id',
                'core/store',
                'store_id'
            ),
            'store_id', $installer->getTable('core/store'), 'store_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
        )

        ->addForeignKey(
            $installer->getFkName(
                'mageworx_seoreports/product',
                'product_id',
                'catalog/product',
                'entity_id'
            ),
            'product_id', $installer->getTable('catalog/product'), 'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
        );

    $installer->getConnection()->createTable($productReportTable);

    if ($installer->getConnection()->isTableExists($installer->getTable('seosuite_report_category'))) {
        $installer->getConnection()->dropTable($this->getTable('seosuite_report_category'));
    }

    if ($installer->getConnection()->isTableExists($installer->getTable('mageworx_seoreports/category'))) {
        $installer->getConnection()->dropTable($this->getTable('mageworx_seoreports/category'));
    }

    $categoryReportTable = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_seoreports/category'))

        ->addColumn(
            'entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'Entity ID'
        )

        ->addColumn(
            'category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Category ID'
        )

        ->addColumn(
            'store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
            ), 'Store ID'
        )

        ->addColumn(
            'level', Varien_Db_Ddl_Table::TYPE_TINYINT, 10, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Level'
        )

        ->addColumn(
            'url_path', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
            'nullable'  => false,
            ), 'Category URL Path'
        )

        ->addColumn(
            'name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
            'nullable'  => false,
            ), 'Category Name'
        )

        ->addColumn(
            'prepared_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
            'nullable'  => false,
            ), 'Category Prepared Name'
        )

        ->addColumn(
            'name_dupl', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => 0,
            ), 'Category Name Duplicate Count'
        )

        ->addColumn(
            'meta_title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
            'nullable'  => false,
            ), 'Category Meta Title'
        )

        ->addColumn(
            'prepared_meta_title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
            'nullable'  => false,
            ), 'Category Prepared Meta Title'
        )

        ->addColumn(
            'meta_title_len', Varien_Db_Ddl_Table::TYPE_SMALLINT, 3, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Category Meta Title Lenght'
        )

        ->addColumn(
            'meta_title_dupl', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => 0,
            ), 'Category Meta Title Duplicate Count'
        )

        ->addColumn(
            'meta_descr_len', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Category Meta Description Length'
        )

        ->addColumn(
            'url', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
            'nullable'  => false,
            ), 'Category URL'
        )

        ->addColumn(
            'url_dupl', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => 0,
            ), 'Category URL Duplicate Count'
        )

        ->addIndex(
            $installer->getIdxName('mageworx_seoreports/category', 'prepared_name'),
            array(
                'prepared_name' => array('name' => 'prepared_name', 'size' => 8)
            )
        )

        ->addIndex(
            $installer->getIdxName('mageworx_seoreports/category', 'prepared_meta_title'),
            array(
                'prepared_meta_title' => array('name' => 'prepared_meta_title', 'size' => 8)
            )
        )

        ->addIndex(
            $installer->getIdxName('mageworx_seoreports/category', array('entity_id', 'category_id', 'store_id')),
            array('entity_id', 'category_id', 'store_id')
        )

        ->addForeignKey(
            $installer->getFkName(
                'mageworx_seoreports/category',
                'store_id',
                'core/store',
                'store_id'
            ),
            'store_id', $installer->getTable('core/store'), 'store_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
        )

        ->addForeignKey(
            $installer->getFkName(
                'mageworx_seoreports/category',
                'category_id',
                'catalog/category',
                'entity_id'
            ),
            'category_id', $installer->getTable('catalog/category'), 'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
        );

    $installer->getConnection()->createTable($categoryReportTable);

    if ($installer->getConnection()->isTableExists($installer->getTable('seosuite_report_cms'))) {
        $installer->getConnection()->dropTable($this->getTable('seosuite_report_cms'));
    }

    if ($installer->getConnection()->isTableExists($installer->getTable('mageworx_seoreports/cms'))) {
        $installer->getConnection()->dropTable($this->getTable('mageworx_seoreports/cms'));
    }

    $pageReportTable = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_seoreports/cms'))

        ->addColumn(
            'entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'Entity ID'
        )

        ->addColumn(
            'page_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, 6, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Page ID'
        )

        ->addColumn(
            'store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
            ), 'Store ID'
        )

        ->addColumn(
            'url_path', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
            'nullable'  => false,
            ), 'Page URL Path'
        )

        ->addColumn(
            'heading', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
            'nullable'  => false,
            ), 'Page Heading'
        )

        ->addColumn(
            'prepared_heading', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
            'nullable'  => false,
            ), 'Page Prepared Heading'
        )

        ->addColumn(
            'heading_dupl', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => 0,
            ), 'Page Heading Duplicate Count'
        )

        ->addColumn(
            'meta_title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
            'nullable'  => false,
            ), 'Page Meta Title'
        )

        ->addColumn(
            'prepared_meta_title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1024, array(
            'nullable'  => false,
            ), 'Page Prepared Meta Title'
        )

        ->addColumn(
            'meta_title_len', Varien_Db_Ddl_Table::TYPE_SMALLINT, 3, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Page Meta Title Lenght'
        )

        ->addColumn(
            'meta_title_dupl', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => 0,
            ), 'Page Meta Title Duplicate Count'
        )

        ->addColumn(
            'meta_descr_len', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Page Meta Description Length'
        )

        ->addIndex(
            $installer->getIdxName('mageworx_seoreports/cms', 'prepared_heading'),
            array(
                'prepared_heading' => array('name' => 'prepared_heading', 'size' => 8)
            )
        )

        ->addIndex(
            $installer->getIdxName('mageworx_seoreports/cms', 'prepared_meta_title'),
            array(
                'prepared_meta_title' => array('name' => 'prepared_meta_title', 'size' => 8)
            )
        )

        ->addIndex(
            $installer->getIdxName('mageworx_seoreports/cms', array('entity_id', 'page_id', 'store_id')),
            array('entity_id', 'page_id', 'store_id')
        )

        ->addForeignKey(
            $installer->getFkName(
                'mageworx_seoreports/cms',
                'store_id',
                'core/store',
                'store_id'
            ),
            'store_id', $installer->getTable('core/store'), 'store_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
        )

        ->addForeignKey(
            $installer->getFkName(
                'mageworx_seoreports/cms',
                'page_id',
                'cms/page',
                'page_id'
            ),
            'page_id', $installer->getTable('cms/page'), 'page_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
        );

    $installer->getConnection()->createTable($pageReportTable);

try {
     $pathFrom = 'mageworx_seo/seosuite/product_report_status';
     $pathTo   = 'mageworx_seo/seoreports/product_report_status';
     $collection = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter('path', $pathFrom);
     if ($collection->count() > 0) {
         foreach ($collection as $coreConfig) {
             $coreConfig->setPath($pathTo)->setValue(0)->save();
         }
     }
} catch (Exception $e) {
     Mage::log($e->getMessage(), Zend_Log::ERR);
}

 try {
     $pathFrom = 'mageworx_seo/seosuite/category_report_status';
     $pathTo   = 'mageworx_seo/seoreports/category_report_status';
     $collection = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter('path', $pathFrom);
     if ($collection->count() > 0) {
         foreach ($collection as $coreConfig) {
             $coreConfig->setPath($pathTo)->setValue(0)->save();
         }
     }
 } catch (Exception $e) {
     Mage::log($e->getMessage(), Zend_Log::ERR);
 }

 try {
     $pathFrom = 'mageworx_seo/seosuite/cms_report_status';
     $pathTo   = 'mageworx_seo/seoreports/cms_report_status';
     $collection = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter('path', $pathFrom);
     if ($collection->count() > 0) {
         foreach ($collection as $coreConfig) {
             $coreConfig->setPath($pathTo)->setValue(0)->save();
         }
     }
 } catch (Exception $e) {
     Mage::log($e->getMessage(), Zend_Log::ERR);
 }

$installer->endSetup();
