<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Model_Source_Meta_PageNum extends MageWorx_SeoAll_Model_Source
{
    const PAGER_ADD_BEFORE = 'begin';
    const PAGER_ADD_AFTER  = 'end';
    const PAGER_ADD_OFF    = 'off';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::PAGER_ADD_BEFORE,
                'label' => Mage::helper('mageworx_seoextended')->__('At the Beginning')
            ),
            array(
                'value' => self::PAGER_ADD_AFTER,
                'label' => Mage::helper('mageworx_seoextended')->__('At the end')
            ),
            array(
                'value' => self::PAGER_ADD_OFF,
                'label' => Mage::helper('catalog')->__('No')
            )
        );
    }
}