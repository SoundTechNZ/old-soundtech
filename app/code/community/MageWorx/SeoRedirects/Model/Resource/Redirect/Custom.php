<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoRedirects_Model_Resource_Redirect_Custom extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('mageworx_seoredirects/redirect_custom', 'custom_redirect_id');
    }

    /**
     * Load rewrite information for request
     *
     * @param   MageWorx_SeoRedirects_Model_Redirect_Custom $object
     * @param   array|string $path
     * @return  MageWorx_SeoRedirects_Model_Redirect_Custom
     */
    public function loadByCustomRequestPath($object, $path)
    {
        if (!is_array($path)) {
            $path = array($path);
        }

        $pathBind = array();
        foreach ($path as $key => $url) {
            $pathBind['path' . $key] = $url;
        }

        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getMainTable())
            ->where('request_entity_id IN (:' . implode(', :', array_flip($pathBind)) . ')')
            ->where('store_id IN(?)', array(Mage_Core_Model_App::ADMIN_STORE_ID, (int)$object->getStoreId()))
            ->where('request_entity_type_id IN(?)',
                array(MageWorx_SeoRedirects_Model_Redirect_Custom::CUSTOM_URL_ENTITY_TYPE_ID));

        $items = $adapter->fetchAll($select, $pathBind);

        if(isset($items[0])) {
            $object->setData($items[0]);
        }

        $this->unserializeFields($object);

        return $this;
    }

    public function loadByEntity($object, $entityTypeId, $entityId)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getMainTable())
            ->where("request_entity_id = '" . $entityId . "'")
            ->where("store_id = '" . $object->getStoreId() . "'")
            ->where("request_entity_type_id = '" . $entityTypeId . "'");

        $items = $adapter->fetchAll($select);
        if(isset($items[0])) {
            $object->setData($items[0]);
        }

        $this->unserializeFields($object);

        return $this;
    }

    public function loadByTargetEntity($object, $entityTypeId, $entityId)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getMainTable())
            ->where('target_entity_id = ' . $entityId)
            ->where('store_id = ' . $object->getStoreId())
            ->where('target_entity_type_id = ' . $entityTypeId);

        $items = $adapter->fetchAll($select);
        if(isset($items[0])) {
            $object->setData($items[0]);
        }

        $this->unserializeFields($object);

        return $this;
    }
}