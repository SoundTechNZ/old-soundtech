<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_PaymentDetect
 */


/**
 * Class Amasty_PaymentDetect_Helper_Data
 *
 * @author Artem Brunevski
 */
class Amasty_PaymentDetect_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * system configuration settings
     */
    const VAR_HIDE_DROPDOWN = 'amasty_paymentdetect/card_type/hide_dropdown';
    const VAR_SHOW_ICONS = 'amasty_paymentdetect/card_type/show_icons';
    const VAR_ICON_WIDTH = 'amasty_paymentdetect/card_type/icon_width';

    const VAR_VISA_ICON = 'amasty_paymentdetect/visa/icon';
    const VAR_AMEX_ICON = 'amasty_paymentdetect/amex/icon';
    const VAR_MASTERCARD_ICON = 'amasty_paymentdetect/mastercard/icon';
    const VAR_DISCOVER_ICON = 'amasty_paymentdetect/discover/icon';
    const VAR_JCB_ICON = 'amasty_paymentdetect/jcb/icon';
    const VAR_MAESTRO_ICON = 'amasty_paymentdetect/maestro/icon';

    const VAR_VISA_TITLE = 'amasty_paymentdetect/visa/title';
    const VAR_AMEX_TITLE = 'amasty_paymentdetect/amex/title';
    const VAR_MASTERCARD_TITLE = 'amasty_paymentdetect/mastercard/title';
    const VAR_DISCOVER_TITLE = 'amasty_paymentdetect/discover/title';
    const VAR_JCB_TITLE = 'amasty_paymentdetect/jcb/title';
    const VAR_MAESTRO_TITLE = 'amasty_paymentdetect/maestro/title';

    const VAR_VISA_ORDER = 'amasty_paymentdetect/visa/order';
    const VAR_AMEX_ORDER = 'amasty_paymentdetect/amex/order';
    const VAR_MASTERCARD_ORDER = 'amasty_paymentdetect/mastercard/order';
    const VAR_DISCOVER_ORDER = 'amasty_paymentdetect/discover/order';
    const VAR_JCB_ORDER = 'amasty_paymentdetect/jcb/order';
    const VAR_MAESTRO_ORDER = 'amasty_paymentdetect/maestro/order';

    /**
     * @return string
     */
    public function hideDropdown()
    {
        return Mage::getStoreConfig(self::VAR_HIDE_DROPDOWN);
    }

    /**
     * @return string
     */
    public function showIcons()
    {
        return Mage::getStoreConfig(self::VAR_SHOW_ICONS);
    }

    /**
     * @return string
     */
    public function getIconWidth()
    {
        return Mage::getStoreConfig(self::VAR_ICON_WIDTH);
    }

    /**
     * @return string
     */
    public function getVisaTitle()
    {
        return Mage::getStoreConfig(self::VAR_VISA_TITLE);
    }

    /**
     * @return string
     */
    public function getAmexTitle()
    {
        return Mage::getStoreConfig(self::VAR_AMEX_TITLE);
    }

    /**
     * @return string
     */
    public function getMastercardTitle()
    {
        return Mage::getStoreConfig(self::VAR_MASTERCARD_TITLE);
    }

    /**
     * @return string
     */
    public function getDiscoverTitle()
    {
        return Mage::getStoreConfig(self::VAR_DISCOVER_TITLE);
    }

    /**
     * @return string
     */
    public function getJcbTitle()
    {
        return Mage::getStoreConfig(self::VAR_JCB_TITLE);
    }

    /**
     * @return string
     */
    public function getMaestroTitle()
    {
        return Mage::getStoreConfig(self::VAR_MAESTRO_TITLE);
    }

    /**
     * @return string
     */
    public function getVisaOrder()
    {
        return Mage::getStoreConfig(self::VAR_VISA_ORDER);
    }

    /**
     * @return string
     */
    public function getAmexOrder()
    {
        return Mage::getStoreConfig(self::VAR_AMEX_ORDER);
    }

    /**
     * @return string
     */
    public function getMastercardOrder()
    {
        return Mage::getStoreConfig(self::VAR_MASTERCARD_ORDER);
    }

    /**
     * @return string
     */
    public function getDiscoverOrder()
    {
        return Mage::getStoreConfig(self::VAR_DISCOVER_ORDER);
    }

    /**
     * @return string
     */
    public function getJcbOrder()
    {
        return Mage::getStoreConfig(self::VAR_JCB_ORDER);
    }

    /**
     * @return string
     */
    public function getMaestroOrder()
    {
        return Mage::getStoreConfig(self::VAR_MAESTRO_ORDER);
    }

    /**
     * @return string
     */
    public function getVisaUrl()
    {
        return $this->getUrl(Mage::getStoreConfig(self::VAR_VISA_ICON));
    }

    /**
     * @return string
     */
    public function getAmexUrl()
    {
        return $this->getUrl(Mage::getStoreConfig(self::VAR_AMEX_ICON));
    }

    /**
     * @return string
     */
    public function getMastercardUrl()
    {
        return $this->getUrl(Mage::getStoreConfig(self::VAR_MASTERCARD_ICON));
    }

    /**
     * @return string
     */
    public function getDiscoverUrl()
    {
        return $this->getUrl(Mage::getStoreConfig(self::VAR_DISCOVER_ICON));
    }

    /**
     * @return string
     */
    public function getJcbUrl()
    {
        return $this->getUrl(Mage::getStoreConfig(self::VAR_JCB_ICON));
    }

    /**
     * @return string
     */
    public function getMaestroUrl()
    {
        return $this->getUrl(Mage::getStoreConfig(self::VAR_MAESTRO_ICON));
    }

    /**
     * @param $image
     * @return string
     */
    protected function getUrl($image)
    {
        return Mage::getBaseUrl('media') . 'amasty/paymentdetect/logo/' . $image;
    }
}