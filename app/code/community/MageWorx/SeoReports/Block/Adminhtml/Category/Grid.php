<?php
/**
 * MageWorx
 * MageWorx SeoReports Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoReports
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoReports_Block_Adminhtml_Category_Grid extends MageWorx_SeoReports_Block_Adminhtml_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('category_id');
        $this->setDefaultSort('id');
    }

    protected function _prepareCollection()
    {
        $store      = $this->_getStore();
        $maxLengthMetaTitle = Mage::helper('mageworx_seoreports/config')->getMaxLengthMetaTitle();
        $maxLengthMetaDescription = Mage::helper('mageworx_seoreports/config')->getMaxLengthMetaDescription();

        $collection = Mage::getResourceModel('mageworx_seoreports/category_collection');
        if ($store) {
            $collection->addFieldToFilter('store_id', $store);
        }
        else {
            $collection->getSelect()->where('store_id <>?', 0);
        }

        $collection->getSelect()->where('store_id <>?', 0);

        $additionalCondition = $this->_getAdditionalCondition();

        $collection->getSelect()->where(
            "`meta_title_len` = 0   OR
            `meta_title_len` > " . $maxLengthMetaTitle . "  OR
            `meta_descr_len` = 0   OR
            `meta_descr_len` > " . $maxLengthMetaDescription . " OR
            `name_dupl` > 1        OR
            `meta_title_dupl` > 1 OR
            `prepared_meta_title` = ''" .
            $additionalCondition
        );

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn(
            'id',
            array(
            'header' => Mage::helper('mageworx_seoreports')->__('ID'),
            'width'  => '50px',
            'type'   => 'number',
            'index'  => 'category_id',
            'align'  => 'center',
            'use_index' => true
            )
        );

        $this->addColumn(
            'name',
            array(
            'header' => Mage::helper('mageworx_seoreports')->__('Category Name'),
            'type'   => 'text',
            'index'  => 'name',
            'align'  => 'left',
            )
        );

        $this->addColumn(
            'url',
            array(
            'header'   => Mage::helper('mageworx_seoreports')->__('Url'),
            'renderer' => 'mageworx_seoreports/adminhtml_grid_renderer_url',
            'type'     => 'text',
            'index'    => 'url_path',
            'align'    => 'left',
            )
        );

        $this->addColumn(
            'level',
            array(
            'header' => Mage::helper('mageworx_seoreports')->__('Level'),
            'width'  => '50px',
            'type'   => 'number',
            'index'  => 'level',
            'align'  => 'center',
            )
        );

        $this->addColumn(
            'name_error',
            array(
            'renderer' => 'mageworx_seoreports/adminhtml_grid_renderer_error',
            'filter'   => 'mageworx_seoreports/adminhtml_grid_filter_error',
            'type'     => 'options',
            'options'  => Mage::helper('mageworx_seoreports')->getErrorTypes(array('duplicate')),
            'header'   => Mage::helper('mageworx_seoreports')->__('Name'),
            'index'    => 'name_error',
            'width'    => '150px',
            'align'    => 'center',
            'sortable' => false,
            )
        );

        $this->addColumn(
            'meta_title_error',
            array(
            'renderer' => 'mageworx_seoreports/adminhtml_grid_renderer_error',
            'filter'   => 'mageworx_seoreports/adminhtml_grid_filter_error',
            'type'     => 'options',
            'options'  => Mage::helper('mageworx_seoreports')->getErrorTypes(),
            'header'   => Mage::helper('mageworx_seoreports')->__('Meta Title'),
            'index'    => 'meta_title_error',
            'width'    => '150px',
            'sortable' => false,
            'align'    => 'center',
            )
        );

        $this->addColumn(
            'meta_descr_error',
            array(
            'renderer' => 'mageworx_seoreports/adminhtml_grid_renderer_error',
            'filter'   => 'mageworx_seoreports/adminhtml_grid_filter_error',
            'type'     => 'options',
            'options'  => Mage::helper('mageworx_seoreports')->getErrorTypes(array('missing', 'long')),
            'header'   => Mage::helper('mageworx_seoreports')->__('Meta Description'),
            'index'    => 'meta_descr_error',
            'width'    => '150px',
            'sortable' => false,
            'align'    => 'center',
            )
        );

        if (!Mage::helper('mageworx_seoall/version')->isEeRewriteActive()) {
            $this->addColumn(
                'url_error', array(
                'renderer' => 'mageworx_seoreports/adminhtml_grid_renderer_error',
                'filter'   => 'mageworx_seoreports/adminhtml_grid_filter_error',
                'type'     => 'options',
                'options'  => Mage::helper('mageworx_seoreports')->getErrorTypes(array('duplicate')),
                'header'   => Mage::helper('mageworx_seoreports')->__('URL key duplicate'),
                'index'    => 'url_error',
                'width'    => '100px',
                'align'    => 'center',
                'sortable' => false,
                )
            );
        }

        return parent::_prepareColumns();
    }

    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addFieldToFilter('store_id', $value);
    }

    /**
     * Prepare grid massaction actions
     *
     * @return this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('category_id');
        $this->getMassactionBlock()->setFormFieldName('external_category_ids');
        $data  =  array('block' => $this, 'store' => $this->_getStore());
        Mage::dispatchEvent('mageworx_seoreports_category_grid_preparemassaction', $data);

        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl(
            'adminhtml/catalog_category/edit',
            array('id'    => $row->getCategoryId(), 'store' => $this->getRequest()->getParam('store'))
        );
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    protected function _getAdditionalCondition()
    {
        if (!Mage::helper('mageworx_seoall/version')->isEeRewriteActive()) {
            return " OR `url_dupl` > 1";
        }
    }
}
