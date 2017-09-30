<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Model_Resource_Catalog_Category_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('mageworx_seoextended/catalog_category');
    }

    /**
     * Map of extended category fields
     *
     * @var array
     */
    protected $_map = array('fields' => array(
        'id'           => 'main_table.id',
        'store_id'     => 'main_table.store_id',
        'attribute_id' => 'main_table.attribute_id',
        'category_id'  => 'main_table.category_id',
    ));

    /**
     * @param int $attributeId
     * @param int $categoryId
     * @param int $storeId
     * @return $this
     */
    public function getFilteredCollection($attributeId, $categoryId, $storeId)
    {
        $this->addFieldToFilter('attribute_id', $attributeId);
        $this->addFieldToFilter('category_id', $categoryId);
        $this->addFieldToFilter('store_id', $storeId);

        return $this;
    }
}