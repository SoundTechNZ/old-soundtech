<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_XSitemap_Model_Resource_Blog_Page extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('blog/blog', 'post_id');
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function getCollection($storeId)
    {
        $read   = $this->_getReadAdapter();
        $select = $read->select()
            ->from(
                array('main_table' => $this->getTable('blog')),
                array('post_id', 'identifier AS url', 'update_time AS date')
            )
            ->join(
                array('store_table' => $this->getTable('store')), 'main_table.post_id=store_table.post_id', array()
            )
            ->where('store_table.store_id IN(?)', array(0, $storeId))
            ->where('status IN(?)', array(1, 3));

        $query = $read->query($select);
        while ($row   = $query->fetch()) {
            $post                  = $this->_prepareObject($row);
            $posts[$post->getId()] = $post;
        }

        return $posts;
    }

    protected function _prepareObject(array $data)
    {
        $object = new Varien_Object();
        $object->setId($data['post_id']);
        $object->setUrl($data['url']);
        $object->setDate($data['date']);

        return $object;
    }
}