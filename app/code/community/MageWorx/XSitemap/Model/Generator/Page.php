<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_XSitemap_Model_Generator_Page extends MageWorx_XSitemap_Model_Generator_Abstract
{
    protected $_code = 'page';

    public function generate($storeId, $writer, $counter)
    {
        $this->_storeId = $storeId;
        $this->_storeBaseUrl = $this->_helperStore->getStoreBaseUrl($this->_storeId);

        $changefreq = $this->_helper->getPageChangeFrequency();
        $collection = Mage::getResourceModel('mageworx_xsitemap/cms_page')->getCollection($this->_storeId);

        $altCodes = $this->_helper->getAlternateFinalCodes('cms', $this->_storeId);

        foreach ($collection as $item) {
            $isHomePage = $this->_helper->isHomePage($item->getUrl());

            if ($isHomePage) {
                $isHomePage = true;
                $item->setUrl('');
                $priority = 1;
            } else {
                $priority = $this->_helper->getPagePriority();
            }

            $writer->write(
                $this->_getItemUrl($item, $isHomePage),
                $this->_helper->getCurrentDate(),
                $changefreq,
                $priority,
                false,
                $this->_getAlternateUrls($altCodes, $item, $isHomePage)
            );
        }

        unset($collection);
    }

    /**
     * @param $item
     * @param bool $isHomePage
     * @return string
     */
    protected function _getItemUrl($item, $isHomePage)
    {
        if ($isHomePage) {
            return $this->_helperTrailingSlash->trailingSlash('home', $this->_storeBaseUrl);
        }

        return $this->_helperTrailingSlash->trailingSlash($this->_code, $this->_storeBaseUrl . $item->getUrl());
    }

    /**
     * @param $item
     * @param $isHomePage
     * @return array
     */
    protected function _getAlternateUrls($altCodes, $item, $isHomePage)
    {
        $alternateUrls = array();

        if (empty($altCodes)) {
            return $alternateUrls;
        }

        $alternateUrlsCollection = Mage::getResourceModel('mageworx_seobase/hreflang_cms_page')
            ->getAllCmsUrls(array_keys($altCodes), $this->_storeId, $item->getId(), $isHomePage);

        if (empty($alternateUrlsCollection)) {
            return $alternateUrls;
        }

        if ($isHomePage) {
            $alternateUrlsCollection = array_shift($alternateUrlsCollection);
            $storeUrls = $storeUrls = $alternateUrlsCollection['alternateUrls'];

            foreach ($storeUrls as $storeId => $altUrl) {
                if (!empty($altCodes[$storeId])) {
                    $altUrl = $this->_helperTrailingSlash->trailingSlash('home', $altUrl, $storeId);
                    $alternateUrls[$altCodes[$storeId]] = $altUrl;
                }
            }
        } else {
            $storeUrls = $alternateUrlsCollection[$item->getId()]['alternateUrls'];

            foreach ($storeUrls as $storeId => $altUrl) {
                if (!empty($altCodes[$storeId])) {
                    $alternateUrls[$altCodes[$storeId]] = $altUrl;
                }
            }
        }

        return array_unique($alternateUrls);
    }
}