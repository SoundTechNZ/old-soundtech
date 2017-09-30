<?php
/**
 * MageWorx
 * MageWorx SeoAll Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoAll
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoAll_Helper_Category extends Mage_Core_Helper_Abstract
{
    /**
     * @param int $id
     * @return bool
     */
    public function getCategoryNameById($id, $storeId)
    {
        if ($id) {
            $resourceModel = Mage::getResourceModel('catalog/category');
            if (is_callable(array($resourceModel, 'getAttributeRawValue'))) {
                return $resourceModel->getAttributeRawValue($id, 'name', $storeId);
            }

            /** @var Mage_Catalog_Model_Resource_Category_Collection $collection */
            $collection = Mage::getModel('catalog/category')->getCollection();
            $collection->addIdFilter(array($id));
            $collection->addAttributeToSelect('name');
            $category = $collection->getFirstItem();
            return $category->getAttributeText('name');
        }

        return '';
    }

    /**
     * @param array $ids
     * @return mixed
     * @throws Mage_Core_Exception
     */
    public function getCategoryNames(array $ids)
    {
        /** @var Mage_Catalog_Model_Resource_Category_Collection $collection */
        $collection = Mage::getModel('catalog/category')->getCollection();
        $collection->addIdFilter($ids);
        $collection->addAttributeToSelect('name');

        $result = array();
        foreach ($collection as $item) {
            $result[$item->getId()] = $item->getData('name');
        }

        return $result;
    }

    /**
     * @param int $id
     * @param null|int $storeId
     * @param bool $withRoot
     * @return array|mixed
     */
    public function getParentCategoryNamesById($id, $storeId, $withRoot = false)
    {
        $path = $this->getCategoryPathById($id, $storeId);

        if (!$path) {
            return array();
        }

        $rawIds = explode('/', $path);
        $ids = array();

        foreach ($rawIds as $id) {
            if ($id == 1) {
                continue;
            }

            if (!$withRoot && $id == Mage::app()->getStore($storeId)->getRootCategoryId()) {
                continue;
            }

            $ids[] = $id;
        }

        array_filter($ids);

        if (!$ids) {
            return array();
        }

        return $this->getCategoryNames($ids);
    }

    public function getCategoryPathById($id, $storeId)
    {
        if ($id) {
            $resourceModel = Mage::getResourceModel('catalog/category');
            if (is_callable(array($resourceModel, 'getAttributeRawValue'))) {
                return $resourceModel->getAttributeRawValue($id, 'path', $storeId);
            }

            /** @var Mage_Catalog_Model_Resource_Category_Collection $collection */
            $collection = Mage::getModel('catalog/category')->getCollection();
            $collection->addIdFilter(array($id));
            $collection->addAttributeToSelect('path');
            $category = $collection->getFirstItem();
            return $category->getAttributeText('path');
        }

        return '';
    }

}