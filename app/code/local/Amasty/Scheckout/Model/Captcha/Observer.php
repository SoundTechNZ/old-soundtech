<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Scheckout
 */

if ('true' == (string)Mage::getConfig()->getNode('modules/Mage_Captcha/active')) {
    class Amasty_Scheckout_Model_Captcha_Observer extends Mage_Captcha_Model_Observer {}
} else {
    $autoloader = Varien_Autoload::instance();
    $autoloader->autoload('Amasty_Scheckout_Model_Captcha_Observer_Rewrite');
}
