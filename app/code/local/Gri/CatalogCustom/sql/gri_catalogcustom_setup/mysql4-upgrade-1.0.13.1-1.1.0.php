<?php
/* @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

/* @var $category Gri_CatalogCustom_Model_Category */
$category = Mage::getModel('catalog/category');
/* @var $parentCategory Gri_CatalogCustom_Model_Category */
$parentCategory = Mage::getModel('catalog/category');
$suffix = '.html';
/* @var $helper Gri_CatalogCustom_Helper_Category */
$helper = Mage::helper('gri_catalogcustom/category');


/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
$data = array(
    array(
        'identifier' => 'pre-order',
        'title' => 'Pre Order',
        'content' => <<<EOT
<p>{{block type="catalog/product_list" name="product_list" as="product_list" template="catalog/product/list.phtml" }}</p>
EOT
    ,
    ),
    array(
        'identifier' => 'pre-sales',
        'title' => 'Pre Sales',
        'content' => <<<EOT
<p>{{block type="catalog/product_list" name="product_list" as="product_list" template="catalog/product/list.phtml" }}</p>
EOT
    ,
    ),
);
$blocks = array();
foreach ($data as $d) {
    $block->unsetData();
    $block->load($d['identifier']);
    foreach ($d as $k => $v) {
        $block->setData($k, $v);
    }
    $block->setStores(array(0))->save();
    $blocks[$block->getIdentifier()] = $block->getId();
}

// Create new special categories
$parentCategories = array(
    'shoes',
    'clothing',
    'accessories',
    'ninewest/shop',
    'stevemadden/shop',
);
$specialCategories = array(
    'pre-order' => array(
        'name' => 'Pre Order',
    ),
    'pre-sales' => array(
        'name' => 'Pre Sales',
    ),
);
$commonAttributes = array(
    'is_active' => 1,
    'include_in_menu' => 0,
    'is_anchor' => 1,
    'display_mode' => $category::DM_PAGE,
);
/* @var $urlRewriteModel Mage_Core_Model_Url_Rewrite */
$urlRewriteModel = Mage::getModel('core/url_rewrite');
$category->getResource()->getAttribute('url_key')->setIsGlobal(1);
$category->getResource()->getAttribute('display_mode')->setIsGlobal(1);
$category->getResource()->getAttribute('landing_page')->setIsGlobal(1);
$category->getResource()->getAttribute('available_sort_by')->setIsGlobal(1);
$category->getResource()->getAttribute('is_anchor')->setIsGlobal(1);
$category->getResource()->getAttribute('include_in_menu')->setIsGlobal(1);
$storeId = 0;

/* @var $urlResource Mage_Catalog_Model_Resource_Url */
$urlResource = Mage::getResourceModel('catalog/url');
foreach ($parentCategories as $parent) {
    $urlRewriteModel->unsetData()->setStoreId(1);
    if (!$parentId = $urlRewriteModel->loadByRequestPath($parent . $suffix)->getCategoryId()) continue;
    $parentCategory->unsetData()->setStoreId($storeId)->load($parentId);
    foreach ($specialCategories as $urlKey => $data) {
        $tmp = $helper->getCategory($parentCategory, $urlKey);
        $category = $tmp ? $tmp : $category->unsetData()->setStoreId($storeId);
        $data = array_merge($data, $commonAttributes, array(
            'url_key' => $urlKey,
            'parent_id' => $parentId,
            'path' => $parentCategory->getPath(),
            'landing_page' => $blocks[$urlKey],
        ));
        $category->addData($data)->save();
        $urlResource->saveCategoryAttribute($category, 'url_key');
    }
}
$installer->endSetup();
