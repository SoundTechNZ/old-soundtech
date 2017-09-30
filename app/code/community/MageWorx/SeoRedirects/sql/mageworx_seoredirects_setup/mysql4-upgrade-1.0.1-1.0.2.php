<?php 
/**
 * MageWorx
 * MageWorx SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

$installer = $this;
$installer->startSetup();

$installer->updateAttribute('catalog_category', 'redirect_priority', 'is_required', 0);

$installer->endSetup();
