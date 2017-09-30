<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_XSitemap_Model_Generator_SplashPage extends MageWorx_XSitemap_Model_Generator_Abstract
{
    const CODE = 'splash_page_pro';

    public function generate($storeId, $writer, $counter)
    {
        $this->_storeId = $storeId;
        $this->_storeBaseUrl = $this->_helperStore->getStoreBaseUrl($this->_storeId);

        if ($this->_helper->isFishpigAttributeSplashProGenerateEnabled()) {
            $splashProPages = $this->getFishpigAttributSplashProPages();

            if (!count($splashProPages) > 0) {
                return;
            }

            foreach ($splashProPages as $page) {
                $url = substr($page->getUrl(), strpos($page->getUrl(), $page->getUrlKey()));
                $url = $this->getStoreItemUrl($url);
                $url = $this->_helperTrailingSlash->trailingSlash('splashProPage', $url);

                $lastmode = $this->_helper->getFishpigAttributSplashProLastModifiedDate($page);
                $writer->write(
                    $url,
                    $lastmode,
                    $this->_helper->getFishpigAttributSplashProChangeFrequency(),
                    $this->_helper->getFishpigAttributSplashProPriority()
                );
            }
        }
    }

    public function getFishpigAttributSplashProPages()
    {
        $pages = Mage::getResourceModel('splash/page_collection')
            ->addStoreFilter($this->_storeId)
            ->addFieldToFilter('status', 1)
            ->load();

        return $pages;
    }
}