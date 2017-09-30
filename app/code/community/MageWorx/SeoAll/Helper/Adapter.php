<?php
/**
 * MageWorx
 * SeoAll Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoAll
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoAll_Helper_Adapter extends Mage_Core_Helper_Abstract
{
    public function getDateForFilename()
    {
        return Mage::getSingleton('core/date')->date('Y-m-d_H-i-s');
    }

    /**
     * @param int|null $storeId
     * @return boolean
     */
    public function isReviewFriendlyUrlEnable($storeId = null)
    {
        if ($this->isModuleEnabled('MageWorx_SeoBase')) {
            return Mage::helper('mageworx_seobase')->isReviewFriendlyUrlEnable($storeId);
        }

        return false;
    }

    /**
     * @param $extension
     * @param $class
     * @param $methods
     * @return bool
     */
    public function getIsExtensionClassAvailable($extension, $class, $methods = array())
    {
        if (!$this->isModuleEnabled($extension)) {
            return false;
        }

        if (!class_exists($class)) {
            return false;
        }

        if ($methods) {
            foreach ($methods as $method) {
                if (!is_callable(array($class, $method))) {
                    return false;
                }
            }
        }

        return true;
    }
}