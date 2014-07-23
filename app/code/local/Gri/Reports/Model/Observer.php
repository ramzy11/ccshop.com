<?php

class Gri_Reports_Model_Observer extends Varien_Object
{

    public function observeOrderCancellation(Varien_Event_Observer $observer)
    {
        /* @var $creditmemo Mage_Sales_Model_Order_Creditmemo */
        $creditmemo = $observer->getEvent()->getCreditmemo();
        /* @var $orderReport Gri_Reports_Model_Report_Order */
        $orderReport = Mage::getModel('gri_reports/report_order');
        $orderReport->orderCancellation($creditmemo);
    }

    public function observeOrderCancellation2(Varien_Event_Observer $observer)
    {
        /* @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getOrder();
        /* @var $orderReport Gri_Reports_Model_Report_Order */
        $orderReport = Mage::getModel('gri_reports/report_order');
        $orderReport->orderCancellation2($order);
    }

    public function observeOrderPayment(Varien_Event_Observer $observer)
    {
        /* @var $invoice Mage_Sales_Model_Order_Invoice */
        $invoice = $observer->getEvent()->getInvoice();
        $invoice->setPaidAt(Varien_Date::formatDate(time()));
        $order = $invoice->getOrder();
        /* @var $orderReport Gri_Reports_Model_Report_Order */
        $orderReport = Mage::getModel('gri_reports/report_order');
        $orderReport->orderPayment($order, $invoice);
    }

    public function observeOrderPlacement(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        /* @var $orderReport Gri_Reports_Model_Report_Order */
        $orderReport = Mage::getModel('gri_reports/report_order');
        $orderReport->orderPlacement($order);
    }

    public function observeOrderShipment(Varien_Event_Observer $observer)
    {
        /* @var $shipment Mage_Sales_Model_Order_Shipment */
        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();
        /* @var $orderReport Gri_Reports_Model_Report_Order */
        $orderReport = Mage::getModel('gri_reports/report_order');
        $orderReport->orderShipment($order, $shipment);
    }
}
