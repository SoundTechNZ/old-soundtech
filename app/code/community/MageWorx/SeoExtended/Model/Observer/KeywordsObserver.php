<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Model_Observer_KeywordsObserver
{
    /**
     * @var array
     */
    static protected $_singleton = array();

    /**
     * Event: core_block_abstract_to_html_after
     * @param $observer
     * @return bool
     */
    public function cropMetaKeywordsTag($observer)
    {
        if ($observer->getBlock() instanceof Mage_Page_Block_Html_Head) {
            if (!$this->_observerCanRun(__METHOD__)) {
                return false;
            }

            if (Mage::helper('mageworx_seoextended/config')->getStatusCropMetaKeywordsTag() == 'no') {
                return false;
            }

            if (Mage::helper('mageworx_seoextended/config')->getStatusCropMetaKeywordsTag() == 'yes') {
                $actionName  = Mage::helper('mageworx_seoall/request')->getCurrentFullActionName();
                $ignorePages = Mage::helper('mageworx_seoextended/config')->getIgnorePagesForMetaKeywords();

                if (!empty($ignorePages) && in_array($actionName, $ignorePages)) {
                    return false;
                }
            }

            $html = $observer->getTransport()->getHtml();
            if ($html) {
                $q       = '"';
                $matches = array();
                $ret     = preg_match_all(
                    "/<\s*meta\s{1,}name\s{0,}=\s{0,}['|$q]\s{0,1}keywords\s{0,}['|$q]\s{1,}content\s{0,}=\s{0,}['|$q](.{0,}?)['|$q]\s{0,}\/\s{0,}>\s{0,}\n{0,}/i",
                    $html, $matches, PREG_SET_ORDER
                );

                if ($ret && !empty($matches[0][0])) {
                    $modifyHtml = str_replace($matches[0][0], '', $html);
                    if (Mage::helper('mageworx_seoextended/config')->getStatusCropMetaKeywordsTag() == 'yes') {
                        $html = $modifyHtml;
                    }
                    elseif (Mage::helper('mageworx_seoextended/config')->getStatusCropMetaKeywordsTag() == 'for_empty') {
                        if (isset($matches[0][1]) && $matches[0][1] == "") {
                            $html = $modifyHtml;
                        }
                    }
                }

                $observer->getTransport()->setHtml($html);
            }
        }
    }

    /**
     * @param string $method
     * @return bool
     */
    protected function _observerCanRun($method)
    {
        if (!isset(self::$_singleton[$method])) {
            self::$_singleton[$method] = true;
            return true;
        }

        return false;
    }

}