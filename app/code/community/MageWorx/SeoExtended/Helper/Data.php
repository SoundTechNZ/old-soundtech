<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Retrieve step of modification of the template by requested params
     * @return string
     */
    public function getStep()
    {
        if(!Mage::app()->getRequest()->getParam('id') && !Mage::app()->getRequest()->getParam('attribute_id')){
            return 'new_step_1';
        }
        elseif(!Mage::app()->getRequest()->getParam('id') && Mage::app()->getRequest()->getParam('attribute_id')){
            return 'new_step_2';
        }
        elseif(Mage::app()->getRequest()->getParam('id')){
            return 'edit';
        }
    }

    /**
     * @return string
     */
    public function getLostDataConfirmMessage()
    {
        return $this->__("Please confirm the action. All data that has not been saved will be lost.");
    }
}