<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Block_Adminhtml_Catalog_Seocategory_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('seoextended_seocategory_grid');
        $this->setDefaultSort('entity_id');
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
        $storeId = $this->_getStoreId();

        /** @var Mage_Catalog_Model_Resource_Category_Collection $collection */
        $collection = Mage::getResourceModel('catalog/category_collection');
        $collection->setStoreId($storeId);

        if((int)$storeId) {
            $rootId  = Mage::app()->getStore($storeId)->getRootCategoryId();
            $collection->addFieldToFilter('path', array('like'=> "1/$rootId/%"));
        }

        $collection
            ->addAttributeToSelect('name', 'left')
            ->addAttributeToSelect('meta_title', 'left')
            ->addAttributeToSelect('meta_description', 'left')
            ->addAttributeToSelect('meta_keywords', 'left')
            ->addFieldToFilter('level', array('nin' => '0')) //disable root categories
            ->addFieldToFilter('level', array('nin' => '1')); //disable default category

        $data = array('collection' => $collection);
        Mage::dispatchEvent('mageworx_seoextended_seocategory_grid_preparecollection', $data);

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
            'entity_id',
            array(
                'type'   => 'number',
                'header' => Mage::helper('catalog')->__('ID'),
                'align'  => 'right',
                'width'  => '50px',
                'index'  => 'entity_id'
            )
        );

        $this->addColumn(
            'category_name',
            array(
                'header'    => $helperData->__('Category Name'),
                'index'        => 'entity_id',
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

        $data = array('block' => $this);
        Mage::dispatchEvent('mageworx_seoextended_seocategory_grid_preparecolumns', $data);

        return parent::_prepareColumns();
    }

    /**
     * Prepare grid massaction actions
     *
     * @return this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('seoextended_seocategory_ids');

        $this->getMassactionBlock()->addItem(
            'mass_edit',
            array(
                'label'      => Mage::helper('mageworx_seoextended')->__('Bulk Edit'),
                'url'        => $this->getUrl('*/*/massChangePrepare', array('_current' => true)),
            )
        );

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

        $data = array('block' => $this, 'store' => $this->_getStoreId());
        Mage::dispatchEvent('mageworx_seoextended_seocategory_grid_preparemassaction', $data);

        return $this;
    }

    /**
     *
     * @param Mage_Catalog_Model_Resource_Category_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    public function filterCallback($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        $collection->addIdFilter($value);

        return $collection;
    }

    /**
     * @return int
     */
    protected function _getStoreId()
    {
        return (int)Mage::app()->getRequest()->getParam('store');
    }

    /**
     * Get url for row
     *
     * @param string $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return '';
    }

    /**
     * @param Varien_Object $item
     * @return array
     */
    public function getMultipleRows($item)
    {
        return array();
    }
}