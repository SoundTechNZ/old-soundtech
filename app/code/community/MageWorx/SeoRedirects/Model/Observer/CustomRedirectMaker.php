<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoRedirects_Model_Observer_CustomRedirectMaker extends Mage_Core_Model_Abstract
{
    /**
     *
     * @param type $observer
     * @return null
     */
    public function redirect($observer)
    {
        if (!Mage::helper('mageworx_seoredirects')->isCustomRedirectEnabled()) {
            return;
        }

        Mage::getModel('mageworx_seoredirects/redirect_custom')->rewrite();
    }
}