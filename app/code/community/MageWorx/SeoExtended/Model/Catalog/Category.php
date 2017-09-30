<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

/**
 * Class MageWorx_SeoExtended_Model_Catalog_Category
 *
 * @method int getCategoryId()
 * @method this setCategoryId(int $categoryId)
 * @method int getAttributeId()
 * @method this setAttributeId(int $attributeId)
 * @method int getStoreId()
 * @method this setStoreId(int $storeId)
 * @method string getMetaTitle()
 * @method this setMetaTitle(string $metaTitle)
 * @method string getMetaDescription()
 * @method this setMetaDescription(string $metaDescription)
 * @method string getMetaKeywords()
 * @method this setMetaKeywords(string $metaKeywords)
 * @method string getDescription()
 * @method this setDescription(string $description)
 * @method string getCategoryPathNames()
 * @method this setCategoryPathNames(string $names)
 * @method string getCategoryName()
 * @method this setCategoryName(string $name)
 */
class MageWorx_SeoExtended_Model_Catalog_Category extends Mage_Core_Model_Abstract
{
    /**
     * @var string
     */
    protected $attributeLabel;

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('mageworx_seoextended/catalog_category');
        $this->setIdFieldName('id');
    }

    /**
     * @return MageWorx_SeoExtended_Model_Resource_Catalog_Category_Collection|object
     */
    public function findStoreAnalog()
    {
        if (!$this->getCategoryId() || !$this->getStoreId() || !$this->getAttributeId()) {
            return $this->getCollection();
        }

        $validStoreIds = $this->getValidStoreIds();

        if (!$validStoreIds) {
            return $this->getCollection();
        }

        /** @var MageWorx_SeoExtended_Model_Resource_Catalog_Category_Collection $collection */
        $collection = $this->getCollection();
        $collection->addFieldToFilter('store_id', $validStoreIds);
        $collection->addFieldToFilter('category_id', $this->getCategoryId());
        $collection->addFieldToFilter('attribute_id', $this->getAttributeId());

        return $collection;
    }

    /**
     * Retrieve all enabled store IDs with the same root category ID except current store ID
     *
     * @return array
     */
    public function getValidStoreIds()
    {
        $result = array();

        if (!$this->getCategoryId() || !$this->getStoreId()) {
            return $result;
        }

        $rootCategoryId = Mage::app()->getStore($this->getStoreId())->getRootCategoryId();

        /** @var MageWorx_SeoAll_Helper_Store $seoAllHelper */
        $seoAllHelper = Mage::helper('mageworx_seoall/store');
        $storeIds = $seoAllHelper->getAllEnabledStoreIds();

        if (($key = array_search($this->getStoreId(), $storeIds)) !== false) {
            unset($storeIds[$key]);
        }

        foreach ($storeIds as $storeId) {
            $storeRootCategoryId =  Mage::app()->getStore($storeId)->getRootCategoryId();
            if ($rootCategoryId == $storeRootCategoryId) {
                $result[] = $storeId;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        $storeId = Mage::app()->getStore()->getId();

        if ($this->getId()) {
            if ($this->getCategoryId()) {

                /** @var MageWorx_SeoAll_Helper_Category $helperCategory */
                $helperCategory = Mage::helper('mageworx_seoall/category');
                $names = implode('/', $helperCategory->getParentCategoryNamesById($this->getCategoryId(), 0));
                $this->setCategoryPathNames($names);
                $nameList = explode('/', $names);
                $name = array_pop($nameList);
                $this->setCategoryName($name);
            }

            if ($this->getAttributeId()) {
                /** @var Mage_Eav_Model_Entity_Attribute $attributeModel */
                $attributeModel = Mage::getModel('eav/entity_attribute')->load($this->getAttributeId());
                $this->setAttributeLabel($attributeModel->getStoreLabel());
            }
        }

        parent::_afterLoad();
    }

    /**
     * @return mixed|string
     */
    public function getAttributeLabel()
    {
        if (!$this->attributeLabel) {
            /** @var Mage_Eav_Model_Entity_Attribute $attributeModel */
            $attributeModel = Mage::getModel('eav/entity_attribute')->load($this->getAttributeId());
            $this->attributeLabel = $attributeModel->getStoreLabel();
        }

        return $this->attributeLabel;
    }

    /**
     * @return array|null
     */
    public function getMarkedCategories()
    {
        if ($this->getStoreId() && $this->getAttributeId()) {
            /** @var MageWorx_SeoExtended_Model_Resource_Catalog_Category_Collection $collection */
            $collection = $this->getCollection();
            $collection->addFieldToFilter('store_id', $this->getStoreId());
            $collection->addFieldToFilter('attribute_id', $this->getAttributeId());
            $collection->addFieldToSelect('category_id', $this->getCategoryId());
            return $collection->getColumnValues('category_id');
        }

        return null;
    }
}