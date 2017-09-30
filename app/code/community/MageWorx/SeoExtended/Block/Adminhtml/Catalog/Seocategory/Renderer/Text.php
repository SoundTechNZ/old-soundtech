<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Block_Adminhtml_Catalog_Seocategory_Renderer_Text extends MageWorx_SeoExtended_Block_Adminhtml_Catalog_Seocategory_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    protected function _render(Varien_Object $row)
    {
        $html = '<input type="text" ';
        $html .= $this->_getNameString($row);
        $html .= ' value="' . $row->getData($this->getColumn()->getIndex()) . '" ';
        $html .= 'class="input-text ' . $this->getColumn()->getInlineCss() . '"/>';
        return $html;
    }
}
