<?php
/**
 * MageWorx
 * MageWorx SeoBreadcrumbs Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBreadcrumbs
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBreadcrumbs_Model_Observer_SeoCategory
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
                MageWorx_SeoBreadcrumbs_Helper_Data::BREADCRUMBS_PRIORITY_CODE,
                array(
                    'label' => Mage::helper('mageworx_seobreadcrumbs')->__('Change Breadcrumbs Priority'),
                    'url' => $block->getUrl(
                        '*/mageworx_seobreadcrumbs_seocategory/massChangeBreadcrumbsPriority/store/',
                        array('store' => $observer->getStore())
                    ),
                    'additional' => array(
                        'visibility' => array(
                            'name' => MageWorx_SeoBreadcrumbs_Helper_Data::BREADCRUMBS_PRIORITY_CODE,
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
                MageWorx_SeoBreadcrumbs_Helper_Data::BREADCRUMBS_PRIORITY_CODE, array(
                'type'   => 'number',
                'header' => Mage::helper('mageworx_seobreadcrumbs')->__('Breadcrumbs priority'),
                'index'  => MageWorx_SeoBreadcrumbs_Helper_Data::BREADCRUMBS_PRIORITY_CODE,
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
                MageWorx_SeoBreadcrumbs_Helper_Data::BREADCRUMBS_PRIORITY_CODE, array(
                'header' => Mage::helper('mageworx_seobreadcrumbs')->__('Breadcrumbs Priority'),
                'type' => 'number',
                'index' => MageWorx_SeoBreadcrumbs_Helper_Data::BREADCRUMBS_PRIORITY_CODE,
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
        $collection->addAttributeToSelect(MageWorx_SeoBreadcrumbs_Helper_Data::BREADCRUMBS_PRIORITY_CODE, 'left');
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
                MageWorx_SeoBreadcrumbs_Helper_Data::BREADCRUMBS_PRIORITY_CODE,
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