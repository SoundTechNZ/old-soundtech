<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_XSitemap_Model_GeneratorFactory
{
    const CATEGORY_GENERATOR_NAME = 'category';
    const PRODUCT_GENERATOR_NAME  = 'product';
    const TAG_GENERATOR_NAME      = 'tag';
    const CMS_GENERATOR_NAME      = 'page';
    const LINKS_GENERATOR_NAME    = 'additional_links';
    const BLOG_GENERATOR_NAME     = 'blog';
    const SITEMAP_FINISH_NAME     = 'sitemap_finish';

    const TITLE_KEY = 'title';
    const MODEL_KEY = 'model';

    protected $_data;

    /**
     * @return array
     */
    public function getData()
    {
        if (!$this->_data) {
            $helper = Mage::helper('mageworx_xsitemap');

            $generatorsData = array(
                self::CATEGORY_GENERATOR_NAME => array(
                    self::TITLE_KEY => $helper->__('Generated categories'),
                    self::MODEL_KEY => 'mageworx_xsitemap/generator_category'
                ),
                self::PRODUCT_GENERATOR_NAME => array(
                    self::TITLE_KEY => $helper->__('Generated products'),
                    self::MODEL_KEY => 'mageworx_xsitemap/generator_product'
                ),
                self::TAG_GENERATOR_NAME => array(
                    self::TITLE_KEY => $helper->__('Generated tags'),
                    self::MODEL_KEY => 'mageworx_xsitemap/generator_tag'
                ),
                self::CMS_GENERATOR_NAME => array(
                    self::TITLE_KEY => $helper->__('Generated CMS Pages'),
                    self::MODEL_KEY => 'mageworx_xsitemap/generator_page',
                ),
                self::LINKS_GENERATOR_NAME => array(
                    self::TITLE_KEY => $helper->__('Generated Additional links'),
                    self::MODEL_KEY => 'mageworx_xsitemap/generator_additionalLinks',
                )
            );

            $additionalGeneratorsData = array();
            $container = new Varien_Object();
            $container->setGenerators($additionalGeneratorsData);

            Mage::dispatchEvent('mageworx_xsitemap_add_generator', array('container' => $container));

            foreach ($container->getGenerators() as $key => $additionalGenerator) {
                if (in_array($key, array_keys($generatorsData))) {
                    continue;
                }

                if (!empty($additionalGenerator['title']) && !empty($additionalGenerator['model'])) {
                    $generatorsData[$key] = $additionalGenerator;
                }
            }

            $generatorsData[self::SITEMAP_FINISH_NAME] = array(
                'title' => $helper->__('Generated sitemap index'),
                'model' => '',
            );

            $this->_data = $generatorsData;
        }

        return $this->_data;
    }

    /**
     * @return string
     */
    public function getFirstEntityCode()
    {
        return self::CATEGORY_GENERATOR_NAME;
    }

    /**
     * @return string
     */
    public function getEndEntityCode()
    {
        return self::SITEMAP_FINISH_NAME;
    }
}