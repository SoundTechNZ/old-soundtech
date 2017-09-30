<?php
/**
 * MageWorx
 * MageWorx SeoBreadcrumbs Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBreadcrumbs
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoBreadcrumbs_Block_Adminhtml_Breadcrumbs_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _construct()
    {
        $this->setId('breadcrumbsGrid');
        $this->_controller = 'adminhtml_breadcrumbs';
        $this->setUseAjax(true);
        $this->setDefaultSort('breadcrumbs_priority');
        $this->setDefaultDir('desc');
    }
 
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('catalog/category_collection')
        ->addAttributeToSelect('name', 'left')
        ->addAttributeToSelect('is_active', 'left')
        ->addAttributeToSelect('url_path', 'left')
        ->addAttributeToSelect('breadcrumbs_priority', 'left')
        ->addFieldToFilter('level', array('nin' => '0')) //disable root categories
        ->addFieldToFilter('level', array('nin' => '1')); //disable default category
        $this->setCollection($collection);
 
        return parent::_prepareCollection();
        return $this;
    }
 
    protected function _prepareColumns()
    {
        $helper = Mage::helper('mageworx_seobreadcrumbs');

        $this->addColumn(
            'category_id', array(
            'header' => $helper->__('ID'),
            'index'  => 'entity_id',
            'width'  => '60'
            )
        );

        $this->addColumn(
            'name', array(
            'header'        => $helper->__('Title'),
            'align'         => 'left',
            'filter_index'  => 'name',
            'index'         => 'name',
            'type'          => 'text',
            'truncate'      => 50,
            'escape'        => true,
            )
        );

        $this->addColumn(
            'name', array(
            'header'       => $helper->__('Category Name'),
            'index'        => 'name',
            'width'  => '800'
            )
        );

        $this->addColumn(
            'url_path', array(
            'header' => $helper->__('URL Path'),
            'index'  => 'url_path',
            'width'  => '350'
            )
        );

        $this->addColumn(
            'path', array(
            'header' => $helper->__('Path'),
            'index'  => 'path',
            'width'  => '200'
            )
        );

        $this->addColumn(
            'level', array(
            'header'       => $helper->__('Level'),
            'index'        => 'level',
            'width'  => '60'
            )
        );
        $this->addColumn(
            'breadcrumbs_priority', array(
            'header' => $helper->__('Breadcrumbs priority'),
            'renderer' => 'mageworx_seobreadcrumbs/adminhtml_breadcrumbs_grid_renderer',
            'index'  => 'breadcrumbs_priority',
            'width'  => '100'
            )
        );

        return parent::_prepareColumns();
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}
