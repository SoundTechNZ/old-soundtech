<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
abstract class MageWorx_SeoExtended_Block_Adminhtml_Catalog_Seocategory_Renderer_Abstract extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    abstract protected function _render(Varien_Object $row);

    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        return $this->_render($row);
    }

    /**
     * @param Varien_Object $row
     * @return string
     */
    protected function _getNameString(Varien_Object $row)
    {
        return 'name="category_data[' . $row->getEntityId() .'][' . $this->getColumn()->getId() . ']"';
    }
}
