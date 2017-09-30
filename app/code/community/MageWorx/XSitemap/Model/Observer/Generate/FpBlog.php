<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_XSitemap_Model_Observer_Generate_FpBlog
{
    public function addGenerator($observer)
    {
        /** @var MageWorx_XSitemap_Helper_Data $helper */
        $helper = Mage::helper('mageworx_xsitemap');

        if ($helper->isBlogGenerateEnabled() && $helper->isModuleOutputEnabled('AW_Blog')) {
            $container = $observer->getEvent()->getContainer();
            $generators = $container->getGenerators();

            $helper = Mage::helper('mageworx_xsitemap');
            $titleKey = MageWorx_XSitemap_Model_GeneratorFactory::TITLE_KEY;
            $modelKey = MageWorx_XSitemap_Model_GeneratorFactory::MODEL_KEY;

            $generatorName = MageWorx_XSitemap_Model_Generator_FpBlog::CODE;

            $generators[$generatorName] = array();
            $generators[$generatorName][$titleKey] = $helper->__('Generated AW Blog');
            $generators[$generatorName][$modelKey] = 'mageworx_xsitemap/generator_awBlog';

            $container->setGenerators($generators);
        }

        return $this;
    }
}