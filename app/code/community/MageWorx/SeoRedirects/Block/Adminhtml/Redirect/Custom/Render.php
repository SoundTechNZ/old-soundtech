<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoRedirects_Block_Adminhtml_Redirect_Custom_Render extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Varien_Object $row
     * @param string $type
     * @return string
     */
    public function getTitle(Varien_Object $row, $type)
    {
        if ($row[$type . '_entity_type_id'] == MageWorx_SeoRedirects_Model_Redirect_Custom::CUSTOM_URL_ENTITY_TYPE_ID) {
            return $row[$type . '_entity_id'];
        }

        /** @var MageWorx_SeoRedirects_Model_Source_Custom_EntityName $nameSource */
        $nameSource = Mage::getSingleton('mageworx_seoredirects/source_custom_entityName');
        $titleCollections = $nameSource->getTitleCollections();

        $collection = $titleCollections[$row[$type . '_entity_type_id']];
        $item = $collection->getItemById($row[$type . '_entity_id']);

        if (!$item) {
            return '<div style="color:red">' . $row[$type . '_entity_id'] . '<div>';
        }

        switch ($row[$type . '_entity_type_id']) {
            case MageWorx_SeoRedirects_Model_Redirect_Custom::PRODUCT_ENTITY_TYPE_ID:
                $title = $item->getName() . ';<br>sku: ' . $item->getSku();
                break;
            case MageWorx_SeoRedirects_Model_Redirect_Custom::CATEGORY_ENTITY_TYPE_ID:
                $title = $item->getName() . ';<br>id: ' .  $row[$type . '_entity_id'];
                break;
            case MageWorx_SeoRedirects_Model_Redirect_Custom::CMS_PAGE_ENTITY_TYPE_ID:
                $title = $item->getTitle();
                break;
            default:
                break;
        }

        return $title;
    }
}