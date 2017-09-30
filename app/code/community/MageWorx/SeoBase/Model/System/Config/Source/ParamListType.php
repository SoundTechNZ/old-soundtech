<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_System_Config_Source_ParamListType
{
    const WHITE_LIST_ID = 'white';
    const BLACK_LIST_ID = 'black';

    public function toOptionArray()
    {
        return array(
            array('value' => self::BLACK_LIST_ID, 'label' => Mage::helper('mageworx_seobase')->__('Black List')),
            array('value' => self::WHITE_LIST_ID, 'label' => Mage::helper('mageworx_seobase')->__('White List')),
        );
    }

}