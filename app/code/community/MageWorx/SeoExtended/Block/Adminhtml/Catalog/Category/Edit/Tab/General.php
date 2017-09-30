<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Block_Adminhtml_Catalog_Category_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form before rendering HTML
     *
     * @return MageWorx_SeoXTemplates_Block_Adminhtml_Template_Edit_Tab_General
     */
    protected function _prepareForm()
    {
        /** @var MageWorx_SeoExtended_Model_Catalog_Category $model */
        $model = Mage::registry('current_attribute_category_instance');

        $data = $model->getData();

        if ($storeId = (int)$this->getRequest()->getParam('store')) {
            $data['store_id'] = $storeId;
        }

        if ($attributeId = (int)$this->getRequest()->getParam('attribute_id')) {
            $data['attribute_id'] = $attributeId;
        }

        $model->setData($data);

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset(
            'base_fieldset',
            array('legend' => Mage::helper('mageworx_seoextended')->__('Settings'))
        );

        $fieldset->addField(
            'attribute_name', 'label',
            array(
                'label'    => Mage::helper('mageworx_seoextended')->__('Attribute'),
                'value'   => $model->getAttributeLabel()
            )
        );

        $fieldset->addField(
            'attribute_id', 'hidden',
            array(
                'name'     => 'general[attribute_id]',
            )
        );

        $fieldset->addField(
            'store_id', 'hidden',
            array(
                'name'     => 'general[store_id]',
            )
        );

        if ($model->getId()) {
            $fieldset->addField(
                'category_path_names', 'label',
                array(
                    'label'   => Mage::helper('mageworx_seoextended')->__('Category Name'),
                    'value'   => $model->getCategoryPathNames()
                )
            );
        } else {
            $fieldset->addType('mageworx_select', get_class(Mage::getModel('mageworx_seoall/form_element_select')));
            /** @var MageWorx_SeoAll_Model_Source_Category $sourceCategoryModel */
            $sourceCategoryModel = Mage::getSingleton('mageworx_seoall/source_category');

            $fieldset->addField(
                'category_name', 'mageworx_select',
                array(
                    'name'     => 'general[category_id]',
                    'label'    => Mage::helper('mageworx_seoextended')->__('Category Name'),
                    'values'   => $sourceCategoryModel->toOptionArray($model->getMarkedCategories()),
                    'required' => true,
                )
            );
        }

        $fieldset->addField(
            'meta_title', 'text',
            array(
                'name'     => 'general[meta_title]',
                'label'    => Mage::helper('mageworx_seoextended')->__('Meta Title'),
                'title'    => Mage::helper('mageworx_seoextended')->__('Meta Title'),
                'style'  => 'width:700px',
                'required' => false,
                'note' => $this->_getNotice()
            )
        );

        $fieldset->addField(
            'meta_description', 'textarea',
            array(
                'name'     => 'general[meta_description]',
                'label'    => Mage::helper('mageworx_seoextended')->__('Meta Description'),
                'title'    => Mage::helper('mageworx_seoextended')->__('Meta Description'),
                'style'  => 'width:700px',
                'required' => false,
                'note' => $this->_getNotice()
            )
        );

        $fieldset->addField(
            'meta_keywords', 'text',
            array(
                'name'     => 'general[meta_keywords]',
                'label'    => Mage::helper('mageworx_seoextended')->__('Meta Keywords'),
                'title'    => Mage::helper('mageworx_seoextended')->__('Meta Keywords'),
                'style'  => 'width:700px',
                'required' => false,
            )
        );

        $fieldset->addField(
            'description',
            'editor',
            array(
                'name'   => 'general[description]',
                'label'  => Mage::helper('mageworx_seoextended')->__('Description'),
                'title'  => Mage::helper('mageworx_seoextended')->__('Description'),
                'style'  => 'width:700px; height:500px;',
                'config' => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            )
        );

        $form->addValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return string
     */
    protected function _getNotice()
    {
        if (Mage::helper('mageworx_seoextended/adapter')->isAvailableSeoTemplates()) {
            $msg = $this->__('Dynamic variables and randomizer are available. See the description on the SEO XTemplates Category Filter Edit Page');

            $msg .= ' ' . '(<i>' . Mage::helper('adminhtml')->__('Catalog');
            $msg .= ' > ' . Mage::helper('mageworx_seoxtemplates')->__('SEO Extended Templates');
            $msg .= ' > ' . Mage::helper('mageworx_seoxtemplates')->__('For Category Filters');
            $msg .= '</i>)';

            return $msg;
        }
    }
}