<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
abstract class MageWorx_XSitemap_Model_Generator_Abstract implements MageWorx_XSitemap_Model_GeneratorInterface
{
    /**
     * @var int
     */
    protected $_storeId;

    /**
     * @var string
     */
    protected $_storeBaseUrl;

    /**
     * @var MageWorx_XSitemap_Helper_Data
     */
    protected $_helper;

    /**
     * @var MageWorx_XSitemap_Helper_Factory
     */
    protected $_helperFactory;

    /**
     * @var MageWorx_SeoAll_Helper_TrailingSlash
     */
    protected $_helperTrailingSlash;

    /**
     * @var MageWorx_SeoAll_Helper_Store
     */
    protected $_helperStore;


    public function __construct()
    {
        $this->_helper = Mage::helper('mageworx_xsitemap');
        $this->_helperFactory = Mage::helper('mageworx_xsitemap/factory');
        $this->_helperStore = Mage::helper('mageworx_seoall/store');
        $this->_helperTrailingSlash = Mage::helper('mageworx_seoall/trailingSlash');
    }

    /**
     * @return mixed
     */
    public function getCurrentTotal()
    {
        return 0;
    }

    public function getCounter()
    {
        return 0;
    }
}