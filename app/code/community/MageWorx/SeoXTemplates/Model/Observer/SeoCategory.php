<?php
/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoXTemplates_Model_Observer_SeoCategory
{
    /**
     * @var bool
     */
    protected $_isValidScope;

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function addMassactionToSeoCategoryGrid($observer)
    {
        if ($this->_isAttributeValidForCurrentScope()) {
            $block = $observer->getBlock();
            $block->getMassactionBlock()->addItem(
                'category_seo_name',
                array(
                    'label' => Mage::helper('mageworx_seoxtemplates')->__('Change Category SEO Name'),
                    'url' => $block->getUrl(
                        '*/mageworx_seoxtemplates_seocategory/massChangeCategorySeoName/store/',
                        array('store' => $observer->getStore())
                    ),
                    'additional' => array(
                        'visibility' => array(
                            'name' => 'category_seo_name',
                            'type' => 'text',
                        )
                    )
                )
            );
        }

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function addColumnToSeoCategoryGrid($observer)
    {
        if ($this->_isAttributeValidForCurrentScope()) {
            $block = $observer->getBlock();
            $block->addColumn(
                'category_seo_name', array(
                'type' => 'text',
                'header' => Mage::helper('mageworx_seoxtemplates')->__('Category SEO Name'),
                'index'  => 'category_seo_name',
                )
            );
        }

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function addColumnToSeoCategoryMassEdit($observer)
    {
        if ($this->_isAttributeValidForCurrentScope()) {
            $block = $observer->getBlock();
            $renderer = $observer->getInputRendererClass();

            $block->addColumn(
                'category_seo_name', array(
                'header' => Mage::helper('mageworx_seoxtemplates')->__('Category SEO Name'),
                'type' => 'text',
                'index' => 'category_seo_name',
                'renderer' => $renderer
                )
            );
        }

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function addFieldToSeoCategoryGrid($observer)
    {
        $collection = $observer->getCollection();
        $collection->addAttributeToSelect('category_seo_name', 'left');
        return $this;
    }

    /**
     * @return int
     */
    protected function _getStoreId()
    {
        return (int)Mage::app()->getRequest()->getParam('store');
    }

    /**
     * @return bool
     */
    protected function _isAttributeValidForCurrentScope()
    {
        if ($this->_isValidScope === null) {
            /** @var MageWorx_SeoAll_Helper_Attribute $helperAttribute */
            $helperAttribute = Mage::helper('mageworx_seoall/attribute');

            if ($helperAttribute->isAttributeValidForCurrentScope(
                Mage_Catalog_Model_Category::ENTITY,
                'category_seo_name',
                $this->_getStoreId()
            )) {
                $this->_isValidScope = true;
            } else {
                $this->_isValidScope = false;
            }
        }

        return $this->_isValidScope;
    }
}