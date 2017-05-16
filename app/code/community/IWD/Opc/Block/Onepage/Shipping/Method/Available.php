<?php


if (Mage::helper('core')->isModuleEnabled('Shipperhq_Splitrates')) {
   require_once ('app/code/community/IWD/Opc/Block/Shipping/Method/Shipperhq/AvailableBase.php');
} else {
    require_once (__DIR__ . '/Shipperhq/Standard/AvailableBase.php');
}
class IWD_Opc_Block_Onepage_Shipping_Method_Available
    extends IWD_Opc_Block_Onepage_Shipping_Method_Shipperhq_Standard_AvailableBase
{
    
}