<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();


/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
$data = array(
    array(
        'identifier' => 'ninewest-about-look-store',
        'title' => 'Nine West Left Navigation',
        'is_active' => 1,
        'content' => <<<EOT
        <div class="brand-left">
            <ul>
                <li><a href="{{store_url=\'ninewest/about\'}}">ABOUT</a></li>
                <li><a href="{{store_url=\'ninewest/lookbook\'}}">LOOK BOOK</a></li>
                <li><a href="{{store_url=\'ninewest/store\'}}">STORE LOCATOR</a></li>
            </ul>
        </div>
EOT
        ,
    ),
    array(
        'identifier' => 'eqiq-about-look-store',
        'title' => 'EQ:IQ Left Navigation',
        'is_active' => 1,
        'content' => <<<EOT
        <div class="brand-left">
            <ul>
                <li><a href="{{store_url=\'eqiq/about\'}}">ABOUT</a></li>
                <li><a href="{{store_url=\'eqiq/lookbook\'}}">LOOK BOOK</a></li>
                <li><a href="{{store_url=\'eqiq/store\'}}">STORE LOCATOR</a></li>
            </ul>
        </div>
EOT
		,
    ),

    array(
        'identifier' => 'stevemadden-about-look-store',
        'title' => 'Steve Madden Left Navigation',
        'is_active' => 1,
        'content' => <<<EOT
        <div class="brand-left">
            <ul>
                <li><a href="{{store_url=\'stevemadden/about\'}}">ABOUT</a></li>
                <li><a href="{{store_url=\'stevemadden/lookbook\'}}">LOOK BOOK</a></li>
                <li><a href="{{store_url=\'stevemadden/store\'}}">STORE LOCATOR</a></li>
            </ul>
        </div>
EOT
        ,
    ),

    array(
        'identifier' => 'betseyjohnson-about-look-store',
        'title' => 'Betsey Johnson Left Navigation',
        'is_active' => 1,
        'content' => <<<EOT
        <div class="brand-left">
            <ul>
                <li><a href="{{store_url=\'betseyjohnson/about\'}}">ABOUT</a></li>
                <li><a href="{{store_url=\'betseyjohnson/lookbook\'}}">LOOK BOOK</a></li>
                <li><a href="{{store_url=\'betseyjohnson/store\'}}">STORE LOCATOR</a></li>
            </ul>
		</div>
EOT
        ,
    ),
);
foreach ($data as $d) {
    $block->unsetData();
    $block->load($d['identifier']);
    foreach ($d as $k => $v) {
        $block->setData($k, $v);
    }
    $block->setStores(array(0))->save();
}

$installer->endSetup();
