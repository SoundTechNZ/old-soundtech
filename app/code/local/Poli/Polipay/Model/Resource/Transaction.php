<?php

class Poli_Polipay_Model_Resource_Transaction extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('polipay/polipay_transactions', 'orderno');
		$this->_isPkAutoIncrement=false;
    }
	
}
