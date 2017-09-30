<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoMarkup_Helper_Abstract extends Mage_Core_Helper_Abstract
{
    /** @var  MageWorx_SeoMarkup_Helper_Data */
    protected $_helper;

    /** @var  MageWorx_SeoMarkup_Helper_Config */
    protected $_helperConfig;

    public function __construct()
    {
        $this->_helper = Mage::helper('mageworx_seomarkup');
        $this->_helperConfig = Mage::helper('mageworx_seomarkup/config');
    }
}