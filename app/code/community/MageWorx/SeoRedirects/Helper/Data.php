<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoRedirects_Helper_Data extends Mage_Core_Helper_Abstract
{
    const DAY_COUNT_IN_NOT_CLEAN = 30;

    /**
     * XML config path seo redirects enabled
     */
    const XML_PATH_PRODUCT_REDIRECT_ENABLED     = 'mageworx_seo/seoredirects/enabled_for_dead_product';

    /**
     * XML config path seo redirects type
     */
    const XML_PATH_PRODUCT_REDIRECT_TYPE        = 'mageworx_seo/seoredirects/product_redirect_type';

    /**
     * XML config path seo redirects by priority
     */
    const XML_PATH_PRODUCT_REDIRECT_BY_PRIORITY = 'mageworx_seo/seoredirects/product_redirect_by_priority';

    /**
     * XML config path seo redirects count of day
     */
    const XML_PATH_PRODUCT_REDIRECT_STABLE_DAY  = 'mageworx_seo/seoredirects/product_redirect_count_stable_day';

    /**
     * XML config path seo custom redirects enabled
     */
    const XML_PATH_CUSTOM_REDIRECT_ENABLED      = 'mageworx_seo/seoredirects/custom_redirect_enabled';

    /**
     * XML config path seo redirects enabled
     */
    const XML_PATH_CUSTOM_REDIRECT_FOR_DELETED_ENTITIES     = 'mageworx_seo/seoredirects/custom_redirect_enabled_for_deleted_entities';

    /**
     * Checks if redirects is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return (bool)Mage::getStoreConfigFlag(self::XML_PATH_PRODUCT_REDIRECT_ENABLED, $storeId);
    }

    /**
     * Retrieve redirect type
     *
     * @param int|null $storeId
     * @return int
     */
    public function getRedirectType($storeId = null)
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_PRODUCT_REDIRECT_TYPE, $storeId);
    }

    /**
     * Checks if force redirect by priority is enabled
     *
     * @return boolean
     */
    public function isForceProductRedirectByPriority($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_PRODUCT_REDIRECT_BY_PRIORITY, $storeId);
    }

    /**
     * Retrieve count of previous days during which redirects won't be cleared
     *
     * @param int $storeId
     * @return int
     */
    public function getCountStableDay($storeId = null)
    {
        $count = (int)Mage::getStoreConfig(self::XML_PATH_PRODUCT_REDIRECT_STABLE_DAY, $storeId);

        return ($count < self::DAY_COUNT_IN_NOT_CLEAN) ? self::DAY_COUNT_IN_NOT_CLEAN : $count;
    }

    /**
     *
     * @return int
     */
    public function getDefaultStoreId()
    {
        if(Mage::app()->isSingleStoreMode()){
            return (int)Mage::app()->getStore(true)->getId();
        }

        if(Mage::app()->getDefaultStoreView()){
            return (int)Mage::app()->getDefaultStoreView()->getId();
        }

        return (int)array_shift(Mage::helper('mageworx_seoall/store')->getAllEnabledStore())->getStoreId();
    }

    /**
     * Checks if force redirect by priority is enabled
     *
     * @param int $storeId
     * @return boolean
     */
    public function isCustomRedirectEnabled($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CUSTOM_REDIRECT_ENABLED, $storeId);
    }


    /**
     * Checks if force redirect by priority is enabled
     *
     * @param int $storeId
     * @return boolean
     */
    public function isCustomRedirectForDeletedEntitiesEnabled($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CUSTOM_REDIRECT_FOR_DELETED_ENTITIES, $storeId);
    }

    /**
     * Get cms id by identifier
     *
     * @param int $id
     * @param int $storeId
     * @return int | null
     */
    public function getCmsTitleById($id, $storeId)
    {
        $resource = Mage::getSingleton('core/resource');

        $coreResource = Mage::getSingleton('core/resource');
        $conn = $coreResource->getConnection('core_read');

        /** @var $select Varien_Db_Select */
        $select = $conn->select('n.`title`')
            ->from(array('n' =>  $resource->getTableName('cms/page')))
            ->joinInner(
                array(
                    's' => $resource->getTableName('cms/page_store')),
                'n.page_id = s.page_id',
                array()
            )
            ->where('n.page_id = ?', $id)
            ->where('store_id IN(?)', array(Mage_Core_Model_App::ADMIN_STORE_ID, (int)$storeId));

        $results = $conn->fetchAll($select);
        if(isset($results[0]['title'])) {
            return $results[0]['title'];
        }
        return null;
    }

    /**
     * Get cms id by identifier
     *
     * @param int $identifier
     * @return int | null
     */
    public function getCmsIdByIdentifier($identifier, $storeId)
    {
        $resource = Mage::getSingleton('core/resource');

        $coreResource = Mage::getSingleton('core/resource');
        $conn = $coreResource->getConnection('core_read');

        /** @var $select Varien_Db_Select */
        $select = $conn->select('n.`page_id`')
            ->from(array('n' =>  $resource->getTableName('cms/page')))
            ->joinInner(
                array(
                    's' => $resource->getTableName('cms/page_store')),
                'n.page_id = s.page_id',
                array()
            )
            ->where('n.identifier = ?', $identifier)
            ->where('store_id IN(?)', array(Mage_Core_Model_App::ADMIN_STORE_ID, (int)$storeId));

        $results = $conn->fetchAll($select);
        if(isset($results[0]['page_id'])) {
            return $results[0]['page_id'];
        }
        return null;
    }
}