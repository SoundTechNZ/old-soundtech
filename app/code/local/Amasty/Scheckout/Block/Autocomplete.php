<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Scheckout
 */


/**
 * Class Amasty_Scheckout_Block_Autocomplete
 * @author Artem Brunevski
 */
class Amasty_Scheckout_Block_Autocomplete extends Mage_Core_Block_Template
{
    const VAR_ENABLED = 'amscheckout/autocomplete/enable';
    const VAR_KEY = 'amscheckout/autocomplete/key';

    protected function _toHtml()
    {
        $html = '';
        if (Mage::getStoreConfig(self::VAR_ENABLED) && Mage::getStoreConfig(self::VAR_KEY)){
            $html = '<script '.
                        'src="//maps.googleapis.com/maps/api/js?key=' . Mage::getStoreConfig(self::VAR_KEY) . '&signed_in=true&libraries=places&callback=amastyScheckoutInitAutocomplete">' .
                    '</script>';
        }

        return $html;
    }
}