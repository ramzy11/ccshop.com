<?php

class Gri_Shipping_Model_Carrier_Tablerate extends Mage_Shipping_Model_Carrier_Tablerate
{
    /**
     * @var Mage_Shipping_Model_Rate_Request
     */
    protected $_request;

    protected function _restorePostCode(Mage_Shipping_Model_Rate_Request $request)
    {
        $request->setDestPostcode($request->getOrigPostcode());
        return $this;
    }

    protected function _setCityPostCode(Mage_Shipping_Model_Rate_Request $request)
    {
        $request->setOrigPostcode($request->getDestPostcode());
        $request->setDestPostcode($request->getDestCityCode());
        return $this;
    }

    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        $this->_request = $request;
        $this->_setCityPostCode($request);
        $result = parent::collectRates($request);
        $this->_restorePostCode($request);
        return $result;
    }

    public function getConfigData($field)
    {
        $data = parent::getConfigData($field);
        if ($this->_request && in_array($field, array('title', 'name')) && strpos($data, $separator = '|')) {
            $country = $this->_request->getDestCountryId();
            $explodedData = explode($separator, $data);
            $data = array_shift($explodedData);
            foreach ($explodedData as $v) {
                $v = explode(':', $v, 2);
                if (isset($v[0], $v[1]) && $country == trim($v[0])) {
                    $data = trim($v[1]);
                    break;
                }
            }
        }
        return $data;
    }

    public function getTrackingInfo($number)
    {
        /* @var $track Mage_Sales_Model_Order_Shipment_Track */
        $track = Mage::getModel('sales/order_shipment_track')->load($number, 'track_number');
        return array(
            'title' => $track->getTitle(),
            'number' => $number,
        );
    }

    public function isTrackingAvailable()
    {
        return TRUE;
    }
}
