<?php
/**
 * Magento
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@magentocommerce.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade Magento to newer
* versions in the future. If you wish to customize Magento for your
* needs please refer to http://www.magentocommerce.com for more information.
*
* @category    Mage
* @package     Mage_Adminhtml
* @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

/**
 * admin product edit tabs
*
* @category   Mage
* @package    Mage_Adminhtml
* @author      Magento Core Team <core@magentocommerce.com>
*/
class Gri_CatalogCustom_Block_Adminhtml_Sizemap_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	protected $_attributeTabBlock = 'adminhtml/catalog_product_edit_tab_attributes';

	public function __construct()
	{
		parent::__construct();
		$this->setId('sizemap_info_tabs');
		$this->setDestElementId('sizemap_edit_form');
		$this->setTitle(Mage::helper('catalog')->__('Size Map Information'));
	}


	/**
	 * Getting attribute block name for tabs
	 *
	 * @return string
	 */
	public function getAttributeTabBlock()
	{
		if (is_null(Mage::helper('adminhtml/catalog')->getAttributeTabBlock())) {
			return $this->_attributeTabBlock;
		}
		return Mage::helper('adminhtml/catalog')->getAttributeTabBlock();
	}

	public function setAttributeTabBlock($attributeTabBlock)
	{
		$this->_attributeTabBlock = $attributeTabBlock;
		return $this;
	}

	/**
	 * Translate html content
	 *
	 * @param string $html
	 * @return string
	 */
	protected function _translateHtml($html)
	{
		Mage::getSingleton('core/translate_inline')->processResponseBody($html);
		return $html;
	}

	protected function _beforeToHtml()
	{

		$this->addTab('form_section', array(
			'label'     => Mage::helper('gri_catalogcustom')->__('Item Information'),
			'title'     => Mage::helper('gri_catalogcustom')->__('Item Information'),
			'content'   => $this->getLayout()->createBlock('gri_catalogcustom/adminhtml_sizemap_tab_form')->toHtml(),
		));

		return parent::_beforeToHtml();
	}
}
