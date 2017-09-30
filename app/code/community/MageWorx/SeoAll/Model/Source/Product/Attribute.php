<?php
/**
 * MageWorx
 * MageWorx SeoAll Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoAll
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoAll_Model_Source_Product_Attribute extends MageWorx_SeoAll_Model_Source
{
    protected $_options;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->_options === null) {
            /** @var Mage_Catalog_Model_Resource_Product_Attribute_Collection $attributes */
            $attributes = Mage::getResourceModel('catalog/product_attribute_collection');
            $attributes->addVisibleFilter();
            $attributes->addIsFilterableFilter();

            $attributeArray = array();

            foreach ($attributes as $attribute) {
                $attributeArray[] = array(
                    'label' => $attribute->getData('frontend_label'),
                    'value' => $attribute->getData('attribute_id'),
                );
            }

            $this->_options = $attributeArray;
        }

        return $this->_options;
    }
}