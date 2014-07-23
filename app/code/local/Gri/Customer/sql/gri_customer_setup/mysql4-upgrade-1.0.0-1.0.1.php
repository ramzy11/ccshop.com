<?php
/* @var $this Mage_Customer_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$attributesInfo = array(
    'city_id' => array(
        'label'     => 'City Id',
        'visible'   => FALSE,
        'required'  => FALSE,
        'type'      => 'int',
        'used_in_forms' => array(
            'adminhtml_customer_address',
            'customer_address_edit',
            'customer_register_address',
        )
    ),
);

foreach ($attributesInfo as $attributeCode => $attributeParams) {
    $installer->addAttribute('customer_address', $attributeCode, $attributeParams);
    if (!empty($attributeParams['used_in_forms'])) {
        /* @var $attribute Mage_Customer_Model_Attribute */
        $attribute = Mage::getSingleton('eav/config')->getAttribute('customer_address', $attributeCode);
        $attribute->setData('used_in_forms', $attributeParams['used_in_forms'])->save();
    }
}

$quoteAddressTable = $installer->getTable('sales/quote_address');
$orderAddressTable = $installer->getTable('sales/order_address');
if (!$installer->getConnection()->fetchOne("SHOW COLUMNS FROM `{$quoteAddressTable}` LIKE 'city_id'")) {
    $installer->run("ALTER TABLE `{$quoteAddressTable}` ADD COLUMN `city_id` INT(10) UNSIGNED NULL;");
}
if (!$installer->getConnection()->fetchOne("SHOW COLUMNS FROM `{$orderAddressTable}` LIKE 'city_id'")) {
    $installer->run("ALTER TABLE `{$orderAddressTable}` ADD COLUMN `city_id` INT(10) UNSIGNED NULL;");
}

$installer->endSetup();
