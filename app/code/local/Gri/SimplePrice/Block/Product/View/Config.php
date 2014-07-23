<?php

class Gri_SimplePrice_Block_Product_View_Config extends Mage_Catalog_Block_Product_Abstract
{

    protected function _construct()
    {
        parent::_construct();
        $this->getSimplePriceConfigJson();
    }

    public function getSimplePriceConfigJson()
    {
        if ($this->getData('simiple_price_config_json') === NULL) {
            $config = array(
                'availableProducts' => (object)NULL,
            );
            /* @var $type Mage_Catalog_Model_Product_Type_Configurable */
            if (
                $this->getSimplePriceHelper()->isEnabled() &&
                ($product = $this->getProduct())
            ) {
                if ($prices = $this->getSimplePriceHelper()->getSimplePrices($product)) {
                    Mage::registry('remove_unavailable_products') or $product->setFinalPrice(max($prices))
                        ->setMinimalPrice(min($prices));
                    foreach ($prices as &$v) {
                        $v = Mage::app()->getStore()->convertPrice($v);
                    }
                    $config['availableProducts'] = $prices;
                }
                $product->setSimplePricePriceCalculated(TRUE);
            }
            $this->setData('simiple_price_config_json', Mage::helper('core')->jsonEncode($config));
        }
        return $this->getData('simiple_price_config_json');
    }

    /**
     * @return Gri_SimplePrice_Helper_Data
     */
    public function getSimplePriceHelper()
    {
        return $this->helper('gri_simpleprice');
    }
}
