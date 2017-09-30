<?php
 /**
  * MageWorx
  * MageWorx_SeoRedirects Extension
  *
  * @category   MageWorx
  * @package    MageWorx_SeoRedirects
  * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
  */
 class MageWorx_SeoRedirects_Model_Observer_SeoCategory
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
                'redirect_priority',
                array(
                    'label' => Mage::helper('mageworx_seoredirects')->__('Change Redirect Priority'),
                    'url' => $block->getUrl(
                        '*/mageworx_seoredirects_seocategory/massChangeRedirectPriority/store/',
                        array('store' => $observer->getStore())
                    ),
                    'additional' => array(
                        'visibility' => array(
                            'name' => 'redirect_priority',
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
                'redirect_priority', array(
                'type'   => 'number',
                'header' => Mage::helper('mageworx_seoredirects')->__('Redirect Priority'),
                'index'  => 'redirect_priority',
                'width'  => '100'
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
                'redirect_priority', array(
                'header' => Mage::helper('mageworx_seoredirects')->__('Redirect Priority'),
                'type' => 'number',
                'index' => 'redirect_priority',
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
        $collection->addAttributeToSelect('redirect_priority', 'left');
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
                'redirect_priority',
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