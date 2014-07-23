<?php

/**
 * Class Gri_Reports_Model_Report_Order_Item
 * @method Gri_Reports_Model_Resource_Report_Order getResource()
 */
class Gri_Reports_Model_Report_Order_Item extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('gri_reports/report_order_item');
    }

    /**
     * @return Gri_CatalogCustom_Helper_Category
     */
    public function getCategoryHelper()
    {
        return Mage::helper('gri_catalogcustom/category');
    }

    public function orderPlacement(Mage_Sales_Model_Order $order)
    {
        $createdDay = $this->getResource()->getDayString($createdAt = $order->getCreatedAt());
        $createdMonth = $this->getResource()->getMonthString($createdAt);
        $createdYear = $this->getResource()->getYearString($createdAt);
        /* @var $salesColumnBlock Mage_Adminhtml_Block_Sales_Items_Column_Default */
        $salesColumnBlock = Mage::app()->getLayout()->createBlock('adminhtml/sales_items_column_default');
        $shopCategories = $this->getCategoryHelper()->getShopCategories($order->getStore());
        /* @var $item Mage_Sales_Model_Order_Item */
        foreach ($order->getItemsCollection() as $item) {
            if ($item->getParentItemId()) continue;
            /* @var $product Gri_CatalogCustom_Model_Product */
            $product = $item->getProduct();
            $salesColumnBlock->setItem($item);
            $color = $size = '';
            foreach ($salesColumnBlock->getOrderOptions() as $_option) {
                if (isset($_option['code']) && (substr($_option['code'], 0, 5) == 'color')) {
                    $color = $_option['value'];
                    continue;
                }
                if (isset($_option['code']) && (substr($_option['code'], 0, 4) == 'size')) {
                    $size = $_option['value'];
                    continue;
                }
            }
            $baseCategory = $subCategory = $bottomCategory = NULL;
            $productCategories = $product->getCategoryCollection()
                ->setStoreId($order->getStoreId())
                ->joinAttribute('name', 'catalog_category/name', 'entity_id');
            foreach ($productCategories as $category) {
                foreach ($shopCategories as $shopCategory) {
                    if ($category->getId() == $shopCategory->getId() ||
                        strpos($category->getPath(), '/' . $shopCategory->getId() . '/')
                    ) {
                        switch ($category->getLevel()) {
                            case 2:
                                $baseCategory = $category;
                                break;
                            case 3:
                                $subCategory = $category;
                                break;
                            case 4:
                                $bottomCategory = $category;
                                break;
                        }
                        break;
                    }
                }
            }
            $data = array(
                'order_item_id' => $item->getId(),
                'order_created_day' => $createdDay,
                'order_created_month' => $createdMonth,
                'order_created_year' => $createdYear,
                'style_no' => $product->getStyleNo(),
                'style_name' => $product->getStyleName(),
                'color' => $color,
                'size' => $size,
                'list_price' => $price = $product->getPrice(),
                'brand' => $product->getAttributeText('brand'),
                'base_category' => $baseCategory ? $baseCategory->getName() : NULL,
                'sub_category' => $subCategory ? $subCategory->getName() : NULL,
                'bottom_category' => $bottomCategory ? $bottomCategory->getName() : NULL,
                'base_category_id' => $baseCategory ? $baseCategory->getId() : NULL,
                'sub_category_id' => $subCategory ? $subCategory->getId() : NULL,
                'bottom_category_id' => $bottomCategory ? $bottomCategory->getId() : NULL,
                'product_created_at' => $product->getCreatedAt(),
            );
            $order->setOriginalPriceSubtotal($price * $item->getQtyOrdered() + $order->getOriginalPriceSubtotal());
            $this->unsetData()->addData($data)->save();
        }
        return $this;
    }

    public function orderShipment(Mage_Sales_Model_Order $order)
    {
        $shippingDay = $this->getResource()->getDayString($shippedAt = $order->getShippedAt(), FALSE);
        $shippingMonth = $this->getResource()->getMonthString($shippedAt, FALSE);
        $shippingYear = $this->getResource()->getYearString($shippedAt, FALSE);
        /* @var $item Mage_Sales_Model_Order_Item */
        foreach ($order->getItemsCollection() as $item) {
            if ($item->getParentItemId()) continue;
            $data = array(
                'order_item_id' => $item->getId(),
                'order_shipping_day' => $shippingDay,
                'order_shipping_month' => $shippingMonth,
                'order_shipping_year' => $shippingYear,
            );
            $this->unsetData()->addData($data)->save();
        }
    }
}
