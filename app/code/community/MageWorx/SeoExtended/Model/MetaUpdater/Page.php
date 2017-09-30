<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Model_MetaUpdater_Page extends MageWorx_SeoExtended_Model_MetaUpdater
{
    /**
     * {@inheritdoc}
     */
    public function update($block)
    {
        $title               = $this->_getTitle($block);
        $metaDescription     = $this->_updateMetaDescription($block->getDescription());
        $metaKeywords        = $this->_updateMetaKeywords($block->getKeywords());

        $this->_setTitle($block, $title);
        $this->_setMetaDescription($block, $metaDescription);
        $this->_setMetaKeywords($block, $metaKeywords);
    }

    /**
     * @param Mage_Page_Block_Html_Head $block
     * @return string
     */
    protected function _getTitle($block)
    {
        if (Mage::getSingleton('cms/page')->getMetaTitle()) {
            $title = Mage::getSingleton('cms/page')->getMetaTitle();
        }
        elseif (Mage::getSingleton('cms/page')->getData('published_revision_id')) {
            $collection = Mage::getResourceModel('enterprise_cms/page_revision_collection');
            $collection->getSelect()->where(
                'revision_id=?',
                Mage::getSingleton('cms/page')->getData('published_revision_id')
            );
            $pageData   = $collection->getFirstItem();

            if (!$pageData->getMetaTitle()) {
                $title = $pageData->getMetaTitle();
            }
        } else {
            $title = parent::_getTitle($block);
        }

        return $title;
    }

    /**
     * @param string $description
     * @return string
     */
    protected function _updateMetaDescription($description)
    {
        if ($metaDescription = Mage::getSingleton('cms/page')->getMetaDescription()) {
            return $metaDescription;
        }

        return $description;
    }

    /**
     * @param string $keywords
     * @return string
     */
    protected function _updateMetaKeywords($keywords)
    {
        if ($metaKeywords = Mage::getSingleton('cms/page')->getKeywords()) {
            return $metaKeywords;
        }

        return $keywords;
    }

}