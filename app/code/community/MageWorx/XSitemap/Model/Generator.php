<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_XSitemap_Model_Generator
{
    /**
     * @var MageWorx_XSitemap_Model_Sitemap
     */
    protected $_model;

    /**
     * @var MageWorx_XSitemap_Helper_Data
     */
    protected $_helper;

    /**
     * @var MageWorx_XSitemap_Model_GeneratorInterface
     */
    protected $_generator;

    /**
     * @var MageWorx_XSitemap_Model_GeneratorFactory
     */
    protected $_generatorFactory;

    /**
     * @var MageWorx_XSitemap_Model_Writer
     */
    protected $_xmlWriter;

    /**
     * @var string
     */
    protected $_entityName;

    /**
     * @var int
     */
    protected $_storeId;

    /**
     * @var int
     */
    protected $_counter      = 0;

    /**
     * @var int
     */
    protected $_totalProduct = 0;


    /**
     * @param MageWorx_XSitemap_Model_Sitemap $model
     * @param string $entityName
     */
    protected function _init(MageWorx_XSitemap_Model_Sitemap $model, $entityName)
    {
        $this->_entityName          = $entityName;
        $this->_model               = $model;
        $this->_storeId             = $model->getStoreId();
        $this->_generatorFactory    = Mage::getSingleton('mageworx_xsitemap/generatorFactory');
        $this->_helper              = Mage::helper('mageworx_xsitemap');
        $this->_helper->init($this->_storeId);
        $this->_initWriter($entityName);
    }

    /**
     * @param MageWorx_XSitemap_Model_Sitemap $model
     * @param string $entityName
     * @param int $counter
     */
    public function generateXml(MageWorx_XSitemap_Model_Sitemap $model, $entityName, $counter)
    {
        $this->_init($model, $entityName);

        if (!$this->_entityName || $this->_entityName == $this->_generatorFactory->getEndEntityCode()) {
            unset($this->_xmlWriter);
            return;
        }

        $generator = $this->_getGenerator();
        $generator->generate($this->_storeId, $this->_xmlWriter, $counter);
    }

    /**
     * @param string $entityName
     */
    protected function _initWriter($entityName)
    {
        $this->_xmlWriter = Mage::getModel('mageworx_xsitemap/writer');
        $this->_xmlWriter->init(
            $this->_model->getFullPath(), $this->_model->getSitemapFilename(),
            $this->_model->getFullTempPath(), $this->_isFirstStepGeneration($entityName),
            $this->_isEndStepGeneration($entityName), $this->_getStoreBaseUrlForSitemapIndex()
        );
    }

    /**
     * @param $entityName
     * @return bool
     */
    protected function _isFirstStepGeneration($entityName)
    {
        // category - first entity name in entity name list when step by step generate xml (from GUI)
        return (!$entityName || $entityName == $this->_generatorFactory->getFirstEntityCode());
    }

    /**
     * @param string $entityName
     * @return bool
     */
    protected function _isEndStepGeneration($entityName)
    {
        return (!$entityName || $entityName == $this->_generatorFactory->getEndEntityCode());
    }

    /**
     * @return int
     */
    public function getCounter()
    {
        if (!$this->_generator) {
            return 0;
        }

        return $this->_generator->getCounter();
    }

    /**
     * @return false|Mage_Core_Model_Abstract|MageWorx_XSitemap_Model_GeneratorInterface
     */
    protected function _getGenerator()
    {
        if (!$this->_generator) {
            $generatorData = $this->_generatorFactory->getData();
            $this->_generator = Mage::getModel($generatorData[$this->_entityName]['model']);
        }

        return $this->_generator;
    }

    /**
     * @return int
     */
    public function getTotalProduct()
    {
        return $this->getCurrentTotal();
    }

    /**
     * @return int
     */
    public function getCurrentTotal()
    {
        if ($this->_generator) {
            return $this->_generator->getCurrentTotal();
        }

        return 0;
    }

    /**
     * @return string
     */
    protected function _getStoreBaseUrlForSitemapIndex()
    {
        /** @var MageWorx_SeoAll_Helper_Store $helperStoreUrl */
        $helperStoreUrl = Mage::helper('mageworx_seoall/store');

        $basePart = $helperStoreUrl->getStoreBaseUrlType($this->_storeId, Mage_Core_Model_Store::URL_TYPE_WEB);

        return $basePart . ltrim($this->_model->getSitemapPath(), '/');
    }
}