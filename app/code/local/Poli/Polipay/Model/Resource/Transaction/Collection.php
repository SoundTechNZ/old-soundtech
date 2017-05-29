<?php

class Poli_Polipay_Model_Resource_Transaction_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('polipay/transaction');
    }
}
