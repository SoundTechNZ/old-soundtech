<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Scheckout
 */

if ('true' != (string)Mage::getConfig()->getNode('modules/Enterprise_GiftCardAccount/active')) {
    class Amasty_Scheckout_Block_Giftcard extends Mage_Checkout_Block_Cart_Abstract {}
} else {
    $autoloader = Varien_Autoload::instance();
    $autoloader->autoload('Amasty_Scheckout_Block_Giftcard_Rewrite');
}
