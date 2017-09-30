<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Block_Adminhtml_System_Config_Frontend_Separator extends Mage_Core_Block_Template implements Varien_Data_Form_Element_Renderer_Interface
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $class = 'class="indent"';

        $htmlId = "row_" . $element->getHtmlId();
        $label = $element->getLabel();

        return '<tr id="' . $htmlId . '" class="system-fieldset-sub-head"><td colspan="5"><h4 ' . $class . '>' . $label . '</h4></td></tr>';
    }
}