<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_XSitemap_Model_Generator_AwBlog extends MageWorx_XSitemap_Model_Generator_Abstract
{
    const CODE = 'aw_blog';

    public function generate($storeId, $writer, $counter)
    {
        $this->_storeId = $storeId;
        $this->_storeBaseUrl = $this->_helperStore->getStoreBaseUrl($this->_storeId);

        $changefreq = (string)Mage::getStoreConfig('sitemap/blog/changefreq');

        if (!$changefreq) {
            $changefreq = $this->_helper->getBlogPriority();
        }

        $priority = (string)Mage::getStoreConfig('sitemap/blog/priority');

        if (!$priority) {
            $priority = $this->_helper->getBlogPriority();
        }

        $collection = Mage::getModel('blog/blog')->getCollection()->addStoreFilter($this->_storeId);
        Mage::getSingleton('blog/status')->addEnabledFilterToCollection($collection);
        $route = Mage::getStoreConfig('blog/blog/route', $storeId);
        if ($route == "") {
            $route = "blog";
        }

        foreach ($collection as $item) {
            $url     = htmlspecialchars($this->_storeBaseUrl . $route . '/' . $item->getIdentifier()) . '/';
            $lastmod = $this->_helper->getCurrentDate();
            $writer->write($url, $lastmod, $changefreq, $priority);
        }
    }
}