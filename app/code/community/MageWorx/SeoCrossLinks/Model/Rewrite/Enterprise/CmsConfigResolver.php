<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

//We use require_once for avoid MEQP1 error "Only one class is allowed in a file"

/** @var MageWorx_SeoAll_Helper_Adapter $adapter */
$adapter = Mage::helper('mageworx_seoall/adapter');

if ($adapter->getIsExtensionClassAvailable(
    'MageWorx_SeoBase',
    'MageWorx_SeoBase_Model_Rewrite_Enterprise_CmsConfig'
)
) {
    if (defined('COMPILER_INCLUDE_PATH')) {
        require_once 'MageWorx_SeoCrossLinks_Model_Rewrite_Enterprise_ResolverForSeoBase.php';
    } else {
        require_once 'MageWorx/SeoCrossLinks/Model/Rewrite/Enterprise/ResolverForSeoBase.php';
    }
} else {
    if (defined('COMPILER_INCLUDE_PATH')) {
        require_once 'MageWorx_SeoCrossLinks_Model_Rewrite_Enterprise_ResolverForEnterprise.php';
    } else {
        require_once 'MageWorx/SeoCrossLinks/Model/Rewrite/Enterprise/ResolverForEnterprise.php';
    }
}