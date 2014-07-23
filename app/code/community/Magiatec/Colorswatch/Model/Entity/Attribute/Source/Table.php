<?php

class Magiatec_Colorswatch_Model_Entity_Attribute_Source_Table extends Mage_Eav_Model_Entity_Attribute_Source_Table
{

    /**
     * Retrieve Full Option values array
     *
     * @param bool $withEmpty       Add empty option to array
     * @param bool $defaultValues
     * @return array
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        if (Mage::registry('skip_swatch_sort')) return parent::getAllOptions($withEmpty, $defaultValues);
        $optionKey = $this->getAttribute()->getStoreId();
        /* @var Magiatec_Colorswatch_Helper_Swatch $swatchHelper */
        $swatchHelper = Mage::helper('magiatecolorswatch/swatch');
        $product = $swatchHelper->getCurrentProduct();
        $sortSwatch = $product && $product->getId() && $product->getTypeInstance() instanceof Mage_Catalog_Model_Product_Type_Configurable &&
            $swatchHelper->checkAttributes($this->getAttribute()->getId());
        $sortSwatch and $optionKey .= ':' . $product->getId();

        if (!is_array($this->_options)) {
            $this->_options = array();
        }
        if (!is_array($this->_optionsDefault)) {
            $this->_optionsDefault = array();
        }
        if (!isset($this->_options[$optionKey])) {
            /* @var Gri_CatalogCustom_Model_Resource_Entity_Attribute_Option_Collection $collection */
            $collection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->addFieldToFilter('main_table.attribute_id', $this->getAttribute()->getId())
                ->setStoreFilter($this->getAttribute()->getStoreId());

            if ($sortSwatch) {
                $collection->getSelect()->distinct()
                    ->join(
                        array('cpsa' => $collection->getTable('catalog/product_super_attribute')),
                        'cpsa.attribute_id = main_table.attribute_id AND cpsa.product_id = ' . $product->getId(),
                        array()
                    )
                    ->joinLeft(
                        array('mcp' => $collection->getTable('magiatecolorswatch/product')),
                        'mcp.product_super_attribute_id = cpsa.product_super_attribute_id AND mcp.value_index = main_table.option_id',
                        array('swatch_sort' => 'sort')
                    )
                    ->order('mcp.sort asc');
            }

            $collection->setPositionOrder('asc');
            $collection->load();

            $this->_options[$optionKey]        = $collection->toOptionArray();
            $this->_optionsDefault[$optionKey] = $collection->toOptionArray('default_value');
            if ($sortSwatch) {
                foreach ($this->_options[$optionKey] as $k => $v) {
                    $this->_options[$optionKey][$k]['sort'] = $collection->getItemById($v['value'])->getSwatchSort();
                    $this->_optionsDefault[$optionKey][$k]['sort'] = $collection->getItemById($v['value'])->getSwatchSort();
                }
            }
        }
        $options = ($defaultValues ? $this->_optionsDefault[$optionKey] : $this->_options[$optionKey]);
        if ($withEmpty) {
            array_unshift($options, array('label' => '', 'value' => ''));
        }

        return $options;
    }

}
