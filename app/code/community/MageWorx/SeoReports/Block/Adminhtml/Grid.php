<?php
/**
 * MageWorx
 * MageWorx SeoReports Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoReports
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoReports_Block_Adminhtml_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setUseAjax(true);
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);

        if (!Mage::registry('error_types')){
            Mage::register('error_types', Mage::helper('mageworx_seoreports')->getErrorTypes());
        }
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        return $storeId ? $storeId : Mage::app()->getStore(true)->getId();
    }

    /**
     * Prepare grid massaction column
     *
     * @return unknown
     */
    protected function _prepareMassactionColumn()
    {
        parent:: _prepareMassactionColumn();
        if (is_object($this->_columns['massaction'])) {
            $this->_columns['massaction']->setData('use_index', true);
        }

        return $this;
    }
}
