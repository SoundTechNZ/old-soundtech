<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Model_Observer_CategoryFilter extends Mage_Core_Model_Abstract
{
    /**
     * @param Varien_Event_Observer $observer
     * @return string|false
     */
    public function setItemType($observer)
    {
        $container = $observer->getEvent()->getContainer();
        $model = $container->getModel();

        if (is_object($model) && $model instanceof MageWorx_SeoXTemplates_Model_Template_CategoryFilter) {
            $container->setItemType('categoryFilter');
        }

        return false;
    }

    public function observerName(Varien_Event_Observer $observer)
    {
        $couponContainer = $observer->getEvent()->getCouponContainer();
        $couponContainer->setCode('some_coupon_code');
    }
}