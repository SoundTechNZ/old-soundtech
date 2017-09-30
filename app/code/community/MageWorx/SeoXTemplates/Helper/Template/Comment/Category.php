<?php
/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoXTemplates_Helper_Template_Comment_Category extends MageWorx_SeoXTemplates_Helper_Template_Comment
{
    /** @var  string */
    protected $_categoriesSeparator;

    /** @var  string */
    protected $_listSeparator;

    /** @var  string */
    protected $_pairSeparator;

    /**
     * Retrieve comment for template edit page
     * @param int $typeId
     * @return string
     * @throws Exception
     */
    public function getComment($typeId)
    {
        /** @var MageWorx_SeoXTemplates_Helper_Config $helperConfig */
        $helperConfig = Mage::helper('mageworx_seoxtemplates/config');

        $this->_categoriesSeparator = $helperConfig->getSeparatorForCategories();
        $this->_listSeparator       = $helperConfig->getSeparatorForList();
        $this->_pairSeparator       = $helperConfig->getSeparatorForPair();

        $comment = '<br>' . $this->_getStaticVariableHeader();
        switch($typeId){
            case MageWorx_SeoXTemplates_Helper_Template_Category::CATEGORY_META_TITLE:
            case MageWorx_SeoXTemplates_Helper_Template_Category::CATEGORY_META_DESCRIPTION:
            case MageWorx_SeoXTemplates_Helper_Template_Category::CATEGORY_META_KEYWORDS:
            case MageWorx_SeoXTemplates_Helper_Template_Category::CATEGORY_DESCRIPTION:
                $comment .= '<br><p>' . $this->_getCategoryComment();
                $comment .= '<br><p>' . $this->_getCategoriesComment();
                $comment .= '<br><p>' . $this->_getParentCategoryComment();
                $comment .= '<br><p>' . $this->_getParentCategoryByLevelComment();
                $comment .= '<br><p>' . $this->_getSubcategoriesComment();
                $comment .= '<br><p>' . $this->_getWebsiteNameComment();
                $comment .= '<br><p>' . $this->_getStoreNameComment();
                $comment .= '<br><p>' . $this->_getStoreViewNameComment();

                $comment     .= '<br>' . $this->_getDynamicVariableHeader();
                $comment .= '<br><p>' . $this->_getLnAllFiltersComment();
                $comment .= '<br><p>' . $this->_getLnPersonalFiltersComment();
//                $comment .= '<br><p>' . $this->_getAdditionalComment();
                $comment .= '<br><p>' . $this->_getRandomizeComment();
                break;
            case MageWorx_SeoXTemplates_Helper_Template_Category::CATEGORY_SEO_NAME:
                $comment .= '<br>' . $this->_getCategoryComment();
                $comment .= '<br>' . $this->_getCategoriesComment();
                $comment .= '<br>' . $this->_getWebsiteNameComment();
                $comment .= '<br>' . $this->_getStoreNameComment();
                $comment .= '<br>' . $this->_getStoreViewNameComment();
                break;
            default:
                throw new Exception($this->__('SEO XTemplates: Unknow Category Template Type'));
        }

        return $comment;
    }

    protected function _getStaticVariableHeader()
    {
        return '<p><p><b><u>' . $this->__('Static Template variables:') . '</u></b>' . ' ' .
            $this->__('their values are written in product/category attributes in the backend.') . ' ' .
            $this->__('The values of randomizer feature will also be written in the attibutes.');
    }

    protected function _getDynamicVariableHeader()
    {
        return '<br><p><p><b><u>' . $this->__('Dynamic Template variables:') . '</u></b>' .
        ' <font color = "#ea7601">' . $this->__('their values will only be seen on the frontend. In the backend you’ll see the variables themselves.') . '</font>' .
        ' ' . $this->__('Here randomizer values will change with every page refresh.');
    }

    protected function _getCategoryComment()
    {
        return '<b>[category]</b> - ' . $this->__('output a current category name') . ';';
    }

    protected function _getCategoriesComment()
    {
        return '<b>[categories]</b> - ' . $this->__('output a current categories chain starting from the first parent category and ending a current category') . ';';
    }

    protected function _getParentCategoryComment()
    {
        return '<b>[parent_category]</b> - ' . $this->__('outputs parent category name') . ';';
    }
    protected function _getParentCategoryByLevelComment()
    {
        $html = '<b>[parent_category_1]</b> - ' . $this->__('outputs the 1st parent category name. It equals to [parent_category]') . ';';
        $html .= '<br>' . '<b>[parent_category_2]</b> - ' . $this->__('outputs the 2st parent category name') . ';';
        $html .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;' . $this->__('etc.');
        $html .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;' . $this->__('The orders of the parent categories is as follows: <br>&nbsp;&nbsp;&nbsp;&nbsp; yoursite/parent_category_3/parent_category_2/parent_category_1/category.html') . ';';
        return $html;
    }

    protected function _getSubcategoriesComment()
    {
        return '<b>[subcategories]</b> - ' . $this->__('output a list of subcategories for a current category') . ';';
    }

    protected function _getWebsiteNameComment()
    {
        return '<b>[website_name]</b> - ' . $this->__('output a current website name') . ';';
    }

    protected function _getStoreNameComment()
    {
        return '<b>[store_name]</b> - ' . $this->__('output a current store name') . ';';
    }

    protected function _getStoreViewNameComment()
    {
        return '<b>[store_view_name]</b> - ' . $this->__('output a current store view name') . ';';
    }

    protected function _getLnAllFiltersComment()
    {
        $string = '<b>[filter_all]</b> - ' . $this->__('inserts all chosen product attributes of LN on the category page.');
        $string .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . $this->__('Example:') . " <b>" . '[category][ – parameters: {filter_all}]' . "</b>";
        $string .= " - " . $this->__('If "color", "occasion", and "shoe size" attributes are chosen, on the frontend you will see:');
        $string .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;" .
            $this->__(
                '"Shoes – parameters: Color%sRed%sOccasion%sCasual%sShoe Size%s6.5"',
                $this->_pairSeparator,
                $this->_listSeparator,
                $this->_pairSeparator,
                $this->_listSeparator,
                $this->_pairSeparator
            );
        $string .= " - " . $this->__('If no attributes are chosen, you will see: "Shoes".');
        $string .= '<br><b>[filter_all_label]</b> - ' . $this->__('inserts all chosen product attribute labels of LN on the category page.');
        $string .= "&nbsp;&nbsp;" . $this->__('Ex:') . ' ';
        $string .=
            $this->__(
                '"Shoes – parameters: Color%sOccasion%sShoe Size"',
                $this->_listSeparator,
                $this->_listSeparator
            );
        $string .= '<br><b>[filter_all_value]</b> - ' . $this->__('inserts all chosen product attribute values of LN on the category page.');
        $string .= "&nbsp;&nbsp;" . $this->__('Ex:') . ' ';
        $string .=
            $this->__(
                '"Shoes – parameters: Red%sCasual%s6.5"',
                $this->_listSeparator,
                $this->_listSeparator
            );
        return $string;
    }

    protected function _getLnPersonalFiltersComment()
    {
        $string = '<b>[filter_<i>attribute_code</i>]</b> - ' . $this->__('insert product attribute label-value if exists.');
        $string .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . $this->__('Example:') . ' <b>[category][ in {filter_color}]</b>';
        $string .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . $this->__('Will translate to "Shoes in Color Red" on the frontend.');

        $string .= '<br><b>[filter_<i>attribute_code</i>_label]</b> - ' . $this->__('inserts mentioned product attribute label on the category LN page.');
        $string .= '<br><b>[filter_<i>attribute_code</i>_value]</b> - ' . $this->__('inserts mentioned product attribute value on the category LN page.');

        return $string;
    }

    protected function _getAdditionalComment()
    {
        $note = '<p><font color = "#ea7601">';
        $note .= $this->__('Note: The variables [%s] and [%s] will be replaced by their values Only on the front-end.', 'filter_all', "filter_<i>attribute_code</i>");
        $note .= ' ' . $this->__('So, in the backend you will still see [%s] and [%s].', 'filter_all', "filter_<i>attribute_code</i>");

        $note .= '</font>';

        return $note;
    }

    protected function _getRandomizeComment()
    {
        return '<p>Randomizer feature is available. The construction like [Buy||Order||Purchase] will use a randomly picked word.<br>
        Also randomizers can be used within other template variables, ex: [-parameters:||-filters: {filter_all}]. Number of randomizers blocks is not limited within the template.<br>';
    }
}