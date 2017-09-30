<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Block_Adminhtml_Catalog_Seocategory_Renderer_Yesno extends MageWorx_SeoExtended_Block_Adminhtml_Catalog_Seocategory_Renderer_Abstract
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

        $selectedYes = ($row->getData($this->getColumn()->getIndex()) == '1') ? ' selected' : '';
        $selectedNo  = ($row->getData($this->getColumn()->getIndex()) == '0') ? ' selected' : '';

        $html .= '
            <option value="0" ' . $selectedNo . '>' . Mage::helper('catalog')->__('No') .' </option>
            <option value="1" ' . $selectedYes . '>' . Mage::helper('catalog')->__('Yes') .' </option>
        </select>';

        return $html;
    }
}
