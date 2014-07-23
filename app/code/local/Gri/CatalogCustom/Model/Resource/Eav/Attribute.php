<?php
class Gri_CatalogCustom_Model_Resource_Eav_Attribute extends Mage_Catalog_Model_Resource_Eav_Attribute
{
  protected function _afterSave()
    {
        /**
         * Save size attibute options to size map
         */
    	/*
    	if($this->getAttributeCode()=='size' && $values = $this->getData('option')) {
    		$sizemap = Mage::getModel('gri_catalogcustom/sizemap');
    		$sizemapValues = array();
    		$optionValues = array();
    		foreach($sizemap->getCollection() as $v) $sizemapValues[$v['admin_size']] = $v['universal_size'];
       		if($values['value']) {
    			foreach($values['value'] as $k=>$v ) {
    				$adminSize = explode('-',$v[0]);
    				if(!in_array(strtolower($adminSize[0]),array('clothing','shoes'))) continue;
    				$optionValues[$v[0]] = '';
    			}
    		}
    		$delete = array_diff_key($sizemapValues,$optionValues);
    		$insert = array_diff_key($optionValues,$sizemapValues);
        	$w = Mage::getSingleton('core/resource')->getConnection('core_write');
    		if($insert) {
    			$insertData = array();
    			foreach($insert as $k => $v) $insertData[] = array('admin_size' => $k, 'universal_size' => '');
    			$w->insertArray('size_mapping',array('admin_size','universal_size'),$insertData);
    		}
    	   if($delete) $w->delete('size_mapping', $w->quoteInto('admin_size IN (?)', array_keys($delete)));
      }
      */
    		return parent::_afterSave();
    }
}