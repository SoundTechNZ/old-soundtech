<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
abstract class MageWorx_SeoXTemplates_Model_Adapter_CategoryFilter extends MageWorx_SeoXTemplates_Model_Adapter
{
    /**
     * @param MageWorx_SeoXTemplates_Model_Template $template
     */
    protected function _apply($template)
    {
        if ($this->_testMode == 'csv') {
            $this->_applyCsv($template);
        } else {
            $this->_applyDb($template);
        }
    }

    /**
     * @param MageWorx_SeoXTemplates_Model_Template $template
     * @throws Exception
     */
    protected function _applyDb($template)
    {
        /** @var MageWorx_SeoExtended_Model_Catalog_Category $categoryFilter */
        foreach ($this->_collection as $categoryFilter) {

            /** @var Mage_Catalog_Model_Category $category */
            $category = Mage::getModel('catalog/category');
            $category->setStoreId($categoryFilter->getStoreId());
            $category->load($categoryFilter->getCategoryId());
            $category->setAttributeId($categoryFilter->getAttributeId());

            $attributeValue = $this->_converter->convert($category, $template->getCode());

            foreach ($this->_attributeCodes as $attributeCode)
            {
                if (trim($attributeValue) == '') {
                    continue;
                }

                $categoryFilter->setData($attributeCode, $attributeValue);
                $categoryFilter->save();
            }
        }
    }

    /**
     * @param MageWorx_SeoXTemplates_Model_Template $template
     * @throws Exception
     */
    protected function _applyCsv($template)
    {
        if ($this->_collection->count()) {

            /** @var MageWorx_SeoXTemplates_Helper_Data $helper */
            $helper = Mage::helper('mageworx_seoxtemplates');

            $path = $helper->getCsvFilePath();
            $name = $helper->getCsvFileName($template);
            $file = $helper->getCsvFile($path, $name);

            if(!file_exists($file)){
                $csvHeader = $this->_getHeaderData();
            }

            $io = new Varien_Io_File();
            $io->open(array('path' => $path));
            $io->streamOpen($file, 'a+');
            $io->streamLock(true);
            $io->setAllowCreateFolders(true);

            if(!empty($csvHeader)){
                $io->streamWriteCsv($csvHeader);
            }

            $categoryIds = $this->_collection->getColumnValues('category_id');

            /** @var MageWorx_SeoAll_Helper_Category $helperCategory */
            $helperCategory = Mage::helper('mageworx_seoall/category');

            /** @var array $categoryNames */
            $categoryNames  = $helperCategory->getCategoryNames($categoryIds);
        }

        /** @var MageWorx_SeoExtended_Model_Catalog_Category $categoryFilter */
        foreach ($this->_collection as $categoryFilter) {

            /** @var Mage_Catalog_Model_Category $category */
            $category = Mage::getModel('catalog/category');
            $category->setStoreId($categoryFilter->getStoreId());
            $category->load($categoryFilter->getCategoryId());
            $category->setAttributeId($categoryFilter->getAttributeId());

            $attributeValue = $this->_converter->convert($category, $template->getCode());

            foreach ($this->_attributeCodes as $attributeCode)
            {
                if (trim($attributeValue) == '') {
                    $valueForCsv = $categoryFilter->getData($attributeCode);
                } else {
                    $valueForCsv = $attributeValue;
                }

                $attributeLabel = '';
                $attributes = Mage::getSingleton('mageworx_seoall/source_product_attribute')->toArray();
                if (!empty($attributes[$categoryFilter->getAttributeId()])) {
                    $attributeLabel = $attributes[$categoryFilter->getAttributeId()];
                }

                /** @var MageWorx_SeoAll_Helper_Store $helperStore */
                $helperStore =  Mage::helper('mageworx_seoall/store');

                $categoryName = '';
                if(!empty($categoryNames[$categoryFilter->getCategoryId()])) {
                    $categoryName = $categoryNames[$categoryFilter->getCategoryId()];
                }

                $report = array(
                    'filter_id'      => $categoryFilter->getId(),
                    'attribute_id'   => $categoryFilter->getAttributeId(),
                    'attribute_name' => $attributeLabel,
                    'category_id'    => $categoryFilter->getCategoryId(),
                    'category_name'  => $categoryName,
                    'store_id'       => $this->_storeId,
                    'store_name'     => $helperStore->getStoreById($this->_storeId)->getName(),
                    'property'       => $attributeCode,
                    'old_value'      => $categoryFilter->getData($attributeCode),
                    'value'          => $valueForCsv
                );
                $io->streamWriteCsv($report);
            }
        }
    }

    /**
     * Retrieve header for report
     * @return array
     */
    protected function _getHeaderData()
    {
        return array(
            Mage::helper('mageworx_seoxtemplates')->__('SEO Filter ID'),
            Mage::helper('mageworx_seoxtemplates')->__('Attribute ID'),
            Mage::helper('mageworx_seoxtemplates')->__('Attribute Code'),
            Mage::helper('mageworx_seoxtemplates')->__('Category ID'),
            Mage::helper('mageworx_seoxtemplates')->__('Category Name'),
            Mage::helper('mageworx_seoxtemplates')->__('Store ID'),
            Mage::helper('mageworx_seoxtemplates')->__('Store Name'),
            Mage::helper('mageworx_seoxtemplates')->__('Target Property'),
            Mage::helper('mageworx_seoxtemplates')->__('Current Value'),
            Mage::helper('mageworx_seoxtemplates')->__('New Value')
        );
    }

}