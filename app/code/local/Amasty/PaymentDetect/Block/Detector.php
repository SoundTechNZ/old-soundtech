<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_PaymentDetect
 */

/**
 * Class Amasty_PaymentDetect_Block_Detector
 * @author Artem Brunevski
 */
class Amasty_PaymentDetect_Block_Detector extends Mage_Checkout_Block_Onepage_Abstract
{
    /**
     * payment methods icons
     * @return array
     */
    public function getIcons()
    {
        /** @var Amasty_PaymentDetect_Helper_Data $helper */
        $helper = Mage::helper('amasty_paymentdetect');

        return array(
            'visa' => $helper->getVisaUrl(),
            'amex' => $helper->getAmexUrl(),
            'mastercard' => $helper->getMastercardUrl(),
            'discover' => $helper->getDiscoverUrl(),
            'jcb' => $helper->getJcbUrl(),
            'maestro' => $helper->getMaestroUrl(),
        );
    }

    /**
     * payment methods titles
     * @return array
     */
    public function getTitles()
    {
        /** @var Amasty_PaymentDetect_Helper_Data $helper */
        $helper = Mage::helper('amasty_paymentdetect');

        return array(
            'visa' => $helper->getVisaTitle(),
            'amex' => $helper->getAmexTitle(),
            'mastercard' => $helper->getMastercardTitle(),
            'discover' => $helper->getDiscoverTitle(),
            'jcb' => $helper->getJcbTitle(),
            'maestro' => $helper->getMaestroTitle(),
        );
    }

    /**
     * payment methods orders
     * @return array
     */
    public function getOrders()
    {
        /** @var Amasty_PaymentDetect_Helper_Data $helper */
        $helper = Mage::helper('amasty_paymentdetect');

        return array(
            'visa' => $helper->getVisaOrder(),
            'amex' => $helper->getAmexOrder(),
            'mastercard' => $helper->getMastercardOrder(),
            'discover' => $helper->getDiscoverOrder(),
            'jcb' => $helper->getJcbOrder(),
            'maestro' => $helper->getMaestroOrder(),
        );
    }

    /**
     * payment methods configuration
     * @return array
     */
    public function getModules()
    {
        return array(
            'ccsave' => array(
                'selectors' => array(
                    'number' => 'input#ccsave_cc_number',
                    'type' => 'select#ccsave_cc_type'
                ),
                'options' => array(
                    'visa' => 'VI',
                    'amex' => 'AE',
                    'mastercard' => 'MC',
                    'discover' => 'DI',
                    'jcb' => 'JCB',
                    'maestro' => 'SM'
                )
            ),
            'authorizenet' => array(
                'selectors' => array(
                    'number' => 'input#authorizenet_cc_number',
                    'type' => 'select#authorizenet_cc_type'
                ),
                'options' => array(
                    'visa' => 'VI',
                    'amex' => 'AE',
                    'mastercard' => 'MC',
                    'discover' => 'DI',
                    'jcb' => 'JCB',
                    'maestro' => 'SM'
                )
            ),
            'authnetcim' => array(
                'selectors' => array(
                    'number' => 'input#authnetcim_cc_number',
                    'type' => 'select#authnetcim_cc_type'
                ),
                'options' => array(
                    'visa' => 'VI',
                    'amex' => 'AE',
                    'mastercard' => 'MC',
                    'discover' => 'DI',
                    'jcb' => 'JCB',
                    'maestro' => 'SM'
                )
            )
        );
    }
}
