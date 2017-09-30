<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Block_Adminhtml_Catalog_Category_Edit_Tab_Prepare extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $model = Mage::registry('current_attribute_category_instance');
        $data  = $model->getData();
        if ($storeId = $this->getRequest()->getParam('store')) {
            $data['store_id'] = $storeId;
        }

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset(
            'prepare',
            array('legend' => Mage::helper('mageworx_seoextended')->__('SEO for Category Filter - Attribute Setting'))
        );

        $fieldset->addField(
            'attribute_name', 'select',
            array(
                'label'    => Mage::helper('mageworx_seoextended')->__('Attribute'),
                'name'     => 'general[attribute_id]',
                'required' => true,
                'options'  => Mage::getSingleton('mageworx_seoall/source_product_attribute')->toArray(),
            )
        );

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
