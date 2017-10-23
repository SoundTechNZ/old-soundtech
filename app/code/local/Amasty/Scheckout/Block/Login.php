<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Scheckout
 */


/**
 * Class Amasty_Scheckout_Block_Login
 * @author Artem Brunevski
 */

class Amasty_Scheckout_Block_Login extends Mage_Core_Block_Template
{
    /**
     * @return mixed
     */
    public function isAllowGuestCheckout()
    {
        return Mage::helper('amscheckout')->isAllowGuestCheckout();
    }

    public function getEmailMessagesBlock()
    {
        return $this->getMessagesBlock()->addWarning(Mage::helper('amscheckout')->__("Please enter valid Email."));
    }

    public function getPasswordMessagesBlock()
    {
        $this->getEmailMessagesBlock()->getMessageCollection()->clear();
        return $this->getMessagesBlock()->addWarning(Mage::helper('amscheckout')->getPasswordLengthMessage());
    }

    public function getValidTlds()
    {
        $validTlds = Mage::getModel('amscheckout/validate_hostname')->getValidTlds();
        return Mage::helper('core')->jsonEncode($validTlds);
    }
}
