<?php
/**
 * MageWorx
 * MageWorx SeoFriendlyLN Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoFriendlyLN
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

/**
 * Class MageWorx_SeoFriendlyLN_Model_Catalog_Category
 * @deprecated
 */
class MageWorx_SeoFriendlyLN_Model_Catalog_Category extends Mage_Catalog_Model_Category
{

    protected function _construct()
    {
        $this->_init('catalog/category');
    }

}
