<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Observer_SeoCategory
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

            /** @var MageWorx_SeoBase_Model_Catalog_Product_Attribute_Source_Meta_Robots $metaRobotsSource */
            $metaRobotsSource = Mage::getSingleton('mageworx_seobase/catalog_product_attribute_source_meta_robots');

            $block->getMassactionBlock()->addItem(
                'meta_robots',
                array(
                    'label' => Mage::helper('mageworx_seobase')->__('Change Meta Robots'),
                    'url' => $block->getUrl(
                        '*/mageworx_seobase_seocategory/massChangeMetaRobots/store/',
                        array('store' => $observer->getStore())
                    ),
                    'additional' => array(
                        'visibility' => array(
                            'name' => 'meta_robots',
                            'type' => 'select',
                            'values' => $metaRobotsSource->toArray()
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

            /** @var MageWorx_SeoBase_Model_Catalog_Product_Attribute_Source_Meta_Robots $metaRobotsSource */
            $metaRobotsSource = Mage::getSingleton('mageworx_seobase/catalog_product_attribute_source_meta_robots');

            $block->addColumn(
                'meta_robots', array(
                'header' => Mage::helper('mageworx_seobase')->__('Category Meta Robots'),
                'index'  => 'meta_robots',
                'type'    => 'options',
                'options' => $metaRobotsSource->toArray()
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

            /** @var MageWorx_SeoBase_Model_Catalog_Product_Attribute_Source_Meta_Robots $metaRobotsSource */
            $metaRobotsSource = Mage::getSingleton('mageworx_seobase/catalog_product_attribute_source_meta_robots');

            $block->addColumn(
                'meta_robots', array(
                'header' => Mage::helper('mageworx_seobase')->__('Category Meta Robots'),
                'type' => 'options',
                'index' => 'meta_robots',
                'options' => $metaRobotsSource->toArray(),
                'renderer' => 'mageworx_seobase/adminhtml_renderer_seocategory_metarobots'
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
        $collection->addAttributeToSelect('meta_robots', 'left');
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
                'meta_robots',
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