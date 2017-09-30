<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Model_Observer_MetaUpdaterObserver
{
    /**
     * @var array
     */
    static protected $_singleton = array();

    /**
     * Modify meta data.
     * Event: core_block_abstract_to_html_before
     *
     * @param Varien_Event_Observer $observer
     */
    public function modifyMeta($observer)
    {
        $block = $observer->getBlock();

        if ($block->getNameInLayout() != 'head') {
            return false;
        }

        if (!$this->_observerCanRun(__METHOD__)) {
            return false;
        }

        /** @var MageWorx_SeoExtended_Model_Factory_Action_MetaUpdaterFactory $factory */
        $factory = Mage::getSingleton('mageworx_seoextended/factory_action_metaUpdaterFactory');
        $updater = $factory->getModel();

        if (!($updater instanceof MageWorx_SeoExtended_Model_MetaUpdater)) {
            return false;
        }

        return $updater->update($block);
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