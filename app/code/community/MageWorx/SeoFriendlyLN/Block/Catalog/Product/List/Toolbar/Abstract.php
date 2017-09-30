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
    'Amasty_Sorting',
    'Amasty_Sorting_Block_Catalog_Product_List_Toolbar'
)
) {
    require_once 'MageWorx/SeoFriendlyLN/Block/Catalog/Product/List/Toolbar/AbstractForAmastySorting.php';
}
elseif ($adapter->getIsExtensionClassAvailable(
    'Amasty_Shopby',
    'Amasty_Shopby_Block_Catalog_Product_List_Toolbar'
)
) {
    if (defined('COMPILER_INCLUDE_PATH')) {
        require_once 'MageWorx_SeoFriendlyLN_Block_Catalog_Product_List_Toolbar_AbstractForAmastyShopBy.php';
    } else {
        require_once 'MageWorx/SeoFriendlyLN/Block/Catalog/Product/List/Toolbar/AbstractForAmastyShopBy.php';
    }
} else {
    if (defined('COMPILER_INCLUDE_PATH')) {
        require_once 'MageWorx_SeoFriendlyLN_Block_Catalog_Product_List_Toolbar_AbstractForMage.php';
    } else {
        require_once 'MageWorx/SeoFriendlyLN/Block/Catalog/Product/List/Toolbar/AbstractForMage.php';
    }
}
