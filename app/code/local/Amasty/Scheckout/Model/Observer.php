<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Scheckout
 */
class Amasty_Scheckout_Model_Observer
{
    protected function _getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }


    public function onControllerActionPredispatch($observer)
    {
        if (Mage::getStoreConfig('amscheckout/general/enabled')) {
            if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_cart_index'
            ) {
                $hlr = Mage::helper("amscheckout");

                if ($hlr->isShoppingCartOnCheckout()) {
                    $quote = $this->_getOnepage()->getQuote();
                    if (!$quote->hasItems() || $quote->getHasError() || !$quote->validateMinimumAmount()) {
                        return;
                    } else {
                        // Compose array of messages to add
                        $messages = array();
                        foreach ($this->_getOnepage()->getQuote()->getMessages() as $message) {
                            if ($message) {
                                // Escape HTML entities in quote message to prevent XSS
                                $message->setCode(Mage::helper('core')->escapeHtml($message->getCode()));
                                $messages[] = $message;
                            }
                        }

                        $this->getCustomerSession()->addUniqueMessages($messages);

                        foreach (Mage::getSingleton('checkout/session')->getMessages()->getItems() as $message) {

                            $this->getCustomerSession()->addMessage($message);
                        }


                        $url = Mage::getUrl('checkout/onepage', array('_secure' => true));
                        Mage::app()->getFrontController()->getResponse()->setRedirect($url)->sendResponse();
                    }
                }
            } else if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_onepage_index') {
                Mage::getModel("amscheckout/cart")->initAmscheckout();
            }
        }
    }

    public function getCustomerSession()
    {
//        $customer = $this->getData('customer_session');
//        if (is_null($customer)) {
        $customer = Mage::getSingleton('customer/session');
//            $this->setData('customer_session', $customer);
//        }
        return $customer;
    }

    public function getCustomer()
    {
        return $this->getCustomerSession()->getCustomer();
    }


    public function handleBlockOutput($observer)
    {
        if (Mage::getStoreConfig('amscheckout/general/enabled')) {
            $block = $observer->getBlock();

            $transport = $observer->getTransport();
            $html = null;

            if ($block instanceof Mage_Checkout_Block_Onepage_Shipping_Method ||
                $block instanceof Bigone_Nominal_Block_Onepage_Shipping_Method
            ) {
                $html = $this->_prepareOnepageShippingMethodHtml($transport);
            } else if ($block instanceof Mage_Checkout_Block_Onepage_Payment) {
                $html = $this->_prepareOnepagePaymentHtml($transport);
            } else if ($block instanceof Mage_Checkout_Block_Onepage_Shipping_Method_Available
                || $block instanceof Bigone_Nominal_Block_Onepage_Shipping_Method_Available
            ) {
                $hlr = Mage::helper("amscheckout");
                if ($hlr->reloadAfterShippingMethodChanged()) {
                    $html = $this->_prepareOnepageShippingMethodAvailableHtml($transport);
                }
            } else if ($block instanceof Mage_Checkout_Block_Onepage_Payment_Methods) {
                $hlr = Mage::helper("amscheckout");

                if ($hlr->reloadPaymentShippingMethodChanged()) {
                    $html = $this->_prepareOnepagePaymentMethodsHtml($transport);
                }
            } else if ($block instanceof Mage_Checkout_Block_Onepage_Review) {
                $html = $this->_prepareOnepageReviewHtml($transport);
            } else if ($block instanceof Mage_Checkout_Block_Agreements) {
                $html = $this->_prepareOnepageAgreementsHtml($transport);
            } else if ($block instanceof  Mage_Customer_Block_Address_Edit) {
                $fullActionName = Mage::app()->getFrontController()->getAction()->getFullActionName();
                if ($fullActionName == 'customer_address_form') {
                    $addressData = $block->getAddress()->getData();
                    $html = $this->_prepareCustomAddressFormHtml($transport, $addressData);
                }
            }

            if ($html)
                $transport->setHtml($html);
        }
    }

    public function addAdditionalDataToAddress($observer)
    {
        if (Mage::getStoreConfig('amscheckout/general/enabled')) {
            $type = $observer->getType()->getCode();
            if ($type == 'html' || $type == 'pdf') {
                $helperCf = Mage::helper('amscheckout/customfield');
                $address     = $observer->getAddress();
                $addressData = $address->getData();
                $addressType = $this->_getAddressType($address);
                $defaultFormat = $observer->getType()->getDefaultFormat();
                $defaultFormat = $helperCf->clearPreviousChanged($defaultFormat, $addressData, $type);

                $separate = $helperCf->getSeparate($type);
                $strToInsert = $helperCf->prepareFormat($addressData, $addressType, $separate);
                $changedFormat = $defaultFormat . $strToInsert;
                $observer->getType()->setDefaultFormat($changedFormat);
            }
        }
    }

    public function checkCustomFieldInAttributes($observer)
    {
        $collection = $observer->getCollection();
        if ($collection instanceof Mage_Customer_Model_Resource_Form_Attribute_Collection) {
            if (Mage::getStoreConfig('amscheckout/general/enabled')) {
                $fullActionName = Mage::app()->getFrontController()->getAction()->getFullActionName();
                if ($fullActionName == 'adminhtml_sales_order_address' || $fullActionName == 'adminhtml_customer_edit') {
                    $address = Mage::registry('order_address');
                    $addressType = ($address) ? $address->getAddressType() : 'all';
                    $disFields = Mage::helper('amscheckout/customfield')->prepareDisabledCustomFields($addressType);
                    if (!empty($disFields)) {
                        $collection->addFieldToFilter('attribute_code', array('nin' => $disFields));
                    }
                }
            }
        }
    }

    protected function _prepareOnepageShippingMethodAvailableHtml($transport)
    {
        $html = $transport->getHtml();
        $js = array('<script>');

        $js[] = '
            $$("#checkout-shipping-method-load input[type=radio]").each(function(input){
                input.observe("click", function(){
                    updateCheckout("shipping_method");
                })
            })
        ';

        $js[] = '</script>';

        return $html . implode('', $js);
    }

    protected function _prepareOnepagePaymentMethodsHtml($transport)
    {
        $html = $transport->getHtml();
        $js = array('<script>');

        $js[] = '
            $$("#co-payment-form input[type=checkbox]").each(function(input){
                input.observe("click", function(){
                    updateCheckout("payment_method");
                })
            })
            
            $$("#co-payment-form input[type=radio]").each(function(input){
                input.observe("click", function(){
                    updateCheckout("payment_method");
                })
            })
        ';

        $js[] = '</script>';

        return $html . implode('', $js);
    }

    protected function _insertHtml($html, $id, $insert)
    {

        if (!Mage::helper("amscheckout")->isQuickFirstLoad()) {
            $insert .= "<script>$('amloading-" . $id . "').hide();</script>";
        }
        return str_replace('<div style="display: none;">:AM_REPLACE</div>', $insert, $html);
    }

    protected function _insertCustomFieldsHtml($destHtml = '', $srcHtml = '')
    {
        $pattern = '/^(.+?country_id.+?\<\/select\>.*?)(\<\/li\>.+)$/sm';
        $replacement = '$1' . $srcHtml . '$2';
        return preg_replace($pattern, $replacement, $destHtml);
    }

    protected function _prepareOnepageShippingMethodHtml($transport)
    {
        $html = $transport->getHtml();

        $output = "";

        if (!Mage::helper("amscheckout")->isQuickFirstLoad()) {
            $output = Mage::helper("amscheckout")->getLayoutHtml("checkout_onepage_shippingmethod");
        }

        return $this->_insertHtml($html, "checkout-shipping-method-load", $output);
    }

    protected function _prepareOnepagePaymentHtml($transport)
    {
        $html = $transport->getHtml();

        $output = "";

        if (!Mage::helper("amscheckout")->isQuickFirstLoad()) {
            $output = Mage::helper("amscheckout")->getLayoutHtml("checkout_onepage_paymentmethod");
        }

        return $this->_insertHtml($html, "co-payment-form", $output);
    }

    protected function _prepareOnepageReviewHtml($transport)
    {
        $html = $transport->getHtml();

        $output = "";

        if (!Mage::helper("amscheckout")->isQuickFirstLoad()) {
            $output = Mage::helper("amscheckout")->getLayoutHtml("checkout_onepage_review");
        }

        return $this->_insertHtml($html, "checkout-review-load", $output);
    }

    protected function _prepareOnepageAgreementsHtml($transport)
    {
        $html = $transport->getHtml();
        $html = str_replace("<form", "<div", $html);
        $html = str_replace("</form", "</div", $html);
        return $html;
    }

    protected function _prepareCustomAddressFormHtml($transport, $addressData)
    {
        $html    = $transport->getHtml();
        $block   = Mage::app()->getLayout()->createBlock('amscheckout/customer_address_amform');
        $block->setAddressData($addressData);
        $block->setCFArea('billing');
        $addHtml = $block->toHtml();
        $block->setCFArea('shipping');
        $addHtml .= $block->toHtml();
        return $this->_insertCustomFieldsHtml($html, $addHtml);
    }

    protected function _isJoined($from)
    {
        $found = false;
        foreach ($from as $alias => $data) {
            if ($alias === 'custom_fields') {
                $found = true;
                break;
            }
        }
        return $found;
    }

    protected function _getAddressType($address)
    {
        $addressType = $address->getAddressType();

        if (is_null($addressType)) {
            $addressId       = $address->getId();
            $customerSession = $this->getCustomer();
            if ((int)$customerSession->getDefaultBilling() == $addressId) {
                $addressType = 'billing';
            } else if ((int)$customerSession->getDefaultShipping() == $addressId) {
                $addressType = 'shipping';
            }
        }
        return $addressType;
    }
}