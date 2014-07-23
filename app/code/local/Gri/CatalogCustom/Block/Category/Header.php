<?php

class Gri_CatalogCustom_Block_Category_Header extends Mage_Core_Block_Text
{

    protected function _toHtml()
    {
        $html = '';
        /* @var $category Gri_CatalogCustom_Model_Category */
        if ($category = Mage::registry('current_category')) {
            $identifier = strtr($category->getUrlPath(), array(
                '/' => '-',
                '.html' => '',
            ));
            /* @var $block Mage_Cms_Model_Block */
            $block = Mage::getModel('cms/block');
            $block->setStoreId($category->getStoreId())->load($identifier);
            if ($block->getIsActive()) {
                /* @var $cmsHelper Mage_Cms_Helper_Data */
                $cmsHelper = Mage::helper('cms');
                $processor = $cmsHelper->getBlockTemplateProcessor();
                $html = $processor->filter($block->getContent());
            } else {
                $html = <<<EOT
<div class="page-title category-title">
    <h2>{$category->getName()}</h2>
</div>
EOT;
            }
        }
        $this->setText($html);
        return parent::_toHtml();
    }
}
