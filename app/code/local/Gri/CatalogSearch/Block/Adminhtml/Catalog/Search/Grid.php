<?php
class Gri_CatalogSearch_Block_Adminhtml_Catalog_Search_Grid extends Mage_Adminhtml_Block_Catalog_Search_Grid
{
	protected function _prepareColumns()
	{
		$this->addColumnAfter('promoted_terms', array(
			'header'    => Mage::helper('catalog')->__('Promoted Terms'),
			'align'     => 'left',
			'index'     => 'promoted_terms',
			'width'     => '160px'
		),'synonym_for');
		parent::_prepareColumns();
	}
}