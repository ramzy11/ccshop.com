<?php
class Gri_CatalogSearch_Model_Layer extends Mage_CatalogSearch_Model_Layer
{
	public function prepareProductCollection($collection)
	{
		$collection->addFieldToFilter('type_id',array('neq' => 'grouped'));
		return parent::prepareProductCollection($collection);
	}
}