<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Block_Adminhtml_Catalog_Seocategory_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_catalog_seocategory';
        $this->_objectId   = 'id';
        $this->_blockGroup = 'mageworx_seoextended';
        parent::__construct();        
    }    

    protected function _prepareLayout()
    {
        if ((int)$this->_getStoreId() !== 0) {
            $message = $this->__('You are going to make changes for the particular (non-default) store. Are you sure?');

            $this->updateButton(
                'save',
                'onclick',
                "if( confirm('{$message}') ) {
                    editForm.submit()
                }"
            );
        }

        $message = Mage::helper('mageworx_seoextended')->getLostDataConfirmMessage();
        $this->updateButton('reset', 'onclick', "confirmSetLocation('{$message}', '{window.location.href}')");
        $this->updateButton('back', 'onclick', "confirmSetLocation('{$message}', '{$this->getBackUrl()}')");
       
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    protected function _getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('store' => (int) $this->getRequest()->getParam('store')));
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $currentStoreName =  Mage::getModel('core/store')->load($this->_getStoreId())->getName();
        return $this->__('Mass Edit Category SEO Params for %s Store View', $currentStoreName);
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/', array('store' => $this->_getStoreId()));
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
