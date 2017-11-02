<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Scheckout
 */

if ('true' != (string)Mage::getConfig()->getNode('modules/Amasty_GiftCard/active')) {
    class Amasty_Scheckout_Block_Amgiftcard extends Mage_Checkout_Block_Cart_Abstract {}
} else {
    $autoloader = Varien_Autoload::instance();
    $autoloader->autoload('Amasty_Scheckout_Block_Amgiftcard_Rewrite');
}
