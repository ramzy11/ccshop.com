<?php
    /* @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
    $installer = $this;
    $installer->startSetup();

    /* @var $page Mage_Cms_Model_Page */
    $page = Mage::getModel('cms/page');
    $data = array (
        'identifier'=> 'flash-sale-terms-condition',
        'title'=> 'Flash Sale Terms Condition',
        'content'=> '{{block type="cms/block" block_id="flash_terms_and_condition"}}'
    );

    // Chinese
    $page->addData($data);
    $page->setStores(array(1))->save();
    $page->unsetData();

    // English
    $page->setStoreId(2)->setLoadInactive(TRUE)->load($data['identifier']);
    $page->addData($data);
    $page->setStores(array(2))->save();

    $installer->endSetup();
