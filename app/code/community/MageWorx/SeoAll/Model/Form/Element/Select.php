<?php
/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoAll_Model_Form_Element_Select extends Varien_Data_Form_Element_Select
{
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
    }

    protected function _optionToHtml($option, $selected)
    {
        if (is_array($option['value'])) {
            $html ='<optgroup label="'.$option['label'].'">'."\n";
            foreach ($option['value'] as $groupItem) {
                $html .= $this->_optionToHtml($groupItem, $selected);
            }

            $html .='</optgroup>'."\n";
        }
        else {
            $html = '<option value="'.$this->_escape($option['value']).'"';
            $html.= isset($option['title']) ? 'title="'.$this->_escape($option['title']).'"' : '';
            $html.= isset($option['style']) ? 'style="'.$option['style'].'"' : '';
            $html.= isset($option['disabled']) ? ' disabled' : '';

            if (in_array($option['value'], $selected)) {
                $html.= ' selected="selected"';
            }

            $html.= '>'.$this->_escape($option['label']). '</option>'."\n";
        }

        return $html;
    }
}