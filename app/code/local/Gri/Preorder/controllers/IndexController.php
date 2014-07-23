<?php

/**
 *  index  controller
 */
class Gri_Preorder_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     *   index
     */
    public function indexAction() {
       $isEnabled = Mage::getSingleton('gri_preorder/preorder')->isEnabled();
       if(!$isEnabled){
          $this->_forward('noRoute');
          return false;
       }

       $this->loadLayout();
       $this->getLayout()->getBlock('breadcrumbs')->addCrumb('home', array(
            'label' => $homeLabel = Mage::helper('catalog')->__('Home'),
            'title' => $homeLabel,
            'link' => Mage::getUrl(),
       ))->addCrumb('preorder', array(
            'label' => $label = $this->__('Pre Order'),
            'title' => $label,
       ));

       $category = Mage::getModel('catalog/category');
       $category->setShopCategory($category)->setUrlKey('presales');
       Mage::register('current_category', $category);
       $this->_title()->_title($label);
       $this->renderLayout();
    }
}
