<?php

class IWD_Opc_Block_Onepage_Shipping_Method_Shipperhq_AvailableBase
    extends Shipperhq_Splitrates_Block_Checkout_Onepage_Shipping_Method_Available
{
    public function getTemplate()
    {
        return 'shipperhq/checkout/onepage/shipping_method/available.phtml';
    }
}
