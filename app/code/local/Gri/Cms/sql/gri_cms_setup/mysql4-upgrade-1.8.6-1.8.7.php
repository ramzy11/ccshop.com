<?php
/* Brand Landing Page Block */
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
$brandUrlKeys = array(
    'eqiq',
    'ninewest',
    'anne-klein',
    'betseyjohnson',
    'carolinnaespinosa',
    'joan-david',
    'jeannepierre'
);


$content = <<<EOT
<div class="about-brand">
    <h2>About brand</h2>
    <ul>
        <li><a href="#">About</a></li>
        <li><a href="#">Look Book</a></li>
        <li><a href="#">Store Locator</a></li>
    </ul>
</div>
EOT;

foreach ($brandUrlKeys as $urlKey) {
    $block->unsetData();
    $data = array(
        'identifier' => 'about-' . $urlKey . '-brand',
        'title' => 'About ' . ucfirst($urlKey) . ' Brand',
        'is_active' => 1,
        'content' => $content
    );

    $block->setData($data);
    $block->setStores(array(0))->save();
}

$installer->endSetup();
