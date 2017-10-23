<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Scheckout
 */

require_once 'Mage/Checkout/controllers/OnepageController.php';
class Amasty_Scheckout_OnepageController extends Mage_Checkout_OnepageController
{
    
    protected $_skip_generate_html = false;
//    public function getOnepage()
//    {
//        return Mage::getSingleton('amscheckout/type_onepage');
//    }
//    
    protected function _amSaveBilling(){
        $billing = $this->getRequest()->getPost('billing', array());
        
        $this->saveMethodAction();
            
        $this->saveBillingAction();

        $usingShippingCase = isset($billing['use_for_shipping']) ? (int)$billing['use_for_shipping'] : 0;

        if (!$usingShippingCase)
            $this->saveShippingAction();
    }
    
    protected function _amSaveShipping(){
        $this->saveShippingAction();
    }
    
    protected function _amSaveShippingMethod(){
        $this->saveShippingMethodAction();
        
        $quote = $this->getOnepage()->getQuote();
        
        $this->_mwRewardPoints();
            
//        $this->getOnepage()->getQuote()->setTotalsCollectedFlag(false);
//        $this->getOnepage()->getQuote()->collectTotals();
//        $this->getOnepage()->getQuote()->save();
    }
    
    protected function _amSavePaymentMethod(){
        $this->savePaymentAction();
    }


    protected function _saveSteps($completeOrder = false)
    {
        $ret = NULL;

        if ($this->_expireAjax()) {
            return;
        }

        /** @var Amasty_Scheckout_Helper_Data $helper */
        $helper = Mage::helper("amscheckout");

        $updatedSection = $this->getRequest()->getPost('updated_section', null);

        if ($this->getRequest()->isPost()) {

            if (!$completeOrder) {
                $this->getRequest()->setPost('method', 'guest');
            }

            $beforeResponse = $this->getResponse();

            $amResponse = Mage::getModel("amscheckout/response");
            $this->_response = $amResponse;

            $billing = $this->getRequest()->getParam('billing');
            if (isset($billing['confirm_password'])) {
                $password = $billing['confirm_password'];
                if (!Mage::helper("amscheckout")->checkPassword($password)) {
                    $amResponse->setError($helper->getPasswordLengthMessage());
                } else {
                    $this->_skip_generate_html = true;
//            $this->_amSavePaymentMethod();

                    if ($completeOrder) {
                        $this->_amSaveBilling();
                        $this->_amSaveShippingMethod();
                        $this->_amSavePaymentMethod();
                    } else {
                        switch ($updatedSection) {
                            case "billing":
                                $this->_amSaveBilling();
                                break;
                            case "shipping":
                                $this->_amSaveShipping();
                                break;
                            case "shipping_method":
                                $this->_amSaveShippingMethod();
                                break;
                            case "payment_method":
                                $this->_amSavePaymentMethod();
                                break;
                            default:
                                $this->_amSaveBilling();
                                $this->_amSaveShipping();
                                $this->_amSaveShippingMethod();
                                $this->_amSavePaymentMethod();
                                break;
                        }
                    }
                }
            }

            $this->getOnepage()->getQuote()->setTotalsCollectedFlag(false);
            if (!$this->checkRestrictions()) {
                $amResponse->setError($helper->__("Shipping method must be specified"));
            } else if ($completeOrder && $amResponse->getErrorsCount() == 0 && !$amResponse->getRedirect()) {
                $this->saveOrderAction();
            }
            $this->getOnepage()->getQuote()->collectTotals();
            $this->_skip_generate_html = false;
            $this->_response = $beforeResponse;
            $ret = $amResponse;
        }

        return $ret;
    }
    
    protected function checkRestrictions()
    {
        $isRestricted = false;
        $shippingMethod = $this->getOnepage()->getQuote()->getShippingAddress()->getShippingMethod();
        
        $isRestricted = $isRestricted || strpos($shippingMethod, 'error') || empty($shippingMethod);
        
        return $this->getOnepage()->getQuote()->isVirtual() ? true : !($isRestricted);
    }
    
    protected function _mwRewardPoints()
    {
        if ((string)Mage::getConfig()->getNode('modules/MW_RewardPoints/active') == 'true') {
            $store_id = Mage::app()->getStore()->getId();
            $step = Mage::helper('rewardpoints/data')->getPointStepConfig($store_id);
            
            $rewardpoints = $this->getRequest()->getParam('mw_amount');
            if($rewardpoints <0) $rewardpoints = - $rewardpoints;

            $rewardpoints = round(($rewardpoints/$step),0) * $step;
            if($rewardpoints >= 0)
            {
                Mage::helper('rewardpoints')->setPointToCheckOut($rewardpoints);
            }
        }
    }
    
    
    protected function _getRequiredFields()
    {
        $ret = array(
            "billing" => array(),
            "shipping" => array(),
        );
        
        $hlr = Mage::helper("amscheckout");
        $billingFields = $hlr->getFields("billing");
        $shippingFields = $hlr->getFields("shipping");
        
        foreach($billingFields as $field){
            if ($field["field_required"] == 1 && $field["field_disabled"] == 0)
                $ret["billing"][] = str_replace("billing:", "", $field["field_key"]);
        }
        
        foreach($shippingFields as $field){
            if ($field["field_required"] == 1 && $field["field_disabled"] == 0)
                $ret["shipping"][] = str_replace("shipping:", "", $field["field_key"]);
        }
        return $ret;
        
    }
    
    protected function _reloadRequest($skipRequired = true)
    {
        $billingDefaults = array(
            'firstname' => '-',
            'lastname' => '-',
            'email' => 'email@example.com',
            'street' => array(
                '-'
            ),
            'city' => '-',
            'region_id' => '-',
//            'region' => '-',
            'postcode' => '-',
            'telephone' => '-',
//            'fax' => '-',
            'taxvat' => '-',
            'customer_password' => 'email@example.com',
            'confirm_password' => 'email@example.com'
        );
        
        $shippingDefaults = array(
//            'prefix' => '-',
//            'postfix' => '-',
            'firstname' => '-',
            'lastname' => '-',
            'street' => array(
                '-'
            ),
            'city' => '-',
            'region_id' => '-',
//            'region' => '-',
            'postcode' => '-',
            'telephone' => '-',
//            'fax' => '-',
        );
        
        $billing = $this->getRequest()->getPost('billing', array());
        $shipping = $this->getRequest()->getPost('shipping', array());
        
        $requiredFields = $this->_getRequiredFields();

        if (!isset($billing['customer_password'])) {
            $billing['customer_password'] = $billing['confirm_password'] = '';
        }

        foreach($billingDefaults as $key => $def){
            $val = isset($billing[$key]) ? $billing[$key] : "";

            $empty = $val == "" || (is_array($val) && implode("", $val) == "");
            
            if ($key == 'email' && !$empty){
                $empty = !Zend_Validate::is($val, 'EmailAddress');
            }
            
            if ($empty){
                
                if ($skipRequired || !in_array($key, $requiredFields["billing"])){
                    $billing[$key] = $def;
                }
                
            }
        }
        
        if (
                isset($billing['customer_password']) &&
                $billing['customer_password'] != $billing['confirm_password'] && 
                $skipRequired
            ){
            
            $billing['confirm_password'] = $billing['customer_password'];
        }
        
        foreach($shippingDefaults as $key => $def){
            $val = isset($shipping[$key]) ? $shipping[$key] : "";

            $empty = $val == "" || (is_array($val) && implode("", $val) == "");
            
            if ($empty){
                
                if ($skipRequired || !in_array($key, $requiredFields["shipping"])){
                    $shipping[$key] = $def;
                }
                
            } 
        }

        $this->getRequest()->setPost('billing', $billing);
        $this->getRequest()->setPost('shipping', $shipping);
    }
    
    public function updateAction(){
        $this->_prepareGifts();
        $this->_reloadRequest();
        $amResponse = $this->_saveSteps(FALSE);
//        $this->_redirect('*/*/render', array('_secure' => true));
//    }
//
//    public function renderAction(){
//
//        $this->getOnepage()->getQuote()->setTotalsCollectedFlag(false);
//        $this->getOnepage()->getQuote()->collectTotals();
//        $this->getOnepage()->getQuote()->save();
        
        
        
        $this->_render();
    }
    
    public function renderAction(){
        $quote = $this->getOnepage()->getQuote();

        if (Mage::helper("amscheckout")->isQuickFirstLoad()) {
            $quote->collectTotals();
        }

        $quote->getShippingAddress()->setCollectShippingRates(true);
        
        $this->_render(true);
    }
    
    protected function _render($fullReload = false){
        $hlr = Mage::helper("amscheckout");
        $updatedSection = $this->getRequest()->getPost('updated_section', null);
        $html = array();
        
        switch ($updatedSection){
            case "shipping_method":
                    $html["review"] = $this->_getReviewHtml();
                    $html["base_grand_total_updated"] = $this->getQuoteBaseGrandTotal();

                    if ($hlr->reloadPaymentShippingMethodChanged() || $fullReload) {
                        $html["payment_method"] = $this->_getPaymentMethodsHtml();
                    }

                break;
            case "payment_method":
                    $html["review"] = $this->_getReviewHtml();
                break;
            default:
                    $html["review"] = $this->_getReviewHtml();

                    if ($hlr->reloadAfterShippingMethodChanged() || $fullReload) {
                        $html["shipping_method"] = $this->_getShippingMethodsHtml();
                    }

                    if ($hlr->reloadPaymentShippingMethodChanged() || $fullReload) {
                        $html["payment_method"] = $this->_getPaymentMethodsHtml();
                    }
                break;
        }
        
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
            "html" => $html
        )));
    }
    
    protected function _updateShoppingCart(){
        $hlr = Mage::helper("amscheckout");
        
        $cartData = $this->getRequest()->getParam($hlr->isShoppingCartOnCheckout() && !$hlr->isMergeShoppingCartCheckout() ?
            'cart' : 'review'
        , array());
            
        $filter = new Zend_Filter_LocalizedToNormalized(
            array('locale' => Mage::app()->getLocale()->getLocaleCode())
        );
        foreach ($cartData as $index => $data) {
            if (isset($data['qty'])) {
                $cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
            }
        }

        $cart = $this->_getCart();
        if (! $cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
            $cart->getQuote()->setCustomerId(null);
        }

        $cartData = $cart->suggestItemsQty($cartData);
        $cart->updateItems($cartData)->save();
    }
    
    protected function _emptyShoppingCart()
    {
        $this->_getCart()->truncate();
        $this->_getCart()->save();   
    }
    
    public function cartAction(){
        if ($this->_expireAjax()) {
            return;
        }
        
        $updateAction = (string)$this->getRequest()->getParam('update_cart_action');

        switch ($updateAction) {
            case 'empty_cart':
                $this->_emptyShoppingCart();
                break;
            case 'update_qty':
                $this->_updateShoppingCart();
                break;
            default:
                $this->_updateShoppingCart();
        }

        if (0 == $this->_getCart()->getItemsCount()) {
            $this->getResponse()->setBody('false');
            return;
        }

        $this->_reloadRequest();
        $amResponse = $this->_saveSteps(FALSE);
        
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
            "html" => array(
                "review" => $this->_getReviewHtml(),
                "cart" => $this->_getCartHtml(),
                
                "shipping_method" => $this->_getShippingMethodsHtml(),
                "payment_method" => $this->_getPaymentMethodsHtml(),
            )
        )));
    }
    
    public function deleteAction(){
        if ($this->_expireAjax()) {
            return;
        }
        $id = (int) $this->getRequest()->getParam('delete_cart_id');

        $this->_prepareGifts();
        
        $this->_getCart()->removeItem($id)
                  ->save();

        if (0 == $this->_getCart()->getItemsCount()) {
            $this->getResponse()->setBody('false');
            return;
        }

        $this->_reloadRequest();
        $amResponse = $this->_saveSteps(FALSE);
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
            "html" => array(
                "review" => $this->_getReviewHtml(),
                "cart" => $this->_getCartHtml(),
                
                "shipping_method" => $this->_getShippingMethodsHtml(),
                "payment_method" => $this->_getPaymentMethodsHtml(),
            )
        )));
    }
    
    
    
    public function checkoutAction(){
        $res = array();
        $this->_reloadRequest(FALSE);
        
        $postMethod = $this->getRequest()->getParam('method');

        $this->_prepareGifts();
        
        $amResponse = $this->_saveSteps(FALSE);

        $paymentMethod = $this->getOnepage()->getQuote()->getPayment()->getMethod();
        if (
            $paymentMethod == 'sagepayserver' ||
            $paymentMethod == 'sagepaydirectpro' ||
            $paymentMethod == 'sagepayform' ||
            $paymentMethod == 'sagepaypaypal'
        ){
            if ($requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds()) {
                $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
                if ($diff = array_diff($requiredAgreements, $postedAgreements)) {

                    $this->getRequest()->setPost('method', $postMethod);

                    $amResponse = $this->_saveSteps(TRUE);
                }
            }

            $this->getRequest()->setPost('method', $postMethod);

            $this->_amSaveBilling();
            
            if ($postMethod == 'register' && $this->getOnepage()->customerEmailExists()){
                $amResponse->setError(Mage::helper('checkout')->__('There is already a customer registered using this email address. Please login using this email address or enter a different email address to register your account.'));
            }

            if ($amResponse->getErrorsCount() == 0){

                $this->getRequest()->setPost('method', $postMethod);

                Mage::getSingleton('checkout/session')->setAmscheckoutIsSubscribed(Mage::app()->getRequest()->getParam('is_subscribed', false) == true);

                if ($paymentMethod == 'sagepayserver') {
                    $this->_forward('saveOrder', 'serverPayment', 'sgps', $this->getRequest()->getParams());
                    return;
                } else if ($paymentMethod == 'sagepaydirectpro') {
                    $this->_forward('saveOrder', 'directPayment', 'sgps', $this->getRequest()->getParams());
                    return;
                } else if ($paymentMethod == 'sagepayform') {
                    $this->_forward('saveOrder', 'formPayment', 'sgps', $this->getRequest()->getParams());
                    return;
                } else if ($paymentMethod == 'sagepaypaypal') {
                    $resultData = array(
                        'success' => 'true',
                        'response_status' => 'paypal_redirect',
                        'redirect' => Mage::getModel('core/url')->addSessionParam()->getUrl('sgps/paypalexpress/go', array('_secure' => true))
                    );
                    return $this->getResponse()->setBody(Zend_Json :: encode($resultData));

                } else if ($paymentMethod == 'sagepaynit') {
                    $this->_forward('saveOrder', 'nitPayment', 'sgps', $this->getRequest()->getParams());
                    return;
                }

            } else {
                $messagesBlock = $this->getLayout()->getMessagesBlock();

                foreach($amResponse->getErrors() as $error){
                    $messagesBlock->addError($error);
                }

                $res = array(
                    "status" => "error",
                    "errorsHtml" => $messagesBlock->toHtml(),
                    "errors" => implode("\n", $amResponse->getErrors())
                );
            }
        
        } else {

        $this->getRequest()->setPost('method', $postMethod);
        
        $amResponse = $this->_saveSteps(TRUE);
        
        $redirectUrl = $amResponse->getRedirect();
        
        $agreements = $this->_checkAgreements();
        if (isset($agreements['error_messages'])){
            if (is_array($agreements['error_messages'])) {
                $agreements['error_messages'] = array_unique($agreements['error_messages']);
            }
            $amResponse->setError($agreements['error_messages']);
            $redirectUrl = null;
        }
        
        if ($redirectUrl && $amResponse->getErrorsCount() == 0) {
            $res = array(
                "redirect_url" => $redirectUrl
            );
        }
        else if ($amResponse->getErrorsCount() == 0) {
            $res = array(
                "status" => "ok"
            );
        } else {
            $messagesBlock = $this->getLayout()->getMessagesBlock();

            foreach($amResponse->getErrors() as $error) {
                $messagesBlock->addError($error);
            }

            $res = array(
                "status" => "error",
                "errorsHtml" => $messagesBlock->toHtml(),
                "errors" => implode("\n", $amResponse->getErrors())
            );
        }
        }
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($res));
        
    }
    
    protected function _checkAgreements() {

        $result = array();
        $requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds();
        if ($requiredAgreements) {
            $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
            $diff = array_diff($requiredAgreements, $postedAgreements);
            if ($diff) {
                $result['success'] = false;
                $result['error'] = true;
                $result['error_messages'] = $this->__('Please agree to all the terms and conditions before placing the order.');

            }
        }
        return $result;
    }


    public function savePaymentAction()
    {
        $result = array();
        
        if ($this->_expireAjax()) {
            return;
        }

//        $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
//        if (!empty($customerAddressId)) {
//            $address = $this->getOnepage()->getQuote()->getBillingAddress();
//            $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
//            $address->importCustomerAddress($customerAddress)->setSaveInAddressBook(0);
//
//            $address2 = $this->getOnepage()->getQuote()->getShippingAddress();
//            $address2->importCustomerAddress($customerAddress)->setSaveInAddressBook(0);
//        }

        $payment = $this->getRequest()->getPost('payment', array());
        
        try {
            $this->getOnepage()->savePayment($payment);

            if($payment){
                $this->getOnepage()->getQuote()->getPayment()->importData($payment);
            }
            $paymentRedirect = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();

            if ($paymentRedirect){
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
                   'redirect' => $paymentRedirect
                )));
            }
        }
        catch(Exception $e) {
                //
        }
        
        return ;
    }
    
    protected function _getShippingMethodsHtml()
    {
        $output = "";
        
        if (!$this->_skip_generate_html){
            $this->getLayout()->getUpdate()->setCacheId(uniqid("amscheckout_shipping"));
            $output = Mage::helper("amscheckout")->getLayoutHtml("checkout_onepage_shippingmethod");
        }
        
        return $output;
    }

    protected function _getPaymentMethodsHtml()
    {
        $output = "";
        
        if (!$this->_skip_generate_html){
            
            $this->getLayout()->getUpdate()->setCacheId(uniqid("amscheckout_payment"));
            $output = Mage::helper("amscheckout")->getLayoutHtml("checkout_onepage_paymentmethod");
        }
        
        return $output;
    }

    protected function _getReviewHtml()
    {
        $output = "";
        
        if (!$this->_skip_generate_html){
            $this->getLayout()->getUpdate()->setCacheId(uniqid("amscheckout_review"));
            
            $output = Mage::helper("amscheckout")->getLayoutHtml("checkout_onepage_review");
        }
        
        return $output;
    }
    
    protected function _getCouponHtml()
    {
        $output = "";
        
        if (!$this->_skip_generate_html){
            Mage::app()->getRequest()->setActionName('coupon');
            $this->getLayout()->getUpdate()->setCacheId(uniqid("amscheckout_coupon"));

            $output = Mage::helper("amscheckout")->getLayoutHtml("amscheckout_onepage_coupon");
        }
        
        return $output;
    }
    
    protected function _getGiftCardHtml()
    {
        $output = "";

        if (!$this->_skip_generate_html){
            Mage::app()->getRequest()->setActionName('add');
            $this->getLayout()->getUpdate()->setCacheId(uniqid("amscheckout_giftcardaccount"));

            $output = Mage::helper("amscheckout")->getLayoutHtml("amscheckout_onepage_giftcardaccount");
        }

        return $output;
    }

    protected function _getAmGiftCardHtml()
    {
        $output = "";

        if (!$this->_skip_generate_html){
            Mage::app()->getRequest()->setActionName('add');
            $this->getLayout()->getUpdate()->setCacheId(uniqid("amscheckout_amgiftcardaccount"));

            $output = Mage::helper("amscheckout")->getLayoutHtml("amscheckout_onepage_amgiftcardaccount");
        }

        return $output;
    }
    
    protected function _getCartHtml()
    {
        $output = "";
        $hlr = Mage::helper("amscheckout");
        
        if (!$this->_skip_generate_html && $hlr->isShoppingCartOnCheckout() && !$hlr->isMergeShoppingCartCheckout()){
            $this->getLayout()->getUpdate()->setCacheId(uniqid("amscheckout_cart"));

            $output = Mage::helper("amscheckout")->getLayoutHtml("amscheckout_cart");
        }
        
        return $output;
    }

    /**
     * @return Amasty_Scheckout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }
    
    public function cancelCouponAction(){
        $messagesBlock = $this->getLayout()->getMessagesBlock();

        $messagesBlock->addSuccess($this->__('Coupon code was canceled.'));

        $this->getOnepage()->getQuote()->setTotalsCollectedFlag(false);
        $this->getOnepage()->getQuote()->collectTotals();
        $this->getOnepage()->getQuote()->save();

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
            "html" => array(
                "review" => $this->_getReviewHtml(),
                "shipping_method" => $this->_getShippingMethodsHtml(),
                "payment_method" => $this->_getPaymentMethodsHtml(),
                "coupon" => array(
                    "message" => $messagesBlock->toHtml(),
                    "output" => $this->_getCouponHtml()
                )
                
            )
        )));
    }
    
    public function couponPostAction(){
       
        $response = array(
            "html" => array(
                "coupon" => array(
                    "message" => NULL,
                    "output" => NULL
                )
            )
        );
        
        $output = &$response["html"]["coupon"]["output"];
        
        $messagesBlock = $this->getLayout()->getMessagesBlock();
        
        $couponCode = (string) $this->getRequest()->getParam('coupon_code');
        if ($this->getRequest()->getParam('remove') == 1) {
            $couponCode = '';
        }
        $oldCouponCode = $this->getOnepage()->getQuote()->getCouponCode();

        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            
        } else {
            $this->_reloadRequest();

            try {
                $codeLength = strlen($couponCode);
        
                $isCodeLengthValid = $codeLength && $codeLength <= 255;

                $this->getOnepage()->getQuote()->getShippingAddress()->setCollectShippingRates(true);
                $this->getOnepage()->getQuote()->setCouponCode($isCodeLengthValid ? $couponCode : '')
                    ->collectTotals()
                    ->save();

                if ($codeLength) {
                    if ($isCodeLengthValid && $couponCode == $this->getOnepage()->getQuote()->getCouponCode()) {
                        $messagesBlock->addSuccess($this->__('Coupon code "%s" was applied.', Mage::helper('core')->escapeHtml($couponCode)));
                    } else {
                         $messagesBlock->addError($this->__('Coupon code "%s" is not valid.', Mage::helper('core')->escapeHtml($couponCode)));
                    }
                } else {
                    $messagesBlock->addSuccess($this->__('Coupon code was canceled.'));
                }
                $output = $this->_getCouponHtml();

                $this->getOnepage()->getQuote()->setTotalsCollectedFlag(false);
                $this->getOnepage()->getQuote()->collectTotals();
                $this->getOnepage()->getQuote()->save();
                
                $response["html"]["review"] = $this->_getReviewHtml();
                $response["html"]["shipping_method"] = $this->_getShippingMethodsHtml();
                $response["html"]["payment_method"] = $this->_getPaymentMethodsHtml();
                $response["html"]["base_grand_total_updated"] = $this->getQuoteBaseGrandTotal();


            } catch (Mage_Core_Exception $e) {
                $messagesBlock->addError($e->getMessage());
            } catch (Exception $e) {
                $messagesBlock->addError($this->__('Cannot apply the coupon code.'));
                Mage::logException($e);
            }
        }
        
        
        $response["html"]["coupon"]["message"] = $messagesBlock->toHtml();
                
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    /**
     * Get Current Quote Base Grand Total
     *
     * @return float
     */
    protected function getQuoteBaseGrandTotal()
    {
        return (float)$this->getOnepage()->getQuote()->getBaseGrandTotal();
    }

    protected function _renderGiftCart(&$response){
        $this->getOnepage()->getQuote()->collectTotals();
        $this->getOnepage()->getQuote()->save();

        $response["html"]["giftcard"]["output"] = $this->_getGiftCardHtml();

        $response["html"]["review"] = $this->_getReviewHtml();
        $response["html"]["payment_method"] = $this->_getPaymentMethodsHtml();
    }

    public function giftcartAction(){
        $response = array(
                    "html" => array(
                        "giftcard" => array(
                            "message" => NULL,
                            "output" => NULL
                        )
                    )
                );

        $messagesBlock = $this->getLayout()->getMessagesBlock();

        $data = $this->getRequest()->getPost();
        if (isset($data['giftcard_code'])) {
            $code = $data['giftcard_code'];
            try {
                if (strlen($code) > Enterprise_GiftCardAccount_Helper_Data::GIFT_CARD_CODE_MAX_LENGTH) {
                    Mage::throwException(Mage::helper('enterprise_giftcardaccount')->__('Wrong gift card code.'));
                }
                Mage::getModel('enterprise_giftcardaccount/giftcardaccount')
                    ->loadByCode($code)
                    ->addToCart();

                $this->_renderGiftCart($response);

                $messagesBlock->addSuccess(
                    $this->__('Gift Card "%s" was added.', Mage::helper('core')->escapeHtml($code))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::dispatchEvent('enterprise_giftcardaccount_add', array('status' => 'fail', 'code' => $code));
                $messagesBlock->addError(
                    $e->getMessage()
                );
            } catch (Exception $e) {
                $messagesBlock->addError($this->__('Cannot apply gift card.'));
            }
        }

        $response["html"]["giftcard"]["message"] = $messagesBlock->toHtml();

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    public function giftcartcancelAction(){
        $response = array(
                    "html" => array(
                        "giftcard" => array(
                            "message" => NULL,
                            "output" => NULL
                        )
                    )
                );

        $messagesBlock = $this->getLayout()->getMessagesBlock();

        if ($code = $this->getRequest()->getParam('code')) {
            try {
                Mage::getModel('enterprise_giftcardaccount/giftcardaccount')
                    ->loadByCode($code)
                    ->removeFromCart();

                $this->_renderGiftCart($response);

                $messagesBlock->addSuccess(
                    $this->__('Gift Card "%s" was removed.', Mage::helper('core')->escapeHtml($code))
                );
            } catch (Mage_Core_Exception $e) {
                $messagesBlock->addError(
                    $e->getMessage()
                );
            } catch (Exception $e) {
                $messagesBlock->addError($this->__('Cannot remove gift card.'));
            }

        }

        $response["html"]["giftcard"]["message"] = $messagesBlock->toHtml();

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));

    }

    protected function _renderAmGiftCart(&$response){
        $this->getOnepage()->getQuote()->collectTotals();
        $this->getOnepage()->getQuote()->save();

        $response["html"]["amgiftcard"]["output"] = $this->_getAmGiftCardHtml();

        $response["html"]["review"] = $this->_getReviewHtml();
        $response["html"]["payment_method"] = $this->_getPaymentMethodsHtml();
    }


    public function amgiftcartAction(){
        $response = array(
            "html" => array(
                "amgiftcard" => array(
                    "message" => NULL,
                    "output" => NULL
                )
            )
        );

        $messagesBlock = $this->getLayout()->getMessagesBlock();

        $data = $this->getRequest()->getPost();
        if (isset($data['amgiftcard_code'])) {
            $code = trim($data['amgiftcard_code']);
            try {
                Mage::getModel('amgiftcard/account')
                    ->loadByCode($code)
                    ->addToCart();

                $this->_renderAmGiftCart($response);

                $messagesBlock->addSuccess(
                    $this->__('Gift Card "%s" was added.', Mage::helper('core')->escapeHtml($code))
                );
            } catch (Mage_Core_Exception $e) {
                $messagesBlock->addError(
                    $e->getMessage()
                );
            } catch (Exception $e) {
                $messagesBlock->addError($this->__('Cannot apply gift card.'));
            }
        }

        $response['baseGrandTotal'] = $this->getOnepage()->getQuote()->getBaseGrandTotal();

        $response["html"]["amgiftcard"]["message"] = $messagesBlock->toHtml();

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }


    public function amgiftcartcancelAction(){
        $response = array(
            "html" => array(
                "amgiftcard" => array(
                    "message" => NULL,
                    "output" => NULL
                )
            )
        );

        $messagesBlock = $this->getLayout()->getMessagesBlock();

        if ($code = $this->getRequest()->getParam('code')) {
            try {
                Mage::getModel('amgiftcard/account')
                    ->loadByCode($code)
                    ->removeFromCart();

                $this->_renderGiftCart($response);

                $messagesBlock->addSuccess(
                    $this->__('Gift Card "%s" was removed.', Mage::helper('core')->escapeHtml($code))
                );
            } catch (Mage_Core_Exception $e) {
                $messagesBlock->addError(
                    $e->getMessage()
                );
            } catch (Exception $e) {
                $messagesBlock->addError($this->__('Cannot remove gift card.'));
            }

        }

        $response["html"]["amgiftcard"]["message"] = $messagesBlock->toHtml();

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));

    }

    protected function _expireAjax()
    {
        if ($this->getOnepage()->getQuote()->getHasError()){ //show errors on checkout without redirect to cart
            return false;
        }

        return parent::_expireAjax();
    }

    protected function _prepareGifts()
    {
        $giftMessage = $this->getRequest()->getParam('giftmessage');
        if (is_array($giftMessage)) {
            $quoteItemsIds = $this->_collectQuoteItemIds();
            foreach ($giftMessage as $entityId => $message) {
                if ($message['type'] == 'quote_item') {
                    if (!in_array($entityId, $quoteItemsIds) && isset($giftMessage[$entityId])) {
                        unset($giftMessage[$entityId]);
                    }
                }
            }
            $this->getRequest()->setPost('giftmessage', $giftMessage);
        }
    }

    protected function _collectQuoteItemIds()
    {
        $quoteItemsIds = array();
        $quote      = $this->getOnepage()->getQuote();
        foreach ($quote->getAllVisibleItems() as $item) {
            $quoteItemsIds[] = $item->getId();
        }

        return $quoteItemsIds;
    }
}
