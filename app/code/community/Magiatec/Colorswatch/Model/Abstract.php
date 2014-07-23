<?php
class Magiatec_Colorswatch_Model_Abstract extends Mage_Core_Model_Abstract
{
    protected $_images = array();

    public function getImages()
    {
        return $this->_images;
    }

    public function addImage($imageData)
    {
        $this->_images[] = $imageData;
    }

    public function unsetValues()
    {
        $this->_images = array();
        return $this;
    }

    public function saveImages()
    {
        foreach ($this->getImages() as $data) {
            $this->setData($data);
            $this->save();
        }
        return $this;
    }

    public function loadByAttributes(array $data)
    {
        /* @var $collection Magiatec_Colorswatch_Model_Resource_Product_Collection */
        $collection = $this->getCollection();
        foreach ($data as $k => $v) {
            $collection->addFieldToFilter($k, $v);
        }
        return $collection->count() ? $collection->getFirstItem() : FALSE;
    }
}
