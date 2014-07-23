<?php

class Gri_SalesRule_Model_Rule_Condition_Product_Subselect extends Mage_SalesRule_Model_Rule_Condition_Product_Subselect
{

    public function validate(Varien_Object $object)
    {
        if (!$this->getConditions()) {
            return false;
        }

        $attr = $this->getAttribute();
        $total = 0;
        /* @var $item Gri_SalesRule_Model_Quote_Item */
        foreach ($object->getQuote()->getAllItems() as $item) {
            if (!$item->getParentItemId() && Mage_SalesRule_Model_Rule_Condition_Product_Combine::validate($item)) {
                $total += $item->getData($attr);
            }
        }

        return $this->validateAttribute($total);
    }
}
