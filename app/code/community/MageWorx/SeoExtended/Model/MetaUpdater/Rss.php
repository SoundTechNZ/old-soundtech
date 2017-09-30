<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Model_MetaUpdater_Rss extends MageWorx_SeoExtended_Model_MetaUpdater
{
    /**
     * {@inheritdoc}
     */
    public function update($block)
    {
        $title               = $this->_updateTitle();
        $metaDescription     = $this->_updateMetaDescription();

        $this->_setTitle($block, $title);
        $this->_setMetaDescription($block, $metaDescription);

        return true;
    }

    /**
     * @return string
     */
    protected function _updateTitle()
    {
        return Mage::helper('mageworx_seoextended')->__('RSS Feed') . ' | ' . Mage::app()->getWebsite()->getName();
    }

    /**
     * @return string
     */
    protected function _updateMetaDescription()
    {
        return Mage::helper('mageworx_seoextended')->__('RSS Feed') . ' | ' . Mage::app()->getWebsite()->getName();
    }
}
