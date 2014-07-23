<?php
    /* @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
    $installer = $this;
    $installer->startSetup();

    /* @var $block Mage_Cms_Model_Block */
    $block = Mage::getModel('cms/block');
    $data = array (
        'identifier'=> 'flash_terms_and_condition',
        'title'=> 'Flash Terms And Condition',
        'content'=> "<h1>terms and condition</h1>
        <ul>
            <li>1. Lorem Ipsum is simply dummy text of the printing and typesetting industry.</li>
            <li>2. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</li>
            <li>3. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</li>
            <li>4. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</li>
        </ul>" );

    // Chinese
    $block->addData($data);
    $block->setStores(array(1))->save();
    $block->unsetData();

    // English
    $block->setStoreId(2)->setLoadInactive(TRUE)->load($data['identifier']);
    $block->addData($data);
    $block->setStores(array(2))->save();

    $installer->endSetup();
