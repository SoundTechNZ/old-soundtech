<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Scheckout
 */


/**
 * Class Amasty_Scheckout_LoginController
 * @author Artem Brunevski
 */
class Amasty_Scheckout_LoginController extends Mage_Checkout_Controller_Action
{
    public function checkAction()
    {
        $email = $this->getRequest()->getPost('email');
        $password = $this->getRequest()->getPost('password');
        $websiteId = Mage::app()->getStore()->getWebsiteId();
        $messagesBlock = $this->getLayout()->getMessagesBlock();
        $customerLoggedIn = false;
        /** @var Amasty_Scheckout_Helper_Data $helper */
        $helper = Mage::helper("amscheckout");

        try{
            $checkCustomer = Mage::getModel('customer/customer')
                ->setWebsiteId($websiteId);

            $checkCustomer->loadByEmail($email);

            if ($checkCustomer->getId()) {

                if ($password) {
                    try {
                        $this->_getCustomerSession()->login($email, $password);
                        $customerLoggedIn = true;
                    } catch (Mage_Core_Exception $e) {
                        switch ($e->getCode()) {
                            case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                                $messagesBlock->addWarning($helper->__($e->getMessage()));
                                break;
                            default:
                                throw new Exception($e->getMessage());
                        }
                    }
                }

                if ($customerLoggedIn === false && $password === '')
                {
                    $messagesBlock->addWarning($helper->__("You have account created in this store. Please provide the password and you will be logged in automatically"));
                }

            } else {
                /*if (!$password) {
                    $messagesBlock->addWarning($helper->__("Please provide the password to automatically create an account and register the order to this account"));
                } else*/
                if (!$helper->checkPassword($password)) {
                    $messagesBlock->addWarning($helper->getPasswordLengthMessage());
                }
            }

        } catch (Exception $e){
            $messagesBlock->addError($e->__toString());
        }


        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
            'exists' => $checkCustomer->getId() ? 1 : 0,
            'message' => $messagesBlock->toHtml(),
            'customerLoggedIn' => $customerLoggedIn
        )));
    }

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }
}
