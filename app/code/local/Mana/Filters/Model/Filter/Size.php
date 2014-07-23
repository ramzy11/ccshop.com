<?php
class Mana_Filters_Model_Filter_Size extends Mana_Filters_Block_Filter {

	public function __construct()
	{
		parent::__construct();
		$this->_filterModelName = 'mana_filters/filter_size';
	}
}