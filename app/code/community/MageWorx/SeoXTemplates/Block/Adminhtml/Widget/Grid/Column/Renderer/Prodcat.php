<?php
/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoXTemplates_Block_Adminhtml_Widget_Grid_Column_Renderer_Prodcat extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $text = array();
        $catIds = $row->getCatIds();

        /** @var MageWorx_SeoAll_Model_Source_Category $categorySource */
        $categorySource = Mage::getSingleton('mageworx_seoall/source_category');
        $allCats = $categorySource->toArray();

        if ($catIds && is_string($catIds)) {
            foreach (explode(',', $catIds) as $id) {
                if (isset($allCats[$id])) {
                    $text[] = str_replace(array('&nbsp;', '--'), '', $allCats[$id]);
                }
            }
        }

        return implode(', ', $text);
    }
}