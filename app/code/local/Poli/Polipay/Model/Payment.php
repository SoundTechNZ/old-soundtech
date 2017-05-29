<?php


class Poli_Polipay_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
    	
		
    protected $_code			= 'polipay_payment';
    protected $_paymentMethod	= 'payment';
	
	

    protected $_formBlockType = 'polipay/form';
    protected $_infoBlockType = 'polipay/info';


    protected $_isGateway               = false;
    protected $_canAuthorize            = false;
    protected $_canCapture              = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

    protected $_defaultLocale			= 'en';

   
    protected $_order;

    /**
     * Get order model
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
		if (!$this->_order) {
			$this->_order = $this->getInfoInstance()->getOrder();
		}
		return $this->_order;
    }

    public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('polipay/processing/redirect');
    }

    /**
     * Return payment method type string
     *
     * @return string
     */
    public function getPaymentMethodType()
    {
        return $this->_paymentMethod;
    }



	public function getTestMode(){
		
		return $this->getConfigData('testmode');

	}

	public function getTotal(){
		if ($this->getConfigData('use_store_currency')) {
        	$price      = number_format($this->getOrder()->getGrandTotal(),2,'.','');
		}else
        	$price      = number_format($this->getOrder()->getBaseGrandTotal(),2,'.','');
		return $price;			
		
	}
	public function getCurrency(){
		
        if ($this->getConfigData('use_store_currency')) {
        	$currency   = $this->getOrder()->getOrderCurrencyCode();
    	} else {
        	$currency   = $this->getOrder()->getBaseCurrencyCode();
    	}
		
	return $currency;		
	}
	/*
	 public function getUrl()
    {
    	return $this->_testUrl;
    }
	*/
	public function getMerchantCode(){
		
		return $this->getConfigData('merchantcode');
	}
	
	public function getOrderStatus(){
		return $this->getConfigData('order_status');
	}
	public function getAuthCode(){
		return Mage::helper('core')->decrypt($this->getConfigData('authcode'));	}


   
  
		
}

