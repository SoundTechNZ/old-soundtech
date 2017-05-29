<?php

class Poli_Polipay_Model_Resource_Receipt extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('polipay/polipay_receipts', 'TransactionID');
		$this->_isPkAutoIncrement=false;
    }
}
