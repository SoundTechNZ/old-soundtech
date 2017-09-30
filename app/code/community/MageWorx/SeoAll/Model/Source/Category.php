<?php
/**
 * MageWorx
 * MageWorx SeoAll Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoAll
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoAll_Model_Source_Category
{
    /**
     * @var array|null
     */
    protected $_options;

    /**
     *
     * @return array
     */
    public function toArray()
    {
        $options = array();
        foreach ($this->_getCategoryTree() as $category) {
            $options[$category['value']] =  $category['label'];
        }

        return array('' => '') + $options;
    }


    /**
     * @param array $disabledCategories
     * @return array
     */
    public function toOptionArray($disabledCategories = array())
    {

        $options = array();
        foreach ($this->_getCategoryTree() as $category) {
            $params['value'] = $category['value'];
            $params['label'] = $category['label'];


            if (in_array($category['value'], $disabledCategories)) {
                $params['disabled'] = 1;
            } else {
                unset($params['disabled']);
            }

            $options[] = $params;
        }

        return array('' => '') + $options;
    }

    /**
     *
     * @param Varien_Data_Tree_Node $node
     * @param array $values
     * @param int $level
     * @return array
     */
    protected function _createCategoryTree(Varien_Data_Tree_Node $node, $values, $level = 0)
    {
        $level++;

        $values[$node->getId()]['value'] =  $node->getId();
        $values[$node->getId()]['label'] = str_repeat("--", max($level-1, 0)) . $node->getName();
        $values[$node->getId()]['disabled'] = true;

        foreach ($node->getChildren() as $child) {
            $values = $this->_createCategoryTree($child, $values, $level);
        }

        return $values;
    }

    /**
     *
     * @return array
     */
    protected function _getCategoryTree()
    {
        if ($this->_options === null) {
            $store = Mage::app()->getFrontController()->getRequest()->getParam('store', 0);
            $parentId = $store ? Mage::app()->getStore($store)->getRootCategoryId() : 1;

            $tree = Mage::getResourceSingleton('catalog/category_tree')->load();
            $root = $tree->getNodeById($parentId);

            if ($root && $root->getId() == 1) {
                $root->setName(Mage::helper('catalog')->__('Root'));
            }

            $collection = Mage::getModel('catalog/category')->getCollection()
                ->setStoreId($store)
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('is_active');

            $tree->addCollectionData($collection, true);
            $this->_options = $this->_createCategoryTree($root, array());
        }

        return $this->_options;
    }
}
