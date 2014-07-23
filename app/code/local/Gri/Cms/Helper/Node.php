<?php
class Gri_Cms_Helper_Node extends Mage_Core_Helper_Abstract
{

	/*
	 * menu_nodes show in menu
	*/
	public $menu_nodes = array('as-seen-in','events');

	public function getNodeData() {
		$nodes = $this->menu_nodes;
		$data = array();
		foreach($nodes as $v) {
			$node = Mage::getModel('gri_cms/hierarchy_node')->loadByRequestUrl($v);
			if($node->getId()) {
				$data[$v] = array(
					'name' => $node->getLabel(),
					'id' => 'cms-node-' . $node->getId(),
					'url' => Mage::getBaseUrl() . $v,
					'children' => array()
				);
				$children = $node->getCollection()->addFieldToFilter('xpath',array('like' => $node->getId().'%'));
				if($children) {
					foreach($children as $child) {
						if($child->getId() == $node->getId()) continue;
						$data[$v]['children'][] = array(
							'name' => Mage::getModel('cms/page')->load($child->getPageId())->getTitle(),
							'id' => 'cms-node-' . $child->getId(),
							'url' => Mage::getBaseUrl() . $child->getData('request_url'),
						);
					}
				}
			}

		}
		return $data;
	}

	public function addNodesToMenu($topMenu)
	{
		$this->_addNodesToMenu($this->getNodeData(),$topMenu);
	}

	public function _addNodesToMenu($data, $topMenu)
	{
		$tree = $topMenu->getTree();
		if($data) {
			foreach($data as $v) {
				$node = new Varien_Data_Tree_Node($v, 'id', $tree, $topMenu);
				$topMenu->addChild($node);
				if(isset($v['children']) && $v['children'])
				{
					$this->_addNodesToMenu($v['children'], $node);
				}
			}
		}
	}
}