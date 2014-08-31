<?php
/** @var $this Mage_Customer_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$installer->updateAttribute('customer', 'dob',array(
    'visible' => true,
));
$installer->addAttribute('customer', 'customer_country_id',array(
        'label'	 => 'Country',
        'type' => 'varchar',
        'input' => 'select',
        'backend' => '',
        'frontend' => '',
        'source' => 'customer/entity_address_attribute_source_country',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible' => true,
        'required' => true,
        'user_defined' => true,
        'option' => array (),
        'unique' => false )
);
$installer->addAttribute('customer', 'customer_area_code',array(
        'label'	 => 'Area Code',
        'type' => 'varchar',
        'input' => 'text',
        'backend' => '',
        'frontend' => '',
        'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible' => true,
        'required' => true,
        'user_defined' => true,
        'option' => array (),
        'unique' => false )
);
$installer->addAttribute('customer', 'mobile',array(
        'label'	 => 'Mobile Number',
        'type' => 'varchar',
        'input' => 'text',
        'backend' => '',
        'frontend' => '',
        'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible' => true,
        'required' => true,
        'user_defined' => true,
        'option' => array (),
        'unique' => true )
);
$installer->addAttribute('customer', 'title',array(
        'label'	 => 'title',
        'type' => 'varchar',
        'input' => 'text',
        'backend' => '',
        'frontend' => '',
        'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible' => true,
        'required' => true,
        'user_defined' => true,
        'option' => array (),
        'unique' => false )
);
$installer->addAttribute('customer', 'mailing_address',array(
        'label'	 => 'Mailing Address',
        'type' => 'text',
        'input' => 'textarea',
        'backend' => '',
        'frontend' => '',
        'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible' => true,
        'required' => false,
        'user_defined' => true,
        'option' => array (),
        'unique' => false )
);

$installer->endSetup();