<?php

class Gri_CatalogCustom_Model_Product_Type_Configurable extends Mage_Catalog_Model_Product_Type_Configurable
{

    /**
     * Retrieve Selected Attributes info
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getSelectedAttributesInfo($product = NULL)
    {
        $attributes = array();
        Varien_Profiler::start('CONFIGURABLE:' . __METHOD__);
        if ($attributesOption = $this->getProduct($product)->getCustomOption('attributes')) {
            $data = unserialize($attributesOption->getValue());
            $this->getUsedProductAttributeIds($product);

            $usedAttributes = $this->getProduct($product)->getData($this->_usedAttributes);

            foreach ($data as $attributeId => $attributeValue) {
                if (isset($usedAttributes[$attributeId])) {
                    /* @var $attribute Mage_Catalog_Model_Product_Type_Configurable_Attribute */
                    $attribute = $usedAttributes[$attributeId];
                    $code = $attribute->getProductAttribute()->getAttributeCode();
                    $label = $attribute->getLabel();
                    /* @var $value Mage_Catalog_Model_Resource_Eav_Attribute */
                    $value = $attribute->getProductAttribute();
                    if ($code == 'color_code' && $colorLabel = $this->getChildColorLabel($product, $value)) {
                        $value = $colorLabel;
                    } else if ($value->getSourceModel()) {
                        $value = $value->getSource()->getOptionText($attributeValue);
                    } else {
                        $value = '';
                    }
                    $attributes[] = array('code' => $code, 'label' => $label, 'value' => $value);
                }
            }
        }
        Varien_Profiler::stop('CONFIGURABLE:' . __METHOD__);
        return $attributes;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getChildColorLabel($product)
    {
        $customOptions = $product->getCustomOptions();
        if (isset($customOptions['simple_product']) && $simpleProduct = $customOptions['simple_product']) {
            $simpleProductId = $simpleProduct->getProductId();
        }
        $select = $product->getResource()->getReadConnection()->select();
        $table = $product->getResource()->getEntityTable();
        $attribute = $product->getResource()->getAttribute('color_label');
        $colorTable = $attribute->getBackendTable();
        $select->from(array('e' => $table), array())
            ->join(array('dv' => $colorTable), 'dv.entity_id = e.entity_id
AND dv.store_id = 0
AND dv.attribute_id = ' . $attribute->getAttributeId(), array())
            ->joinLeft(array('sv' => $colorTable), 'sv.entity_id = e.entity_id
AND sv.store_id = "' . $product->getStoreId() . '"
AND sv.attribute_id = ' . $attribute->getAttributeId(), array())
            ->columns(array('value' => 'IFNULL(sv.value, dv.value)'));
        if (isset($simpleProductId) && $simpleProductId) $select->where('e.entity_id = ?', $simpleProductId);
        else $select->where('e.sku = ?', $product->getSku());
        return $select->query()->fetchColumn();
    }
}
