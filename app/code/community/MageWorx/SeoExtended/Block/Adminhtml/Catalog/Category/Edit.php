<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Block_Adminhtml_Catalog_Category_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_catalog_category';
        $this->_objectId   = 'id';
        $this->_blockGroup = 'mageworx_seoextended';
        parent::__construct();

        /** @var MageWorx_SeoExtended_Helper_Data $helper */
        $helper = Mage::helper('mageworx_seoextended');
        if ($helper->getStep() == 'new_step_1') {
            $this->_removeButton('reset');
            $this->_updateButton(
                'save', '',
                array(
                    'label'      => Mage::helper('mageworx_seoextended')->__('Continue Edit'),
                    'onclick'    => "editForm.submit($('edit_form').action + 'prepare/edit/store/{$this->_getStoreId()}')",
                    'class'      => 'save',
                    'sort_order' => 20
                ), -100
            );
        }
        else {
            $this->_updateButton(
                'save', '',
                array(
                    'label'      => Mage::helper('mageworx_seoextended')->__('Save'),
                    'onclick'    => "editForm.submit($('edit_form').action + 'store/{$this->_getStoreId()}')",
                    'class'      => 'save',
                    'sort_order' => 20
                ), 1
            );

            $saveMultistoreAll   = 'save_multistore_all/1/store/' . $this->_getStoreId();
            $saveMultistoreEmpty = 'save_multistore_empty/1/store/' . $this->_getStoreId();

            $this->_addButton(
                'save_multistore_all', array(
                'label'     => Mage::helper('mageworx_seoextended')->__('Save For Each Store'),
                'onclick'   => "editForm.submit($('edit_form').action + '{$saveMultistoreAll}')",
                'class'     => 'save',
                ), 0, 0
            );

            $this->_addButton(
                'save_multistore_empty', array(
                'label'     => Mage::helper('salesrule')->__('Save For Each Store if Empty'),
                'onclick'   => "editForm.submit($('edit_form').action + '{$saveMultistoreEmpty}')",
                'class'     => 'save',
                ), 0, 0
            );
        }
    }

    public function getDeleteUrl()
    {
        return $this->getUrl(
            '*/*/delete', array(
                $this->_objectId => $this->getRequest()->getParam($this->_objectId),
                'store' => $this->_getStoreId()
            )
        );
    }

    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/' . $this->_controller . '/save', array('store' => $this->_getStoreId()));
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return $this->__('Edit SEO for Category Filters');
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
