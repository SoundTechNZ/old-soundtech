<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

//We use require_once for avoid MEQP1 error "Only one class is allowed in a file"

/** @var MageWorx_SeoAll_Helper_Adapter $adapter */
$adapter = Mage::helper('mageworx_seoall/adapter');

if ($adapter->getIsExtensionClassAvailable(
    'MageWorx_SearchAutocomplete',
    'MageWorx_SearchAutocomplete_Block_Review_Helper'
)
) {
    if (defined('COMPILER_INCLUDE_PATH')) {
        require_once 'MageWorx_SeoBase_Block_Review_Helper_AbstractForSearchAutocomplite.php';
    } else {
        require_once 'MageWorx/SeoBase/Block/Review/Helper/AbstractForSearchAutocomplite.php';
    }
}
else {
    if (defined('COMPILER_INCLUDE_PATH')) {
        require_once 'MageWorx_SeoBase_Block_Review_Helper_AbstractForMage.php';
    } else {
        require_once 'MageWorx/SeoBase/Block/Review/Helper/AbstractForMage.php';
    }
}