<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

/**
 * Class MageWorx_XSitemap_Model_Generator_ProductByEvent
 *
 * We dispatch the standard magento event: ‘sitemap_products_generating_before’.
 * We send the empty collection, since we suppose, that it will be used for adding additional URLs,
 * but no for modifying the existed product URLs.
 *
 * You can also use 'mageworx_xsitemap_add_generator' event for adding additional URLs.
 * In this case, it is possible add own priority, changefreg and date.
 */
class MageWorx_XSitemap_Model_Generator_ProductByEvent extends MageWorx_XSitemap_Model_Generator_Abstract
{
    public function generate($storeId, $writer, $counter)
    {
        $this->_storeBaseUrl = $this->_helperStore->getStoreBaseUrl($storeId);

        $collection = new Varien_Object();
        $collection->setItems(array());

        Mage::dispatchEvent(
            'sitemap_products_generating_before', array(
            'collection' => $collection,
            'store_id' => $storeId
            )
        );

        $changefreq = $this->_helper->getProductChangeFrequency();
        $priority   = $this->_helper->getProductPriority();

        foreach ($collection as $item) {
            $writer->write(
                $this->_getItemUrl($item),
                $this->_getItemChangeDate($item),
                $changefreq,
                $priority,
                false
            );
        }
    }

    /**
     * @param Varien_Object $item
     * @return string
     */
    protected function _getItemUrl($item)
    {
        $url = $this->_storeBaseUrl . $item->getUrl();
        return $url;
        //return $this->_helperTrailingSlash->trailingSlash($this->_code, $url);
    }

    /**
     * @return string
     */
    protected function _getItemChangeDate()
    {
        return $this->_helper->getCurrentDate();
    }
}