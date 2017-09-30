<?php
/**
 * MageWorx
 * MageWorx SeoBreadcrumbs Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBreadcrumbs
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */


$installer = $this;

$installer->addAttribute(
    'catalog_category', MageWorx_SeoBreadcrumbs_Helper_Data::BREADCRUMBS_PRIORITY_CODE,
    array(
        'group'            => 'General Information',
        'type'             => 'text',
        'backend'          => '',
        'frontend'         => '',
        'label'            => 'Breadcrumbs Priority',
        'input'            => 'text',
        'class'            => '',
        'source'           => 'MageWorx_SeoBreadcrumbs_Model_System_Config_Source',
        'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible'          => true,
        'required'         => false,
        'user_defined'     => false,
        'default'          => 0,
        'apply_to'         => '',
        'visible_on_front' => false,
        'sort_order'       => 9,
        'frontend_class'   => 'validate-percents',
        'note'             => '100 is the highest priority. This setting defines the priority of each category to be selected for the product breadcrumbs.'
    )
);

$installer->endSetup();