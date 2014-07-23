<?php

/**
 * Wishlist block customer items
 *
 * @category    Gri
 * @package     Gri_Wishlist
 * @author      Bcn Team <kanelu@bcnetcom.com>
 */
class Gri_Wishlist_Block_Customer_Wishlist_Items extends Mage_Core_Block_Template
{
    /**
     * Retreive table column object list
     *
     * @return array
     */
    public function getColumns()
    {
        $columns = array();
        foreach ($this->getSortedChildren() as $code) {
            $child = $this->getChild($code);
            if ($child->isEnabled()){
                $columns[] = $child;
            }
        }
        return $columns;
    }
}
