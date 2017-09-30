<?php
/**
 * MageWorx
 * MageWorx SeoBreadcrumbs Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBreadcrumbs
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBreadcrumbs_Adminhtml_Mageworx_SeobreadcrumbsController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Breadcrumbs'));
        $this->loadLayout();
        $this->_setActiveMenu('mageworx_seobreadcrumbs');
        $this->renderLayout();
    }

    public function updatePriorityAction()
    {
        $fieldId = (int) $this->getRequest()->getParam('id');
        $breadcrumbsPriority = $this->getRequest()->getParam('breadcrumbs_priority');
        if ($fieldId) {
            return Mage::getModel('catalog/category')->load($fieldId)
                ->setBreadcrumbsPriority($breadcrumbsPriority)
                ->save();
        }

        return false;
    }
    
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('mageworx_seobreadcrumbs/adminhtml_breadcrumbs_grid')->toHtml()
        );
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/mageworx_seobreadcrumbs');
    }
}
