<?php

class Gri_CatalogCustom_Block_Home_ShopNewIn extends Mage_Core_Block_Template
{
    protected $_collection = null ;

    protected $_template = 'catalog/home/shop_new_in.phtml';

    /**
     * @return Mage_Reports_Model_Resource_Product_Index_Viewed_Collection
     */
    public function getShopNewIn()
    {
        //Reference : Mage_Catalog_Block_Product_New
        if($this->_collection == null ) {
            $todayStartOfDayDate  = Mage::app()->getLocale()->date()
                ->setTime('00:00:00')
                ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

            $todayEndOfDayDate  = Mage::app()->getLocale()->date()
                ->setTime('23:59:59')
                ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

            $this->_collection = Mage::getSingleton('catalog/product')->getCollection()
                ->addAttributeToFilter('news_from_date', array('or'=> array(
                    0 => array('date' => true, 'to' => $todayEndOfDayDate),
                    1 => array('is' => new Zend_Db_Expr('null')))
                ), 'left')
                ->addAttributeToFilter('news_to_date', array('or'=> array(
                    0 => array('date' => true, 'from' => $todayStartOfDayDate),
                    1 => array('is' => new Zend_Db_Expr('null')))
                ), 'left')
                ->addAttributeToFilter(
                    array(
                        array('attribute' => 'news_from_date', 'is'=>new Zend_Db_Expr('not null')),
                        array('attribute' => 'news_to_date', 'is'=>new Zend_Db_Expr('not null'))
                    )
                )
                ->addAttributeToSelect('*')
                ->addPriceData()
                ->addStoreFilter()
                //->setOrder('created_at', 'desc')
                ->addAttributeToSort('news_from_date', 'desc')
                ->setPage(1, 20);

            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_collection);
            Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($this->_collection);

        }

        return $this->_collection;
    }
}
