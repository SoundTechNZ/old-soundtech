<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoRedirects_Adminhtml_Mageworx_Seoredirects_Redirect_CustomController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Grid SEO Redirect action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_init();
        $this->renderLayout();
    }

    protected function _init()
    {
        $this->_title(Mage::helper('core')->__('Custom SEO Redirects'));
        $this->loadLayout();
        $this->_setActiveMenu('catalog/seoredirects');
    }

    /**
     * New Custom Redirect action
     *
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit Custom Redirect action
     *
     * @return void
     */
    public function editAction()
    {
        $this->_init();
        $this->_initInstance();
        $this->_addContent($this->getLayout()->createBlock('mageworx_seoredirects/adminhtml_redirect_custom_edit'))
            ->_addLeft($this->getLayout()->createBlock('mageworx_seoredirects/adminhtml_redirect_custom_edit_tabs'));
        $this->renderLayout();
    }

    /**
     * Init redirect model instance
     *
     * @return MageWorx_SeoRedirects_Model_Redirect
     */
    protected function _initInstance()
    {
        $id    = (int)$this->getRequest()->getParam('custom_redirect_id');
        $model = Mage::getModel('mageworx_seoredirects/redirect_custom');

        if ($id) {
            $model->load($id);

            if (!$model->getId()) {
                $message = Mage::helper('mageworx_seoredirects')->__('Unable to find Custom Redirect by ID.');
                Mage::getSingleton('adminhtml/session')->addError($message);
            }
        }
        if(count($model->getData())) {
            $model = $this->prepareDataForEdit($model);
        }
        Mage::register('current_redirect_instance', $model);
        return $model;
    }

    /**
     * Save Custom Redirect action
     *
     * @return this
     */
    public function saveAction()
    {
        $model = $this->_initInstance();
        $data    = $this->prepareDataForSave($this->getRequest()->getPost());
        $validData = $this->validateCustomRedirectData($data);

        $session = Mage::getSingleton('adminhtml/session');

        if (empty($data) || !$validData) {
            return $this->_redirect('*/*/edit', array('_current' => true));
        }

        try {
            if (isset($data['stores'])) {
                foreach ($data['stores'] as $storeId) {
                    $model = Mage::getModel('mageworx_seoredirects/redirect_custom');
                    $data['store_id'] = $storeId;
                    $model->addData($data);
                    $model->save();
                }
            } else {
                $model->addData($data);
                $model->save();
            }

            return $this->_redirect('*/*/' . $this->getRequest()->getParam('ret', 'index'));
        }
        catch (Exception $e) {
            $message = "Redirect from request entity already exist for chosen store view.";
            $session->addError($message);
            return $this->_redirect(
                '*/*/edit', array(
                    'custom_redirect_id' => $this->getRequest()->getParam('custom_redirect_id'),
                )
            );
        }
    }

    /**
     * Prepare data for saving
     *
     * @param $model
     * @return data
     */
    public function prepareDataForEdit($model)
    {
        $data = $model->getData();
        $cases = array('request', 'target');
        foreach ($cases as $case) {
            switch ($data[$case . '_entity_type_id']) {
                case MageWorx_SeoRedirects_Model_Redirect_Custom::PRODUCT_ENTITY_TYPE_ID:
                    $model->setData($case . '_product_id', $data[$case . '_entity_id']);
                    break;
                case MageWorx_SeoRedirects_Model_Redirect_Custom::CATEGORY_ENTITY_TYPE_ID:
                    $model->setData($case . '_category_id', $data[$case . '_entity_id']);
                    break;
                case MageWorx_SeoRedirects_Model_Redirect_Custom::CUSTOM_URL_ENTITY_TYPE_ID:
                    $model->setData($case . '_custom_url_id', $data[$case . '_entity_id']);
                    break;
                case MageWorx_SeoRedirects_Model_Redirect_Custom::CMS_PAGE_ENTITY_TYPE_ID:
                    $model->setData($case . '_cms_page_id', $data[$case . '_entity_id']);
                    break;
            }

            $model->setData($case . '_entity_type_id', $case . '_' . $data[$case . '_entity_type_id']);
        }

        return $model;
    }

    /**
     * Prepare data for saving
     *
     * @param $data
     * @param $model
     * @return data
     */
    public function prepareDataForSave($data)
    {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'request_custom_url_id':
                case 'request_product_id':
                case 'request_category_id':
                case 'request_cms_page_id':
                    $data['request_entity_id'] = $value;
                    break;
                case 'target_custom_url_id':
                case 'target_product_id':
                case 'target_category_id':
                case 'target_cms_page_id':
                    $data['target_entity_id'] = $value;
                    break;
                case 'request_entity_type_id':
                    $data['request_entity_type_id'] = substr($value, 8);
                    break;
                case 'target_entity_type_id':
                    $data['target_entity_type_id'] = substr($value, 7);
                    break;
            }
        }

        if (isset($data['stores'])) {
            $stores = array();
            if (array_search('0', $data['stores']) !== false) {
                foreach (Mage::app()->getStores() as $store) {
                    $stores[] = $store->getId();
                }
                $data['stores'] = $stores;
            }

            if (count($data['stores']) == 1) {
                $data['store_id'] = $data['stores'][0];
            }
        }

        $data['date_modified'] = date('Y-m-d h:i:s');
        if (!$this->getRequest()->getParam('custom_redirect_id')) {
            $data['date_created'] = date('Y-m-d h:i:s');
        }
        return $data;
    }

    /**
     * validate data
     *
     * @param $data
     * @return boolean
     */
    public function validateCustomRedirectData($data)
    {
        $session = Mage::getSingleton('adminhtml/session');
        if ($data['request_entity_type_id'] == $data['target_entity_type_id']
            && $data['request_entity_id'] == $data['target_entity_id']) {
            $message = Mage::helper('mageworx_seoredirects')
                ->__('Target Entity could not be as same as Request Entity.');
            $session->addError($message);
            return false;
        }
        $cases = array('request', 'target');
        foreach ($cases as $case) {
            switch ($data[$case . '_entity_type_id']){
                case MageWorx_SeoRedirects_Model_Redirect_Custom::CUSTOM_URL_ENTITY_TYPE_ID:
                    break;
                case MageWorx_SeoRedirects_Model_Redirect_Custom::PRODUCT_ENTITY_TYPE_ID:
                    $collection = Mage::getResourceModel('catalog/product_collection');
                    $collection->addFieldToFilter('entity_id', $data[$case . '_entity_id']);
                    $foundIds = $collection->getAllIds();
                    if(!$foundIds) {
                        $message = Mage::helper('mageworx_seoredirects')
                            ->__(" product doesn't exist");
                        $session->addError($case . $message);
                        return false;
                    }
                    break;
                case MageWorx_SeoRedirects_Model_Redirect_Custom::CATEGORY_ENTITY_TYPE_ID:
                    $collection = Mage::getResourceModel('catalog/category_collection');
                    $collection->addFieldToFilter('entity_id', $data[$case . '_entity_id']);
                    $foundIds = $collection->getAllIds();
                    if(!$foundIds) {
                        $message = Mage::helper('mageworx_seoredirects')
                            ->__(" category doesn't exist");
                        $session->addError($case . $message);
                        return false;
                    }
                    break;
                case MageWorx_SeoRedirects_Model_Redirect_Custom::CMS_PAGE_ENTITY_TYPE_ID:
                    foreach($data['stores'] as $storeId) {
                        $foundIds = Mage::getResourceModel('cms/page_collection')
                            ->addFieldToFilter('page_id', $data[$case . '_entity_id'])
                            ->getAllIds();
                        if(!$foundIds) {
                            $message = Mage::helper('mageworx_seoredirects')
                                ->__(" CMS page doesn't exist on chosen store view");
                            $session->addError($case . $message . '(ID ' . $storeId . ')');
                            return false;
                        }
                    }
                    break;
            }
        }

        return true;
    }

    /**
    * SEO Redirects mass delete action
    *
    * @return this
    */
    public function massDeleteAction()
    {
        $this->_init();
        $ids = $this->getRequest()->getParam('redirects');

        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Redirect(s)'));
        }
        else {
            try {
                //Models are created for use of events
                foreach ($ids as $id) {
                    $model = Mage::getModel('mageworx_seoredirects/redirect_custom')->load($id);
                    $model->delete();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__(
                        'Total of %d record(s) were successfully deleted',
                        count($ids)
                    )
                );
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    public function massChangeStatusAction()
    {
        $this->_massInitAction('status');
        $this->_redirect('*/*/index');
    }

    protected function _massInitAction($param)
    {
        $ids = $this->getRequest()->getParam('redirects');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Redirect(s)'));
        }
        else {
            try {
                //Models are created for use of events
                foreach ($ids as $id) {
                    $model = Mage::getModel('mageworx_seoredirects/redirect_custom')->load($id);
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

    /**
     * Delete Custom Redirect action
     *
     * @return void
     */
    public function deleteAction()
    {
        $model   = $this->_initInstance();
        $helper  = Mage::helper('mageworx_seoredirects');
        $session = Mage::getSingleton('adminhtml/session');

        try {
            $model->delete();
            $session->addSuccess($helper->__('Custom Redirect has been deleted'));
        } catch (Exception $e) {
            $session->addError($e->getMessage());
        }

        $this->_redirect('*/*/index');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/mageworx_seoredirects');
    }
}