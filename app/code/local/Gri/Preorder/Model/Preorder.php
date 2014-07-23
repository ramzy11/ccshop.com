<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Gri
 * @package     Gri_Presale
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Preorder  data model
 *
 *
 * @category    Gri
 * @package     Gri_Preorder
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Gri_Preorder_Model_Preorder extends Mage_Core_Model_Abstract {

    public function isEnabled() {
        return Mage::helper('gri_preorder')->isEnabled();
    }

    /**
     * @param Gri_CatalogCustom_Model_Category $category
     * @return array
     */
    public function getProductIds($category = NULL)
    {
        /* @var $productCollection Mage_Catalog_Model_Resource_Product_Collection */
        $productCollection = Mage::getSingleton('catalog/product')->getCollection();
        $productCollection->addAttributeToSelect('*')
            ->addAttributeToFilter('type_id', array('eq' => 'configurable'))
            ->addAttributeToFilter('preorder_to_date', array('from' => $this->_getNowDate()))
            //->addAttributeToFilter('preorder_from_date',array('to' => $this->_getNowDate()))
        ;
        $productCollection->addCategoryFilter($category);

        $select = $productCollection->getSelect();
        $select->reset($select::COLUMNS)->columns('e.entity_id');

        return $select->query()->fetchAll();
    }

    /**
     *  get product
     *
     */
    public function _initProduct() {
        $categoryId = (int) $this->getRequest()->getParam('category', FALSE);
        $productId = (int) $this->getRequest()->getParam('id');

        $params = new Varien_Object();
        $params->setCategoryId($categoryId);

        return Mage::helper('catalog/product')->initProduct($productId, $this, $params);
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return bool
     */
    public function isPreorder($product)
    {
        $from = $product->getPreorderFromDate();
        $to = $product->getPreorderToDate();

        return $this->isEnabled() && ($from || $to) &&
            Mage::app()->getLocale()->isStoreDateInInterval($product->getStore(), $from, $to);
    }

    /**
     *  getter  2012-12-31 01:10:10
     */
    public function _getNowDate() {
        return Mage::getSingleton('core/locale')->date()->toString('y-MM-dd H:m:s');
    }

    /**
     *
     *  @return  Mage_Customer_Model_Customer
     */
    public function getCustomer() {
        return Mage:: getSingleton('customer/session')->getCustomer();
    }

    public function getCustomerGroupId() {
        $customer = $this->getCustomer();
        return  $customer->getId() ? $customer->getGroupId() : 0 ;
    }

    /**
     *  customer is vip
     */
    public function isVip() {
        $customer = $this->getCustomer();
        $group_id = $customer->getGroupId();

        $vipIds =  array(
                     $this->getGroupIdByLevel('offlinevip'),
                     $this->getGroupIdByLevel('silver'),
                     $this->getGroupIdByLevel('gold'));

        return in_array($group_id, $vipIds) ? TRUE : FALSE ;
    }

    /**
     *  addto cart
     *
     *  @return bool
     */
    public function addto_cart($product) {
        return  $this->isPreorder($product) && !$this->isVip() ? FALSE : TRUE ;
    }


    /**
     *  @param  $level
     */
    public  function  getGroupIdByLevel($level){
      return  Mage::helper('gri_vip')->getGroupIdByVipLevel($level);
    }

}
