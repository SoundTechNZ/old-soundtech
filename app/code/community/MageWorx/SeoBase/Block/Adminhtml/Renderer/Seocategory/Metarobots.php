<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoBase_Block_Adminhtml_Renderer_Seocategory_Metarobots extends MageWorx_SeoExtended_Block_Adminhtml_Catalog_Seocategory_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    protected function _render(Varien_Object $row)
    {
        $html = '<select ' . $this->_getNameString($row)  . '>';

        /** @var MageWorx_SeoBase_Model_Catalog_Product_Attribute_Source_Meta_Robots $metaRobotsSource */
        $metaRobotsSource = Mage::getSingleton('mageworx_seobase/catalog_product_attribute_source_meta_robots');

        foreach($metaRobotsSource->toArray(true, false) as $key => $value) {
            $selected = ($row->getData($this->getColumn()->getIndex()) == $key) ? ' selected' : '';
            $html .= '<option value="' . $key .'" ' . $selected . '>' . $value .'</option>';
        }

        $html .= '</select>';

        return $html;
    }
}
