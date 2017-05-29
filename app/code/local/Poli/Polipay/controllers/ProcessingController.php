<?php
ob_start();
class Poli_Polipay_ProcessingController extends Mage_Core_Controller_Front_Action {

    protected $_order = NULL;
    protected $_paymentInst = NULL;

    protected function _getCheckout() {
        return Mage::getSingleton('checkout/session');
    }

    public function redirectAction() {
        try {
            $session = $this->_getCheckout();

            if ($session->getPolipayQuoteId()) {
                $layout = $this->loadLayout();
                $block = $layout->getLayout()->getBlock('polipay.clean');
                $block->setCancelUrl(Mage::getUrl('polipay/processing/cancel', array('cToken' => base64_encode($session->getPolipayToken()))));
                $layout->renderLayout();
                return;
            }

            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($session->getLastRealOrderId());
            if (!$order->getId()) {
                Mage::throwException('No order for processing found');
            }
            $order->setState(
                Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, $this->_getPendingPaymentStatus(), Mage::helper('polipay')->__('Order created and attempting to communicate with POLi Payments.')
				)->save();
            $payment = $order->getPayment()->getMethodInstance();

            require_once(Mage::getBaseDir() . '/lib/polipayment/classes.php');

            $currencycode = $payment->getCurrency();

            $amt = $payment->getTotal();

            $tr = new PoliInitiateTransactionInput();
            $tr->CurrencyAmount = $amt;
            $tr->CurrencyCode = $currencycode;
            $tr->MerchantCode = $payment->getMerchantCode();
            $tr->MerchantData = $order->getId();
            $tr->SelectedFICode = '';
            $tr->MerchantRef = Mage::getSingleton('checkout/session')->getLastRealOrderId();
            $tr->NotificationURL = htmlentities(Mage::getUrl('polipay/processing/notify'));
            $tr->SuccessfulURL = htmlentities(Mage::getUrl('polipay/processing/success'));
            $tr->UnsuccessfulURL = htmlentities(Mage::getUrl('polipay/processing/cancel'));
            $tr->MerchantCheckoutURL = htmlentities(Mage::getUrl('polipay/processing/cancel'));
            $tr->MerchantHomePageURL = htmlentities(Mage::getUrl('home'));
            $tr->UserIPAddress = $_SERVER['REMOTE_ADDR'];
            $tr->Timeout = 1000;
            $tr->MerchantDateTime = date("Y-m-d\TH:i:s");
            $client = new PoliRestClient($payment->getMerchantCode(), $payment->getAuthCode(), $payment->getTestMode());
            $transaction = $client->initiatetransaction($tr, $errorout, $code);
			$this->_debug($transaction);
	
			try {
				$order->setState(
					Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, $this->_getPendingPaymentStatus(), Mage::helper('polipay')->__('Order redirect URL: '.(string) $transaction->NavigateURL)
					)->save();
			} catch (Exception $e) {
				$order->setState(
					Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, $this->_getPendingPaymentStatus(), Mage::helper('polipay')->__('Order redirect URL: '.$e)
					)->save();
			}
			
            if (isset($errorout) && count($errorout)) {
                foreach ($errorout as $error) {
                    Mage::throwException((string) $error->Message);
                }
            }

            if ($code != 'Initiated' && $code != 'FinancialInstitutionSelected') {
                throw new Exception("Invalid POLi transaction code: ".$code." :: ".$transaction);
            }

            $data = array('orderno' => Mage::getSingleton('checkout/session')->getLastRealOrderId(),'orderno2' => $order->getId(), 'refno' => (string) $transaction->TransactionRefNo, 'currency' => $currencycode, 'amount' => $amt, 'status' => 0, 'token' => (string) $transaction->TransactionToken);
            $m = Mage::getModel('polipay/transaction');
            $m->load(Mage::getSingleton('checkout/session')->getLastRealOrderId());
            if ($m->getOrderno()) {
                Mage::throwException("Order already processed");
            }

            $m->setData($data);
            $m->save();

            $session->setPolipayQuoteId($session->getQuoteId());
            $session->setPolipayToken((string) $transaction->TransactionToken);
            $session->setPolipayLastOrderId($order->getId());
            //$session->getQuote()->setIsActive(false)->save();
            $session->unsQuoteId();
			
            
            $order->setState(
                Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, $this->_getPendingPaymentStatus(), Mage::helper('polipay')->__('Customer was redirected to POLi Payments. POLi ID: '.$transaction->TransactionRefNo)
            )->save();
			
            $this->_redirectUrl((string) $transaction->NavigateURL);

            return;
        } catch (Mage_Core_Exception $e) {
		    $this->_debug('POLi Payment plugin error: ' . $e->getMessage());
            Mage::logException($e);
            $this->_getCheckout()->addError('POLi Payments plugin error: '.$e->getMessage());
        } catch (Exception $e) {
            $this->_debug('POLi Payment plugin error: ' . $e->getMessage());
            Mage::logException($e);
			$this->_getCheckout()->addError('POLi Payments plugin error: '.$e->getMessage());
        }
        $session->addNotice(Mage::helper('polipay')->__('Payment cannot proceed. please try again.'));

        $this->_redirect('checkout/cart');
    }

    private function completeTransaction($token, $orderid, $updateid) {
        require_once(Mage::getBaseDir() . '/lib/polipayment/classes.php');

        $this->_paymentInst = $payment = Mage::getSingleton('polipay/payment');

        $client = new PoliRestClient($payment->getMerchantCode(), $payment->getAuthCode(), $payment->getTestMode());
        $transaction = $client->getTransaction($token, $errorout, $code);
		$this->_debug($transaction);
        if (isset($errorout) && count($errorout)) {
            foreach ($errorout as $error) {
                Mage::throwException((string) $error->Message);
            }
        }
		if ($code == 'Cancelled') {
            $order = Mage::getModel('sales/order')->load($updateid);
            if ($order->getId()){
				$order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, Mage::helper('polipay')->__('Customer has cancelled their transaction.'))->save();			
			}
		}
        if ($code == 'Completed') {
            $trn = Mage::getModel('polipay/transaction');
            $trn->load($transaction->MerchantReference);

            if ($trn->getStatus() == 1) {
                exit;
            }
            $trn->setStatus(1);
            $trn->save();

            $arr = array('AmountPaid' => (string) $transaction->AmountPaid
                , 'BankReceipt' => (string) $transaction->BankReceipt
                , 'BankReceiptDateTime' => (string) $transaction->BankReceiptDateTime
                , 'CountryCode' => (string) $transaction->CountryCode
                , 'CountryName' => (string) $transaction->CountryName
                , 'CurrencyCode' => (string) $transaction->CurrencyCode
                , 'CurrencyName' => (string) $transaction->CurrencyName
                , 'EndDateTime' => (string) $transaction->EndDateTime
                , 'ErrorCode' => (string) $transaction->ErrorCode
                , 'ErrorMessage' => (string) $transaction->ErrorMessage
                , 'EstablishedDateTime' => (string) $transaction->EstablishedDateTime
                , 'FinancialInstitutionCode' => (string) $transaction->FinancialInstitutionCode
                , 'FinancialInstitutionCountryCode' => (string) $transaction->FinancialInstitutionCountryCode
                , 'FinancialInstitutionName' => (string) $transaction->FinancialInstitutionName
                , 'MerchantAcctName' => (string) $transaction->MerchantAcctName
                , 'MerchantAcctNumber' => (string) $transaction->MerchantAcctNumber
                , 'MerchantAcctSortCode' => (string) $transaction->MerchantAcctSortCode
                , 'MerchantAcctSuffix' => (string) $transaction->MerchantAcctSuffix
                , 'MerchantDefinedData' => (string) $transaction->MerchantDefinedData
                , 'MerchantEstablishedDateTime' => (string) $transaction->MerchantEstablishedDateTime
                , 'MerchantReference' => (string) $transaction->MerchantReference
                , 'PaymentAmount' => (string) $transaction->PaymentAmount
                , 'StartDateTime' => (string) $transaction->StartDateTime
                , 'TransactionID' => (string) $transaction->TransactionID
                , 'TransactionRefNo' => (string) $transaction->TransactionRefNo);
				
			
			
            $rec = Mage::getModel('polipay/receipt');
            $rec->setData($arr)->save();
			
            $order = Mage::getModel('sales/order')->load($updateid);

            if ($order->getId()){
				$payment = $order->getPayment()->getMethodInstance();
				$updatestatus = $payment->getOrderStatus();
				
				$invoiceId = Mage::getModel('sales/order_invoice_api')->create($order->getIncrementId(), array(), 'Paid with POLi Payments');
				if ($order->getTotalPaid() == 0 || $order->getTotalPaid() == null){
					$order->setBaseTotalPaid($arr['AmountPaid']);
					$this->_processSale($order, $arr['PaymentAmount'], $arr['CurrencyCode'], $arr['TransactionID'], $arr['TransactionRefNo'], $updatestatus);
				}
			}
            return $rec;
        }
        return null;
    }

    public function notifyAction() {
		        

        try {
            require_once(Mage::getBaseDir() . '/lib/polipayment/classes.php');
            $this->_paymentInst = $payment = Mage::getSingleton('polipay/payment');
			
            $errorout = null;
			if (!$token)
				$token = $this->getRequest()->getPost('Token', '');
			if (!$token) {
				$token = $this->getRequest()->get('token', '');
			}
			
			
			
            if (!$token)
                exit;

			require_once(Mage::getBaseDir() . '/lib/polipayment/classes.php');

			$this->_paymentInst = $payment = Mage::getSingleton('polipay/payment');

			$client = new PoliRestClient($payment->getMerchantCode(), $payment->getAuthCode(), $payment->getTestMode());
			$transaction = $client->getTransaction($token, $errorout, $code);
			

			
			
			


			
            $order = Mage::getModel('sales/order')->load($transaction->MerchantDefinedData);
			
			$payment = $order->getPayment()->getMethodInstance();
			
			$updatestatus = $payment->getOrderStatus();

            $client = new PoliRestClient($payment->getMerchantCode(), $payment->getAuthCode(), $payment->getTestMode());

            $transaction = $client->getTransaction($token, $errorout, $code);

            if (isset($errorout) && count($errorout)) {

                foreach ($errorout as $error) {

                    Mage::throwException((string) $error->Message);
                }
            }
			if ($code == 'Cancelled') {
				if ($order->getId()){
					$order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, Mage::helper('polipay')->__('Customer has cancelled their transaction.'))->save();			
				}
			}
			
			$trn = Mage::getModel('polipay/transaction')->getCollection()->addFieldToFilter('token', $token)->getFirstItem();

            if (!$trn) {
                Mage::app()->getResponse()
                        ->setHeader('HTTP/1.1', '500 Service Unavailable')
                        ->sendResponse();
                exit;
            }
			
            if ($code == 'Completed') {

                $trn->setStatus(1);
                $trn->save();
                $arr = array('AmountPaid' => (string) $transaction->AmountPaid
                    , 'BankReceipt' => (string) $transaction->BankReceipt
                    , 'BankReceiptDateTime' => (string) $transaction->BankReceiptDateTime
                    , 'CountryCode' => (string) $transaction->CountryCode
                    , 'CountryName' => (string) $transaction->CountryName
                    , 'CurrencyCode' => (string) $transaction->CurrencyCode
                    , 'CurrencyName' => (string) $transaction->CurrencyName
                    , 'EndDateTime' => (string) $transaction->EndDateTime
                    , 'ErrorCode' => (string) $transaction->ErrorCode
                    , 'ErrorMessage' => (string) $transaction->ErrorMessage
                    , 'EstablishedDateTime' => (string) $transaction->EstablishedDateTime
                    , 'FinancialInstitutionCode' => (string) $transaction->FinancialInstitutionCode
                    , 'FinancialInstitutionCountryCode' => (string) $transaction->FinancialInstitutionCountryCode
                    , 'FinancialInstitutionName' => (string) $transaction->FinancialInstitutionName
                    , 'MerchantAcctName' => (string) $transaction->MerchantAcctName
                    , 'MerchantAcctNumber' => (string) $transaction->MerchantAcctNumber
                    , 'MerchantAcctSortCode' => (string) $transaction->MerchantAcctSortCode
                    , 'MerchantAcctSuffix' => (string) $transaction->MerchantAcctSuffix
                    , 'MerchantDefinedData' => (string) $transaction->MerchantDefinedData
                    , 'MerchantEstablishedDateTime' => (string) $transaction->MerchantEstablishedDateTime
                    , 'MerchantReference' => (string) $transaction->MerchantReference
                    , 'PaymentAmount' => (string) $transaction->PaymentAmount
                    , 'StartDateTime' => (string) $transaction->StartDateTime
                    , 'TransactionID' => (string) $transaction->TransactionID
                    , 'TransactionRefNo' => (string) $transaction->TransactionRefNo);
                $rec = Mage::getModel('polipay/receipt');
                $rec->setData($arr)->save();
				if ($order->getTotalPaid() == 0 || $order->getTotalPaid() == null){
					$order->setBaseTotalPaid($arr['AmountPaid']);
					$this->_processSale($order, $arr['PaymentAmount'], $arr['CurrencyCode'], $arr['TransactionID'], $arr['TransactionRefNo'], $updatestatus);
				}
                
                $invoiceId = Mage::getModel('sales/order_invoice_api')->create($order->getIncrementId(), array(), 'Paid with POLi Payments');
				Mage::app()->getResponse()
                        ->setHeader('HTTP/1.1', '200 OK')
                        ->sendResponse();
                exit;
            }
        } catch (Mage_Core_Exception $e) {
            $this->_debug('Polipay response error: ' . $e->getMessage());
            $this->getResponse()->setBody(
                    $this->getLayout()
                            ->createBlock($this->_failureBlockType)
                            ->setOrder($this->_order)
                            ->toHtml()
            );
            throw new Exception($e);
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }

    public function cancelAction($token = '') {
        if (!$token)
            $token = $this->getRequest()->getPost('Token', '');
        if (!$token) {
            $token = $this->getRequest()->get('Token', '');
        }
		if (!$token) {
            $token = $this->getRequest()->get('token', '');
        }
        if (!$token) {
            $token = base64_decode($this->getRequest()->get('cToken', ''));
        }
        if ($token) {
            $transaction = Mage::getModel('polipay/transaction')->getCollection()->addFieldToFilter('token', $token)->getFirstItem();
            $trn = Mage::getModel('polipay/transaction');
            $trn->setId($transaction['orderno'])->delete();
        }
        $session = $this->_getCheckout();
        $quote = $session->getQuote();
        $quoteid = $session->getPolipayQuoteId(true);
        $quote->load($quoteid);
        $session->setQuoteId($quoteid);
        $session->getQuote()->setIsActive(true)->save();

        $order = Mage::getModel('sales/order');
        $order->load($session->getPolipayLastOrderId(true));
        if ($order->getId()) {
            $order->cancel();
            $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, Mage::helper('polipay')->__('Cancelation of payment'));
            $order->save();
        }

        $session->addNotice(Mage::helper('polipay')->__('Cancelation of payment'));
        $this->_redirect('checkout/cart');
        return;
    }

    public function successAction() {
        $session = $this->_getCheckout();
/*
		// Removed from ticket #4957
        if (!$session->getLastSuccessQuoteId()) {
            $this->_redirect('checkout/cart');
            return;
        }
*/
        try {
            $token = $this->getRequest()->get('token');
            $orderid = $session->getPolipayLastOrderId(true);
            if ($session->getPolipayQuoteId()) {
                $session->setQuoteId($session->getPolipayQuoteId(true));
                $session->getQuote()->setIsActive(false)->save();
            }
            $session->unsPolipayLastOrderId();

            $layout = $this->loadLayout();

            $block = $layout->getLayout()->getBlock('polipay.success');

            $transaction = Mage::getModel('polipay/transaction')->getCollection()->addFieldToFilter('token', $token)->getFirstItem();
            if (!$transaction || !isset($transaction['orderno'])) {
                $block->setError('1');
            } else if ($transaction['status']) {
                $receipt = Mage::getModel('polipay/receipt')->getCollection()->addFieldToFilter('MerchantReference', $transaction['orderno'])->getFirstItem();
                $block->setReceipt($receipt);
            } else {
                $receipt = $this->completeTransaction($token, $transaction['orderno'], $orderid);
                if ($receipt == null) {
                    $block->setError('1');
                } else {
                    $block->setReceipt($receipt);
                }
            }
			
            $session->clear();
            $this->_initLayoutMessages('checkout/session');
            $layout->renderLayout();            
            if ($orderid)
                Mage::dispatchEvent('checkout_polipay_controller_success_action', array('order_ids' => array($orderid)));

            return;
        } catch (Mage_Core_Exception $e) {
            $this->_getCheckout()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_debug('Polipay error: ' . $e->getMessage());
            Mage::logException($e);
        }

        $this->_redirect('checkout/cart');
    }

    /**
     * Process success response
     */
    protected function _processSale($order, $amt, $cur, $tid, $tref, $updatestatus) {
        // check transaction amount and currency
        if ($this->_paymentInst->getConfigData('use_store_currency')) {
            $price = number_format($order->getGrandTotal(), 2, '.', '');
            $currency = $order->getOrderCurrencyCode();
        } else {
            $price = number_format($order->getBaseGrandTotal(), 2, '.', '');
            $currency = $order->getBaseCurrencyCode();
        }

        // check transaction amount
        if ($price != $amt)
            Mage::throwException('Transaction currency doesn\'t match.');

        // check transaction currency
        if ($currency != $cur)
            Mage::throwException('Transaction currency doesn\'t match.');

        $trs = $order->getPayment()->setTransactionId($tref)->addTransaction(Mage_Payment_Model_Method_Abstract::ACTION_ORDER);
        $trs->save();
	
		
		$orderstatus = "processing";
		if($updatestatus == "pending") {$orderstatus = "pending";}
        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, $orderstatus, Mage::helper('polipay')->__('Customer returned successfully'))->save();
		
        $order->sendNewOrderEmail();
        $order->setEmailSent(true);

        $order->save();
    }

    protected function _getPendingPaymentStatus() {
        return Mage::helper('polipay')->getPendingPaymentStatus();
    }

    /**
     * Log debug data to file
     *
     * @param mixed $debugData
     */
    protected function _debug($debugData) {
        Mage::log($debugData, null, 'payment_polipay.log', true);
    }

}