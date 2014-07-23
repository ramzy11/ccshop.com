<?php
class Gri_Cms_Block_Allmenu extends Gri_Cms_Block_Hierarchy_Menu
{
		/**
		 * Return the first level note collecion
		 */
		public function getNotesCollection()
		{
			return Mage::getModel('gri_cms/hierarchy_node')->getCollection()->addFieldToFilter('scope_id',Mage::app()->getStore()->getId())
                         ->addFieldToFilter('level',array("eq"=>1));
		}

		protected function _construct()
		{
			$this->_node = $this->getNotesCollection()->getFirstItem();
			parent::_construct();
			$this->_allowedListAttributes = array('start', 'value', 'compact', // %attrs
				'id', 'class', 'style', 'title', // %coreattrs
				'lang', 'dir', // %i18n
				'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove',
				'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup' // %events
			);
			$this->_allowedLinkAttributes = array(
				'charset', 'type', 'name', 'hreflang', 'rel', 'rev', 'accesskey', 'shape',
				'coords', 'tabindex', 'onfocus', 'onblur', // %attrs
				'id', 'class', 'style', 'title', // %coreattrs
				'lang', 'dir', // %i18n
				'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove',
				'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup' // %events
			);
			$this->_allowedSpanAttributes = array('id', 'class', 'style', 'title', // %coreattrs
				'lang', 'dir', // %i18n
				'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove',
				'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup' // %events
			);

		}
		/**
		 * Retrieve tree slice array
		 *
		 * @return array
		 */
		public function getTree()
		{
			$up   = $this->_getData('up');
			if (!abs(intval($up))) {
				$up = 0;
			}
			$down = $this->_getData('down');
			if (!abs(intval($down))) {
				$down = 0;
			}
			$tree = $this->_node
			->setCollectActivePagesOnly(true)
			->setCollectIncludedPagesOnly(true)
			->setTreeMaxDepth($down)
			->setTreeIsBrief($this->isBrief())
			->getTreeSlice($up, 1);
			return $tree;
		}

		public function getMenuEnabled() {
			return true;
		}
		protected function _toHtml()
		{
			$this->_node = $this->getNotesCollection()->getFirstItem();
			return parent::_toHtml();
		}
	}

