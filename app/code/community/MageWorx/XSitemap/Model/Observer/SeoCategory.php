<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_XSitemap_Model_Observer_SeoCategory
{
    /**
     * @var bool|null
     */
    protected $_isValidScope;

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function addMassactionToSeoCategoryGrid($observer)
    {
        if ($this->_isAttributeValidForCurrentScope('exclude_from_sitemap')) {
            $block = $observer->getBlock();
            $block->getMassactionBlock()->addItem(
                'exclude_from_sitemap',
                array(
                    'label' => Mage::helper('mageworx_xsitemap')->__('Change "Exclude from XML Sitemap"'),
                    'url' => $block->getUrl(
                        '*/mageworx_seocategory/massChangeExcludeFromXmlSitemap/store/',
                        array('store' => $observer->getStore())
                    ),
                    'additional' => array(
                        'visibility' => array(
                            'name' => 'exclude_from_sitemap',
                            'type' => 'select',
                            'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray()
                        )
                    )
                )
            );
        }

        if ($this->_isAttributeValidForCurrentScope('exclude_from_html_sitemap')) {
            $block->getMassactionBlock()->addItem(
                'exclude_from_html_sitemap',
                array(
                    'label' => Mage::helper('mageworx_xsitemap')->__('Change "Exclude from HTML Sitemap"'),
                    'url' => $block->getUrl(
                        '*/mageworx_seocategory/massChangeExcludeFromHtmlSitemap/store/',
                        array('store' => $observer->getStore())
                    ),
                    'additional' => array(
                        'visibility' => array(
                            'name' => 'exclude_from_html_sitemap',
                            'type' => 'select',
                            'values'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray()
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
        if ($this->_isAttributeValidForCurrentScope('exclude_from_sitemap')) {
            $block = $observer->getBlock();
            $block->addColumn(
                'exclude_from_sitemap', array(
                'header' => Mage::helper('mageworx_xsitemap')->__('Exclude from XML Sitemap'),
                'index' => 'exclude_from_sitemap',
                'type'    => 'options',
                'options' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray()
                )
            );
        }

        if ($this->_isAttributeValidForCurrentScope('exclude_from_html_sitemap')) {
            $block = $observer->getBlock();
            $block->addColumn(
                'exclude_from_html_sitemap', array(
                'header' => Mage::helper('mageworx_xsitemap')->__('Exclude from HTML Sitemap'),
                'index' => 'exclude_from_html_sitemap',
                'type'    => 'options',
                'options' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray()
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
        if ($this->_isAttributeValidForCurrentScope('exclude_from_sitemap')) {
            $block = $observer->getBlock();
            $renderer = $observer->getSelectRendererClass();

            $block->addColumn(
                'exclude_from_sitemap', array(
                'header' => Mage::helper('mageworx_xsitemap')->__('Exclude from XML Sitemap'),
                'type' => 'options',
                'index' => 'exclude_from_sitemap',
                'options' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray(),
                'renderer' => $renderer
                )
            );
        }

        if ($this->_isAttributeValidForCurrentScope('exclude_from_html_sitemap')) {
            $block = $observer->getBlock();
            $renderer = $observer->getSelectRendererClass();

            $block->addColumn(
                'exclude_from_html_sitemap', array(
                'header' => Mage::helper('mageworx_xsitemap')->__('Exclude from HTML Sitemap'),
                'type' => 'options',
                'index' => 'exclude_from_html_sitemap',
                'options' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray(),
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
        $collection->addAttributeToSelect('exclude_from_sitemap', 'left');
        $collection->addAttributeToSelect('exclude_from_html_sitemap', 'left');

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
     * @param string $attributeCode
     * @return bool
     */
    protected function _isAttributeValidForCurrentScope($attributeCode)
    {
        /** @var MageWorx_SeoAll_Helper_Attribute $helperAttribute */
        $helperAttribute = Mage::helper('mageworx_seoall/attribute');

        if ($helperAttribute->isAttributeValidForCurrentScope(
            Mage_Catalog_Model_Category::ENTITY,
            $attributeCode,
            $this->_getStoreId()
        )) {
            return true;
        }

        return false;
    }
}