<?php
/**
 * MageWorx
 * MageWorx SeoBreadcrumbs Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBreadcrumbs
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoBreadcrumbs_Block_Adminhtml_Breadcrumbs extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'mageworx_seobreadcrumbs';
        $this->_controller = 'adminhtml_breadcrumbs';
        $this->_headerText = Mage::helper('mageworx_seobreadcrumbs')->__('Breadcrumbs Priority for Categories');
        parent::__construct();
        $this->_removeButton('add');
    }
}
