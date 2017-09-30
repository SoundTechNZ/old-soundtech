<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_XSitemap_Model_Generator_FpBlog extends MageWorx_XSitemap_Model_Generator_Abstract
{
    const CODE = 'fp_blog';

    /**
     * @var array
     */
    protected $_initialEnvironmentInfo;

    public function generate($storeId, $writer, $counter)
    {
        $this->_storeId = $storeId;
        $this->_storeBaseUrl = $this->_helperStore->getStoreBaseUrl($this->_storeId);

        if (!Mage::getStoreConfigFlag('wordpress/module/enabled', $this->_storeId)) {
            return false;
        }

        $writer->write(
            htmlspecialchars(Mage::helper('wordpress')->getUrl()),
            $this->_helper->getCurrentDate(),
            'daily',
            '1.0'
        );

        $collection = Mage::getResourceModel('wordpress/post_collection')
            ->addIsViewableFilter()
            ->setOrderByPostDate()
            ->load();

        foreach ($collection as $item) {
            $writer->write(
                htmlspecialchars($item->getUrl()),
                $item->getPostModifiedDate('Y-m-d'),
                $this->_helper->getBlogPriority(),
                $this->_helper->getBlogPriority()
            );
        }

        $this->_stopEmulation();
    }

    /**
     * @return void
     */
    protected function _startEmulation()
    {
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $this->_initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($this->_storeId);
    }

    /**
     * @return void
     */
    protected function _stopEmulation()
    {
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $appEmulation->stopEnvironmentEmulation($this->_initialEnvironmentInfo);
    }
}