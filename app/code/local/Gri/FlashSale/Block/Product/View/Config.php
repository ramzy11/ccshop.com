<?php

class Gri_FlashSale_Block_Product_View_Config extends Mage_Catalog_Block_Product_Abstract
{

    protected function _construct()
    {
        parent::_construct();
        $this->getFlashSaleConfigJson();
    }

    public function getFlashSaleConfigJson()
    {
        if ($this->getData('flash_sale_config_json') === NULL) {
            $config = array(
                'availableProducts' => array(),
                'removeUnavailableProducts' => $removeUnavailableProducts = Mage::registry('remove_unavailable_products'),
            );
            if ($product = $this->getProduct()) {
                if ($flashSaleProduct = $this->getFlashSaleProduct()) {
                    foreach ($flashSaleProduct->getSubProducts() as $availableProduct) {
                        $config['availableProducts'][$availableProduct->getId()] = $availableProduct->getPrice();
                    }
                    $removeUnavailableProducts && $config['availableProducts'] and
                        $product->setFinalPrice(max($config['availableProducts']))
                            ->setFlashSalePriceCalculated(TRUE);
                    foreach ($config['availableProducts'] as &$v) $v = Mage::app()->getStore()->convertPrice($v);
                }
            }
            $this->setData('flash_sale_config_json', Mage::helper('core')->jsonEncode($config));
        }
        return $this->getData('flash_sale_config_json');
    }

    /**
     * @return Gri_FlashSale_Helper_Data
     */
    public function getFlashSaleHelper()
    {
        return $this->helper('gri_flashsale');
    }

    public function getFlashSaleProduct()
    {
        if (!$this->getProduct()) return FALSE;
        return $this->getFlashSaleHelper()->getActiveFlashSale()->getParentProductById($this->getProduct()->getId());
    }
}
