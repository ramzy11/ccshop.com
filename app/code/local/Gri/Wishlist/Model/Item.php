<?php

class Gri_Wishlist_Model_Item extends Mage_Wishlist_Model_Item
{

    public function delete()
    {
        $productName = $this->getProduct()->getName();
        parent::delete();
        if (Mage::getDesign()->getArea() == Mage_Core_Model_App_Area::AREA_FRONTEND) {
            $message = Mage::helper('gri_message')->__('%s was removed from your wishlist.', $productName);
            Mage::getSingleton('customer/session')->addSuccess($message);
        }
        return $this;
    }
}
