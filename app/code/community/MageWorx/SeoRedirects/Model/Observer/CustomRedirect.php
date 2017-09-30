<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoRedirects_Model_Observer_CustomRedirect extends Mage_Core_Model_Abstract
{
    protected $_priorityCategories = array();

    protected $_productNames = array();

    public function updateCustomRedirect($observer)
    {
        $product = $observer->getEvent()->getProduct();

        if (!$product) {
            return;
        }

        //duplicated and non-saved product
        if (!$product->getSku()) {
            return;
        }

        $id = $product->getId();
        foreach(Mage::app()->getStores() as $store) {
            $product->setStore($store->getId());
            $customRedirect = Mage::getModel('mageworx_seoredirects/redirect_custom')->setStoreId($store->getId())
                ->loadByEntity(MageWorx_SeoRedirects_Model_Redirect_Custom::PRODUCT_ENTITY_TYPE_ID, $id);

            if ($customRedirect->getCustomRedirectId()) {
                $data = array(
                    'request_entity_type_id' => MageWorx_SeoRedirects_Model_Redirect_Custom::CUSTOM_URL_ENTITY_TYPE_ID,
                    'request_entity_id'      => $product->getUrlPath(),
                    'date_modified'          => date('Y-m-d h:i:s')
                );
                $model = $customRedirect->addData($data);
                $model->save();
            } else {
                $customRedirect = $customRedirect->setStoreId($store->getId())
                    ->loadByTargetEntity(MageWorx_SeoRedirects_Model_Redirect_Custom::PRODUCT_ENTITY_TYPE_ID, $id);

                if ($customRedirect->getCustomRedirectId()) {
                    $data = array(
                        'target_entity_type_id' => MageWorx_SeoRedirects_Model_Redirect_Custom::CUSTOM_URL_ENTITY_TYPE_ID,
                        'target_entity_id'      => $product->getUrlPath(),
                        'date_modified'          => date('Y-m-d h:i:s')
                    );
                    $model = $customRedirect->addData($data);
                    $model->save();
                }
            }
        }

        return $this;
    }
}