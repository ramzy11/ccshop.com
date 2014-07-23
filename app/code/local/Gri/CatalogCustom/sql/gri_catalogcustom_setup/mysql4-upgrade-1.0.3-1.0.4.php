<?php
$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
/*
 * check whether best_seller and editors_pick exist, update ortherwise create
*/
$color1_id = Mage::getModel('eav/entity_attribute')->getIdByCode('catalog_product','color1');
if($color1_id) $setup->removeAttribute('4','color1');
$color2_id = Mage::getModel('eav/entity_attribute')->getIdByCode('catalog_product','color2');
if($color2_id) $setup->removeAttribute('4','color2');
$colorOption = array('values' => array(
	0 => 'Animal',
	1 => 'Black',
	2 => 'BLue',
	3 => 'Brown',
	4 => 'Cream',
	5 => 'Green',
	6 => 'Gray',
	7 => 'Metallic',
	8 => 'Multi',
	9 => 'Orange',
	10 => 'Pink',
	11 => 'Purple',
	12 => 'Red',
	13 => 'White',
	14 => 'Yellow'
));
$sizeShoesOption = array('values' => array(
	0 => '5',
	1 => '5.5',
	2 => '6',
	3 => '6.5',
	4 => '7',
	5 => '7.5',
	6 => '8',
	7 => '8.5',
	8 => '9',
	9 => '10',
	10 => '10.5',
	11 => '11'
));
$sizeClothingOption = array('values' => array(
	0 => '02',
	1 => '04',
	2 => '06',
	3 => '08',
	4 => '10',
	5 => '12',
	6 =>'14',
	8 => 'S',
	9 => 'M',
	10 => 'L',
	11 => 'XL'
));
$sizeTotalOption = array();
$sizeTotalOption['values'] = array_unique(array_merge(array_values($sizeShoesOption['values']), array_values($sizeClothingOption['values'])));
$data = array();
$data['color_filter_1'] = array(
	'label' => 'Color',
	'filterable' => 1,
	'configurable' => 0,
	'option' => $colorOption,
	);
$data['color_filter_2'] = array(
	'label' => 'Color2',
	'filterable' => 1,
	'configurable' => 0,
	'option' => $colorOption,
	);
/*
$data['color'] = array(
	'label' => 'Color',
	'filterable' => 0,
	'configurable' => 1,
	'option' => $colorOption,
	);
	*/
$data['size_shoes'] = array(
	'label' => 'Shoes Size',
	'filterable' => 0,
	'configurable' => 1,
	'option' => $sizeShoesOption
);
$data['size_clothing'] = array(
	'label' => 'Clothing Size',
	'filterable' => 0,
	'configurable' => 1,
	'option' => $sizeClothingOption
);
$data['size_filter_1'] = array(
	'label' => 'Size',
	'filterable' => 1,
	'configurable' => 0,
	'option' => $sizeTotalOption,
);
$data['size_filter_2'] = array(
	'label' => 'Size2',
	'filterable' => 1,
	'configurable' => 0,
	'option' => $sizeTotalOption,
);
foreach($data as $k => $v) {
	if(!$attribute = Mage::getModel('eav/entity_attribute')->getIdByCode('catalog_product',$k)) {
		$setup->addAttribute('4', $k, array(
			'group'         => 'Specific',
			'input'         => 'select',
			'type'          => 'int',
			'label'         => $v['label'],
			'backend'       => '',
			'visible'       => 0,
			'required'      => 0,
			'user_defined' => 1,
			'searchable' => 0,
			'filterable' => $v['filterable'],
			'comparable'    => 0,
			'visible_on_front' => 0,
			'visible_in_advanced_search'  => 0,
			'is_html_allowed_on_front' => 0,
			"configurable" => $v['configurable'],
			'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
			'option' => $v['option']
		));
	}
}
$installer->endSetup();
