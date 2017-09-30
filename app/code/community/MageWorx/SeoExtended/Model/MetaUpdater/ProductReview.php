<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Model_MetaUpdater_ProductReview extends MageWorx_SeoExtended_Model_MetaUpdater
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
        $product = Mage::registry('current_product');
        $review  = Mage::registry('current_review');
        if ($product && $review) {
            $title = Mage::helper('mageworx_seoextended')->__('Review for') . ' ' . $product->getName();
            $author = $review->getNickname();
            if ($author) {
                $title .= ' ' . Mage::helper('mageworx_seoextended')->__('by') . ' ' . $author;
            }
        }

        return $title;
    }

    /**
     * @param string $description
     * @return string
     */
    protected function _updateMetaDescription($description)
    {
        $review = Mage::registry('current_review');

        if ($review) {
            $reviewTitle = $review->getTitle() ? ': ' . $review->getTitle() : '';
            $description = $this->_updateTitle('') . $reviewTitle;
        }

        return $description;
    }
}