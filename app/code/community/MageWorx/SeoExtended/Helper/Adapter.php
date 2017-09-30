<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Helper_Adapter extends Mage_Core_Helper_Abstract
{
    const SEO_EXTENDED_TEMPLATE_NAME = 'MageWorx_SeoXTemplates';

    /**
     * @return bool
     */
    public function isAvailableSeoTemplates()
    {
        return $this->isModuleOutputEnabled(self::SEO_EXTENDED_TEMPLATE_NAME);
    }
}