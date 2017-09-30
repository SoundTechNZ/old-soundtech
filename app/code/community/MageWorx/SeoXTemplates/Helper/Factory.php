<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Helper_Factory extends Mage_Core_Helper_Abstract
{
    /**
     *
     * @var MageWorx_SeoXTemplates_Model_Template
     */
    protected $_model = null;

    /**
     *
     * @param MageWorx_SeoXTemplates_Model_Template $model
     */
    public function setModel($model)
    {
        $this->_model = $model;
    }

    /**
     *
     * @return MageWorx_SeoXTemplates_Model_Template
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     *
     * @return string
     * @throws Exception
     */
    public function getItemType()
    {
        if (is_object($this->_model) && ($this->_model instanceof MageWorx_SeoXTemplates_Model_Template)) {
            $container = new Varien_Object();
            $container->setModel($this->_model);

            Mage::dispatchEvent('mageworx_seoxtemplates_item_type_factory', array('container' => $container));

            $result = $container->getItemType();

            if ($result) {
                return $result;
            }
        }

        if($this->_model instanceof MageWorx_SeoXTemplates_Model_Template_Product){
            return 'product';
        }elseif($this->_model instanceof MageWorx_SeoXTemplates_Model_Template_Category){
            return 'category';
        }elseif($this->_model instanceof MageWorx_SeoXTemplates_Model_Template_Blog){
            return 'blog';
        }else{
            throw new Exception($this->__('Unknow template model type.'));
        }
    }

    /**
     *
     * @return MageWorx_SeoXTemplates_Helper_Template
     */
    public function getHelper()
    {
        $itemType = $this->getItemType();

        $container = new Varien_Object();
        $container->setItemTypeModel($itemType);

        Mage::dispatchEvent('mageworx_seoxtemplates_helper_factory', array('container' => $container));

        $result = $container->getHelper();

        if ($result instanceof MageWorx_SeoXTemplates_Helper_Template) {
            return $result;
        }

        return Mage::helper("mageworx_seoxtemplates/template_{$itemType}");
    }

    /**
     *
     * @return MageWorx_SeoXTemplates_Helper_Template_Comment
     */
    public function getCommentHelper()
    {
        $itemType = $this->getItemType();

        $container = new Varien_Object();
        $container->setItemTypeModel($itemType);

        Mage::dispatchEvent('mageworx_seoxtemplates_helper_comment_factory', array('container' => $container));

        $result = $container->getCommentHelper();

        if (is_object($result) && ($result instanceof MageWorx_SeoXTemplates_Helper_Template_Comment)) {
            return $result;
        }

        return Mage::helper("mageworx_seoxtemplates/template_comment_{$itemType}");
    }

}
