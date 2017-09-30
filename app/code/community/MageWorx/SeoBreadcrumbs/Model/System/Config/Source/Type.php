<?php
/**
 * MageWorx
 * MageWorx SeoBreadcrumbs Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBreadcrumbs
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBreadcrumbs_Model_System_Config_Source_Type extends MageWorx_SeoBreadcrumbs_Model_System_Config_Source
{
    const BREADCRUMBS_TYPE_DEFAULT   = 0;
    const BREADCRUMBS_TYPE_SHORTEST  = 1;
    const BREADCRUMBS_TYPE_LONGEST   = 2;

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array('value' => self::BREADCRUMBS_TYPE_DEFAULT,  'label' => __('Default')),
            array('value' => self::BREADCRUMBS_TYPE_SHORTEST, 'label' => __('Use Shortest')),
            array('value' => self::BREADCRUMBS_TYPE_LONGEST,  'label' => __('Use Longest'))
        );
    }
}