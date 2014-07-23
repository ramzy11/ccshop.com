<?php

class Gri_Sales_Block_Product_View_CartRule extends Mage_Core_Block_Template
{

    protected function _getIsEnabled()
    {
        return TRUE;
    }

    protected function _toHtml()
    {
        if (!$this->_getIsEnabled()) return '';
        return parent::_toHtml();
    }

    /**
     * @return bool|Mage_SalesRule_Model_Rule
     */
    public function getApplicableRule()
    {
        if ($this->getData('applicable_rule') === NULL) {
            $this->setData('applicable_rule', FALSE);
            $product = $this->getProduct();
            $address = Mage::getModel('sales/quote_address');
            $product->setProduct($product)
                ->setQty(1)
                ->setBaseRowTotal($product->getPrice())
                ->setAllItems(array($product))
                ->setQuote($product)
                ->setBillingAddress($address)
                ->setShippingAddress($address);
            /* @var $rule Mage_SalesRule_Model_Rule */
            foreach ($this->getRules() as $rule) {
                foreach ($rule->getConditions()->getConditions() as $cond) {
                    if ($cond instanceof Mage_SalesRule_Model_Rule_Condition_Address) continue;
                    if ($cond->validate($product)) {
                        ($storeLabel = $rule->getStoreLabel()) and $rule->setName($storeLabel);
                        $this->setData('applicable_rule', $rule);
                        break;
                    }
                }
            }
        }
        return $this->getData('applicable_rule');
    }

    public function getAvailableVariants()
    {
        if ($this->getData('available_variants')) return $this->getData('available_variants');
        return array(
            'product_name',
            'rule_name',
        );
    }

    public function getCacheKeyInfo()
    {
        $info = parent::getCacheKeyInfo();
        $info['product'] = $this->getProduct()->getId();
        return $info;
    }

    public function getCmsBlockContent()
    {
        if ($this->getData('cms_block_content') === NULL) {
            $html = '';
            /* @var $block Mage_Cms_Model_Block */
            $block = Mage::getModel('cms/block')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($this->getCmsBlockId());
            if ($block->getIsActive()) {
                /* @var $cmsHelper Mage_Cms_Helper_Data */
                $cmsHelper = Mage::helper('cms');
                $processor = $cmsHelper->getBlockTemplateProcessor();
                $html = $processor->filter($block->getContent());
            }
            $this->setData('cms_block_content', $html);
        }
        return $this->getData('cms_block_content');
    }

    /**
     * Retrieve currently viewed product object
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', Mage::registry('product'));
        }
        return $this->getData('product');
    }

    /**
     * @return Mage_SalesRule_Model_Resource_Rule_Collection
     */
    public function getRules()
    {
        if ($this->getData('rules') === NULL) {
            $websiteId = Mage::app()->getStore()->getWebsiteId();
            $customerGroupId = Mage::getSingleton('customer/session')->getCustomer()->getGroupId();
            /* @var $rules Mage_SalesRule_Model_Resource_Rule_Collection */
            $rules = Mage::getModel('salesrule/rule')->getCollection()
                ->setFlag('is_website_table_joined', TRUE);
            $rules->setValidationFilter($websiteId, $customerGroupId)
                ->addFieldToFilter('simple_action', array('in' => array(
                    Mage_SalesRule_Model_Rule::TO_PERCENT_ACTION,
                    Mage_SalesRule_Model_Rule::BY_PERCENT_ACTION,
                    Mage_SalesRule_Model_Rule::TO_FIXED_ACTION,
                    Mage_SalesRule_Model_Rule::BY_FIXED_ACTION,
                    Mage_SalesRule_Model_Rule::CART_FIXED_ACTION,
                    Mage_SalesRule_Model_Rule::BUY_X_GET_Y_ACTION,
                )))
            ;
            $this->setData('rules', $rules);
        }
        return $this->getData('rules');
    }

    public function parseVariants($content)
    {
        $pattern = '/\{\{([^\}]+)\}\}/is';
        $variants = array();
        $availableVariants = array_flip($this->getAvailableVariants());
        if (preg_match_all($pattern, $content, $matches)) foreach ($matches[1] as $v) {
            if (!isset($availableVariants[$v])) continue;
            $variants[] = $v;
        }
        $replacement = array();
        foreach ($variants as $v) {
            $params = explode('_', $v, 2);
            if (!isset($params[1])) continue;
            $object = FALSE;
            switch ($params[0]) {
                case 'product':
                    $object = $this->getProduct();
                    break;
                case 'rule':
                    $object = $this->getApplicableRule();
                    break;
                default:
                    continue;
            }
            if ($object) {
                $replacement['{{' . $v . '}}'] = $object->getData($params[1]);
            }
        }
        return strtr($content, $replacement);
    }
}
