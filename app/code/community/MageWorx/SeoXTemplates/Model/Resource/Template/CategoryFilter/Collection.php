<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Model_Resource_Template_CategoryFilter_Collection extends MageWorx_SeoXTemplates_Model_Resource_Template_Collection
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('mageworx_seoxtemplates/template_categoryFilter');
    }

    /**
     * Add attribute filter
     *
     * @param int $attributeId
     * @return MageWorx_SeoXTemplates_Model_Resource_Template_Collection
     */
    public function addAttributeFilter($attributeId)
    {
        $this->addFieldToFilter('attribute_id', $attributeId);
        return $this;
    }

}