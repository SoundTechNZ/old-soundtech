<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_XSitemap_Model_Observer_Generate_SplashPagePro
{
    public function addGenerator($observer)
    {
        /** @var MageWorx_XSitemap_Helper_Data $helper */
        $helper = Mage::helper('mageworx_xsitemap');

        if ($helper->isFishpigAttributeSplashProGenerateEnabled()
            && $helper->isModuleOutputEnabled('Fishpig_AttributeSplashPro'))
        {
            $container = $observer->getEvent()->getContainer();
            $generators = $container->getGenerators();

            $helper = Mage::helper('mageworx_xsitemap');
            $titleKey = MageWorx_XSitemap_Model_GeneratorFactory::TITLE_KEY;
            $modelKey = MageWorx_XSitemap_Model_GeneratorFactory::MODEL_KEY;

            $generatorName = MageWorx_XSitemap_Model_Generator_SplashPagePro::CODE;

            $generators[$generatorName] = array();
            $generators[$generatorName][$titleKey] = $helper->__('Generated FishPig Splash Pages Pro');
            $generators[$generatorName][$modelKey] = 'mageworx_xsitemap/generator_splashPagePro';

            $container->setGenerators($generators);
        }

        return $this;
    }
}