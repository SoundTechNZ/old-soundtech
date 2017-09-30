<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Block_Adminhtml_Template_CategoryFilter_Edit_Tab_Conditions
    extends MageWorx_SeoXTemplates_Block_Adminhtml_Template_Category_Edit_Tab_Conditions
{
    /**
     * @return string
     */
    protected function _getNote()
    {
        $partOne = Mage::helper('mageworx_seoxtemplates')->
        __('Note: There is only one combination "Template Type – Store View – Category - Attribute" available for the chosen Category.');

        $partTwo =  Mage::helper('mageworx_seoxtemplates')->
        __('So Categories assigned to different templates with the same conditions are disabled in the Category Tree.');

        return $partOne . ' ' . $partTwo;
    }

    /**
     * @return MageWorx_SeoXTemplates_Model_Resource_Template_Relation_CategoryFilter_Collection
     */
    protected function _getRelationCollection()
    {
        return Mage::getResourceModel('mageworx_seoxtemplates/template_relation_categoryFilter_collection');
    }
}