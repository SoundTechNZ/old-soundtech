<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_XSitemap_Model_Generator_Category extends MageWorx_XSitemap_Model_Generator_Abstract
{
    protected $_code = 'category';

    public function generate($storeId, $writer, $counter)
    {
        $this->_storeId = $storeId;
        $this->_storeBaseUrl = $this->_helperStore->getStoreBaseUrl($this->_storeId);

        $changefreq = $this->_helper->getCategoryChangeFrequency();
        $priority   = $this->_helper->getCategoryPriority();

        $collection = $this->_getCollection();
        $altCodes   = $this->_helper->getAlternateFinalCodes($this->_code, $this->_storeId);

        $alternateUrlsCollection = $this->_getAlternateUrlCollection($altCodes);

        foreach ($collection as $item) {
            $model = Mage::getModel('catalog/category')->load($item->getId());

            $writer->write(
                $this->_getItemUrl($item),
                $this->_getItemChangeDate($model),
                $changefreq,
                $priority,
                false,
                $this->_getAlternateUrls($alternateUrlsCollection, $item, $altCodes)
            );
        }

        unset($collection);
    }

    protected function _getCollection()
    {
        return $this->_helperFactory->getCategoryXmlResource()->getCollection($this->_storeId);
    }

    /**
     * @param array $altCodes
     * @return mixed
     */
    protected function _getAlternateUrlCollection($altCodes)
    {
        if (!$altCodes) {
            return false;
        }

        /** @var MageWorx_SeoBase_Helper_Factory $helperSeoFactory */
        $helperSeoFactory = Mage::helper('mageworx_seobase/factory');

        $alternateResource = $helperSeoFactory->getCategoryAlternateUrlResource();

        return $alternateResource->getAllCategoryUrls(array_keys($altCodes));
    }

    /**
     * @param $alternateUrlsCollection
     * @param $item
     * @param $altCodes
     * @return array
     */
    protected function _getAlternateUrls($alternateUrlsCollection, $item, $altCodes)
    {
        if (!empty($alternateUrlsCollection[$item->getId()])) {
            $storeUrls = $alternateUrlsCollection[$item->getId()]['alternateUrls'];
            $alternateUrls = array();
            foreach ($storeUrls as $storeId => $altUrl) {
                if (!empty($altCodes[$storeId])) {
                    $alternateUrls[$altCodes[$storeId]] = $altUrl;
                }
            }
        }

        return !empty($alternateUrls) ? array_unique($alternateUrls) : array();
    }

    /**
     * @param $item
     * @return string
     */
    protected function _getItemUrl($item)
    {
        $url = $this->_storeBaseUrl . $item->getUrl();
        return $this->_helperTrailingSlash->trailingSlash($this->_code, $url);
    }

    /**
     * @param $model
     * @return string
     */
    protected function _getItemChangeDate($model)
    {
        $upTime = $model->getUpdatedAt();
        if ($upTime == '0000-00-00 00:00:00') {
            $upTime = $model->getCreatedAt();
        }

        $upTime = substr($upTime, 0, 10);

        if (!$upTime) {
            $upTime = $this->_helper->getCurrentDate();
        }

        return $upTime;
    }
}