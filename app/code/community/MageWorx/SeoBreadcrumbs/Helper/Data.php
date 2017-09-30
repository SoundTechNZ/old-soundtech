<?php
/**
 * MageWorx
 * MageWorx SeoBreadcrumbs Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBreadcrumbs
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBreadcrumbs_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * SEO Breadcrumbs priority attribute code
     */
    const BREADCRUMBS_PRIORITY_CODE                        = 'breadcrumbs_priority';

    /**
     * XML config path SEO Breadcrumbs enabled
     */
    const XML_PATH_BREADCRUMBS_ENABLED                     = 'mageworx_seo/breadcrumbs/enabled';

    /**
     * XML config path SEO Breadcrumbs type
     */
    const XML_PATH_BREADCRUMBS_TYPE                        = 'mageworx_seo/breadcrumbs/type';

    /**
     * XML config path use category breadcrumbs priority
     */
    const XML_PATH_BREADCRUMBS_BY_CATEGORY                 = 'mageworx_seo/breadcrumbs/by_category_priority';

    /**
     * XML config path breadcrumbs block name
     */
    const XML_PATH_BREADCRUMBS_BLOCK_NAME                  = 'mageworx_seo/breadcrumbs/block_name';


    /**
     * Check if SEO Breadcrumbs enabled
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isSeoBreadcrumbsEnabled($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_BREADCRUMBS_ENABLED, $storeId);
    }

    /**
     * Retrieve SEO Breadcrumbs Path
     *
     * @param int|null $storeId
     * @return int
     */
    public function getSeoBreadcrumbsType($storeId = null)
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_BREADCRUMBS_TYPE, $storeId);
    }

    /**
     * Check if SEO Breadcrumbs by category breadcrumbs priority enabled
     *
     * @param int|null $storeId
     * @return boolean
     */
    public function isUseCategoryBreadcrumbsPriority($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_BREADCRUMBS_BY_CATEGORY, $storeId);
    }

    /**
     * Check if long breadcrumbs mode
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isLongMode($storeId = null)
    {
        return $this->getSeoBreadcrumbsType($storeId) == MageWorx_SeoBreadcrumbs_Model_System_Config_Source_Type::BREADCRUMBS_TYPE_LONGEST;
    }

    /**
     * Check if short breadcrumbs mode
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isShortMode($storeId = null)
    {
        return $this->getSeoBreadcrumbsType($storeId) == MageWorx_SeoBreadcrumbs_Model_System_Config_Source_Type::BREADCRUMBS_TYPE_SHORTEST;
    }

    /**
     * Check if default breadcrumbs mode
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isDefaultMode($storeId = null)
    {
        return $this->getSeoBreadcrumbsType($storeId) == MageWorx_SeoBreadcrumbs_Model_System_Config_Source_Type::BREADCRUMBS_TYPE_DEFAULT;
    }

    /**
     * Get name of breadcrumbs block
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCrumbsBlockName($storeId = null)
    {
        $name = Mage::getStoreConfig(self::XML_PATH_BREADCRUMBS_BLOCK_NAME, $storeId);
        if ($name){
            return $name;
        }

        return 'breadcrumbs';
    }
}