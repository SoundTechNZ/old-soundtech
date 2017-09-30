<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Block_Adminhtml_Template_CategoryFilter_Edit_Tab_Settings extends MageWorx_SeoXTemplates_Block_Adminhtml_Template_Edit_Tab_Settings
{
    /**
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @return $this
     */
    protected function _addCustomField($fieldset)
    {
        /** @var MageWorx_SeoExtended_Model_System_Config_Source_Attribute $optionModel */
        $optionModel = Mage::getSingleton('mageworx_seoall/source_product_attribute');
        $options     = array('' => Mage::helper('adminhtml')->__('-- Please Select --')) + $optionModel->toArray();

        $fieldset->addField(
            'attribute_name', 'select',
            array(
                'label'    => Mage::helper('mageworx_seoextended')->__('Attribute'),
                'name'     => 'general[attribute_id]',
                'required' => true,
                'options'  => $options
            )
        );

        return $this;
    }
}
