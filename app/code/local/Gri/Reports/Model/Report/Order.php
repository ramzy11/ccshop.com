<?php

/**
 * Class Gri_Reports_Model_Report_Order
 * @method Gri_Reports_Model_Resource_Report_Order getResource()
 * @method Gri_Reports_Model_Report_Order setOrder(Mage_Sales_Model_Order $order)
 */
class Gri_Reports_Model_Report_Order extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('gri_reports/report_order');
    }

    /**
     * @return Gri_Reports_Model_Report_Order_Item
     */
    public function getOrderItemReportModel()
    {
        if ($this->_getData('order_item_report_model') === NULL) {
            $model = Mage::getModel('gri_reports/report_order_item');
            $this->setData('order_item_report_model', $model);
        }
        return $this->_getData('order_item_report_model');
    }

    public function orderCancellation(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        /* @var $creditmemo Gri_Sales_Model_Order_Creditmemo */
        if ($creditmemo->getState() == $creditmemo::STATE_ORDER_CANCELED ||
            $creditmemo->getState() == $creditmemo::STATE_CANCELED_CLOSED ||
            $creditmemo->getCreditmemoStatus() == $creditmemo::STATUS_ORDER_CANCELED
        ) {
            $order = $creditmemo->getOrder();
            $data = array(
                'order_id' => $order->getId(),
                'canceled_at' => $creditmemo->getCreatedAt(),
            );
            $this->unsetData()->addData($data)->save();
        }
        return $this;
    }

    public function orderCancellation2(Mage_Sales_Model_Order $order)
    {
        /* @var $creditmemo Gri_Sales_Model_Order_Creditmemo */
        if (($order->getState() == $order::STATE_CANCELED ||
            $order->getState() == $order::STATE_CLOSED) &&
            $order->getOrigData('state') != $order::STATE_CANCELED &&
            $order->getOrigData('state') != $order::STATE_CLOSED
        ) {
            $data = array(
                'order_id' => $order->getId(),
                'canceled_at' => $order->getUpdatedAt(),
            );
            $this->unsetData()->addData($data)->save();
        }
        return $this;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return $this
     */
    public function orderPayment(Mage_Sales_Model_Order $order, $invoice = NULL)
    {
        if (!($invoice instanceof Mage_Sales_Model_Order_Invoice) &&
            !count($order->getInvoiceCollection())
        ) return $this;
        $invoice instanceof Mage_Sales_Model_Order_Invoice or
            $invoice = $order->getInvoiceCollection()->getFirstItem();

        $invoice->getPaidAt() or $invoice->setPaidAt($invoice->getCreatedAt());
        $data = array(
            'order_id' => $order->getId(),
            'order_paid_at' => $invoice->getPaidAt(),
        );
        $this->unsetData()->addData($data)->save();
        return $this;
    }

    public function orderPlacement(Mage_Sales_Model_Order $order)
    {
        $this->getOrderItemReportModel()->orderPlacement($order);
        $createdDay = $this->getResource()->getDayString($createdAt = $order->getCreatedAt());
        $createdMonth = $this->getResource()->getMonthString($createdAt);
        $createdYear = $this->getResource()->getYearString($createdAt);
        $data = array(
            'order_id' => $order->getId(),
            'order_created_day' => $createdDay,
            'order_created_month' => $createdMonth,
            'order_created_year' => $createdYear,
            'original_price_subtotal' => $order->getOriginalPriceSubtotal(),
        );
        $this->unsetData()->addData($data)->save();
        return $this;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @return $this
     */
    public function orderShipment(Mage_Sales_Model_Order $order, $shipment = NULL)
    {
        if (!($shipment instanceof Mage_Sales_Model_Order_Shipment) &&
            !count($order->getShipmentsCollection())
        ) return $this;
        $shipment instanceof Mage_Sales_Model_Order_Shipment or
            $shipment = $order->getShipmentsCollection()->getFirstItem();
        /* @var $track Mage_Sales_Model_Order_Shipment_Track */
        $track = count($shipment->getTracksCollection()) ?
            $shipment->getTracksCollection()->getFirstItem() : new Varien_Object();
        $order->setShippedAt($shippedAt = $shipment->getCreatedAt());
        $this->getOrderItemReportModel()->orderShipment($order);

        $shippingDay = $this->getResource()->getDayString($shippedAt, FALSE);
        $shippingMonth = $this->getResource()->getMonthString($shippedAt, FALSE);
        $shippingYear = $this->getResource()->getYearString($shippedAt, FALSE);
        $data = array(
            'order_id' => $order->getId(),
            'order_shipping_day' => $shippingDay,
            'order_shipping_month' => $shippingMonth,
            'order_shipping_year' => $shippingYear,
            'shipped_at' => $shippedAt,
            'carrier' => $track->getTitle(),
            'track_number' => $track->getNumber(),
        );
        $this->unsetData()->addData($data)->save();
        return $this;
    }
}
