<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Model_MetaUpdater_ProductReviewList extends MageWorx_SeoExtended_Model_MetaUpdater
{
    /**
     * {@inheritdoc}
     */
    public function update($block)
    {
        $title               = $this->_updateTitle($this->_getTitle($block));
        $metaDescription     = $this->_updateMetaDescription($block->getDescription());
        $metaKeywords        = $block->getKeywords();

        $this->_setTitle($block, $title, true);
        $this->_setMetaDescription($block, $metaDescription);
        $this->_setMetaKeywords($block, $metaKeywords);

        return true;
    }

    /**
     * @param string $title
     * @return string
     */
    protected function _updateTitle($title)
    {
        return Mage::helper('mageworx_seoextended')->__('Reviews for') . ' ' . $title;
    }

    /**
     * @param string $description
     * @return string
     */
    protected function _updateMetaDescription($description)
    {
        return Mage::helper('mageworx_seoextended')->__('Reviews for') . ' ' . $description;
    }
}