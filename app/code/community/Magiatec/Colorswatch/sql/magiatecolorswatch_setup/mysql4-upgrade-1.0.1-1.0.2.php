<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/* @var $productModel Mage_Catalog_Model_Product */
$productModel = Mage::getModel('catalog/product');

$groups = array(
    'settings' => array(
        'fields' => array(
            'enabled' => array(
                'value' => '1',
            ),
            'attributes' => array(
                'value' => array(
                    0 => $productModel->getResource()->getAttribute('color')->getId(),
                ),
            ),
            'swatch_width' => array(
                'value' => '15',
            ),
            'swatch_height' => array(
                'value' => '15',
            ),
            'show_not_available' => array(
                'value' => '1',
            ),
        ),
    ),
    'imageswitcher' => array(
        'fields' => array(
            'enabled' => array(
                'value' => '1',
            ),
            'width' => array(
                'value' => '400',
            ),
            'height' => array(
                'value' => '400',
            ),
            'twidth' => array(
                'value' => '75',
            ),
            'theight' => array(
                'value' => '75',
            ),
        ),
    ),
    'zoom' => array(
        'fields' => array(
            'enabled' => array(
                'value' => '1',
            ),
            'type' => array(
                'value' => 'standard',
            ),
            'width' => array(
                'value' => '400',
            ),
            'height' => array(
                'value' => '400',
            ),
            'position' => array(
                'value' => 'right',
            ),
            'preload_text' => array(
                'value' => 'Loading',
            ),
            'opacity' => array(
                'value' => '0.4',
            ),
        ),
    ),
);

Mage::getModel('adminhtml/config_data')
    ->setSection('magiatecolorswatch')
    ->setGroups($groups)
    ->save();

$installer->endSetup();
