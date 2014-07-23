<?php

$jp_zh_store = array(
				'China'=>array(
					'customer_services'=>array('顧客服務熱線','+86 400 820 0006'),
					'store'=>array(
						array(
							'type'=>'直營店鋪',
							'shops'=>array(
								array('上海靜安嘉裡中心','+86 21 6214 7858'),
								array('浦東嘉裡城','+86 21 58388615'),
								array('上海久光百貨','+86 21 62888927'),
								array('廣州友誼百貨','+86 20 83573945'),
								array('沈陽久光百貨','+86 24 22556083'),
								array('宁波银泰百货天一店','+86 574 87093803'),
							)
						),
					),
				),
				'Hong_Kong'=>array(
					'customer_services'=>array('顧客服務熱線','+852 2480 2888'),
					'store'=>array(
						array(
							'type'=>'直營店鋪',
							'shops'=>array(
								array('中建大廈 Central Central','+852 2140 6718'),
							),
						),
					),
				),
				'Macau'=>array(
					'customer_services'=>array('顧客服務熱線','+853 2838 9846'),
					'store'=>array(
						array(
							'type'=>'直營店鋪',
							'shops'=>array(
								array('威尼斯人購物中心','+853 2882 9252'),
								array('金沙廣場Central Central （即將開幕）','-'),
							),
						),
					),
				),
			);
			
$jp_zh_country = array('China'=>'中國','Hong_Kong'=>'香港','Macau'=>'澳門');			
			
echo '<!--'."\n\n";
			
echo 'var store = '.json_encode($jp_zh_store).';'."\n\n";

echo 'var country = '.json_encode($jp_zh_country).';'."\n\n";

echo '-->';