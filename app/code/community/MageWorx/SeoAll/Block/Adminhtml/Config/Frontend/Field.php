<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoAll_Block_Adminhtml_Config_Frontend_Field extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $fieldConfig = $element->getFieldConfig();

        if ($fieldConfig->separator == 1) {
            if (!$fieldConfig->level) {
                return '<tr id="row_' . $element->getHtmlId() . '" class="system-fieldset-sub-head"><td colspan="5"><h4>' . $element->getLabel() . '</h4></td></tr>';
            } elseif ($fieldConfig->level == 1) {
                return '<tr id="row_' . $element->getHtmlId() . '" class="system-fieldset-sub-head"><td colspan="5"><h5>' . $element->getLabel() . '</h5></td></tr>';
            }
        }

        return parent::render($element);
    }

    public function getInfo()
    {
        return array('mageworx_seo_richsnippets_website_rs_main_only' => array('title' => 'ddd', 'comment' => 'qwer'));
    }

    /**
     * Decorate field row html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @param string $html
     * @return string
     */
    protected function _decorateRowHtml($element, $html)
    {
        $fieldConfig = $element->getFieldConfig();
        $level = (int)$fieldConfig->level;

        if ($level) {
            return '<tr class="' . 'indent_' . $level . '" id="row_' . $element->getHtmlId() . '">' . $html . '</tr>';
        }

        return parent::_decorateRowHtml($element, $html);
    }
}