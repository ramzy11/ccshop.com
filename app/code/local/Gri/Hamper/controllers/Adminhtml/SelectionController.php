<?php

class Gri_Hamper_Adminhtml_SelectionController extends Mage_Adminhtml_Controller_Action
{
    protected function _construct()
    {
        $this->setUsedModuleName('Gri_Hamper');
    }

    public function searchAction()
    {
        return $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('hamper/adminhtml_catalog_product_edit_tab_hamper_option_search')
                ->setIndex($this->getRequest()->getParam('index'))
                ->setFirstShow(TRUE)
                ->toHtml()
        );
    }

    public function gridAction()
    {
        return $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('hamper/adminhtml_catalog_product_edit_tab_hamper_option_search_grid',
                    'adminhtml.catalog.product.edit.tab.hamper.option.search.grid')
                ->setIndex($this->getRequest()->getParam('index'))
                ->toHtml()
        );
    }
}
