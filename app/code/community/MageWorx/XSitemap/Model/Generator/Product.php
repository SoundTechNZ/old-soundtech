<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_XSitemap_Model_Generator_Product extends MageWorx_XSitemap_Model_Generator_Abstract
{
    protected $_code = 'product';

    /**
     * @var int
     */
    protected $_counter = 0;

    /**
     * @var int
     */
    protected $_total = 0;

    /**
     * @var array
     */
    protected $_initialEnvironmentInfo;

    public function generate($storeId, $writer, $counter)
    {
        $this->_counter = $counter;
        $this->_storeId = $storeId;
        $this->_storeBaseUrl = $this->_helperStore->getStoreBaseUrl($this->_storeId);

        $this->_total = $this->_helperFactory->getProductXmlResource()->getCollection($this->_storeId, true);

        $isProductImages     = $this->_helper->isProductImages();
        $priority            = $this->_helper->getProductPriority();
        $limit               = $this->_helper->getXmlItemsLimit();

        if ($this->_counter < $this->_total) {
            if ($this->_counter + $limit > $this->_total) {
                $limit = $this->_total - $this->_counter;
            }

            $collection = $this->_getCollection($limit);
            $this->_counter += $limit;

            $altCodes = $this->_helper->getAlternateFinalCodes($this->_code, $this->_storeId);

            $alternateUrlsCollection = $this->_getAlternateUrlCollection($altCodes, $collection);

            $this->_startEmulation($this->_storeId);

            foreach ($collection as $item) {
                $writer->write(
                    $this->_getItemUrl($item),
                    $this->_getItemChangeDate($item),
                    $this->_helper->getProductChangeFrequency(),
                    $priority,
                    $this->_getProductImageUrls($item, $isProductImages),
                    $this->_getAlternateUrls($alternateUrlsCollection, $item, $altCodes)
                );
            }

            $this->_stopEmulation();
            unset($collection);
        }
    }

    /**
     * @param array $altCodes
     * @param $collection
     * @return mixed
     */
    protected function _getAlternateUrlCollection($altCodes, $collection)
    {
        if (!$altCodes) {
            return false;
        }

        $arrayTargetPath = array();
        foreach ($collection as $val) {
            $arrayTargetPath[$val->getId()] = $val->getTargetPath();
        }

        /** @var MageWorx_SeoBase_Helper_Factory $helperSeoFactory */
        $helperSeoFactory = Mage::helper('mageworx_seobase/factory');

        $alternateResource       = $helperSeoFactory->getProductAlternateUrlResource();
        $alternateUrlsCollection = $alternateResource->getAllProductUrls(array_keys($altCodes), $arrayTargetPath);

        return $alternateUrlsCollection;
    }

    /**
     * @param Mage_Catalog_Model_Product $item
     * @return array
     */
    protected function _getProductImageUrls($item, $useProductImage)
    {
        if (!$useProductImage) {
            return false;
        }

        if ($this->_helper->isUseImageCache()) {
            $image = $item->getImage();
            $imageUrl = array();
            $imageUrl[] = Mage::helper('mageworx_xsitemap/catalog_image')->initialize($item, 'image', $image);
        } else {
            $imageUrl = array();
            $gallery  = $item->getGallery();
            if (is_array($gallery) && $gallery) {
                foreach ($gallery as $image) {
                    if ($image['file'] == $item->getImage()) {
                        $imageUrl = array($this->_getProductImageUrl($image['file']));
                        break;
                    }

                    $imageUrl[] = $this->_getProductImageUrl($image['file']);
                }

                $imageUrl = array($imageUrl[0]);
            }
        }

        return $imageUrl;
    }

    /**
     * @param string $imageFile
     * @return string
     */
    protected function _getProductImageUrl($imageFile)
    {
        /** @var MageWorx_SeoAll_Helper_Store $helperStore */
        $helperStore = Mage::helper('mageworx_seoall/store');
        $basePart = $helperStore->getStoreBaseUrlType($this->_storeId, Mage_Core_Model_Store::URL_TYPE_MEDIA);

        return $basePart . 'catalog/product' . $imageFile;
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

    /**
     * @return void
     */
    protected function _startEmulation()
    {
        if ($this->_isNeededEmulation()) {
            $appEmulation = Mage::getSingleton('core/app_emulation');
            $this->_initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($this->_storeId);
        }
    }

    /**
     * @return void
     */
    protected function _stopEmulation()
    {
        if ($this->_isNeededEmulation()) {
            $appEmulation = Mage::getSingleton('core/app_emulation');
            $appEmulation->stopEnvironmentEmulation($this->_initialEnvironmentInfo);
        }
    }

    protected function _getCollection($limit)
    {
        return $this->_helperFactory->getProductXmlResource()->getCollection(
            $this->_storeId,
            false,
            $limit,
            $this->_counter
        );
    }

    /**
     * @return bool
     */
    protected function _isNeededEmulation()
    {
        return $this->_helper->isUseImageCache();
    }

    /**
     * @param $item
     * @return string
     */
    protected function _getItemUrl($item)
    {
        //Custom canonical URL can contain 'http[s]://'
        if (strpos(trim($item->getUrl()), 'http') === 0) {
            $url = $item->getUrl();
        } else {
            $url = $this->_storeBaseUrl . $item->getUrl();
        }

        return $this->_helperTrailingSlash->trailingSlash($this->_code, $url);
    }

    /**
     * @param array $alternateUrlsCollection
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

    public function getCurrentTotal()
    {
        return $this->_total;
    }

    public function getCounter()
    {
        return $this->_counter;
    }
}