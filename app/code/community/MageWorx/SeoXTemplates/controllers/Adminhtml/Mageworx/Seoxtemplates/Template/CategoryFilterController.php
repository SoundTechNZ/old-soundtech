<?php
/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
$dir = Mage::getModuleDir('controllers', 'MageWorx_SeoXTemplates');
require_once($dir.DS.'Adminhtml'.DS.'Mageworx'.DS.'Seoxtemplates'.DS.'Template'.DS.'CategoryController.php');

class MageWorx_SeoXTemplates_Adminhtml_Mageworx_Seoxtemplates_Template_CategoryFilterController
    extends MageWorx_SeoXTemplates_Adminhtml_Mageworx_Seoxtemplates_Template_CategoryController
{

    public function indexAction()
    {
        if(!Mage::helper('mageworx_seoxtemplates')->isModuleEnabled('MageWorx_SeoExtended')){
            $link = '<a target="_blank" href="https://www.mageworx.com/seo-suite-ultimate-magento-extension.html">' . $this->__('link') . '</a>';
            $message = $this->__(
                'SEO Suite Ultimate/SEO Suite Pro extension is disabled or hasn\'t been installed. '
                . 'Make sure you have enabled it or download and install the module using this %s', $link
            );
            Mage::getSingleton('adminhtml/session')->addWarning($message);
            $this->_redirect('adminhtml/dashboard/index');
            return;
        }

        return parent::indexAction();
    }

    protected function _initModel()
    {
        $templateId = $this->getRequest()->getParam('template_id');
        if($templateId){
            $this->_model = $this->_createModel()->load($templateId);
        }else{
            $this->_model = $this->_createModel();
            $this->_model->setStoreId($this->_getStoreIdFromRequest());
            $this->_model->setTypeId($this->_getTypeIdFromRequest());
            $this->_model->setAttributeId($this->_getAttributeIdFromRequest());
        }

        Mage::helper('mageworx_seoxtemplates/factory')->setModel($this->_model);
    }

    /**
     * @return mixed
     */
    protected function _getAttributeIdFromRequest()
    {
        if ($attributeId = $this->getRequest()->getParam('attribute_id')) {
            return $attributeId;
        }
    }

    /**
     * Retrieve template model
     * @return MageWorx_SeoXTemplates_Model_Template_Category
     */
    protected function _createModel()
    {
        return Mage::getModel('mageworx_seoxtemplates/template_categoryFilter');
    }

    /**
     * @param int|null $nestedStoreId
     * @return MageWorx_SeoXTemplates_Model_Resource_Template_Collection
     */
    protected function _getTotalCount($nestedStoreId)
    {
        $this->_createMissingSeoFilters($nestedStoreId);
        return $this->_model->getItemCollectionForApply(0, 999999999, true, $nestedStoreId);
    }

    /**
     * @param int|null $storeId
     * @throws Exception
     */
    protected function _createMissingSeoFilters($nestedStoreId)
    {
        $storeId    = !empty($nestedStoreId) ? $nestedStoreId : $this->_model->getStoreId();

        /** @var MageWorx_SeoExtended_Model_Catalog_Category $categoryFilter */
        $categoryFilter = Mage::getModel('mageworx_seoextended/catalog_category');

        /** @var MageWorx_SeoExtended_Model_Resource_Catalog_Category_Collection $collection */
        $collection = $categoryFilter->getCollection();
        $collection->addFieldToFilter('store_id', $storeId);
        $collection->addFieldToFilter('attribute_id', $this->_model->getAttributeId());
        $issetCategoryIds = $collection->getColumnValues('category_id');

        /** @var MageWorx_SeoXTemplates_Helper_Template_CategoryFilter $helper */
        $helper = Mage::helper('mageworx_seoxtemplates/template_categoryFilter');

        $store = Mage::app()->getStore($storeId);
        $rootId = $store->getRootCategoryId();

        /** @var Mage_Catalog_Model_Resource_Category_Collection $categoryCollection */
        $categoryCollection = Mage::getModel('catalog/category')->getCollection();
        $categoryCollection->addFieldToFilter('path', array('like'=> "1/$rootId/%"));

        if ($helper->isAssignForIndividualItems($this->_model->getAssignType())) {
            $targetCategoryIds = $this->_model->getInItems();
            $categoryCollection->addIdFilter($targetCategoryIds);
        }

        $categoryIds = $categoryCollection->getAllIds();

        foreach($categoryIds as $categoryId) {
            if (in_array($categoryId, $issetCategoryIds)) {
                continue;
            }

            $categoryFilter->setId(null);
            $categoryFilter->setAttributeId($this->_model->getAttributeId());
            $categoryFilter->setCategoryId($categoryId);
            $categoryFilter->setStoreId($storeId);
            $categoryFilter->save();
        }
    }

    /**
     * @param array $data
     * @return array
     */
    protected function _getUrlParamsForPrepareRedirect($data)
    {
        return array(
            'type_id'  => $data['general']['type_id'],
            'store' => $data['general']['store_id'],
            'attribute_id' => $data['general']['attribute_id']
        );
    }
}