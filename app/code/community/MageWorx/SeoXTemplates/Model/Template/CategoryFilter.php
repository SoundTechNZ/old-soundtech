<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Model_Template_CategoryFilter extends MageWorx_SeoXTemplates_Model_Template
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('mageworx_seoxtemplates/template_categoryFilter');
        $this->setIdFieldName('template_id');
    }

    /**
     *
     * @return MageWorx_SeoXTemplates_Model_Template_Relation_Category
     */
    public function getIndividualRelatedModel()
    {
        return Mage::getSingleton('mageworx_seoxtemplates/template_relation_categoryFilter');
    }

    /**
     * Set individual items
     */
    public function loadItems()
    {
        if($this->_getHelper()->isAssignForIndividualItems($this->getAssignType())){
            $itemIds = $this->getIndividualRelatedModel()->getResource()->getItemIds($this->getId());
            $this->setInItems($itemIds);
        }
    }

    /**
     * Retrieve filtered collection for apply (or count)
     *
     * @param int $from
     * @param int $limit
     * @param bool $onlyCountFlag
     * @param int|null $nestedStoreId
     * @return MageWorx_SeoXTemplates_Model_Resource_Template_Collection
     */
    public function getItemCollectionForApply($from, $limit, $onlyCountFlag = false, $nestedStoreId = null)
    {
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        $microtime = microtime(1);

        if ($this->getStoreId() === '0') {
            if ($this->_issetUniqStoreTemplateForAllItems($nestedStoreId)) {
                return 0;
            }

            $excludeItemIds = $this->_getExcludeItemIdsByTemplate($nestedStoreId);
        }
        elseif ($this->getStoreId()) {
            if ($this->_getHelper()->isAssignForAllItems($this->getAssignType())) {
                $excludeItemIds = $this->_getExcludeItemIdsByTemplate($nestedStoreId);
            }
            else {
                $excludeItemIds = false;
            }
        }

        $storeId    = !empty($nestedStoreId) ? $nestedStoreId : $this->getStoreId();
        $rootId     = Mage::app()->getStore($storeId)->getRootCategoryId();

        /** @var MageWorx_SeoExtended_Model_Resource_Catalog_Category_Collection $collection */
        $collection = Mage::getModel('mageworx_seoextended/catalog_category')->getCollection();
        $collection->addFieldToFilter('store_id', $storeId);
        $collection->addFieldToFilter('attribute_id', $this->getAttributeId());

        if($this->_getHelper()->isWriteForEmpty($this->getWriteFor())){
            $adapter = $this->_getHelper()->getTemplateAdapterByModel($this);
            $attributes = $adapter->getAttributeCodes();

            foreach ($attributes as $attribute) {
                $collection->addFieldToFilter($attribute, '');
            }
        }

        if ($this->_getHelper()->isAssignForIndividualItems($this->getAssignType())) {
            $assignItems = (is_array($this->getInItems()) && count($this->getInItems())) ? $this->getInItems() : 0;
            $collection->getSelect()->where('category_id IN (?)', $assignItems);
        }

        if (!empty($excludeItemIds)) {
            $collection->getSelect()->where('category_id NOT IN (?)', $excludeItemIds);
        }

        if ($onlyCountFlag) {
//            echo "<br><font color = green>" . number_format((microtime(1) - $microtime), 5) . " sec need for " . get_class($this) . "</font>"; //exit;
            return $collection->count();
        }

        $collection->getSelect()->limit($limit, $from);

//            echo $collection->getSelect()->__toString(); exit;
//            echo "<pre>"; print_r($collection->getItems()); echo "</pre>"; exit;
//            echo "<br><font color = green>" . number_format((microtime(1) - $microtime), 5) . " sec need for " . get_class($this) . "</font>"; //exit;

        return $collection;

    }

    /**
     *
     * @return array
     */
    public function getAssignForAnalogTemplateCategoryIds()
    {
        $collection = $this->getCollection()
            ->addSpecificStoreFilter($this->getStoreId())
            ->addTypeFilter($this->getTypeId())
            ->addAttributeFilter($this->getAttributeId())
            ->addAssignTypeFilter($this->_getHelper()->getAssignForIndividualItems());
        if ($this->getTemplateId()) {
            $collection->excludeTemplateFilter($this->getTemplateId());
        }

        $templateIDs = $collection->getAllIds();
        $readonlyIds = $this->getIndividualRelatedModel()->getResource()->getItemIds($templateIDs);
        return $readonlyIds;
    }

    /**
     *
     * @param int $nestedStoreId
     * @return array|false
     */
    protected function _getExcludeItemIdsByTemplate($nestedStoreId = null)
    {
        /** @var MageWorx_SeoXTemplates_Model_Resource_Template_CategoryFilter_Collection $templateCollection */
        $templateCollection = $this->getCollection();
        $templateCollection->addTypeFilter($this->getTypeId());
        $templateCollection->addAssignTypeFilter($this->_getHelper()->getAssignForIndividualItems());
        $templateCollection->addAttributeFilter($this->getAttributeId());

        if($this->getStoreId() == '0'){
            if($this->_getHelper()->isAssignForAllItems($this->getAssignType())){
                $templateCollection->addStoreFilter($nestedStoreId);
            }
            elseif($this->_getHelper()->isAssignForIndividualItems($this->getAssignType())){
                $templateCollection->addStoreFilter($nestedStoreId);
                $templateCollection->excludeTemplateFilter($this->getTemplateId());
            }
        }
        elseif ($this->getStoreId()) {
            if($this->_getHelper()->isAssignForAllItems($this->getAssignType())){
                $templateCollection->addSpecificStoreFilter($this->getStoreId());
            }
            elseif($this->_getHelper()->isAssignForIndividualItems($this->getAssignType())){
                return false;
            }
        }

        $templateIds = $templateCollection->getAllIds();

        $excludeItemIds = $this->getIndividualRelatedModel()->getResource()->getItemIds($templateIds);
        return (!empty($excludeItemIds)) ? $excludeItemIds : false;
    }

    /**
     * Retrieve duplicate template that is assigned to all items
     *
     * @return MageWorx_SeoXTemplates_Model_Template|false
     */
    public function getAllTypeDuplicateTemplate()
    {
        /** @var MageWorx_SeoXTemplates_Model_Resource_Template_CategoryFilter_Collection $templateCollection */
        $templateCollection = $this->getCollection()
            ->addSpecificStoreFilter($this->getStoreId())
            ->addTypeFilter($this->getTypeId())
            ->addAttributeFilter($this->getAttributeId())
            ->addAssignTypeFilter($this->_getHelper()->getAssignForAllItems());

        if($this->getTemplateId()){
            $templateCollection->excludeTemplateFilter($this->getTemplateId());
        }

        if ($templateCollection->count()) {
            return $templateCollection->getFirstItem();
        }

        return false;
    }

    /**
     * Check if the template that is assigned to all items already exists
     *
     * @param int $nestedStoreId
     * @return boolean
     */
    protected function _issetUniqStoreTemplateForAllItems($nestedStoreId)
    {
        if ($this->getStoreId() == '0') {
            $templateCollection = $this->getCollection()
                ->addTypeFilter($this->getTypeId())
                ->addAttributeFilter($this->getAttributeId())
                ->addAssignTypeFilter($this->_getHelper()->getAssignForAllItems())
                ->addSpecificStoreFilter($nestedStoreId);

            if($templateCollection->count()){
                return true;
            }
        }

        return false;
    }


    /**
     * Retrine reindex proccesses by templates
     * @param array $nextIds
     * @return array
     */
    public function getReindexProccesses($nextIds)
    {
        return array();
    }

    /**
     *
     * @return MageWorx_SeoXTemplates_Helper_Template_Category
     */
    protected function _getHelper()
    {
        return Mage::helper("mageworx_seoxtemplates/template_categoryFilter");
    }

}
