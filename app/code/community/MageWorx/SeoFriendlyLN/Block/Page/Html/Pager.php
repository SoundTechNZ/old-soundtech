<?php
/**
 * MageWorx
 * MageWorx SeoFriendlyLN Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoFriendlyLN
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoFriendlyLN_Block_Page_Html_Pager extends Mage_Page_Block_Html_Pager
{
    /**
     * @param array $params
     * @return string
     */
    public function getPagerUrl($params = array())
    {
        if($this->_out()){
            return parent::getPagerUrl($params);
        }

        $urlParams                 = array();
        $urlParams['_current']     = true;
        $urlParams['_escape']      = true;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_query']       = $params;

        $url = Mage::helper('mageworx_seofriendlyln')->getLayerFilterUrl($urlParams);

        /** @var MageWorx_SeoFriendlyLN_Helper_Config $helper */
        $helper = Mage::helper('mageworx_seofriendlyln/config');

        $pagerUrlFormat = $helper->getPagerUrlFormat();

        if (isset($params['p']) && Mage::app()->getRequest()->getControllerName() == 'category' && $pagerUrlFormat) {
            $suffix = $helper->getCategoryUrlSuffix();

            $pageNum = $params['p'];
            $url     = str_replace(array('&amp;p=' . $pageNum, '&p=' . $pageNum, '?p=' . $pageNum), '', $url);
            if ($pageNum > 1) {
                if(strpos($url, '?') !== false){
                    $urlArr = explode('?', $url);
                }else{
                    $urlArr = explode('&amp;', $url);
                }

                if ($suffix && substr($urlArr[0], -(strlen($suffix))) == $suffix) {
                    $urlArr[0] = substr($urlArr[0], 0, -(strlen($suffix)));
                }

                $urlArr[0] .= str_replace($helper::PAGER_NUM_PATTERN, $pageNum, $pagerUrlFormat);
                $urlArr[0] .= $suffix;
                $url       = implode('?', $urlArr);
            }
        }

        return $url;
    }

    /**
     * @return bool
     */
    protected function _out()
    {
        if (!Mage::helper('mageworx_seofriendlyln/config')->isLNFriendlyUrlsEnabled()){
            return true;
        }

        if(Mage::helper('mageworx_seofriendlyln')->isIndividualLNFriendlyUrlsDisable()){
            return true;
        }

        if (Mage::app()->getRequest()->getControllerName() != 'category') {
            return true;
        }

        return false;
    }
}