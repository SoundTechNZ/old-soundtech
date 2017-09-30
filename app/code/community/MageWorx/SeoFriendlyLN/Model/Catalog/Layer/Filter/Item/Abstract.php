<?php
/**
 * MageWorx
 * MageWorx SeoFriendlyLN Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoFriendlyLN
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

//We use require_once for avoid MEQP1 error "Only one class is allowed in a file"

/** @var MageWorx_SeoAll_Helper_Adapter $adapter */
$adapter = Mage::helper('mageworx_seoall/adapter');

if ($adapter->getIsExtensionClassAvailable(
    'Amasty_Shopby',
    'Amasty_Shopby_Model_Catalog_Layer_Filter_Item'
)
) {
    if (defined('COMPILER_INCLUDE_PATH')) {
        require_once 'MageWorx_SeoFriendlyLN_Model_Catalog_Layer_Filter_Item_AbstractForAmastyShopBy.php';
    } else {
        require_once 'MageWorx/SeoFriendlyLN/Model/Catalog/Layer/Filter/Item/AbstractForAmastyShopBy.php';
    }
} else {
    if (defined('COMPILER_INCLUDE_PATH')) {
        require_once 'MageWorx_SeoFriendlyLN_Model_Catalog_Layer_Filter_Item_AbstractForMage.php';
    } else {
        require_once 'MageWorx/SeoFriendlyLN/Model/Catalog/Layer/Filter/Item/AbstractForMage.php';
    }
}
