<?php

/**
 * Tab in admin category
 *
 * @category   ProxiBlue
 * @package    DynCatProd
 * @author     Lucas van Staden (sales@proxiblue.com.au)
 */

class ProxiBlue_DynCatProd_Block_Adminhtml_Catalog_Category_Tab_DynCatProd extends Mage_Core_Block_Template {

    /**
     * Constructor
     * */
    public function __construct() {
        parent::__construct();
        $this->setId('catalog_category_dyncatprod');
        $this->setTemplate('dyncatprod/tab.phtml');
    }

}

