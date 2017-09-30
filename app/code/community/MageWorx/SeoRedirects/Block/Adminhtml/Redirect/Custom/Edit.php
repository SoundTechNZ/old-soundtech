<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoRedirects_Block_Adminhtml_Redirect_Custom_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId   = 'custom_redirect_id';
        $this->_blockGroup = 'mageworx_seoredirects';
        $this->_controller = 'adminhtml_redirect_custom';

        parent::__construct();

        if ($this->getRequest()->getParam('custom_redirect_id')) {
            $this->_updateButton(
                'delete', '',
                array(
                    'label'      => Mage::helper('catalog')->__('Delete'),
                    'onclick'    => "deleteConfirm('{$this->__('Are you sure you want to do this?')}',
                                    '{$this->getUrl('*/*/delete', array(
                                        'custom_redirect_id' => (int) $this->getRequest()->getParam('custom_redirect_id'))
                                    )}')",
                    'class'      => 'delete',
                    'sort_order' => 8
                )
            );
        }
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return $this->__('Custom Redirect Edit');
    }
}
