<?php


class Poli_Polipay_Block_Form extends Mage_Payment_Block_Form
{
	 protected $_methodCode = 'polipay_payment';
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('polipay/form.phtml');
    }

   
}