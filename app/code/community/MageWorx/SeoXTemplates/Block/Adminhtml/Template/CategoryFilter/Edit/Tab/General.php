<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Block_Adminhtml_Template_CategoryFilter_Edit_Tab_General extends MageWorx_SeoXTemplates_Block_Adminhtml_Template_Edit_Tab_General
{
    protected function _addCustomField($fieldset)
    {
        $fieldset->addField(
            'attribute_id', 'hidden',
            array(
                'name'   => 'general[attribute_id]',
                'value'  => $this->getRequest()->getParam('attribute_id'),
            )
        );

        return $this;
    }
}
