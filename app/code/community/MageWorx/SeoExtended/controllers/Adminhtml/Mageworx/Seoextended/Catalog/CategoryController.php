<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Adminhtml_Mageworx_Seoextended_Catalog_CategoryController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @return $this|Mage_Core_Controller_Varien_Action
     */
    public function indexAction()
    {
        $defaultStoreId = Mage::helper('mageworx_seoall/store')->getDefaultStoreId();

        if (!array_key_exists('store', $this->getRequest()->getParams())) {
            return $this->_redirect('*/*/*', array('store' => $defaultStoreId));
        }

        $this->_init();
        $this->renderLayout();
    }

    /**
     * New SEO for Category Filters action
     *
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit SEO for Category Filters action
     */
    public function editAction()
    {
        $id    = (int) $this->getRequest()->getParam('id');
        $model = $this->_initInstance();

        if ($model->getId()) {
            $this->_title($this->__('Edit Attribute Category'));
        } else {
            $this->_title($this->__('New Attribute Category'));
        }

        if ($model->getId() || $id == 0) {
            $this->_init();
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
            $this->_addContent($this->getLayout()->createBlock('mageworx_seoextended/adminhtml_catalog_category_edit'))
                ->_addLeft($this->getLayout()->createBlock('mageworx_seoextended/adminhtml_catalog_category_edit_tabs'));
            $this->renderLayout();
        } else {
            $this->_getSession()->addError($this->__('Attribute do not exist'));
            $this->_redirect('*/*/', array('store' => $this->_getStoreId()));
        }
    }

    /**
     * Save SEO for Category Filters action
     *
     */
    public function saveAction()
    {
        $data    = $this->getRequest()->getPost('general');

        if (empty($data)) {
            return $this->_redirect('*/*/index', array('_current' => true));
        }

        if ($this->getRequest()->getParam('prepare')) {
            $params = array(
                'attribute_id'  => $data['attribute_id'],
                'store'         => $this->_getStoreId()
            );
            return $this->_redirect('*/*/edit', $params);
        }

        try {
            $model = $this->_initInstance();
            $this->_save($model, $data);

            return $this->_redirect(
                '*/*/' . $this->getRequest()->getParam('ret', 'index'),
                array('store' => $this->_getStoreId())
            );
        }
        catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            return $this->_redirect(
                '*/*/edit', array(
                    'id' => $this->getRequest()->getParam('id'),
                    'store' => $this->_getStoreId()
                )
            );
        }
    }

    /**
     * Delete SEO for Category Filters action
     *
     */
    public function deleteAction()
    {
        $model   = $this->_initInstance();
        $helper  = Mage::helper('mageworx_seoextended');

        try {
            $model->delete();
            $this->_getSession()->addSuccess($helper->__('The SEO for Category Filter(s) has been deleted'));
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->getResponse()->setRedirect($this->getUrl('*/*/index', array('store' => $this->_getStoreId())));
    }

    protected function _init()
    {
        $this->_title(Mage::helper('mageworx_seoextended')->__('SEO for Category Filters'));
        $this->loadLayout();
        $this->_setActiveMenu('catalog/seoextended');
    }

    /**
     * Init SEO Extended Category model instance
     *
     * @return MageWorx_SeoExtended_Model_Catalog_Category
     */
    protected function _initInstance()
    {
        $id    = (int)$this->getRequest()->getParam('id');
        $model = Mage::getModel('mageworx_seoextended/catalog_category');
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $message = Mage::helper('mageworx_seoextended')->__('Unable to Find SEO for Category Filters.');
                $this->_getSession()->addError($message);
                $this->_redirect('*/*/index', array('store' => $this->_getStoreId()));
            }
        }

        Mage::register('current_attribute_category_instance', $model);
        return $model;
    }

    /**
     * @param MageWorx_SeoExtended_Model_Catalog_Category $currentModel
     * @param array $data
     */
    protected function _save($currentModel, $data)
    {
        $currentModel->addData($data);
        $count = 0;

        if ($this->getRequest()->getParam('save_multistore_all')
            || $this->getRequest()->getParam('save_multistore_empty')
        ) {
            $validStoreIds = $currentModel->getValidStoreIds();
            $issetCollection = $currentModel->findStoreAnalog();
            $issetStores = array();

            foreach ($issetCollection as $item) {
                $storeId = $item->getStoreId();
                $issetStores[] = $storeId;

                if ($this->getRequest()->getParam('save_multistore_all')) {
                    $item->addData($data);
                    $item->setStoreId($storeId);
                    $item->save();
                    $count++;
                }
            }

            foreach ($validStoreIds as $storeId) {
                if (in_array($storeId, $issetStores)) {
                    continue;
                }

                /** @var MageWorx_SeoExtended_Model_Catalog_Category $model */
                $model = Mage::getModel('mageworx_seoextended/catalog_category');
                $model->addData($data);
                $model->setStoreId($storeId);
                $model->setCategoryId($currentModel->getCategoryId());
                $model->setAttributeId($currentModel->getAttributeId());
                $model->setId(null);
                $model->save();
                $count++;
            }
        }

        $currentModel->save();
        $count++;
        $helper = Mage::helper('mageworx_seoextended');

        if ($count > 1) {
            $this->_getSession()->addSuccess($helper->__('%s rows were successfully saved', $count));
        } else {
            $this->_getSession()->addSuccess($helper->__('Row was successfully saved'));
        }
    }

    /**
     * @return array
     */
    protected function _getSeoForCategoryFiltersIds()
    {
        $ids = $this->getRequest()->getParam('seoextended_category_ids');

        return is_array($ids) ? $ids : array();
    }


    public function massDeleteAction()
    {
        $this->_init();
        $ids = $this->_getSeoForCategoryFiltersIds();

        if (empty($ids)) {
            $this->_getSession()->addError($this->__('Please select Row(s)'));
        }
        else {
            try {
                //Models are created for use of events
                foreach ($ids as $id) {
                    $model = Mage::getModel('mageworx_seoextended/catalog_category')->load($id);
                    $model->delete();
                }

                $this->_getSession()->addSuccess(
                    $this->__(
                        'Total of %d record(s) were successfully deleted',
                        count($ids)
                    )
                );
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index', array('store' => $this->_getStoreId()));
    }

    public function massChangeMetaTitleAction()
    {
        $this->_massInitAction('meta_title');
        $this->_redirect('*/*/index', array('store' => $this->_getStoreId()));
    }

    public function massChangeMetaDescriptionAction()
    {
        $this->_massInitAction('meta_description');
        $this->_redirect('*/*/index', array('store' => $this->_getStoreId()));
    }

    public function massChangeMetaKeywordsAction()
    {
        $this->_massInitAction('meta_keywords');
        $this->_redirect('*/*/index', array('store' => $this->_getStoreId()));
    }

    public function massChangeDescriptionAction()
    {
        $this->_massInitAction('description');
        $this->_redirect('*/*/index', array('store' => $this->_getStoreId()));
    }

    protected function _massInitAction($param)
    {
        $ids = $this->_getSeoForCategoryFiltersIds();
        if (empty($ids)) {
            $this->_getSession()->addError($this->__('Please select Row(s)'));
        }
        else {
            try {
                //Models are created for use of events
                foreach ($ids as $id) {
                    $model = Mage::getModel('mageworx_seoextended/catalog_category')->load($id);
                    $model->setData($param, $this->getRequest()->getParam($param))->save();
                }

                $this->_getSession()->addSuccess(
                    $this->__(
                        'Total of %d record(s) were successfully updated',
                        count($ids)
                    )
                );
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/mageworx_seoextended');
    }

    /**
     *
     * @return int
     */
    protected function _getStoreId()
    {
        return (int)$this->getRequest()->getParam('store');
    }
}