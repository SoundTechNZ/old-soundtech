<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoRedirects_Block_Adminhtml_Redirect_Custom_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('seoredirects_custom_grid');
        $this->setDefaultSort('custom_redirect_id');
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
        $collection = Mage::getResourceModel('mageworx_seoredirects/redirect_custom_collection');
        $this->setCollection($collection);
        parent::_prepareCollection();
        Mage::getSingleton('mageworx_seoredirects/source_custom_entityName')->prepareTitleCollections($this->getCollection());
        return $this;
    }

    /**
     * Prepare and add columns to grid
     *
     * @return this
     */
    protected function _prepareColumns()
    {
        $helperData = Mage::helper('mageworx_seoredirects');

        $this->addColumn(
            'custom_redirect_id',
            array(
                'header' => Mage::helper('catalog')->__('ID'),
                'align'  => 'center',
                'width'  => '50px',
                'index'  => 'custom_redirect_id',
            )
        );

        $this->addColumn(
            'request_entity_type_id',
            array(
                'header' => Mage::helper('mageworx_seoredirects')->__('Redirect From(Type)'),
                'align'  => 'center',
                'width'  => '40px',
                'index'  => 'request_entity_type_id',
                'type'    => 'options',
                'options' => Mage::getModel('mageworx_seoredirects/source_custom_requestEntity')->toArray()
            )
        );

        $this->addColumn(
            'target_entity_type_id',
            array(
                'header' => Mage::helper('mageworx_seoredirects')->__('Redirect To(Type)'),
                'align'  => 'center',
                'width'  => '40px',
                'index'  => 'target_entity_type_id',
                'type'    => 'options',
                'options' => Mage::getModel('mageworx_seoredirects/source_custom_targetEntity')->toArray()
            )
        );

        $this->addColumn(
            'redirect_type',
            array(
                'header' => $helperData->__('Redirect Type'),
                'align'  => 'center',
                'width'  => '40px',
                'type'    => 'options',
                'options' => Mage::getModel('mageworx_seoredirects/source_custom_requestType')->toArray(),
                'index'  => 'redirect_type'

            )
        );

           $this->addColumn(
               'request_entity_id',
               array(
                   'header'    => $helperData->__('Redirect From'),
                   'index'     => 'request_entity_id',
                   'sortable'  => true,
                   'align'     => 'left',
                   'width'     => '250px',
                   'type'      => 'text',
                   'renderer'  => 'mageworx_seoredirects/adminhtml_redirect_custom_render_requestEntity',
               )
           );

        $this->addColumn(
            'target_entity_id',
            array(
                'header' => Mage::helper('mageworx_seoredirects')->__('Redirect To'),
                'align'  => 'right',
                'width'  => '200px',
                'index'  => 'target_entity_id',
                'type'   => 'text',
                'align'     => 'left',
                'renderer'  => 'mageworx_seoredirects/adminhtml_redirect_custom_render_targetEntity',
            )
        );

        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('mageworx_seoredirects')->__('Status'),
                'width'   => '60px',
                'align'   => 'center',
                'index'   => 'status',
                'type'    => 'options',
                'options' => Mage::getModel('mageworx_seoredirects/source_custom_status')->toArray()
            )
        );

        $this->addColumn('store',
            array(
                'header'     => Mage::helper('mageworx_seoredirects')->__('Store View'),
                'index'      => 'store_id',
                'type'       => 'store',
                'store_all'  => true,
                'store_view' => true,
                'sortable'   => false,
                'width'      => '200px',
                'align'      => 'center'
            ));

        $this->addColumn(
            'date_created',
            array(
                'header'   => Mage::helper('cms')->__('Date Created'),
                'index'    => 'date_created',
                'type'     => 'datetime',
                'align'    => 'center',
                'default'  => '---',
                'width'    => '200px',
                'sortable' => true,
            )
        );

        $this->addColumn(
            'date_modified',
            array(
                'header'   => Mage::helper('mageworx_seoredirects')->__('Date Modified'),
                'index'    => 'date_modified',
                'type'     => 'datetime',
                'align'    => 'center',
                'default'  => '---',
                'width'    => '200px',
                'sortable' => true,
            )
        );

        $this->addColumn(
            'action',
            array(
                'header'    => Mage::helper('customer')->__('Action'),
                'width'     => '80px',
                'type'      => 'action',
                'getter'    => 'getId',
                'align'     => 'center',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'url'     => array('base' => '*/*/edit'),
                        'field'   => 'custom_redirect_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true,
            )
        );

        return parent::_prepareColumns();
    }

    /**
     * Get url for row
     *
     * @param string $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return false;
    }

    /**
     * Prepare grid massaction actions
     *
     * @return this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('custom_redirect_id');
        $this->getMassactionBlock()->setFormFieldName('redirects');

        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label' => Mage::helper('mageworx_seoredirects')->__('Change "Enabled/Disabled"'),
                'url'   => $this->getUrl('*/*/massChangeStatus', array('_current' => true)),
                'additional' => array(
                    'visibility' => array(
                        'name'     => 'status',
                        'type'     => 'select',
                        'class'    => 'required-entry',
                        'label'    => Mage::helper('cms')->__('Enabled'),
                        'values'   => Mage::getSingleton('mageworx_seoredirects/source_yesno')->toArray()
                    )
                )
            )
        );

        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'   => Mage::helper('catalog')->__('Delete'),
                'url'     => $this->getUrl('*/*/massDelete', array('_current' => true)),
                'confirm' => Mage::helper('mageworx_seoredirects')->__('Are you sure you want to do this?')
            )
        );

        return $this;
    }
}