<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Scheckout
 */


class Amasty_Scheckout_Model_Validate_Hostname extends Zend_Validate_Hostname
{
    public function getValidTlds()
    {
        return $this->_validTlds;
    }
}