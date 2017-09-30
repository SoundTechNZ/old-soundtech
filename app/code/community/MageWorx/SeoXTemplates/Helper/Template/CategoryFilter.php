<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Helper_Template_CategoryFilter extends MageWorx_SeoXTemplates_Helper_Template_Category
{
    /**
     *
     * @return array
     */
    public function getTypeArray()
    {
        return array(
            self::CATEGORY_META_TITLE       => $this->__('Category Meta Title'),
            self::CATEGORY_META_DESCRIPTION => $this->__('Category Meta Description'),
            self::CATEGORY_META_KEYWORDS    => $this->__('Category Meta Keywords'),
            self::CATEGORY_DESCRIPTION      => $this->__('Category Description')
        );
    }

    /**
     * @return array
     */
    public function getTypeCodeArray()
    {
        return array(
            self::CATEGORY_META_TITLE       => 'categoryFilter_meta_title',
            self::CATEGORY_META_DESCRIPTION => 'categoryFilter_meta_description',
            self::CATEGORY_META_KEYWORDS    => 'categoryFilter_meta_keywords',
            self::CATEGORY_DESCRIPTION      => 'categoryFilter_description'
        );
    }

    /**
     *
     * @return array
     */
    public function getAssignTypeArray()
    {
        return array(
            self::ASSIGN_ALL_ITEMS        => $this->__('All Categories'),
            self::ASSIGN_GROUP_ITEMS      => $this->__('By Category Branch'),
            self::ASSIGN_INDIVIDUAL_ITEMS => $this->__('Specific Categories'),
        );
    }
}
