<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Block_Adminhtml_Template_CategoryFilter_Edit extends MageWorx_SeoXTemplates_Block_Adminhtml_Template_Edit
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_template_categoryFilter';
        parent::__construct();
    }

    /**
     * Retrieve JS code for save action
     * @return string
     */
    protected function _getFormScript()
    {
        return
        "
            function saveTemplatesForm() {
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $template      = $this->getSeoTemplate();
        $helperFactory = Mage::helper('mageworx_seoxtemplates/factory');

        if (Mage::helper('mageworx_seoxtemplates')->getStep() == 'edit') {
            $type      = $helperFactory->getHelper()->getTypeByTypeId($template->getTypeId());
            $storeview = $this->_getStoreViewAsString();

            return Mage::helper('mageworx_seoxtemplates')->__(
                "Edit %s Template (\"%s\") for \"%s\" Store View and \"%s\" Attribute",
                $type,
                $template->getName(),
                $storeview,
                $this->_getAttributeAsString()
            );
        }
        elseif (Mage::helper('mageworx_seoxtemplates')->getStep() == 'new_step_2') {
            $type      = $helperFactory->getHelper()->getTypeByTypeId($this->getRequest()->getParam('type_id'));
            $storeview = $this->_getStoreViewAsString();

            return Mage::helper('mageworx_seoxtemplates')->__(
                "Edit \"%s\" Template for \"%s\" Store View and \"%s\" Attribute",
                $type,
                $storeview,
                $this->_getAttributeAsString()
            );
        }
        else {
            $name = ucfirst($helperFactory->getItemType());
            return $helperFactory->__("Create New Template for") . ' ' . $name;
        }
    }


    /**
     * @return string
     * @throws Exception
     */
    protected function _getAttributeAsString()
    {
        if ($this->getRequest()->getParam('attribute_id')) {
            $attributeId = $this->getRequest()->getParam('attribute_id');
        } else {
            $attributeId = $this->getSeoTemplate()->getAttributeId();
        }

        if ($attributeId) {
            /** @var Mage_Eav_Model_Entity_Attribute $attributeModel */
            $attributeModel = Mage::getModel('eav/entity_attribute')->load($attributeId);

            if ($attributeModel->getId()) {
                return $attributeModel->getStoreLabel();
            }
        }

        return $this->__("Unknown");
    }

}
