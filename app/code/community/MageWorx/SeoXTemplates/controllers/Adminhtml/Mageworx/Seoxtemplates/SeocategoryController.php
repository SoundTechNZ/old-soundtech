<?php
/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoXTemplates_Adminhtml_Mageworx_Seoxtemplates_SeocategoryController extends Mage_Adminhtml_Controller_Action
{
    public function massChangeCategorySeoNameAction()
    {
        $value = $this->getRequest()->getParam('category_seo_name', null);

        if ($value === null) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Category SEO Name not found'));
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
                    $model = Mage::getModel('catalog/category')->load($id);
                    $model->setData(
                        'category_seo_name',
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
     * @return int
     */
    protected function _getStoreId()
    {
        return (int)$this->getRequest()->getParam('store');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/categories');
    }
}