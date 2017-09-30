<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoRedirects_Model_Source_Custom_EntityName
{
    /**
     * @var array
     */
    protected $_titles = array();

    public function getTitleCollections() {
        return $this->_titles;
    }
    /**
     * @param $collection
     * @return $this
     */
    public function prepareTitleCollections($collection) {
        if (empty($this->_titles)) {
            $cases = array('request', 'target');
            $titles = array();

            foreach ($collection as $row) {
                foreach ($cases as $case) {
                    $titles[$row[$case . '_entity_type_id']][] = $row[$case . '_entity_id'];
                }
            }

            foreach ($titles as $type => $ids) {
                $ids = array_unique($ids);
                switch ($type) {
                    case MageWorx_SeoRedirects_Model_Redirect_Custom::PRODUCT_ENTITY_TYPE_ID:
                        $this->_titles[$type] = Mage::getModel('catalog/product')
                            ->getCollection()
                            ->addAttributeToSelect('name')
                            ->addAttributeToSelect('sku')
                            ->addFieldToFilter('entity_id', array('in' => $ids));
                        break;
                    case MageWorx_SeoRedirects_Model_Redirect_Custom::CATEGORY_ENTITY_TYPE_ID:
                        $this->_titles[$type] = Mage::getModel('catalog/category')
                            ->getCollection()
                            ->addAttributeToSelect('name')
                            ->addFieldToFilter('entity_id', array('in' => $ids));
                        break;
                    case MageWorx_SeoRedirects_Model_Redirect_Custom::CMS_PAGE_ENTITY_TYPE_ID:
                        $this->_titles[$type] = Mage::getModel('cms/page')
                            ->getCollection()
                            ->addFieldToFilter('page_id', array('in' => $ids));
                        break;
                    default:
                        break;
                }
            }
        }

        return $this;
    }
}