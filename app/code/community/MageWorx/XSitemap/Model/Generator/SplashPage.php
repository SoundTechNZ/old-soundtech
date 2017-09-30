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
    const CODE = 'splash_page';

    public function generate($storeId, $writer, $counter)
    {
        $this->_storeId = $storeId;
        $this->_storeBaseUrl = $this->_helperStore->getStoreBaseUrl($this->_storeId);

        if ($this->_helper->isFishpigAttributeSplashGenerateEnabled()) {
            $splashPages = $this->getFishpigAttributSplashPages();

            if (count($splashPages) > 0) {
                foreach ($splashPages as $page) {
                    $page->setStoreId($this->_storeId);
                    $url = $this->_helperTrailingSlash->trailingSlash('splashPage', $page->getUrl());
                    $lastmode = $page->getUpdatedAt(false) ? $page->getUpdatedAt(false) : $this->_getDate();
                    $writer->write(
                        $url,
                        $lastmode,
                        $this->_helper->getFishpigAttributeSplashPageFrequency(),
                        $this->_helper->getFishpigAttributeSplashPagePriority()
                    );
                }
            }
        }

        if ($this->_helper->isFishpigAttributeSplashGroupPagesEnabled()) {
            $splashGroups = $this->getFishpigAttributSplashGroups();

            if (!count($splashGroups)) {
                return;
            }

            foreach ($splashGroups as $group) {
                if (!$group->canDisplay()) {
                    continue;
                }

                if (Mage::app()->isSingleStoreMode() || $group->getStoreId() == $this->_storeId) {
                    $lastmode = $group->getUpdatedAt(false) ? $group->getUpdatedAt(false) : $this->_getDate();
                    $url      = $this->_helperTrailingSlash->trailingSlash('splashGroup', $group->getUrl());
                    $writer->write(
                        $url,
                        $lastmode,
                        $this->_helper->getFishpigAttributeSplashGroupFrequency(),
                        $this->_helper->getFishpigAttributeSplashGroupPriority()
                    );
                }
            }
        }
    }

    /**
     * Retrieve a collection of splash pages for the sitemap
     *
     * @return Fishpig_AttributeSplash_Model_Resource_Page_Collection
     */
    public function getFishpigAttributSplashPages()
    {
        $pages = Mage::getResourceModel('attributeSplash/page_collection')
            ->addIsEnabledFilter()
            ->addStoreIdFilter($this->_storeId)
            ->load();
        return $pages;
    }

    /**
     * Retrieve a collection of splash groups for the sitemap
     *
     * @return Fishpig_AttributeSplash_Model_Resource_Page_Collection
     */
    public function getFishpigAttributSplashGroups()
    {
        $pages = Mage::getResourceModel('attributeSplash/group_collection')
            ->addIsEnabledFilter()
            ->load();

        return $pages;
    }
}