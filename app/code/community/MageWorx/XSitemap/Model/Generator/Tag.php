<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_XSitemap_Model_Generator_Tag extends MageWorx_XSitemap_Model_Generator_Abstract
{
    protected $_code = 'tag';

    public function generate($storeId, $writer, $counter)
    {
        $this->_storeId = $storeId;
        $this->_storeBaseUrl = $this->_helperStore->getStoreBaseUrl($this->_storeId);

        if ($this->_helper->isModuleOutputEnabled('Mage_Tag') && $this->_helper->isProductTagsGenerateEnabled()) {
            $changefreq = $this->_helper->getProductTagsChangeFrequency();
            $priority   = $this->_helper->getProductTagsPriority();

            $collection = Mage::getModel('tag/tag')->getPopularCollection()
                ->joinFields($this->_storeId)
                ->load();

            foreach ($collection as $item) {
                $writer->write(
                    $this->_getItemUrl($item),
                    $this->_helper->getCurrentDate(),
                    $changefreq,
                    $priority
                );
            }

            unset($collection);
        }
    }

    /**
     * @param $item
     * @return string
     */
    protected function _getItemUrl($item)
    {
        $url = str_replace($this->_storeBaseUrl . 'index.php/', $this->_storeBaseUrl, $item->getTaggedProductsUrl());

        $start = strpos($url, 'tag/');

        if ($start) {
            $url = substr_replace($url, $this->_storeBaseUrl, 0, $start);
        }

        return $this->_helperTrailingSlash->trailingSlash($this->_code, $url);
    }
}