<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Block_Adminhtml_Catalog_Category_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('seoextended_category_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir(Varien_Data_Collection::SORT_ORDER_ASC);
        $this->setSaveParametersInSession(true);
    }

    /**
     * Set collection of grid
     *
     * @return this
     */
    protected function _prepareCollection()
    {
        /** @var MageWorx_SeoExtended_Model_Resource_Catalog_Category_Collection $collection */
        $collection = Mage::getResourceModel('mageworx_seoextended/catalog_category_collection');
        $collection->addFieldToFilter('store_id', $this->_getStoreId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare and add columns to grid
     *
     * @return this
     */
    protected function _prepareColumns()
    {
        $helperData = Mage::helper('mageworx_seoextended');

        $this->addColumn(
            'id',
            array(
                'header' => Mage::helper('catalog')->__('ID'),
                'align'  => 'right',
                'width'  => '50px',
                'index'  => 'id'
            )
        );

        $this->addColumn(
            'attribute_id',
            array(
                'header'    => $helperData->__('Product Attribute'),
                'width' => '250px',
                'type'  => 'options',
                'options' => Mage::getSingleton('mageworx_seoall/source_product_attribute')->toArray(),
                'index'  => 'attribute_id'
            )
        );

        $this->addColumn(
            'category_name',
            array(
                'header'    => $helperData->__('Category Name'),
                'index'        => 'category_id',
                'sortable'    => false,
                'width' => '250px',
                'type'  => 'options',
                'options'    => Mage::getSingleton('mageworx_seoall/source_category')->toArray(),
                'filter_condition_callback' => array($this, 'filterCallback'),
            ), 'category_name'
        );

        $this->addColumn(
            'meta_title',
            array(
                'header'    => $helperData->__('Meta Title'),
                'index'  => 'meta_title',
            )
        );

        $this->addColumn(
            'meta_description',
            array(
                'header'    => $helperData->__('Meta Description'),
                'index'  => 'meta_description',
            )
        );

        $this->addColumn(
            'meta_keywords',
            array(
                'header'    => $helperData->__('Meta Keywords'),
                'index'  => 'meta_keywords',
            )
        );

        /** Use only for support */
        if (false) {
            $this->addColumn(
                'description',
                array(
                    'header'    => $helperData->__('Description'),
                    'index'  => 'description',
                )
            );
        }

        return parent::_prepareColumns();
    }

    /**
     *
     * @param MageWorx_SeoExtended_Model_Resource_Catalog_Category_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return MageWorx_SeoExtended_Model_Resource_Catalog_Category_Collection
     */
    public function filterCallback($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        $collection->addFieldToFilter('category_id', $value);

        return $collection;
    }

    /**
     * Prepare grid massaction actions
     *
     * @return this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('seoextended_category_ids');

        $this->getMassactionBlock()->addItem(
            'meta_title',
            array(
                'label'      => Mage::helper('mageworx_seoextended')->__('Change Meta Title'),
                'url'        => $this->getUrl('*/*/massChangeMetaTitle', array('_current' => true)),
                'additional' => array(
                    'meta_title' => array(
                        'name'     => 'meta_title',
                        'type'     => 'text',
                        'style'    => 'width:400px'
                    )
                )
            )
        );

        $this->getMassactionBlock()->addItem(
            'meta_description',
            array(
                'label'      => Mage::helper('mageworx_seoextended')->__('Change Meta Description'),
                'url'        => $this->getUrl('*/*/massChangeMetaDescription', array('_current' => true)),
                'additional' => array(
                    'meta_description' => array(
                        'name'     => 'meta_description',
                        'type'     => 'textarea',
                        'style'    => 'width:400px'
                    )
                )
            )
        );

        $this->getMassactionBlock()->addItem(
            'meta_keywords',
            array(
                'label'      => Mage::helper('mageworx_seoextended')->__('Change Meta Keywords'),
                'url'        => $this->getUrl('*/*/massChangeMetaKeywords', array('_current' => true)),
                'additional' => array(
                    'meta_keywords' => array(
                        'name'     => 'meta_keywords',
                        'type'     => 'text',
                        'style'    => 'width:400px'
                    )
                )
            )
        );

        /** Use only for support */
        if (false) {
            $this->getMassactionBlock()->addItem(
                'description',
                array(
                    'label' => Mage::helper('mageworx_seoextended')->__('Change Description'),
                    'url' => $this->getUrl('*/*/massChangeDescription', array('_current' => true)),
                    'additional' => array(
                        'description' => array(
                            'name' => 'description',
                            'type' => 'textarea',
                            'style' => 'width:400px'
                        )
                    )
                )
            );
        }

        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'   => Mage::helper('catalog')->__('Delete'),
                'url'     => $this->getUrl('*/*/massDelete', array('_current' => true)),
                'confirm' => Mage::helper('mageworx_seoextended')->__('Are you sure you want to do this?')
            )
        );

        return $this;
    }

    /**
     * Get url for row
     *
     * @param string $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId(), 'store' => $row->getStoreId()));
    }

    /**
     * @return int
     */
    protected function _getStoreId()
    {
        return (int)Mage::app()->getRequest()->getParam('store');
    }
}