<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Adminhtml_Mageworx_Seoextended_Catalog_SeocategoryController extends Mage_Adminhtml_Controller_Action
{
    const CATEGORY_MASS_EDIT_COUNT_MAX = 50;

    /**
     * @return $this|Mage_Core_Controller_Varien_Action
     */
    public function indexAction()
    {
        $defaultStoreId = Mage::helper('mageworx_seoall/store')->getDefaultStoreId();

        if (!array_key_exists('store', $this->getRequest()->getParams())) {
            return $this->_redirect('*/*/*', array('store' => '0'));
        }

        $this->_init();
        $this->renderLayout();
    }

    /**
     * @return $this|Mage_Core_Controller_Varien_Action
     */
    public function massChangeAction()
    {
        if (!array_key_exists('store', $this->getRequest()->getParams())) {
            return $this->_redirect('*/*/*', array('store' => '0'));
        }

        $idsAsString = $this->getRequest()->getParam('current_seocategory_instance_ids');

        $ids = explode(',', $idsAsString);

        if (!$ids) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Category(ies)'));
            return $this->getResponse()->setRedirect($this->_getRefererUrl());
        }

        if (count($ids) > self::CATEGORY_MASS_EDIT_COUNT_MAX) {
            Mage::getSingleton('adminhtml/session')->addWarning($this->_getCategoryMaxCountMessage());
            return $this->getResponse()->setRedirect($this->_getRefererUrl());
        }

        Mage::register('current_seocategory_instance_ids', $ids);

        $this->_init();
        $this->_addContent($this->getLayout()->createBlock('mageworx_seoextended/adminhtml_catalog_seocategory_edit'))
            ->_addLeft($this->getLayout()->createBlock('mageworx_seoextended/adminhtml_catalog_seocategory_edit_tabs'));
        $this->renderLayout();
    }

    /**
     * @return $this|Mage_Core_Controller_Varien_Action
     */
    public function massChangePrepareAction()
    {
        if (!array_key_exists('store', $this->getRequest()->getParams())) {
            return $this->_redirect('*/*/*', array('store' => '0'));
        }

        $key = $this->getRequest()->getParam('massaction_prepare_key');

        if (!$key) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Category(ies)'));
            return $this->getResponse()->setRedirect($this->_getRefererUrl());
        }

        $ids = $this->getRequest()->getParam($key);

        if (!$ids) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Category(ies)'));
            return $this->getResponse()->setRedirect($this->_getRefererUrl());
        }

        if (count($ids) > self::CATEGORY_MASS_EDIT_COUNT_MAX) {
            Mage::getSingleton('adminhtml/session')->addWarning($this->_getCategoryMaxCountMessage());
            return $this->getResponse()->setRedirect($this->_getRefererUrl());
        }

        return $this->_redirect(
            '*/*/massChange',
            array(
                'store' => $this->_getStoreId(),
                'current_seocategory_instance_ids' => implode(',', $ids)
            )
        );

        Mage::register('current_seocategory_instance_ids', $ids);

        $this->_init();
        $this->_addContent($this->getLayout()->createBlock('mageworx_seoextended/adminhtml_catalog_seocategory_edit'))
            ->_addLeft($this->getLayout()->createBlock('mageworx_seoextended/adminhtml_catalog_seocategory_edit_tabs'));
        $this->renderLayout();
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

    protected function _massInitAction($param)
    {
        $value = $this->getRequest()->getParam($param, null);

        if ($value === null) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Value not found'));
            $this->getResponse()->setRedirect($this->_getRefererUrl());
        }

        $key = $this->getRequest()->getParam('massaction_prepare_key');

        if (!$key) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Category(ies)'));
            $this->getResponse()->setRedirect($this->_getRefererUrl());
        }

        $ids = $this->getRequest()->getParam($key);

        if (!$ids) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Category(ies)'));
        }
        else {
            try {
                //Models are created for use of events
                foreach ($ids as $id) {
                    /** @var Mage_Catalog_Model_Category $model */
                    $model = Mage::getModel('catalog/category');
                    $model->setStoreId($this->_getStoreId());
                    $model->load($id);

                    $model->setData(
                        $param,
                        $value
                    );

                    $model->save();
                }

                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($ids))
                );
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }

        $this->getResponse()->setRedirect($this->_getRefererUrl());
    }

    /**
     * Save category after mass edit action
     *
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost('category_data');

        if (empty($data)) {
            return $this->_redirect('*/*/index', array('_current' => true));
        }

        $count = 0;

        foreach ($data as $categoryId => $categoryData) {
            $category = Mage::getModel('catalog/category');
            $category->setStoreId($this->_getStoreId());
            $category->load($categoryId);

            if (!$category->getId()) {
                continue;
            }

            $category->addData($categoryData);

            if (!$category->hasDataChanges()) {
                continue;
            }

            try {
                $category->save();
                $count++;
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                return $this->_redirect(
                    '*/*/massChange', array(
                        'store' => $this->_getStoreId()
                    )
                );
            }
        }

        $helper = Mage::helper('mageworx_seoextended');
        if (!$count) {
            $this->_getSession()->addSuccess($helper->__('None of the categories was updated'));
        } else {
            $this->_getSession()->addSuccess($helper->__('%s category(ies) were successfully updated', $count));
        }

        return $this->_redirect('*/*/*', array('store' => $this->_getStoreId()));
    }

    protected function _init()
    {
        $this->_title(Mage::helper('mageworx_seoextended')->__('Category SEO Grid'));
        $this->loadLayout();
        $this->_setActiveMenu('catalog/seoextended');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        /** @var Mage_Adminhtml_Model_Session $adminSession */
        $adminSession = Mage::getSingleton('admin/session');

        return $adminSession->isAllowed('catalog/categories') && $adminSession->isAllowed('catalog/mageworx_seoextended_seocategory');
    }

    /**
     *
     * @return int
     */
    protected function _getStoreId()
    {
        return (int)$this->getRequest()->getParam('store');
    }

    /**
     * @return string
     */
    protected function _getCategoryMaxCountMessage()
    {
        return $this->__(
            'Sorry, you should choose no more then %s categories for mass edit.',
            self::CATEGORY_MASS_EDIT_COUNT_MAX
        );
    }
}