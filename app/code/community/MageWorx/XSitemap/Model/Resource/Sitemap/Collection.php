<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_XSitemap_Model_Resource_Sitemap_Collection extends Mage_Sitemap_Model_Resource_Sitemap_Collection
{
    public function _construct()
    {
        $this->_init('mageworx_xsitemap/sitemap');
    }

}
