<?php

class Poli_Polipay_Block_Info extends Mage_Payment_Block_Info {

    protected function _construct() {
        parent::_construct();
        $this->setTemplate('polipay/info.phtml');
    }

    public function getMethodCode() {
        return $this->getInfo()->getMethodInstance()->getCode();
    }

}