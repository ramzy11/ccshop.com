<?php
    /* @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
    $installer = $this;
    $installer->startSetup();
    Mage::getConfig()->reinit();
    if('HK' == Mage::getConfig()->getNode('default/' . Mage_Core_Helper_Data::XML_PATH_DEFAULT_COUNTRY)->__toString()){
        /* @var $helper Gri_CatalogCustom_Helper_Category */
        $helper = Mage::helper('gri_catalogcustom/category');
        $dmPage = Mage_Catalog_Model_Category::DM_PAGE;
        $dmProducts = Mage_Catalog_Model_Category::DM_PRODUCT;
        $brands = array(
            array('identifier'=>'jeannepierre', 'title'=>'Jeanne Pierre'),
            array('identifier'=>'ninewest', 'title'=>'Nine West'),
            array('identifier'=>'eqiq', 'title'=>'EQ:IQ'),
            array('identifier'=>'betseyjohnson', 'title'=>'Betsey Johnson'),
            array('identifier'=>'carolinnaespinosa','title'=> 'Carolinna Espinosa'),
            array('identifier'=>'stevemadden','title'=>'Steve Madden')
        );

        $categorys = array(
            'bags'=> array(
                'name' => 'Bags',
                'children' => array(
                    'backpacks' => array(
                        'name'=> 'Backpacks',
                    ),
                    'clutches' => array(
                        'name'=> 'Clutches',
                    ),
                    'cross-body-bags' => array(
                        'name'=> 'Cross Body Bags',
                    ),
                    'shoulder-bags' => array(
                        'name'=> 'Shoulder Bags',
                    ),
                    'top-handle-bags'=> array(
                        'name'=> 'Top-Handle Bags',
                    ),
                    'tote-bags'=> array(
                        'name'=> 'Tote Bags',
                    ),
                    'wristlets'=> array(
                         'name'=> 'Wristlets',
                    ),
                    'others'=> array(
                         'name'=> 'Others',
                    ),
                ),
            ),
            'accessories' => array(
                'name' => 'Accessories',
                'children' =>  array(
                    'belts' => array(
                         'name' => 'Belts',
                    ),
                    'fragrance' => array(
                        'name' => 'Fragrance',
                    ),
                    'hats' => array(
                        'name' => 'Hats',
                    ),
                    'jewelry' => array(
                        'name' => 'Jewelry',
                        'children' => array(
                            'braceletbangles' => array(
                                'name' => 'Bracelets & Bangles',
                            ),
                            'charms' => array(
                                'name' => 'Charms',
                            ),
                            'earrings' => array(
                                'name' => 'Earrings',
                            ),
                            'jewelrybox' => array(
                                'name' => 'Jewelry Box',
                            ),
                            'necklaces' => array(
                                 'name'=> 'Necklaces',
                            ),
                            'rings' => array(
                                 'name'=> 'Rings',
                            ),
                            'others' => array(
                                 'name' => 'Others',
                            )
                        )
                    ),
                    'scarves' => array(
                        'name' => 'Scarves',
                    ),
                    'smallaccessories' => array(
                        'name' => 'Small Accessories',
                        'children' => array(
                            'cardholders'=>array(
                                'name' => 'Card Holders',
                            ),
                            'cosmeticbags'=>array(
                                'name' => 'Cosmetic Bags',
                            ),
                            'ipadcases'=>array(
                                'name' => 'iPad Cases',
                            ),
                            'keyrings'=>array(
                                'name' => 'Key Rings',
                            ),
                            'phonecases'=>array(
                                'name' => 'Phone Cases',
                            ),
                            'stationary'=>array(
                                'name' => 'Stationary',
                            ),
                            'wallets' =>array(
                                'name' => 'Wallets',
                            ),
                            'others' => array(
                                'name' => 'Others',
                            )
                        )
                    ),
                    'sunglasses' => array(
                        'name' => 'Sunglasses',
                    ),
                    'watches' => array(
                        'name' => 'Watches',
                    ),
                ),
            ),
            'clothing' => array(
                'name' => 'Clothing',
                    'children' =>  array(
                        'beachwear' => array(
                            'name' => 'Beachwear',
                        ),
                        'tops' => array(
                            'name' => 'Tops',
                            'children' => array(
                                'blouses' => array(
                                    'name'=> 'Blouses',
                                ),
                                'jersey' => array(
                                    'name'=> 'Jersey',
                                ),
                                'shirts' => array(
                                    'name'=> 'Shirts',
                                ),
                                'vests' => array(
                                    'name'=> 'Vests',
                                ),
                            )
                        ),
                        'outerwear' => array(
                            'name' => 'Outerwear',
                            'children' => array(
                                'coats' => array(
                                    'name'=> 'Coats',
                                ),
                                'jackets' => array(
                                    'name' => 'Jackets',
                                ),
                                'others' => array(
                                    'name' => 'Others',
                                ),
                            )
                        ),
                        'dresses' => array(
                            'name' => 'dresses',
                            'children' => array(
                                'casual-dresses' => array(
                                    'name'=> 'Casual Dresses',
                                ),
                                'evening-dresses' => array(
                                    'name'=> 'Evening Dresses',
                                )
                            )
                        ),
                        'bottoms' => array(
                            'name' => 'Bottoms',
                                'children' => array(
                                    'pants' => array(
                                        'name'=> 'Pants',
                                    ),
                                    'shorts' => array(
                                        'name'=> 'Shorts',
                                    ),
                                    'skirts' => array(
                                         'name'=> 'Skirts',
                                    ),
                                )
                        ),
                        'knitwear' => array(
                            'name' => 'Knitwear',
                            'children' => array(
                                'vests' => array(
                                    'name'=> 'Vests',
                                ),
                                'coats-jackets' => array(
                                    'name'=> 'Coats & Jackets',
                                ),
                                'dresses' => array(
                                    'name'=> 'Dresses',
                                ),
                                'bottoms' => array(
                                    'name'=> 'Bottoms',
                                ),
                                'cardigans' => array(
                                    'name'=> 'Cardigans',
                                ),
                                'sweaters' => array(
                                    'name'=> 'Sweaters',
                                ),
                            )
                        ),
                        'leather' => array(
                            'name'=> 'Leather',
                                'children' => array(
                                    'tops' => array(
                                        'name'=> 'Tops',
                                    ),
                                    'coats-jackets' => array(
                                        'name'=> 'Coats & Jackets',
                                    ),
                                    'dresses' => array(
                                        'name'=> 'Dresses',
                                    ),
                                    'bottoms' => array(
                                        'name'=> 'Bottoms',
                                    ),
                                    'others' => array(
                                        'name'=> 'Others',
                                    ),
                                )
                        ),
                        'shearling-fur' => array(
                            'name'=> 'Shearling/Fur',
                            'children' => array(
                                'tops' => array(
                                    'name'=> 'Tops',
                                ),
                                'coats-jackets' => array(
                                    'name'=> 'Coats & Jackets',
                                ),
                                'dresses' => array(
                                    'name'=> 'Dresses',
                                ),
                            )
                        ),
                    ),
                ),
                'shoes' =>  array(
                    'name' => 'Shoes',
                    'children' =>  array(
                        'boots' => array(
                            'name' => 'Boots',
                            'children' => array(
                                'casual' => array(
                                    'name'=> 'Casual',
                                ),
                                'dress' => array(
                                    'name'=> 'Dress',
                                ),
                                'tailored' => array(
                                    'name'=> 'Tailored',
                                ),
                            )
                        ),
                        'booties' => array(
                            'name' => 'Booties',
                            'children' => array(
                                'casual' => array(
                                    'name'=> 'Casual',
                                ),
                                'dress' => array(
                                    'name'=> 'Dress',
                                ),
                                'tailored' => array(
                                    'name'=> 'Tailored',
                                ),
                            ),
                        ),
                        'flats-ballerinas' => array(
                            'name' => 'Flats & Ballerinas',
                            'children' => array(
                                'casual' => array(
                                    'name'=> 'Casual',
                                ),
                                'dress' => array(
                                    'name'=> 'Dress',
                                ),
                                'flat' => array(
                                    'name'=> 'Flat',
                                ),
                                'sports' => array(
                                    'name'=> 'Sports',
                                ),
                                'others' => array(
                                    'name'=> 'Others',
                                ),
                            ),
                        ),
                        'pumps' => array(
                            'name' => 'Pumps',
                            'children' => array(
                                'casual' => array(
                                    'name'=> 'Casual',
                                ),
                                'dress' => array(
                                    'name'=> 'Dress',
                                ),
                                'high-heel' => array(
                                    'name'=> 'High Heel',
                                ),
                                'low-heel' => array(
                                    'name'=> 'Low Heel',
                                ),
                                'mid-heel' => array(
                                    'name'=> 'Mid Heel',
                                ),
                                'platform' => array(
                                    'name'=> 'Platform',
                                ),
                            ),
                        ),
                        'sandals' => array(
                            'name' => 'Sandals',
                            'children' => array(
                                'career' => array(
                                    'name'=> 'Career',
                                ),
                                'casual' => array(
                                    'name'=> 'Casual',
                                ),
                                'dress' => array(
                                    'name'=> 'Dress',
                                ),
                                'occasion' => array(
                                    'name'=> 'Occasion',
                                ),
                                'sports' => array(
                                    'name'=> 'Sports',
                                ),
                                'tailored' => array(
                                    'name'=> 'Tailored',
                                ),
                                'high-heel' => array(
                                    'name'=> 'High Heel',
                                ),
                                'mid-heel' => array(
                                    'name'=> 'Mid Heel',
                                ),
                                'flat' => array(
                                    'name'=> 'Flat',
                                ),
                                'platform' => array(
                                    'name'=> 'Platform',
                                ),
                                'wedge' => array(
                                    'name'=> 'Wedge',
                                ),
                        ),
                        'sneakers' => array(
                            'name' => 'Sneakers',
                        ),
                    ),
                ),
            ),
        );
        $blocks = array();
    foreach($brands as $brand){
        /* Under Brand */
        $definition = array(
            $brand['identifier'] => array(
                'name' => $brand['title'],
                'children' => array(
                    'shop' => array(
                        'name' => 'Shop',
                        'children' => $categorys
                    ),
                ),
            ),
        );
        $root = Mage::getModel('catalog/category')->setStoreId(0)->load(2);
        $helper->createCategories($definition, $root, $blocks);
    }
    // root/clothing  root/shoes  root/clothing root/bags  root/accessories
    foreach( $categorys as $key => $category){
        /* Under Brand */
        $definition = array(
          $key => $category
        );
        $root = Mage::getModel('catalog/category')->setStoreId(0)->load(2);
        $helper->createCategories($definition, $root, $blocks);
    }
}

$installer->endSetup();

