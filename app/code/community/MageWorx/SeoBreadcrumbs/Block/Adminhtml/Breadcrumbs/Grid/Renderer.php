<?php
/**
 * MageWorx
 * MageWorx SeoBreadcrumbs Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBreadcrumbs
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoBreadcrumbs_Block_Adminhtml_Breadcrumbs_Grid_Renderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{
    public function render(Varien_Object $row)
    {
        $html = parent::render($row);
        $html .= '<button onclick="updatePriority(this, '. $row->getId() .'); return false">' .  Mage::helper('mageworx_seobreadcrumbs')->__('Update') . '</button>';

        return $html;
    }
}