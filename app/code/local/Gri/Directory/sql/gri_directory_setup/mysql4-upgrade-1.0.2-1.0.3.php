<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$countryRegionTable = $installer->getTable('directory/country_region'); //  chinese
$countryRegionNameTable = $installer->getTable('directory/country_region_name'); //  english

$regionCityTable = $installer->getTable('gri_directory/region_city'); //  chinese
$cityNameTable = $installer->getTable('gri_directory/city_name'); // english

$installer->run("
DELETE FROM `{$countryRegionTable}` WHERE `region_id` > 661;
ALTER TABLE  `{$countryRegionTable}` AUTO_INCREMENT = 1;
DELETE FROM `{$regionCityTable}` WHERE `city_id` > 1495;
ALTER TABLE  `{$regionCityTable}` AUTO_INCREMENT = 1;
DELETE FROM `{$cityNameTable}` WHERE `city_id` > 726;
");

$chineseRegions = array(
    // HK
    'HKI'=> array (
              array ( 'default_name' => 'Sheung Wan', 'locale_name' => '上環', 'code' => 'HKI-001', 'region' => 'HKI',),
              array ( 'default_name' => 'Tai Hang', 'locale_name' => '大坑', 'code' => 'HKI-002', 'region' => 'HKI', ),
              array ( 'default_name' => 'The Peak', 'locale_name' => '山頂', 'code' => 'HKI-003',  'region' => 'HKI', ),
              array ( 'default_name' => 'Central',  'locale_name' => '中環', 'code' => 'HKI-004', 'region' => 'HKI', ),
              array ( 'default_name' => 'Tin Hau',  'locale_name' => '天后',  'code' => 'HKI-005',  'region' => 'HKI', ),
              array ( 'default_name' => 'Tai Koo', 'locale_name' => '太古', 'code' => 'HKI-006', 'region' => 'HKI', ),
              array ( 'default_name' => 'North Point', 'locale_name' => '北角', 'code' => 'HKI-007',  'region' => 'HKI', ),
              array ( 'default_name' => 'Mid Level', 'locale_name' => '半山', 'code' => 'HKI-008',  'region' => 'HKI', ),
              array (  'default_name' => 'Shek O', 'locale_name' => '石澳', 'code' => 'HKI-009', 'region' => 'HKI',),
              array (
                  'default_name' => 'West Point/ Sai Ying Pun',
                  'locale_name' => '西環',
                  'code' => 'HKI-010',
                  'region' => 'HKI',
        ),
        array (
            'default_name' => 'Stanley',
            'locale_name' => '赤柱',
            'code' => 'HKI-011',
            'region' => 'HKI',
        ),
        array (
            'default_name' => 'Admiralty',
            'locale_name' => '金鐘',
            'code' => 'HKI-012',
            'region' => 'HKI',
        ),
        array (
            'default_name' => 'Chai Wan',
            'locale_name' => '柴灣',
            'code' => 'HKI-013',
            'region' => 'HKI',
        ),
        array (
            'default_name' => 'Wan Chai',
            'locale_name' => '灣仔',
            'code' => 'HKI-014',
            'region' => 'HKI',
        ),
        array (
            'default_name' => 'Sai Wan Ho',
            'locale_name' => '西灣河',
            'code' => 'HKI-015',
            'region' => 'HKI',
        ),
        array (
            'default_name' => 'Heng Fa Chuen',
            'locale_name' => '杏花村',
            'code' => 'HKI-016',
            'region' => 'HKI',
        ),
        array (
            'default_name' => 'Aberdeen',
            'locale_name' => '香港仔',
            'code' => 'HKI-017',
            'region' => 'HKI',
        ),
        array (
            'default_name' => 'Repulse Bay',
            'locale_name' => '淺\水灣',
            'code' => 'HKI-018',
            'region' => 'HKI',
        ),
        array (
            'default_name' => 'Deep Water Bay',
            'locale_name' => '深水灣',
            'code' => 'HKI-019',
            'region' => 'HKI',
        ),
        array (
            'default_name' => 'Happy Valley',
            'locale_name' => '跑馬地',
            'code' => 'HKI-020',
            'region' => 'HKI',
        ),
        array (
            'default_name' => 'Shau Kei Wan',
            'locale_name' => '筲箕灣',
            'code' => 'HKI-021',
            'region' => 'HKI',
        ),
        array (
            'default_name' => 'Causeway Bay',
            'locale_name' => '銅鑼灣',
            'code' => 'HKI-022',
            'region' => 'HKI',
        ),
        array (
            'default_name' => 'Ap Lei Chau',
            'locale_name' => '鴨脷洲',
            'code' => 'HKI-023',
            'region' => 'HKI',
        ),
        array (
            'default_name' => 'POK FU LAM ',
            'locale_name' => '薄扶林',
            'code' => 'HKI-024',
            'region' => 'HKI',
        ),
        array (
            'default_name' => 'Cyberport',
            'locale_name' => '數碼港',
            'code' => 'HKI-025',
            'region' => 'HKI',
        ),
        array (
            'default_name' => 'Quarry Bay',
            'locale_name' => '鰂魚涌',
            'code' => 'HKI-026',
            'region' => 'HKI',
        ),
       ),
       'KLN'=>array(
        array (
            'default_name' => 'Prince Edward',
            'locale_name' => '太子',
            'code' => 'KLN-001',
            'region' => 'KLN',
        ),
        array (
            'default_name' => 'Jordan',
            'locale_name' => '佐敦',
            'code' => 'KLN-002',
            'region' => 'KLN',
        ),
        array (
            'default_name' => 'Mong Kok',
            'locale_name' => '旺角',
            'code' => 'KLN-003',
            'region' => 'KLN',
        ),
        array (
            'default_name' => 'Yau Tong',
            'locale_name' => '油塘',
            'code' => 'KLN-004',
            'region' => 'KLN',
        ),
        array (
            'default_name' => 'Hung Hom',
            'locale_name' => '紅磡',
            'code' => 'KLN-005',
            'region' => 'KLN',
        ),
        array (
            'default_name' => 'Mei Fu',
            'locale_name' => '美孚',
            'code' => 'KLN-006',
            'region' => 'KLN',
        ),
        array (
            'default_name' => 'Choi Hung',
            'locale_name' => '彩虹',
            'code' => 'KLN-007',
            'region' => 'KLN',
        ),
        array (
            'default_name' => 'Lok Fu',
            'locale_name' => '樂富',
            'code' => 'KLN-008',
            'region' => 'KLN',
        ),
        array (
            'default_name' => 'Lam Tin',
            'locale_name' => '藍田',
            'code' => 'KLN-009',
            'region' => 'KLN',
        ),
        array (
            'default_name' => 'Kwun Tong',
            'locale_name' => '觀塘',
            'code' => 'KLN-010',
            'region' => 'KLN',
        ),
        37 =>
        array (
            'default_name' => 'Kowlook City',
            'locale_name' => '九龍城',
            'code' => 'KLN-011',
            'region' => 'KLN',
        ),
        38 =>
        array (
            'default_name' => 'Kowloon Tong',
            'locale_name' => '九龍塘',
            'code' => 'KLN-012',
            'region' => 'KLN',
        ),
        39 =>
        array (
            'default_name' => 'Kowloon Bay',
            'locale_name' => '九龍灣',
            'code' => 'KLN-013',
            'region' => 'KLN',
        ),
        40 =>
        array (
            'default_name' => 'To Kwa Wan',
            'locale_name' => '土瓜灣',
            'code' => 'KLN-014',
            'region' => 'KLN',
        ),
        41 =>
        array (
            'default_name' => 'Tai Kok Tsui',
            'locale_name' => '大角咀',
            'code' => 'KLN-015',
            'region' => 'KLN',
        ),
        42 =>
        array (
            'default_name' => 'Ngau Tau Kok',
            'locale_name' => '牛頭角',
            'code' => 'KLN-016',
            'region' => 'KLN',
        ),
        43 =>
        array (
            'default_name' => 'Shek Kip Mei',
            'locale_name' => '石硤尾',
            'code' => 'KLN-017',
            'region' => 'KLN',
        ),
        44 =>
        array (
            'default_name' => 'Tsim Sha Tsui',
            'locale_name' => '尖沙咀',
            'code' => 'KLN-018',
            'region' => 'KLN',
        ),
        45 =>
        array (
            'default_name' => 'Ho Man Tin',
            'locale_name' => '何文田',
            'code' => 'KLN-019',
            'region' => 'KLN',
        ),
        46 =>
        array (
            'default_name' => 'Yau Ma Tei',
            'locale_name' => '油麻地',
            'code' => 'KLN-020',
            'region' => 'KLN',
        ),
        47 =>
        array (
            'default_name' => 'Cheung Sha Wan',
            'locale_name' => '長沙灣',
            'code' => 'KLN-021',
            'region' => 'KLN',
        ),
        48 =>
        array (
            'default_name' => 'Lai Chi Kok',
            'locale_name' => '荔枝角',
            'code' => 'KLN-022',
            'region' => 'KLN',
        ),
        49 =>
        array (
            'default_name' => 'Sham Shui Po',
            'locale_name' => '深水埗',
            'code' => 'KLN-023',
            'region' => 'KLN',
        ),
        50 =>
        array (
            'default_name' => 'Wong Tai Sin',
            'locale_name' => '黃大仙',
            'code' => 'KLN-024',
            'region' => 'KLN',
        ),
        51 =>
        array (
            'default_name' => 'Tsz Wan Shan',
            'locale_name' => '慈雲山',
            'code' => 'KLN-025',
            'region' => 'KLN',
        ),
        52 =>
        array (
            'default_name' => 'San Po Kong',
            'locale_name' => '新蒲崗',
            'code' => 'KLN-026',
            'region' => 'KLN',
        ),
        53 =>
        array (
            'default_name' => 'Lei Yue Mun',
            'locale_name' => '鯉魚門',
            'code' => 'KLN-027',
            'region' => 'KLN',
        ),
        54 =>
        array (
            'default_name' => 'Diamond Hill',
            'locale_name' => '鑽石山',
            'code' => 'KLN-028',
            'region' => 'KLN',
        ),
       ),
       'NT'=>array(
        55 =>
        array (
            'default_name' => 'Sheung Shui',
            'locale_name' => '上水',
            'code' => 'NT-001',
            'region' => 'NT',
        ),
        56 =>
        array (
            'default_name' => 'Tai Po',
            'locale_name' => '大埔',
            'code' => 'NT-002',
            'region' => 'NT',
        ),
        57 =>
        array (
            'default_name' => 'Tai Wai',
            'locale_name' => '大圍',
            'code' => 'NT-003',
            'region' => 'NT',
        ),
        58 =>
        array (
            'default_name' => 'Yuen Long',
            'locale_name' => '元朗',
            'code' => 'NT-004',
            'region' => 'NT',
        ),
        59 =>
        array (
            'default_name' => 'Tai Wo',
            'locale_name' => '太和',
            'code' => 'NT-005',
            'region' => 'NT',
        ),
        60 =>
        array (
            'default_name' => 'Tuen Mun',
            'locale_name' => '屯門',
            'code' => 'NT-006',
            'region' => 'NT',
        ),
        61 =>
        array (
            'default_name' => 'Fo Tan',
            'locale_name' => '火炭',
            'code' => 'NT-007',
            'region' => 'NT',
        ),
        62 =>
        array (
            'default_name' => 'Sai Kung',
            'locale_name' => '西貢',
            'code' => 'NT-008',
            'region' => 'NT',
        ),
        63 =>
        array (
            'default_name' => 'Sha Tin',
            'locale_name' => '沙田',
            'code' => 'NT-009',
            'region' => 'NT',
        ),
        64 =>
        array (
            'default_name' => 'Tsing Yi',
            'locale_name' => '青衣',
            'code' => 'NT-010',
            'region' => 'NT',
        ),
        65 =>
        array (
            'default_name' => 'Fan Ling',
            'locale_name' => '粉嶺',
            'code' => 'NT-011',
            'region' => 'NT',
        ),
        66 =>
        array (
            'default_name' => 'Tsuen Wan',
            'locale_name' => '荃灣',
            'code' => 'NT-012',
            'region' => 'NT',
        ),
        67 =>
        array (
            'default_name' => 'Ma Wan',
            'locale_name' => '馬灣',
            'code' => 'NT-013',
            'region' => 'NT',
        ),
        68 =>
        array (
            'default_name' => 'Sham Tseng',
            'locale_name' => '深井',
            'code' => 'NT-014',
            'region' => 'NT',
        ),
        69 =>
        array (
            'default_name' => 'Kwai Fong',
            'locale_name' => '葵芳',
            'code' => 'NT-015',
            'region' => 'NT',
        ),
        70 =>
        array (
            'default_name' => 'Kwai Chung',
            'locale_name' => '葵涌',
            'code' => 'NT-016',
            'region' => 'NT',
        ),
        71 =>
        array (
            'default_name' => 'Tin Shui Wai',
            'locale_name' => '天水圍',
            'code' => 'NT-017',
            'region' => 'NT',
        ),
        72 =>
        array (
            'default_name' => 'Lau Fau Shan ',
            'locale_name' => '流浮山',
            'code' => 'NT-018',
            'region' => 'NT',
        ),
        73 =>
        array (
            'default_name' => 'Ma On Shan',
            'locale_name' => '馬鞍山',
            'code' => 'NT-019',
            'region' => 'NT',
        ),
        74 =>
        array (
            'default_name' => 'Tseung Kwan O',
            'locale_name' => '將軍澳',
            'code' => 'NT-020',
            'region' => 'NT',
        ),
        75 =>
        array (
            'default_name' => 'Lok Ma Chau',
            'locale_name' => '落馬洲',
            'code' => 'NT-021',
            'region' => 'NT',
        ),
        76 =>
        array (
            'default_name' => 'Tung Chung',
            'locale_name' => '東涌',
            'code' => 'NT-022',
            'region' => 'NT',
        ),
        77 =>
        array (
            'default_name' => 'Chap Lap Lok',
            'locale_name' => '赤鱲角',
            'code' => 'NT-023',
            'region' => 'NT',
        ),
        78 =>
        array (
            'default_name' => 'Discovery Bay',
            'locale_name' => '愉景灣',
            'code' => 'NT-024',
            'region' => 'NT',
        ),
    ),
    1 => array(
        79 =>
        array (
            'default_name' => 'Battery Road',
            'code' => '1',
            'region' => '1',
        ),
        80 =>
        array (
            'default_name' => 'Cecil Street',
            'code' => 'sg-1-2',
            'region' => '1',
        ),
        81 =>
        array (
            'default_name' => 'Cross Street',
            'code' => 'sg-1-3',
            'region' => '1',
        ),
        82 =>
        array (
            'default_name' => 'Eu Tong Sen Street',
            'code' => 'sg-1-4',
            'region' => '1',
        ),
        83 =>
        array (
            'default_name' => 'Havelock Road',
            'code' => 'sg-1-5',
            'region' => '1',
        ),
        84 =>
        array (
            'default_name' => 'Marina',
            'code' => 'sg-1-6',
            'region' => '1',
        ),
        85 =>
        array (
            'default_name' => 'Maxwell',
            'code' => 'sg-1-7',
            'region' => '1',
        ),
        86 =>
        array (
            'default_name' => 'New Bridge',
            'code' => 'sg-1-8',
            'region' => '1',
        ),
        87 =>
        array (
            'default_name' => 'Raffles Link/ Place',
            'code' => 'sg-1-9',
            'region' => '1',
        ),
        88 =>
        array (
            'default_name' => 'Republic Boulevard',
            'code' => 'sg-1-10',
            'region' => '1',
        ),
        89 =>
        array (
            'default_name' => 'Robinson',
            'code' => 'sg-1-11',
            'region' => '1',
        ),
        90 =>
        array (
            'default_name' => 'South Bridge Road',
            'code' => 'sg-1-12',
            'region' => '1',
        ),
        91 =>
        array (
            'default_name' => 'Telok Ayer',
            'code' => 'sg-1-13',
            'region' => '1',
        ),
        92 =>
        array (
            'default_name' => 'Temasek Avenue',
            'code' => 'sg-1-14',
            'region' => '1',
        ),
        93 =>
        array (
            'default_name' => 'Temasek Boulevard',
            'code' => 'sg-1-15',
            'region' => '1',
        ),
        94 =>
        array (
            'default_name' => 'Upper Cross Sreet',
            'code' => 'sg-1-16',
            'region' => '1',
        ),
        95 =>
        array (
            'default_name' => 'Upper Pickering Street',
            'code' => 'sg-1-17',
            'region' => '1',
        ),
    ),
    2 => array(
        96 =>
        array (
            'default_name' => 'Anson',
            'code' => '2',
            'region' => '2',
        ),
        97 =>
        array (
            'default_name' => 'Keppel',
            'code' => 'sg-2-2',
            'region' => '2',
        ),
        98 =>
        array (
            'default_name' => 'Prince Edward Road',
            'code' => 'sg-2-3',
            'region' => '2',
        ),
        99 =>
        array (
            'default_name' => 'New Bridge Road',
            'code' => 'sg-2-4',
            'region' => '2',
        ),
        100 =>
        array (
            'default_name' => 'Shenton Way',
            'code' => 'sg-2-5',
            'region' => '2',
        ),
        101 =>
        array (
            'default_name' => 'Tanjong Pagar',
            'code' => 'sg-2-6',
            'region' => '2',
        ),
        102 =>
        array (
            'default_name' => 'Tras Street',
            'code' => 'sg-2-7',
            'region' => '2',
        ),),
    3 => array(
        103 =>
        array (
            'default_name' => 'Alexandra Road',
            'code' => '3',
            'region' => '3',
        ),
        104 =>
        array (
            'default_name' => 'Bukit Merah',
            'code' => 'sg-3-2',
            'region' => '3',
        ),
        105 =>
        array (
            'default_name' => 'Chin Swee Road',
            'code' => 'sg-3-3',
            'region' => '3',
        ),
        106 =>
        array (
            'default_name' => 'Commonwealth',
            'code' => 'sg-3-4',
            'region' => '3',
        ),
        107 =>
        array (
            'default_name' => 'Delta Avenue',
            'code' => 'sg-3-5',
            'region' => '3',
        ),
        108 =>
        array (
            'default_name' => 'Havelock Road',
            'code' => 'sg-3-6',
            'region' => '3',
        ),
        109 =>
        array (
            'default_name' => 'Henderson Road',
            'code' => 'sg-3-7',
            'region' => '3',
        ),
        110 =>
        array (
            'default_name' => 'Jalan Bukit Merah',
            'code' => 'sg-3-8',
            'region' => '3',
        ),
        111 =>
        array (
            'default_name' => 'Kim Tian Road',
            'code' => 'sg-3-9',
            'region' => '3',
        ),
        112 =>
        array (
            'default_name' => 'Leng Kee Road',
            'code' => 'sg-3-10',
            'region' => '3',
        ),
        113 =>
        array (
            'default_name' => 'Margaret Drive',
            'code' => 'sg-3-11',
            'region' => '3',
        ),
        114 =>
        array (
            'default_name' => 'Outram Road',
            'code' => 'sg-3-12',
            'region' => '3',
        ),
        115 =>
        array (
            'default_name' => 'Queensway',
            'code' => 'sg-3-13',
            'region' => '3',
        ),
        116 =>
        array (
            'default_name' => 'Redhill',
            'code' => 'sg-3-14',
            'region' => '3',
        ),
        117 =>
        array (
            'default_name' => 'Tiong Bahru',
            'code' => 'sg-3-15',
            'region' => '3',
        ),),
    4 => array(
        118 =>
        array (
            'default_name' => 'Bukit Purmei',
            'code' => '4',
            'region' => '4',
        ),
        119 =>
        array (
            'default_name' => 'Harbourfront',
            'code' => 'sg-4-2',
            'region' => '4',
        ),
        120 =>
        array (
            'default_name' => 'Lock Road',
            'code' => 'sg-4-3',
            'region' => '4',
        ),
        121 =>
        array (
            'default_name' => 'Maritime Square',
            'code' => 'sg-4-4',
            'region' => '4',
        ),
        122 =>
        array (
            'default_name' => 'Telok Blangah',
            'code' => 'sg-4-5',
            'region' => '4',
        ),),
    5 => array(
        123 =>
        array (
            'default_name' => 'Alexandra Road',
            'code' => '5',
            'region' => '5',
        ),
        124 =>
        array (
            'default_name' => 'Clementi',
            'code' => 'sg-5-2',
            'region' => '5',
        ),
        125 =>
        array (
            'default_name' => 'Dover',
            'code' => 'sg-5-3',
            'region' => '5',
        ),
        126 =>
        array (
            'default_name' => 'Hong Leong Garden',
            'code' => 'sg-5-4',
            'region' => '5',
        ),
        127 =>
        array (
            'default_name' => 'Jalan Buroh',
            'code' => 'sg-5-5',
            'region' => '5',
        ),
        128 =>
        array (
            'default_name' => 'North Buona Vista',
            'code' => 'sg-5-6',
            'region' => '5',
        ),
        129 =>
        array (
            'default_name' => 'Pasir Panjang',
            'code' => 'sg-5-7',
            'region' => '5',
        ),
        130 =>
        array (
            'default_name' => 'Pandan Cresent',
            'code' => 'sg-5-8',
            'region' => '5',
        ),
        131 =>
        array (
            'default_name' => 'Science Park',
            'code' => 'sg-5-9',
            'region' => '5',
        ),
        132 =>
        array (
            'default_name' => 'South Buona Vista',
            'code' => 'sg-5-10',
            'region' => '5',
        ),
        133 =>
        array (
            'default_name' => 'Jalan Lempeng',
            'code' => 'sg-5-11',
            'region' => '5',
        ),
        134 =>
        array (
            'default_name' => 'West Coast',
            'code' => 'sg-5-12',
            'region' => '5',
        ),),
    6 => array(
        135 =>
        array (
            'default_name' => 'Beach Road',
            'code' => '6',
            'region' => '6',
        ),
        136 =>
        array (
            'default_name' => 'City Hall',
            'code' => 'sg-6-2',
            'region' => '6',
        ),
        137 =>
        array (
            'default_name' => 'Clarke Quay',
            'code' => 'sg-6-3',
            'region' => '6',
        ),
        138 =>
        array (
            'default_name' => 'Coleman Steet',
            'code' => 'sg-6-4',
            'region' => '6',
        ),
        139 =>
        array (
            'default_name' => 'High Street',
            'code' => 'sg-6-5',
            'region' => '6',
        ),
        140 =>
        array (
            'default_name' => 'Hill Street',
            'code' => 'sg-6-6',
            'region' => '6',
        ),
        array (
            'default_name' => 'North Bridge',
            'code' => 'sg-6-7',
            'region' => '6',
        ),
        array (
            'default_name' => 'River Valley',
            'code' => 'sg-6-8',
            'region' => '6',
        ),
        array (
            'default_name' => 'St. Andrew\\\'s Road',
            'code' => 'sg-6-9',
            'region' => '6',
        ),),
    7 => array(
        array (
            'default_name' => 'Albert Street',
            'code' => '7',
            'region' => '7',
        ),
        array (
            'default_name' => 'Arab Street',
            'code' => 'sg-7-2',
            'region' => '7',
        ),
        array (
            'default_name' => 'Beach Road',
            'code' => 'sg-7-3',
            'region' => '7',
        ),
        array (
            'default_name' => 'Bencoolen Street',
            'code' => 'sg-7-4',
            'region' => '7',
        ),
        array (
            'default_name' => 'Bras Basah',
            'code' => 'sg-7-5',
            'region' => '7',
        ),
        array (
            'default_name' => 'Bugis',
            'code' => 'sg-7-6',
            'region' => '7',
        ),
        array (
            'default_name' => 'Crawford Lane',
            'code' => 'sg-7-7',
            'region' => '7',
        ),
        151 =>
        array (
            'default_name' => 'Golden Mile',
            'code' => 'sg-7-8',
            'region' => '7',
        ),
        152 =>
        array (
            'default_name' => 'Jalan Sultan',
            'code' => 'sg-7-9',
            'region' => '7',
        ),
        153 =>
        array (
            'default_name' => 'Liang Seah Street',
            'code' => 'sg-7-10',
            'region' => '7',
        ),
        154 =>
        array (
            'default_name' => 'Middle Road',
            'code' => 'sg-7-11',
            'region' => '7',
        ),
        155 =>
        array (
            'default_name' => 'North Bridge',
            'code' => 'sg-7-12',
            'region' => '7',
        ),
        156 =>
        array (
            'default_name' => 'Ophir',
            'code' => 'sg-7-13',
            'region' => '7',
        ),
        157 =>
        array (
            'default_name' => 'Prinsep Street',
            'code' => 'sg-7-14',
            'region' => '7',
        ),
        158 =>
        array (
            'default_name' => 'Queen Street',
            'code' => 'sg-7-15',
            'region' => '7',
        ),
        159 =>
        array (
            'default_name' => 'Rochor',
            'code' => 'sg-7-16',
            'region' => '7',
        ),
        160 =>
        array (
            'default_name' => 'Selegie Road',
            'code' => 'sg-7-17',
            'region' => '7',
        ),
        161 =>
        array (
            'default_name' => 'Short Street',
            'code' => 'sg-7-18',
            'region' => '7',
        ),
        162 =>
        array (
            'default_name' => 'Victoria Lane',
            'code' => 'sg-7-19',
            'region' => '7',
        ),
        163 =>
        array (
            'default_name' => 'Victoria Street',
            'code' => 'sg-7-20',
            'region' => '7',
        ),
        164 =>
        array (
            'default_name' => 'Waterloo Street',
            'code' => 'sg-7-21',
            'region' => '7',
        ),),
    8 => array(
        165 =>
        array (
            'default_name' => 'Jalan Besar',
            'code' => '8',
            'region' => '8',
        ),
        166 =>
        array (
            'default_name' => 'Farrer Park',
            'code' => 'sg-8-2',
            'region' => '8',
        ),
        167 =>
        array (
            'default_name' => 'Kampong Kapor',
            'code' => 'sg-8-3',
            'region' => '8',
        ),
        168 =>
        array (
            'default_name' => 'Gloucester Road',
            'code' => 'sg-8-4',
            'region' => '8',
        ),
        169 =>
        array (
            'default_name' => 'King George\\\'s',
            'code' => 'sg-8-5',
            'region' => '8',
        ),
        170 =>
        array (
            'default_name' => 'Little India',
            'code' => 'sg-8-6',
            'region' => '8',
        ),
        171 =>
        array (
            'default_name' => 'Oxford Road',
            'code' => 'sg-8-7',
            'region' => '8',
        ),
        172 =>
        array (
            'default_name' => 'Kitchener',
            'code' => 'sg-8-8',
            'region' => '8',
        ),
        173 =>
        array (
            'default_name' => 'Rangoon Road',
            'code' => 'sg-8-9',
            'region' => '8',
        ),
        174 =>
        array (
            'default_name' => 'Syed Alwi',
            'code' => 'sg-8-10',
            'region' => '8',
        ),
        175 =>
        array (
            'default_name' => 'Serangoon Road',
            'code' => 'sg-8-11',
            'region' => '8',
        ),
        176 =>
        array (
            'default_name' => 'Tyrwhitt Road',
            'code' => 'sg-8-12',
            'region' => '8',
        ),),
    9 => array(
        177 =>
        array (
            'default_name' => 'Bukit Timah Road',
            'code' => '9',
            'region' => '9',
        ),
        178 =>
        array (
            'default_name' => 'Cairnhill',
            'code' => 'sg-9-2',
            'region' => '9',
        ),
        179 =>
        array (
            'default_name' => 'Clemenceau Avenue',
            'code' => 'sg-9-3',
            'region' => '9',
        ),
        180 =>
        array (
            'default_name' => 'Cuppage',
            'code' => 'sg-9-4',
            'region' => '9',
        ),
        181 =>
        array (
            'default_name' => 'Emerald Hill',
            'code' => 'sg-9-5',
            'region' => '9',
        ),
        182 =>
        array (
            'default_name' => 'Grange Road',
            'code' => 'sg-9-6',
            'region' => '9',
        ),
        183 =>
        array (
            'default_name' => 'Kampong Java',
            'code' => 'sg-9-7',
            'region' => '9',
        ),
        184 =>
        array (
            'default_name' => 'Killiney Road',
            'code' => 'sg-9-8',
            'region' => '9',
        ),
        185 =>
        array (
            'default_name' => 'Kim Seng',
            'code' => 'sg-9-9',
            'region' => '9',
        ),
        186 =>
        array (
            'default_name' => 'Kim Yam Road',
            'code' => 'sg-9-10',
            'region' => '9',
        ),
        187 =>
        array (
            'default_name' => 'Martin Road',
            'code' => 'sg-9-11',
            'region' => '9',
        ),
        188 =>
        array (
            'default_name' => 'Mohamed Sultan',
            'code' => 'sg-9-12',
            'region' => '9',
        ),
        189 =>
        array (
            'default_name' => 'Mount Sophia',
            'code' => 'sg-9-13',
            'region' => '9',
        ),
        190 =>
        array (
            'default_name' => 'Orchard Road',
            'code' => 'sg-9-14',
            'region' => '9',
        ),
        191 =>
        array (
            'default_name' => 'Oxley Rise',
            'code' => 'sg-9-15',
            'region' => '9',
        ),
        192 =>
        array (
            'default_name' => 'Peck Hay Road',
            'code' => 'sg-9-16',
            'region' => '9',
        ),
        193 =>
        array (
            'default_name' => 'Penang Road',
            'code' => 'sg-9-17',
            'region' => '9',
        ),
        194 =>
        array (
            'default_name' => 'River Valley',
            'code' => 'sg-9-18',
            'region' => '9',
        ),
        195 =>
        array (
            'default_name' => 'Scotts Road',
            'code' => 'sg-9-19',
            'region' => '9',
        ),
        196 =>
        array (
            'default_name' => 'Wilkie Road',
            'code' => 'sg-9-20',
            'region' => '9',
        ),),
    10 => array(
        197 =>
        array (
            'default_name' => 'Anderson Road',
            'code' => '10',
            'region' => '10',
        ),
        198 =>
        array (
            'default_name' => 'Ardmore Park',
            'code' => 'sg-10-2',
            'region' => '10',
        ),
        199 =>
        array (
            'default_name' => 'Bukit Timah Road',
            'code' => 'sg-10-3',
            'region' => '10',
        ),
        200 =>
        array (
            'default_name' => 'Cuscaden Road',
            'code' => 'sg-10-4',
            'region' => '10',
        ),
        201 =>
        array (
            'default_name' => 'Draycott Drive',
            'code' => 'sg-10-5',
            'region' => '10',
        ),
        202 =>
        array (
            'default_name' => 'Duke Road',
            'code' => 'sg-10-6',
            'region' => '10',
        ),
        203 =>
        array (
            'default_name' => 'Farrer Road',
            'code' => 'sg-10-7',
            'region' => '10',
        ),
        204 =>
        array (
            'default_name' => 'Ghim Moh',
            'code' => 'sg-10-8',
            'region' => '10',
        ),
        205 =>
        array (
            'default_name' => 'Holland',
            'code' => 'sg-10-9',
            'region' => '10',
        ),
        206 =>
        array (
            'default_name' => 'Jervois Road',
            'code' => 'sg-10-10',
            'region' => '10',
        ),
        207 =>
        array (
            'default_name' => 'King\\\'s Road',
            'code' => 'sg-10-11',
            'region' => '10',
        ),
        208 =>
        array (
            'default_name' => 'Sixth Avenue',
            'code' => 'sg-10-12',
            'region' => '10',
        ),
        209 =>
        array (
            'default_name' => 'Stevens Road',
            'code' => 'sg-10-13',
            'region' => '10',
        ),
        210 =>
        array (
            'default_name' => 'Tanglin Road',
            'code' => 'sg-10-14',
            'region' => '10',
        ),),
    11 => array(
        211 =>
        array (
            'default_name' => 'Adam Road',
            'code' => '11',
            'region' => '11',
        ),
        212 =>
        array (
            'default_name' => 'Andrew Road',
            'code' => 'sg-11-2',
            'region' => '11',
        ),
        213 =>
        array (
            'default_name' => 'Barker Road',
            'code' => 'sg-11-3',
            'region' => '11',
        ),
        214 =>
        array (
            'default_name' => 'Dunearn Road',
            'code' => 'sg-11-4',
            'region' => '11',
        ),
        215 =>
        array (
            'default_name' => 'Essex Road',
            'code' => 'sg-11-5',
            'region' => '11',
        ),
        216 =>
        array (
            'default_name' => 'Hillcrest Road',
            'code' => 'sg-11-6',
            'region' => '11',
        ),
        217 =>
        array (
            'default_name' => 'Kheam Hock',
            'code' => 'sg-11-7',
            'region' => '11',
        ),
        218 =>
        array (
            'default_name' => 'Linden Drive',
            'code' => 'sg-11-8',
            'region' => '11',
        ),
        219 =>
        array (
            'default_name' => 'Lornie Road',
            'code' => 'sg-11-9',
            'region' => '11',
        ),
        220 =>
        array (
            'default_name' => 'Malcolm Road',
            'code' => 'sg-11-10',
            'region' => '11',
        ),
        221 =>
        array (
            'default_name' => 'Marymount Road',
            'code' => 'sg-11-11',
            'region' => '11',
        ),
        222 =>
        array (
            'default_name' => 'Moulmein Road',
            'code' => 'sg-11-12',
            'region' => '11',
        ),
        223 =>
        array (
            'default_name' => 'Newton Road',
            'code' => 'sg-11-13',
            'region' => '11',
        ),
        224 =>
        array (
            'default_name' => 'Novena',
            'code' => 'sg-11-14',
            'region' => '11',
        ),
        225 =>
        array (
            'default_name' => 'Thomson',
            'code' => 'sg-11-15',
            'region' => '11',
        ),
        226 =>
        array (
            'default_name' => 'Toa Payoh Rise',
            'code' => 'sg-11-16',
            'region' => '11',
        ),
        227 =>
        array (
            'default_name' => 'Watten Estate',
            'code' => 'sg-11-17',
            'region' => '11',
        ),
        228 =>
        array (
            'default_name' => 'Whitley Road',
            'code' => 'sg-11-18',
            'region' => '11',
        ),),
     12 => array(
        229 =>
        array (
            'default_name' => 'Balestier',
            'code' => '12',
            'region' => '12',
        ),
        230 =>
        array (
            'default_name' => 'Bendemeer',
            'code' => 'sg-12-2',
            'region' => '12',
        ),
        231 =>
        array (
            'default_name' => 'Geylang Bahru',
            'code' => 'sg-12-3',
            'region' => '12',
        ),
        232 =>
        array (
            'default_name' => 'Jalan Ampas',
            'code' => 'sg-12-4',
            'region' => '12',
        ),
        233 =>
        array (
            'default_name' => 'Jalan Rajah',
            'code' => 'sg-12-5',
            'region' => '12',
        ),
        234 =>
        array (
            'default_name' => 'Kallang',
            'code' => 'sg-12-6',
            'region' => '12',
        ),
        235 =>
        array (
            'default_name' => 'Lavender Street',
            'code' => 'sg-12-7',
            'region' => '12',
        ),
        236 =>
        array (
            'default_name' => 'Serangoon',
            'code' => 'sg-12-8',
            'region' => '12',
        ),
        237 =>
        array (
            'default_name' => 'St Michael\\\'s',
            'code' => 'sg-12-9',
            'region' => '12',
        ),
        238 =>
        array (
            'default_name' => 'Thomson',
            'code' => 'sg-12-10',
            'region' => '12',
        ),
        239 =>
        array (
            'default_name' => 'Toa Payoh',
            'code' => 'sg-12-11',
            'region' => '12',
        ),
        240 =>
        array (
            'default_name' => 'Towner',
            'code' => 'sg-12-12',
            'region' => '12',
        ),),
    13 => array(
        241 =>
        array (
            'default_name' => 'Cedar Avenue',
            'code' => '13',
            'region' => '13',
        ),
        242 =>
        array (
            'default_name' => 'Circuit Road',
            'code' => 'sg-13-2',
            'region' => '13',
        ),
        243 =>
        array (
            'default_name' => 'Genting Road',
            'code' => 'sg-13-3',
            'region' => '13',
        ),
        244 =>
        array (
            'default_name' => 'Joo Seng',
            'code' => 'sg-13-4',
            'region' => '13',
        ),
        245 =>
        array (
            'default_name' => 'Kallang Way',
            'code' => 'sg-13-5',
            'region' => '13',
        ),
        246 =>
        array (
            'default_name' => 'Macpherson',
            'code' => 'sg-13-6',
            'region' => '13',
        ),
        247 =>
        array (
            'default_name' => 'Mount Vernon',
            'code' => 'sg-13-7',
            'region' => '13',
        ),
        248 =>
        array (
            'default_name' => 'Potong Pasir',
            'code' => 'sg-13-8',
            'region' => '13',
        ),
        249 =>
        array (
            'default_name' => 'Tannery',
            'code' => 'sg-13-9',
            'region' => '13',
        ),
        250 =>
        array (
            'default_name' => 'Upper Aljunied Road',
            'code' => 'sg-13-10',
            'region' => '13',
        ),),
     14 => array(
        251 =>
        array (
            'default_name' => 'Aljunied Road',
            'code' => '14',
            'region' => '14',
        ),
        252 =>
        array (
            'default_name' => 'Changi Road',
            'code' => 'sg-14-2',
            'region' => '14',
        ),
        253 =>
        array (
            'default_name' => 'Dakota Crescent',
            'code' => 'sg-14-3',
            'region' => '14',
        ),
        254 =>
        array (
            'default_name' => 'Eunos',
            'code' => 'sg-14-4',
            'region' => '14',
        ),
        255 =>
        array (
            'default_name' => 'Geylang',
            'code' => 'sg-14-5',
            'region' => '14',
        ),
        256 =>
        array (
            'default_name' => 'Guillemard Road',
            'code' => 'sg-14-6',
            'region' => '14',
        ),
        257 =>
        array (
            'default_name' => 'Jalan Eunos',
            'code' => 'sg-14-7',
            'region' => '14',
        ),
        258 =>
        array (
            'default_name' => 'Kaki Bukit',
            'code' => 'sg-14-8',
            'region' => '14',
        ),
        259 =>
        array (
            'default_name' => 'Lor # Geylang',
            'code' => 'sg-14-9',
            'region' => '14',
        ),
        260 =>
        array (
            'default_name' => 'Mattar Road',
            'code' => 'sg-14-10',
            'region' => '14',
        ),
        261 =>
        array (
            'default_name' => 'Old Airport Road',
            'code' => 'sg-14-11',
            'region' => '14',
        ),
        262 =>
        array (
            'default_name' => 'Paya Lebar',
            'code' => 'sg-14-12',
            'region' => '14',
        ),
        263 =>
        array (
            'default_name' => 'Sims Avenue / Drive',
            'code' => 'sg-14-13',
            'region' => '14',
        ),
        264 =>
        array (
            'default_name' => 'Ubi',
            'code' => 'sg-14-14',
            'region' => '14',
        ),),
    15 => array(
        265 =>
        array (
            'default_name' => 'Amber',
            'code' => '15',
            'region' => '15',
        ),
        266 =>
        array (
            'default_name' => 'Brooke Road',
            'code' => 'sg-15-2',
            'region' => '15',
        ),
        267 =>
        array (
            'default_name' => 'Ceylon Road',
            'code' => 'sg-15-3',
            'region' => '15',
        ),
        268 =>
        array (
            'default_name' => 'Dunman Road',
            'code' => 'sg-15-4',
            'region' => '15',
        ),
        269 =>
        array (
            'default_name' => 'East Coast',
            'code' => 'sg-15-5',
            'region' => '15',
        ),
        270 =>
        array (
            'default_name' => 'Fidelio Street',
            'code' => 'sg-15-6',
            'region' => '15',
        ),
        271 =>
        array (
            'default_name' => 'Frankel Avenue',
            'code' => 'sg-15-7',
            'region' => '15',
        ),
        272 =>
        array (
            'default_name' => 'Goodman Road',
            'code' => 'sg-15-8',
            'region' => '15',
        ),
        273 =>
        array (
            'default_name' => 'Joo Chiat',
            'code' => 'sg-15-9',
            'region' => '15',
        ),
        274 =>
        array (
            'default_name' => 'Katong',
            'code' => 'sg-15-10',
            'region' => '15',
        ),
        275 =>
        array (
            'default_name' => 'Koon Seng Road',
            'code' => 'sg-15-11',
            'region' => '15',
        ),
        276 =>
        array (
            'default_name' => 'Margate Road',
            'code' => 'sg-15-12',
            'region' => '15',
        ),
        277 =>
        array (
            'default_name' => 'Marine Drive',
            'code' => 'sg-15-13',
            'region' => '15',
        ),
        278 =>
        array (
            'default_name' => 'Marine Parade',
            'code' => 'sg-15-14',
            'region' => '15',
        ),
        279 =>
        array (
            'default_name' => 'Marine Terrace',
            'code' => 'sg-15-15',
            'region' => '15',
        ),
        280 =>
        array (
            'default_name' => 'Marine Vista',
            'code' => 'sg-15-16',
            'region' => '15',
        ),
        281 =>
        array (
            'default_name' => 'Martia Road',
            'code' => 'sg-15-17',
            'region' => '15',
        ),
        282 =>
        array (
            'default_name' => 'Mountbatten',
            'code' => 'sg-15-18',
            'region' => '15',
        ),
        283 =>
        array (
            'default_name' => 'Palm Avenue',
            'code' => 'sg-15-19',
            'region' => '15',
        ),
        284 =>
        array (
            'default_name' => 'Seraya Road',
            'code' => 'sg-15-20',
            'region' => '15',
        ),
        285 =>
        array (
            'default_name' => 'Siglap Link / View',
            'code' => 'sg-15-21',
            'region' => '15',
        ),
        286 =>
        array (
            'default_name' => 'Still Road',
            'code' => 'sg-15-22',
            'region' => '15',
        ),
        287 =>
        array (
            'default_name' => 'Tanjong Katong Road',
            'code' => 'sg-15-23',
            'region' => '15',
        ),
        288 =>
        array (
            'default_name' => 'Tanjong Rhu',
            'code' => 'sg-15-24',
            'region' => '15',
        ),
        289 =>
        array (
            'default_name' => 'Telok Kurau Road',
            'code' => 'sg-15-25',
            'region' => '15',
        ),
        290 =>
        array (
            'default_name' => 'Upper East Coast',
            'code' => 'sg-15-26',
            'region' => '15',
        ),),
    16 => array(
        291 =>
        array (
            'default_name' => 'Bedok North',
            'code' => '16',
            'region' => '16',
        ),
        292 =>
        array (
            'default_name' => 'Bedok Reservoir',
            'code' => 'sg-16-2',
            'region' => '16',
        ),
        293 =>
        array (
            'default_name' => 'Bedok South',
            'code' => 'sg-16-3',
            'region' => '16',
        ),
        294 =>
        array (
            'default_name' => 'Chai Chee Lane',
            'code' => 'sg-16-4',
            'region' => '16',
        ),
        295 =>
        array (
            'default_name' => 'Changi South',
            'code' => 'sg-16-5',
            'region' => '16',
        ),
        296 =>
        array (
            'default_name' => 'Eastwood',
            'code' => 'sg-16-6',
            'region' => '16',
        ),
        297 =>
        array (
            'default_name' => 'Expo Drive',
            'code' => 'sg-16-7',
            'region' => '16',
        ),
        298 =>
        array (
            'default_name' => 'Kew Drive',
            'code' => 'sg-16-8',
            'region' => '16',
        ),
        299 =>
        array (
            'default_name' => 'New Upper Changi',
            'code' => 'sg-16-9',
            'region' => '16',
        ),
        300 =>
        array (
            'default_name' => 'Sennett Road',
            'code' => 'sg-16-10',
            'region' => '16',
        ),
        301 =>
        array (
            'default_name' => 'Upper Changi Road',
            'code' => 'sg-16-11',
            'region' => '16',
        ),
        302 =>
        array (
            'default_name' => 'Upper East Coast',
            'code' => 'sg-16-12',
            'region' => '16',
        ),
        303 =>
        array (
            'default_name' => 'Xilin Avenue',
            'code' => 'sg-16-13',
            'region' => '16',
        ),),
        '17' => array(
        304 =>
        array (
            'default_name' => 'Airline Road',
            'code' => '17',
            'region' => '17',
        ),
        305 =>
        array (
            'default_name' => 'Airport Boulevard',
            'code' => 'sg-17-2',
            'region' => '17',
        ),
        306 =>
        array (
            'default_name' => 'Airport Cargo',
            'code' => 'sg-17-3',
            'region' => '17',
        ),
        307 =>
        array (
            'default_name' => 'Changi Village',
            'code' => 'sg-17-4',
            'region' => '17',
        ),
        308 =>
        array (
            'default_name' => 'Loyang Avenue / Drive',
            'code' => 'sg-17-5',
            'region' => '17',
        ),
        309 =>
        array (
            'default_name' => 'Upper Changi North',
            'code' => 'sg-17-6',
            'region' => '17',
        ),),
      18 => array(
        310 =>
        array (
            'default_name' => 'Elias',
            'code' => '18',
            'region' => '18',
        ),
        311 =>
        array (
            'default_name' => 'Pasir Ris',
            'code' => 'sg-18-2',
            'region' => '18',
        ),
        312 =>
        array (
            'default_name' => 'Simei',
            'code' => 'sg-18-3',
            'region' => '18',
        ),
        313 =>
        array (
            'default_name' => 'Tampines',
            'code' => 'sg-18-4',
            'region' => '18',
        ),),
     19 => array(
        314 =>
        array (
            'default_name' => 'Airport Road',
            'code' => '19',
            'region' => '19',
        ),
        315 =>
        array (
            'default_name' => 'Anchorvale',
            'code' => 'sg-19-2',
            'region' => '19',
        ),
        316 =>
        array (
            'default_name' => 'Compassvale',
            'code' => 'sg-19-3',
            'region' => '19',
        ),
        317 =>
        array (
            'default_name' => 'Defu Lane',
            'code' => 'sg-19-4',
            'region' => '19',
        ),
        318 =>
        array (
            'default_name' => 'Edgedale Plains',
            'code' => 'sg-19-5',
            'region' => '19',
        ),
        319 =>
        array (
            'default_name' => 'Hougang',
            'code' => 'sg-19-6',
            'region' => '19',
        ),
        320 =>
        array (
            'default_name' => 'Kovan',
            'code' => 'sg-19-7',
            'region' => '19',
        ),
        321 =>
        array (
            'default_name' => 'Lorong Ah Soo',
            'code' => 'sg-19-8',
            'region' => '19',
        ),
        322 =>
        array (
            'default_name' => 'Lorong Chuan',
            'code' => 'sg-19-9',
            'region' => '19',
        ),
        323 =>
        array (
            'default_name' => 'Parry Avenue',
            'code' => 'sg-19-10',
            'region' => '19',
        ),
        324 =>
        array (
            'default_name' => 'Ponggol',
            'code' => 'sg-19-11',
            'region' => '19',
        ),
        325 =>
        array (
            'default_name' => 'Ponggol 17 Avenue',
            'code' => 'sg-19-12',
            'region' => '19',
        ),
        326 =>
        array (
            'default_name' => 'Ponggol 24 Avenue',
            'code' => 'sg-19-13',
            'region' => '19',
        ),
        327 =>
        array (
            'default_name' => 'Rivervale',
            'code' => 'sg-19-14',
            'region' => '19',
        ),
        328 =>
        array (
            'default_name' => 'Sengkang',
            'code' => 'sg-19-15',
            'region' => '19',
        ),
        329 =>
        array (
            'default_name' => 'Serangoon Avenue',
            'code' => 'sg-19-16',
            'region' => '19',
        ),
        330 =>
        array (
            'default_name' => 'Serangoon Central / Garden',
            'code' => 'sg-19-17',
            'region' => '19',
        ),
        331 =>
        array (
            'default_name' => 'Tai Seng',
            'code' => 'sg-19-18',
            'region' => '19',
        ),
        332 =>
        array (
            'default_name' => 'Tampines Road',
            'code' => 'sg-19-19',
            'region' => '19',
        ),
        333 =>
        array (
            'default_name' => 'Upper Serangoon',
            'code' => 'sg-19-20',
            'region' => '19',
        ),
        334 =>
        array (
            'default_name' => 'Yio Chu Kang',
            'code' => 'sg-19-21',
            'region' => '19',
        ),),
    20 => array(
        335 =>
        array (
            'default_name' => 'Ang Mo Kio',
            'code' => '20',
            'region' => '20',
        ),
        336 =>
        array (
            'default_name' => 'Bishan',
            'code' => 'sg-20-2',
            'region' => '20',
        ),
        337 =>
        array (
            'default_name' => 'Braddell Road',
            'code' => 'sg-20-3',
            'region' => '20',
        ),
        338 =>
        array (
            'default_name' => 'Bright Hill',
            'code' => 'sg-20-4',
            'region' => '20',
        ),
        339 =>
        array (
            'default_name' => 'Casuarina',
            'code' => 'sg-20-5',
            'region' => '20',
        ),
        340 =>
        array (
            'default_name' => 'Jalan Leban',
            'code' => 'sg-20-6',
            'region' => '20',
        ),
        341 =>
        array (
            'default_name' => 'Jalan Pemimpin',
            'code' => 'sg-20-7',
            'region' => '20',
        ),
        342 =>
        array (
            'default_name' => 'Sin Ming',
            'code' => 'sg-20-8',
            'region' => '20',
        ),
        343 =>
        array (
            'default_name' => 'Upper Thomson',
            'code' => 'sg-20-9',
            'region' => '20',
        ),),
     21 => array(
        344 =>
        array (
            'default_name' => 'Clementi Park',
            'code' => '21',
            'region' => '21',
        ),
        345 =>
        array (
            'default_name' => 'Dunearn',
            'code' => 'sg-21-2',
            'region' => '21',
        ),
        346 =>
        array (
            'default_name' => 'Jalan Jurong Kechil',
            'code' => 'sg-21-3',
            'region' => '21',
        ),
        347 =>
        array (
            'default_name' => 'Jalan Anak Bukit',
            'code' => 'sg-21-4',
            'region' => '21',
        ),
        348 =>
        array (
            'default_name' => 'King Albert Park',
            'code' => 'sg-21-5',
            'region' => '21',
        ),
        349 =>
        array (
            'default_name' => 'Pandan Valley',
            'code' => 'sg-21-6',
            'region' => '21',
        ),
        350 =>
        array (
            'default_name' => 'Toh Tuck',
            'code' => 'sg-21-7',
            'region' => '21',
        ),
        351 =>
        array (
            'default_name' => 'Toh Yi Drive',
            'code' => 'sg-21-8',
            'region' => '21',
        ),
        352 =>
        array (
            'default_name' => 'Ulu Pandan',
            'code' => 'sg-21-9',
            'region' => '21',
        ),
        353 =>
        array (
            'default_name' => 'Upper Bukit Timah',
            'code' => 'sg-21-10',
            'region' => '21',
        ),),
     22 => array(
        354 =>
        array (
            'default_name' => 'Benoi Road',
            'code' => '22',
            'region' => '22',
        ),
        355 =>
        array (
            'default_name' => 'Boon Lay',
            'code' => 'sg-22-2',
            'region' => '22',
        ),
        356 =>
        array (
            'default_name' => 'Corporation Road',
            'code' => 'sg-22-3',
            'region' => '22',
        ),
        357 =>
        array (
            'default_name' => 'Corporation Walk',
            'code' => 'sg-22-4',
            'region' => '22',
        ),
        358 =>
        array (
            'default_name' => 'International Business Park',
            'code' => 'sg-22-5',
            'region' => '22',
        ),
        359 =>
        array (
            'default_name' => 'Jalan Ahmad Ibrahim',
            'code' => 'sg-22-6',
            'region' => '22',
        ),
        360 =>
        array (
            'default_name' => 'Jalan Boon Lay',
            'code' => 'sg-22-7',
            'region' => '22',
        ),
        361 =>
        array (
            'default_name' => 'Jalan Buroh',
            'code' => 'sg-22-8',
            'region' => '22',
        ),
        362 =>
        array (
            'default_name' => 'Joo Koon Circle',
            'code' => 'sg-22-9',
            'region' => '22',
        ),
        363 =>
        array (
            'default_name' => 'Jurong East',
            'code' => 'sg-22-10',
            'region' => '22',
        ),
        364 =>
        array (
            'default_name' => 'Jurong West',
            'code' => 'sg-22-11',
            'region' => '22',
        ),
        365 =>
        array (
            'default_name' => 'Lok Yang Way',
            'code' => 'sg-22-12',
            'region' => '22',
        ),
        366 =>
        array (
            'default_name' => 'Penjuru Road',
            'code' => 'sg-22-13',
            'region' => '22',
        ),
        367 =>
        array (
            'default_name' => 'Pioneer',
            'code' => 'sg-22-14',
            'region' => '22',
        ),
        368 =>
        array (
            'default_name' => 'Teban Gardens',
            'code' => 'sg-22-15',
            'region' => '22',
        ),
        369 =>
        array (
            'default_name' => 'Toh Guan',
            'code' => 'sg-22-16',
            'region' => '22',
        ),
        370 =>
        array (
            'default_name' => 'Tuas',
            'code' => 'sg-22-17',
            'region' => '22',
        ),
        371 =>
        array (
            'default_name' => 'Yuan Ching Road',
            'code' => 'sg-22-18',
            'region' => '22',
        ),
        372 =>
        array (
            'default_name' => 'Yung Loh Road',
            'code' => 'sg-22-19',
            'region' => '22',
        ),),
    23 => array(
        373 =>
        array (
            'default_name' => 'Bukit Batok',
            'code' => 'sg-23-1',
            'region' => '23',
        ),
        374 =>
        array (
            'default_name' => 'Bukit Panjang',
            'code' => '23',
            'region' => '23',
        ),
        375 =>
        array (
            'default_name' => 'Cashew Road',
            'code' => 'sg-23-3',
            'region' => '23',
        ),
        376 =>
        array (
            'default_name' => 'Chestnut Drive',
            'code' => 'sg-23-4',
            'region' => '23',
        ),
        377 =>
        array (
            'default_name' => 'Choa Chu Kang',
            'code' => 'sg-23-5',
            'region' => '23',
        ),
        378 =>
        array (
            'default_name' => 'Dairy Farm',
            'code' => 'sg-23-6',
            'region' => '23',
        ),
        379 =>
        array (
            'default_name' => 'Fajar Road',
            'code' => 'sg-23-7',
            'region' => '23',
        ),
        380 =>
        array (
            'default_name' => 'Hillview',
            'code' => 'sg-23-8',
            'region' => '23',
        ),
        381 =>
        array (
            'default_name' => 'Jelapang Road',
            'code' => 'sg-23-9',
            'region' => '23',
        ),
        382 =>
        array (
            'default_name' => 'Senja Road',
            'code' => 'sg-23-10',
            'region' => '23',
        ),
        383 =>
        array (
            'default_name' => 'Teck Whye',
            'code' => 'sg-23-11',
            'region' => '23',
        ),
        384 =>
        array (
            'default_name' => 'Upper Bukit Timah',
            'code' => 'sg-23-12',
            'region' => '23',
        ),
        385 =>
        array (
            'default_name' => 'Woodlands',
            'code' => 'sg-23-13',
            'region' => '23',
        ),),
    24 => array(
        386 =>
        array (
            'default_name' => 'Choa Chu Kang',
            'code' => '24',
            'region' => '24',
        ),
        387 =>
        array (
            'default_name' => 'Lim Chu Kang',
            'code' => 'sg-24-2',
            'region' => '24',
        ),
        388 =>
        array (
            'default_name' => 'Neo Tiew Road',
            'code' => 'sg-24-3',
            'region' => '24',
        ),
        389 =>
        array (
            'default_name' => 'Tengah',
            'code' => 'sg-24-4',
            'region' => '24',
        ),),
    25 => array(
        390 =>
        array (
            'default_name' => 'Admiralty Road',
            'code' => 'sg-25-1',
            'region' => '25',
        ),
        391 =>
        array (
            'default_name' => 'Kranji',
            'code' => 'sg-25-2',
            'region' => '25',
        ),
        392 =>
        array (
            'default_name' => 'Mandai Estate',
            'code' => 'sg-25-3',
            'region' => '25',
        ),
        393 =>
        array (
            'default_name' => 'Marsiling Drive',
            'code' => 'sg-25-4',
            'region' => '25',
        ),
        394 =>
        array (
            'default_name' => 'Sungei Kadut',
            'code' => 'sg-25-5',
            'region' => '25',
        ),
        395 =>
        array (
            'default_name' => 'Turf Club Avenue',
            'code' => 'sg-25-6',
            'region' => '25',
        ),
        396 =>
        array (
            'default_name' => 'Woodgrove',
            'code' => 'sg-25-7',
            'region' => '25',
        ),
        397 =>
        array (
            'default_name' => 'Woodlands',
            'code' => 'sg-25-8',
            'region' => '25',
        ),),
    26 => array(
        398 =>
        array (
            'default_name' => 'Lentor Avenue / Green',
            'code' => '26',
            'region' => '26',
        ),
        399 =>
        array (
            'default_name' => 'Mandai Road',
            'code' => 'sg-26-2',
            'region' => '26',
        ),
        400 =>
        array (
            'default_name' => 'Sembawang Road',
            'code' => 'sg-26-3',
            'region' => '26',
        ),
        401 =>
        array (
            'default_name' => 'Springleaf',
            'code' => 'sg-26-4',
            'region' => '26',
        ),
        402 =>
        array (
            'default_name' => 'Tagore',
            'code' => 'sg-26-5',
            'region' => '26',
        ),
        403 =>
        array (
            'default_name' => 'Upper Thomson',
            'code' => 'sg-26-6',
            'region' => '26',
        ),),
    27 => array(
        404 =>
        array (
            'default_name' => 'Admiralty Drive',
            'code' => '27',
            'region' => '27',
        ),
        405 =>
        array (
            'default_name' => 'Canberra Link',
            'code' => 'sg-27-2',
            'region' => '27',
        ),
        406 =>
        array (
            'default_name' => 'Sembawang',
            'code' => 'sg-27-3',
            'region' => '27',
        ),
        407 =>
        array (
            'default_name' => 'Senoko',
            'code' => 'sg-27-4',
            'region' => '27',
        ),
        408 =>
        array (
            'default_name' => 'Woodlands',
            'code' => 'sg-27-5',
            'region' => '27',
        ),
        409 =>
        array (
            'default_name' => 'Yishun',
            'code' => 'sg-27-6',
            'region' => '27',
        ),),
     28 => array(
        410 =>
        array (
            'default_name' => 'Jalan Kayu',
            'code' => '28',
            'region' => '28',
        ),
        411 =>
        array (
            'default_name' => 'Piccadilly',
            'code' => 'sg-28-2',
            'region' => '28',
        ),
        412 =>
        array (
            'default_name' => 'Seletar',
            'code' => 'sg-28-3',
            'region' => '28',
        ),
        413 =>
        array (
            'default_name' => 'Yio Chu Kang',
            'code' => 'sg-28-4',
            'region' => '28',
        ),
    ),
);


$regionCodesToId = array(
    'HKI' => array('region_id'=>'','country_id'=>'HK'),
    'KLN' => array('region_id'=>'','country_id'=>'HK'),
    'NT' => array('region_id'=>'','country_id'=>'HK'),
    '1'=> array('region_id'=>'','country_id'=>'SG'),
    '2'=>array('region_id'=>'','country_id'=>'SG'),
    '3'=>array('region_id'=>'','country_id'=>'SG'),
    '4'=>array('region_id'=>'','country_id'=>'SG'),
    '5'=>array('region_id'=>'','country_id'=>'SG'),
    '6'=>array('region_id'=>'','country_id'=>'SG'),
    '7'=>array('region_id'=>'','country_id'=>'SG'),
    '8'=>array('region_id'=>'','country_id'=>'SG'),
    '9'=>array('region_id'=>'','country_id'=>'SG'),
    '10'=>array('region_id'=>'','country_id'=>'SG'),
    '11'=>array('region_id'=>'','country_id'=>'SG'),
    '12'=>array('region_id'=>'','country_id'=>'SG'),
    '13'=>array('region_id'=>'','country_id'=>'SG'),
    '14'=>array('region_id'=>'','country_id'=>'SG'),
    '15'=>array('region_id'=>'','country_id'=>'SG'),
    '16'=>array('region_id'=>'','country_id'=>'SG'),
    '17'=>array('region_id'=>'','country_id'=>'SG'),
    '18'=>array('region_id'=>'','country_id'=>'SG'),
    '19'=>array('region_id'=>'','country_id'=>'SG'),
    '20'=>array('region_id'=>'','country_id'=>'SG'),
    '21'=>array('region_id'=>'','country_id'=>'SG'),
    '22'=>array('region_id'=>'','country_id'=>'SG'),
    '23'=>array('region_id'=>'','country_id'=>'SG'),
    '24'=>array('region_id'=>'','country_id'=>'SG'),
    '25'=>array('region_id'=>'','country_id'=>'SG'),
    '26'=>array('region_id'=>'','country_id'=>'SG'),
    '27'=>array('region_id'=>'','country_id'=>'SG'),
    '28'=>array('region_id'=>'','country_id'=>'SG'),
);
foreach($regionCodesToId as $region_default_name => $val){
   $region_id = $installer->getConnection()->query("select `region_id` from {$countryRegionTable} where `country_id`='".$val['country_id']."' AND `code`='{$region_default_name}'")->fetchColumn(0);
   $regionCodesToId[$region_default_name]['region_id'] = $region_id;
}

$installer->getConnection()->beginTransaction();
foreach ($chineseRegions as $key => $data) {
    $locale = 'zh_HK';
    foreach ( $data as $_data) {
            // handle english
            $sql = "INSERT INTO `{$regionCityTable}` (`region_id`, `country_id`, `code`, `default_name`) VALUES ('{$regionCodesToId[$key]['region_id']}', '{$regionCodesToId[$key]['country_id']}', '{$_data['code']}', '{$_data['default_name']}');";
            $installer->run($sql);

            // handle chinese
            if (isset($_data['locale_name'])) {
                $cityId = $installer->getConnection()->lastInsertId();
                $sql = "INSERT INTO `{$cityNameTable}` (`locale`, `city_id`, `name`) VALUES ('{$locale}','{$cityId}','{$_data['locale_name']}');";
                $installer->run($sql);
            }
    }
}
$installer->getConnection()->commit();
