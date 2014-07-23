<?php
/* @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

Mage::getConfig()->reinit();
if('HK' == Mage::getConfig()->getNode('default/' . Mage_Core_Helper_Data::XML_PATH_DEFAULT_COUNTRY)->__toString()){
    $brands = array(array('identifier'=>'jeannepierre', 'title'=>'Jeanne Pierre'));

    /* @var $block Mage_Cms_Model_Block */
    $block = Mage::getModel('cms/block');
    foreach($brands as $brand){
        // Get Ninewest Data
        $collection = Mage::getResourceModel('cms/block_collection')
                            ->addFieldToFilter('identifier',array('like'=> 'ninewest%'))
                            ->load();
        $data = array();
        foreach($collection as $item){
            $identifier = str_replace('ninewest', 'jeannepierre', $item->getIdentifier());
            $title = str_ireplace('NINE WEST', 'Jeanne Pierre', $item->getTitle());
            $title = str_ireplace('ninewest', 'jeannepierre', $title);
            $content = str_ireplace('NINE WEST', 'Jeanne Pierre', $item->getContent());
            $content = str_ireplace('ninewest', 'jeannepierre', $content);
            $data[] = array('identifier'=> $identifier,
                            'title'=> $title,
                            'content'=> $content,
                      );
        }

        $blocks = array();
        foreach ($data as $d) {
            $block->unsetData();
            $block->setStoreId(1)->setLoadInactive(TRUE)->load($d['identifier']);
            $block->addData($d);
            $block->setStores(array(1))->save();
            $blocks[1][$block->getIdentifier()] = $block->getId();
            $block->unsetData();
            $block->setStoreId(2)->setLoadInactive(TRUE)->load($d['identifier']);
            $block->addData($d);
            $block->setStores(array(2))->save();
            $blocks[2][$block->getIdentifier()] = $block->getId();
        }

        /* @var $helper Gri_CatalogCustom_Helper_Category */
        $helper = Mage::helper('gri_catalogcustom/category');

        $dmPage = Mage_Catalog_Model_Category::DM_PAGE;
        $dmProducts = Mage_Catalog_Model_Category::DM_PRODUCT;
        $definition = array(
            $brand['identifier'] => array(
                'name' => $brand['title'],
                'include_in_menu' => 0,
                'display_mode' => $dmPage,
                'landing_page' => $brand['identifier'],
                'children' => array(
                    'shop' => array(
                        'name' => 'Shop',
                        'display_mode' => $dmPage,
                        'landing_page' => "{$brand['identifier']}-shop",
                        'children' => array(
                            'best-sellers' => array(
                                'name' => 'Best Sellers',
                                'include_in_menu' => 0,
                                'display_mode' => $dmPage,
                                'landing_page' => 'best_seller',
                            ),
                            'new-arrivals' => array(
                                'name' => 'New Arrivals',
                                'include_in_menu' => 0,
                                'display_mode' => $dmPage,
                                'landing_page' => 'new-arrivals',
                            ),
                            'exclusives' => array(
                                'name' => 'Exclusives',
                            ),
                            'sales' => array(
                                'name' => 'Sales',
                                'include_in_menu' => 0,
                                'display_mode' => $dmPage,
                                'landing_page' => 'sales',
                            ),
                            'exclusives' => array(
                                'name' => 'Exclusives',
                            ),
                            'shop-by-look' => array(
                                'name' => 'Shop by Look',
                                'include_in_menu' => 0,
                                'display_mode' => $dmPage,
                                'landing_page' => 'shop-by-look',
                            ),
                            'pre-order' => array(
                                'name' => 'Pre Order',
                                'include_in_menu' => 0,
                                'display_mode' => $dmPage,
                                'landing_page' => 'pre-order',
                            ),
                            'pre-sales' => array(
                                'name' => 'Pre Sales',
                                'include_in_menu' => 0,
                                'display_mode' => $dmPage,
                                'landing_page' => 'pre-sales',
                            ),
                            'shoes' => array(
                                'name' => 'Shoes',
                                'display_mode' => $dmPage,
                                'landing_page' => "{$brand['identifier']}-shop-shoes",
                                'children' => array(
                                    'pumps' => array(
                                        'name' => 'Pumps',
                                        'children' => array(
                                            'high-heel' => array(
                                                'name' => 'High Heel',
                                            ),
                                            'mid-heel' => array(
                                                'name' => 'Mid Heel',
                                            ),
                                            'platforms' => array(
                                                'name' => 'Platforms',
                                            ),
                                            'wedges' => array(
                                                'name' => 'Wedges',
                                            ),
                                        ),
                                    ),
                                    'flats' => array(
                                        'name' => 'Flats',
                                    ),
                                    'sandals' => array(
                                        'name' => 'Sandals',
                                        'children' => array(
                                            'flat' => array(
                                                'name' => 'Flat',
                                            ),
                                            'high-heel' => array(
                                                'name' => 'High Heel',
                                            ),
                                            'wedges' => array(
                                                'name' => 'Wedges',
                                            ),
                                            'platforms' => array(
                                                'name' => 'Platforms',
                                            ),
                                        ),
                                    ),
                                    'boots' => array(
                                        'name' => 'Boots',
                                    ),
                                    'booties' => array(
                                        'name' => 'Booties',
                                    ),
                                    'platforms' => array(
                                        'name' => 'Platforms',
                                    ),
                                    'wedges' => array(
                                        'name' => 'Wedges',
                                    ),
                                ),
                            ),
                            'clothing' => array(
                                'name' => 'Clothing',
                                'display_mode' => $dmPage,
                                'landing_page' => "{$brand['identifier']}-shop-clothing",
                                'children' => array(
                                    'tops' => array(
                                        'name' => 'Tops',
                                    ),
                                    'dresses' => array(
                                        'name' => 'Dresses',
                                    ),
                                    'skirts' => array(
                                        'name' => 'Skirts',
                                    ),
                                    'pants-and-shorts' => array(
                                        'name' => 'Pants and Shorts',
                                    ),
                                    'outerwear' => array(
                                        'name' => 'Outerwear',
                                    ),
                                    'knitwear' => array(
                                        'name' => 'Knitwear',
                                    ),
                                ),
                            ),
                            'accessories' => array(
                                'name' => 'Accessories',
                                'display_mode' => $dmPage,
                                'landing_page' => "{$brand['identifier']}-shop-accessories",
                                'children' => array(
                                    'bags' => array(
                                        'name' => 'Bags',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $root = Mage::getModel('catalog/category')->setStoreId(0)->load(2);
        $helper->createCategories($definition, $root, $blocks);
    }
}

$installer->endSetup();
