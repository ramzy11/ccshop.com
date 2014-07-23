<?php

$ce_zh_store = array(
				'China'=>array(
					'customer_services'=>array('顧客服務熱線','+86 400 820 0006'),
					'store'=>array(
						array(
							'type'=>'直營店鋪',
							'shops'=>array(
								array('成都王府井','+86 28 8651 7846'),
								array('杭州銀泰百貨 (武林店)','+86 571 8583 6340'),
								array('昆明金格中心匯都店','+86 871 6319 9248'),
								array('上海久光百貨','+86 21 6288 2497'),
								array('上海徐匯太平洋','+86 21 5416 5906'),
								array('上海淮海百盛','+86 21 6437 0250'),
								array('蘇州泰華商城東樓','+86 512 6787 0177'),
								array('天津伊勢丹塘沽店','+86 18812514065'),
								array('北京新光天地','+86 10 6533 1471'),
								array('常州購物中心','+86 519 8999 6330'),
								array('杭州銀泰百貨','+86 0571 87616075'),
								array('濟南銀座','+86 0531 86016581'),
								array('沈陽久光百貨','+86 024 31896167'),
								array('溫州時代廣場','+86 13857789389'),
							),
						),
						array(
							'type'=>'EQ:IQ 配飾店內',
							'shops'=>array(
								array('昆明金鷹','+86 871 530 0144'),
								array('南京金鷹','+86 25 8470 0656'),
								array('寧波銀泰百貨 (天一店)','+86 574 8709 3493'),
								array('青島海信','+86 15376700306'),
								array('青島陽光百貨','+86 532 8667 7030'),
								array('上海巴黎春天百貨','+86 21 6474 1590'),
								array('沈陽新世界百貨中華店','+86 024 31836050'),
								array('深圳芮歐時尚生活百貨','+86 755 2589 7235'),
								array('石家莊北國商城','+86 311 8966 4739'),
								array('蘇州美羅','+86 512 6916 2102'),
								array('蘇州久光百貨','+86 512 6696 1186'),
								array('天津海信','+86 22 2319 8555'),
								array('天津伊勢丹','+86 22 2718 8239'),
								array('武漢國際','+86 27 8571 4603'),
								array('濟南貴和購物中心','+86 0531 80982331'),
							)
						),
						array(
							'type'=>'Central Central店內',
							'shops'=>array(
								array('上海尚嘉中心Central Central','+86 021 60746415'),
								array('廣州友誼正佳','+86 20 8550 5004'),
								array('北京國貿','+86 010 65059669'),
								array('成都樂天百貨','+86 028 65188673'),
								array('上海嘉裡中心','+86 021 62147858'),
							),
						),
					),
				),
				'Hong_Kong'=>array(
					'customer_services'=>array('顧客服務熱線','+852 2480 2888'),
					'store'=>array(
						array(
							'type'=>'EQ:IQ 服裝店鋪',
							'shops'=>array(
								array('尖沙咀圓方','+852 2196 8238'),
							)
						),
						array(
							'type'=>'Central Central內',
							'shops'=>array(
								array('中環畢打街3號中建大廈','+852 2140 6518'),
							)
						),
					),
				),
				'Macau'=>array(
					'customer_services'=>array('顧客服務熱線','+853 2838 9846'),
					'store'=>array(
						array(
							'type'=>'直營店鋪',
							'shops'=>array(
								array('威尼斯人度假村酒店大運河購物中心','+853 2882 9360'),
							),
						),
					),
				),
				'Thailand'=>array(
					'customer_services'=>array('顧客服務熱線','+662 658 1428'),
					'store'=>array(
						array(
							'type'=>'CC Shop店內',
							'shops'=>array(
								array('Central Embassy','即將開幕'),
							)
						),
					),
				),
			);
	
$ce_zh_country = array('China'=>'中國','Hong_Kong'=>'香港','Macau'=>'澳門','Thailand'=>'泰國');
	
echo '<!--'."\n\n";
			
echo 'var store = '.json_encode($ce_zh_store).';'."\n\n";

echo 'var country = '.json_encode($ce_zh_country).';'."\n\n";

echo '-->';

/*
array('上海尚嘉中心Central Central','+86 021 60746415'),
array('广州友谊正佳','+86 20 8550 5004'),
array('北京国贸','+86 010 65059669'),
array('成都乐天百货','+86 028 65188673'),
array('上海嘉里中心','+86 021 62147858'),

*/