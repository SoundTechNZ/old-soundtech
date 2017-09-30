<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoRedirects_Block_Adminhtml_Redirect_Custom_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form before rendering HTML
     *
     * @return this
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $model = Mage::registry('current_redirect_instance');

        $data = (is_object($model) && count($model->getData())) ? $model->getData() : $this->_getDefaultData();

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset(
            'base_fieldset',
            array('legend' => Mage::helper('mageworx_seoredirects')->__('Custom Redirect Settings'))
        );

        $fieldset->addField(
            'redirect_type',
            'select',
            array(
                'label'    => Mage::helper('mageworx_seoredirects')->__('Request Type'),
                'name'     => 'redirect_type',
                'index'    => 'redirect_type',
                'values'   => Mage::getModel('mageworx_seoredirects/source_custom_requestType')->toOptionArray(),
                'required' => true
            )
        );

        $fieldset->addField(
            'status',
            'select',
            array(
                'label'    => Mage::helper('mageworx_seoredirects')->__('Status'),
                'name'     => 'status',
                'index'    => 'status',
                'values'   => Mage::getModel('mageworx_seoredirects/source_custom_status')->toOptionArray(),
                'required' => true
            )
        );

        $disableStore = (bool)$model->getId() ?  true : false;

        $field = $fieldset->addField(
            'store_id', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('cms')->__('Store View'),
                'title'     => Mage::helper('cms')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                'disabled'  => $disableStore
            )
        );

        $requestEntityType = $fieldset->addField(
            'request_entity_type_id',
            'select',
            array(
                'label'     => Mage::helper('mageworx_seoredirects')->__('Request Entity Type'),
                'name'      => 'request_entity_type_id',
                'values'    => Mage::getModel('mageworx_seoredirects/source_custom_requestEntity')->toRequestOptionArray()
            )
        );


        $requestCustomUrl = $fieldset->addField(
            'request_custom_url_id',
            'text',
            array(
                'label'    => Mage::helper('mageworx_seoredirects')->__('Request Custom URL'),
                'name'     => 'request_custom_url_id',
                'index'    => 'request_entity_id',
                'class'    => 'required-entry',
                'required' => true
            )
        );

        $requestProduct = $fieldset->addField(
            'request_product_id',
            'text',
            array(
                'label'    => Mage::helper('mageworx_seoredirects')->__('Request Product ID'),
                'name'     => 'request_product_id',
                'index'    => 'request_entity_id',
                'required' => true
            )
        );

        $categoryOptionArray = Mage::getSingleton('mageworx_seoall/source_category')->toArray();

        $requestCategory = $fieldset->addField(
            'request_category_id',
            'select',
            array(
                'label'    => Mage::helper('mageworx_seoredirects')->__('Request Category ID'),
                'name'     => 'request_category_id',
                'index'    => 'request_entity_id',
                'values'   => $categoryOptionArray,
                'note'     => !empty($categoryNote) ? $categoryNote : '',
                'required' => true
            )
        );

        $requestCmsPage = $fieldset->addField(
            'request_cms_page_id',
            'text',
            array(
                'label'    => Mage::helper('mageworx_seoredirects')->__('Request CMS Page ID'),
                'name'     => 'request_cms_page_id',
                'index'    => 'request_entity_id',
                'required' => true
            )
        );


        $targetEntityType = $fieldset->addField(
            'target_entity_type_id',
            'select',
            array(
                'label'     => Mage::helper('mageworx_seoredirects')->__('Target Entity Type'),
                'name'      => 'target_entity_type_id',
                'values'    => Mage::getModel('mageworx_seoredirects/source_custom_targetEntity')->toTargetOptionArray()
            )
        );


        $targetCustomUrl = $fieldset->addField(
            'target_custom_url_id',
            'text',
            array(
                'label'    => Mage::helper('mageworx_seoredirects')->__('Target Custom URL'),
                'name'     => 'target_custom_url_id',
                'index'    => 'target_entity_id',
                'required' => true
            )
        );

        $targetProduct = $fieldset->addField(
            'target_product_id',
            'text',
            array(
                'label'    => Mage::helper('mageworx_seoredirects')->__('Target Product ID'),
                'name'     => 'target_product_id',
                'index'    => 'target_entity_id',
                'required' => true
            )
        );

        $targetCategory = $fieldset->addField(
            'target_category_id',
            'select',
            array(
                'label'    => Mage::helper('mageworx_seoredirects')->__('Target Category ID'),
                'name'     => 'target_category_id',
                'index'    => 'target_entity_id',
                'values'   => $categoryOptionArray,
                'note'     => !empty($categoryNote) ? $categoryNote : '',
                'required' => true
            )
        );

        $targetCmsPage = $fieldset->addField(
            'target_cms_page_id',
            'text',
            array(
                'label'    => Mage::helper('mageworx_seoredirects')->__('Target CMS Page ID'),
                'name'     => 'target_cms_page_id',
                'index'    => 'target_entity_id',
                'required' => true
            )
        );


        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                ->addFieldMap($targetEntityType->getHtmlId(), $targetEntityType->getName())
                ->addFieldMap($targetCustomUrl->getHtmlId(), $targetCustomUrl->getName())
                ->addFieldMap($targetProduct->getHtmlId(), $targetProduct->getName())
                ->addFieldMap($targetCategory->getHtmlId(), $targetCategory->getName())
                ->addFieldMap($targetCmsPage->getHtmlId(), $targetCmsPage->getName())
                ->addFieldMap($requestEntityType->getHtmlId(), $requestEntityType->getName())
                ->addFieldMap($requestCustomUrl->getHtmlId(), $requestCustomUrl->getName())
                ->addFieldMap($requestProduct->getHtmlId(), $requestProduct->getName())
                ->addFieldMap($requestCategory->getHtmlId(), $requestCategory->getName())
                ->addFieldMap($requestCmsPage->getHtmlId(), $requestCmsPage->getName())
                ->addFieldDependence(
                    $targetCustomUrl->getName(),
                    $targetEntityType->getName(),
                    'target_' . MageWorx_SeoRedirects_Model_Redirect_Custom::CUSTOM_URL_ENTITY_TYPE_ID
                )
                ->addFieldDependence(
                    $targetProduct->getName(),
                    $targetEntityType->getName(),
                    'target_' . MageWorx_SeoRedirects_Model_Redirect_Custom::PRODUCT_ENTITY_TYPE_ID
                )
                ->addFieldDependence(
                    $targetCategory->getName(),
                    $targetEntityType->getName(),
                    'target_' . MageWorx_SeoRedirects_Model_Redirect_Custom::CATEGORY_ENTITY_TYPE_ID
                )
                ->addFieldDependence(
                    $targetCmsPage->getName(),
                    $targetEntityType->getName(),
                    'target_' . MageWorx_SeoRedirects_Model_Redirect_Custom::CMS_PAGE_ENTITY_TYPE_ID
                )
                ->addFieldDependence(
                    $requestCustomUrl->getName(),
                    $requestEntityType->getName(),
                    'request_' . MageWorx_SeoRedirects_Model_Redirect_Custom::CUSTOM_URL_ENTITY_TYPE_ID
                )
                ->addFieldDependence(
                    $requestProduct->getName(),
                    $requestEntityType->getName(),
                    'request_' . MageWorx_SeoRedirects_Model_Redirect_Custom::PRODUCT_ENTITY_TYPE_ID
                )
                ->addFieldDependence(
                    $requestCategory->getName(),
                    $requestEntityType->getName(),
                    'request_' . MageWorx_SeoRedirects_Model_Redirect_Custom::CATEGORY_ENTITY_TYPE_ID
                )
                ->addFieldDependence(
                    $requestCmsPage->getName(),
                    $requestEntityType->getName(),
                    'request_' . MageWorx_SeoRedirects_Model_Redirect_Custom::CMS_PAGE_ENTITY_TYPE_ID
                )
        );

        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve array of default redirects data for current tab
     *
     * @return array
     */
    protected function _getDefaultData()
    {
        return array();
    }

    /**
     *
     * @param int $categoryId
     * @return boolean
     */
    protected function _isInvalidCategoryId($categoryId)
    {
        return !array_key_exists($categoryId, Mage::getSingleton('mageworx_seoall/source_category')->toArray());
    }

    /**
     *
     * @param int $categoryId
     * @return string
     */
    protected function _getInvalidCategoryNote($categoryId)
    {
        return Mage::helper('mageworx_seoredirects')->__('Last request category ID %s is invalid', $categoryId);
    }
}
