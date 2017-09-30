<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoRedirects_Model_Resource_Redirect_Custom_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('mageworx_seoredirects/redirect_custom');
    }

    /**
     * @param array|string $field
     * @param null $condition
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'store_id') {
            if ($condition == array('eq' => '0')) {
                $field = 'store_id';
                foreach (Mage::app()->getStores() as $store) {
                    $stores[] = $store->getId();
                }
                $condition = array('in' => $stores);
            }
        }

        return parent::addFieldToFilter($field, $condition);
    }

}