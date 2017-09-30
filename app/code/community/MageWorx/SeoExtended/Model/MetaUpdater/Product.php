<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Model_MetaUpdater_Product extends MageWorx_SeoExtended_Model_MetaUpdater
{
    /**
     * {@inheritdoc}
     */
    public function update($block)
    {
        $title               = $this->_getTitle($block);
        $metaDescription     = $block->getDescription();
        $metaKeywords        = $block->getKeywords();

        $this->_setTitle($block, $title, true);
        $this->_setMetaDescription($block, $metaDescription);
        $this->_setMetaKeywords($block, $metaKeywords);

        return true;
    }
}
