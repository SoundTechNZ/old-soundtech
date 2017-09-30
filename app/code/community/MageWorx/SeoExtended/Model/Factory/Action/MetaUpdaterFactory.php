<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Model_Factory_Action_MetaUpdaterFactory extends MageWorx_SeoExtended_Model_Factory_Action_Abstract
{
    /**
     * @param null|string $fullActionName
     * @return MageWorx_SeoExtended_Model_MetaUpdater|null
     */
    public function getModel($fullActionName = null)
    {
        /** @var MageWorx_SeoAll_Helper_Request $helperRequest */
        $helperRequest = Mage::helper('mageworx_seoall/request');
        $fullActionName = $fullActionName ? $fullActionName : $helperRequest->getCurrentFullActionName();

        if (!$fullActionName) {
            return null;
        }

        $factoryData = $this->_getFactoryData();

        $modelUri = null;

        if (!empty($factoryData[$fullActionName])) {
            $modelUri = $factoryData[$fullActionName];
        } else {
            list($cExtension, $cController) = explode('_', $fullActionName);

            foreach ($factoryData as $key => $uri) {
                $parts = explode('_', $key);
                $count = count($parts);

                if ($count == 1) {
                    if ($cExtension == $key) {
                        $modelUri = $factoryData[$key];
                        break;
                    }
                } elseif ($count == 2) {
                    if ($cExtension . '_' . $cController == $key) {
                        $modelUri = $factoryData[$key];
                        break;
                    }
                }
            }
        }

        if (!$modelUri) {
            return null;
        }

        return Mage::getSingleton($modelUri);
    }

    /**
     * @return array
     */
    protected function _getFactoryData()
    {
        $data = array(
            'catalog_product_view'   => 'mageworx_seoextended/metaUpdater_product',
            'review_product_list'    => 'mageworx_seoextended/metaUpdater_productReviewList',
            'review_product_view'    => 'mageworx_seoextended/metaUpdater_productReview',
            'catalog_category_view'  => 'mageworx_seoextended/metaUpdater_category',
            'cms_index_index'        => 'mageworx_seoextended/metaUpdater_page',
            'cms_index_defaultIndex' => 'mageworx_seoextended/metaUpdater_page',
            'cms_page_view'          => 'mageworx_seoextended/metaUpdater_page',
            'rss'                    => 'mageworx_seoextended/metaUpdater_rss'
        );

        $container = new Varien_Object();
        $container->setData($data);

        Mage::dispatchEvent('mageworx_seoextended_metaupdater_factory', array('container' => $container));

        if (is_array($container->getData())) {
            return $container->getData();
        }

        return $data;
    }
}