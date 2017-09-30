<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Block_Adminhtml_Catalog_Seocategory_Edit_Tab_Massedit extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('mageworx_seoextended_catalog_seocategories');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir(Varien_Data_Collection::SORT_ORDER_ASC);
        $this->setUseAjax(false);
    }

    protected function _prepareLayout()
    {
        $message = Mage::helper('mageworx_seoextended')->getLostDataConfirmMessage();

        $resetAction  = $this->getJsObjectName().'.resetFilter()';
        $searchAction = $this->getJsObjectName().'.doFilter()';

        $this->setChild(
            'reset_filter_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label'     => Mage::helper('adminhtml')->__('Reset Filter'),
                        'onclick'   => "if( confirm('{$message}') ) {                                        
                                            $resetAction
                                        }",
                    )
                )
        );
        $this->setChild(
            'search_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label'   => Mage::helper('adminhtml')->__('Search'),
                        'onclick' => "if( confirm('{$message}') ) {                                        
                                            $searchAction
                                        }",
                        'class'   => 'task'
                    )
                )
        );
        return $this;
    }

    protected function _prepareCollection()
    {
        $ids = Mage::registry('current_seocategory_instance_ids');

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

        $collection->addIdFilter($ids);

        $data = array('collection' => $collection);
        Mage::dispatchEvent('mageworx_seoextended_seocategory_grid_preparecollection', $data);

        $this->setCollection($collection);

        $data = array('collection' => $collection);
        Mage::dispatchEvent('mageworx_seoextended_seocategory_massedit_preparecollection', $data);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id', array(
            'type'      => 'number',
            'header'    => Mage::helper('catalog')->__('ID'),
            'width'     => '60',
            'index'     => 'entity_id'
            )
        );

        $this->addColumn(
            'name',
            array(
                'header'    => Mage::helper('catalog')->__('Name'),
                'index'     => 'name',
            )
        );

        $this->addColumn(
            'meta_title',
            array(
                'header'    => Mage::helper('catalog')->__('Meta Title'),
                'index'     => 'meta_title',
                'renderer'  => $this->_getEditableTextRendererClass()
            )
        );

        $this->addColumn(
            'meta_description',
            array(
                'header'    => Mage::helper('catalog')->__('Meta Descriptioin'),
                'index'     => 'meta_description',
                'renderer'  => $this->_getEditableTextRendererClass()
            )
        );

        $this->addColumn(
            'meta_keywords',
            array(
                'header'    => Mage::helper('catalog')->__('Meta Keywords'),
                'index'     => 'meta_keywords',
                'renderer'  => $this->_getEditableTextRendererClass()
            )
        );

        $data = array(
            'block' => $this,
            'input_renderer_class' => $this->_getEditableTextRendererClass(),
            'select_renderer_class' => $this->_getEditableSelectRendererClass(),
        );

        Mage::dispatchEvent('mageworx_seoextended_seocategory_massedit_preparecolumns', $data);

        return parent::_prepareColumns();
    }

    /**
     * @param Varien_Object $item
     * @return array
     */
    public function getMultipleRows($item)
    {
        return array();
    }

    /**
     * @return string
     */
    protected function _getEditableTextRendererClass()
    {
        return 'mageworx_seoextended/adminhtml_catalog_seocategory_renderer_text';
    }

    /**
     * @return string
     */
    protected function _getEditableSelectRendererClass()
    {
        return 'mageworx_seoextended/adminhtml_catalog_seocategory_renderer_yesno';
    }

    /**
     * @return int
     */
    protected function _getStoreId()
    {
        return (int)Mage::app()->getRequest()->getParam('store');
    }
}

