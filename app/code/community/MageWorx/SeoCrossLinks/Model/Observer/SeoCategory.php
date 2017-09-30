<?php
 /**
  * MageWorx
  * MageWorx SeoCrossLinks Extension
  *
  * @category   MageWorx
  * @package    MageWorx_SeoCrossLinks
  * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
  */

class MageWorx_SeoCrossLinks_Model_Observer_SeoCategory
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
        if ($this->_isAttributeValidForCurrentScope()) {
            $block = $observer->getBlock();
            $block->getMassactionBlock()->addItem(
                'exclude_from_crosslinking',
                array(
                    'label' => Mage::helper('mageworx_seocrosslinks')->__('Change "Exclude from Crosslinking"'),
                    'url' => $block->getUrl(
                        '*/mageworx_seocrosslinks_seocategory/massChangeExcludeFromCrosslinking/store/',
                        array('store' => $observer->getStore())
                    ),
                    'additional' => array(
                        'visibility' => array(
                            'name' => 'exclude_from_crosslinking',
                            'type' => 'select',
                            'values'   => Mage::getSingleton('mageworx_seocrosslinks/source_yesno')->toArray()
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
                'exclude_from_crosslinking', array(
                'header' => Mage::helper('mageworx_seocrosslinks')->__('Exclude from Crosslinking'),
                'index' => 'exclude_from_crosslinking',
                'type'    => 'options',
                'options' => Mage::getSingleton('mageworx_seocrosslinks/source_yesno')->toArray()
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
            $renderer = $observer->getSelectRendererClass();

            $block->addColumn(
                'exclude_from_crosslinking', array(
                'header' => Mage::helper('mageworx_seocrosslinks')->__('Exclude from Crosslinking'),
                'type' => 'options',
                'index' => 'exclude_from_crosslinking',
                'options' => Mage::getSingleton('mageworx_seocrosslinks/source_yesno')->toArray(),
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
        $collection->addAttributeToSelect('exclude_from_crosslinking', 'left');
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
                'exclude_from_crosslinking',
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