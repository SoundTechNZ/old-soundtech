<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_XSitemap_Model_Generator_AdditionalLinks extends MageWorx_XSitemap_Model_Generator_Abstract
{
    protected $_code = 'additional_links';

    public function generate($storeId, $writer, $counter)
    {
        $this->_storeId = $storeId;
        $this->_storeBaseUrl = $this->_helperStore->getStoreBaseUrl($this->_storeId);

        $changefreq = $this->_helper->getLinkChangeFrequency();
        $priority   = $this->_helper->getLinkPriority();

        $addLinks   = array_filter(
            preg_split('/\r?\n/', $this->_helper->getAdditionalLinksForXmlSitemap($this->_storeId))
        );

        if (count($addLinks)) {
            foreach ($addLinks as $link) {
                if (strpos($link, ',') !== false) {
                    list($link) = explode(',', $link);
                }

                $link = trim($link);
                if (strpos($link, 'http') !== false) {
                    $links[] = new Varien_Object(array('url' => $link));
                }
                else {
                    list($url) = explode("/?", Mage::getModel('core/store')->load($this->_storeId)->getUrl((string) $link));
                    $links[] = new Varien_Object(array('url' => $url));
                }
            }
        }

        if (!empty($links)) {
            foreach ($links as $item) {
                $url     = $this->_helperTrailingSlash->trailingSlash('link', $item->getUrl());
                $lastmod = $this->_helper->getCurrentDate();
                $writer->write($url, $lastmod, $changefreq, $priority);
            }

            unset($links);
        }
    }
}