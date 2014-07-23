<?php
$bj_zh_store = array(
                'China'=>array(
                    'customer_services'=>array('顧客服務熱線','+86 400 820 0006'),
                    'store'=>array(
                        array(
                            'type'=>'直營店鋪',
                            'shops'=>array(
                                array('成都國際金融中心','+86 28 8662 2409'),
                                array('北京三裡屯','+86 10 6416 7552'),
                                array('廣州太古匯','+86 20 3868 2953'),
                                array('上海國際金融中心商場','+86 28 8662 2409'),
                                array('重慶北城天街','-'),
                            )
                        ),
                        array(
                            'type'=>'Central Central店內',
                            'shops'=>array(
                                array('北京國貿商城','+86 010 65059669'),
                                array('成都樂天百貨','+86 028 65188673'),
                                array('上海尚嘉中心','+86 021 60746415'),
                            )
                        )
                    ),
                ),
                'Hong_Kong'=>array(
                    'customer_services'=>array('顧客服務熱線','+852 2480 2888'),
                    'store'=>array(
                        array(
                            'type'=>'Central Central店內',
                            'shops'=>array(
                                array('中環畢打街3號中建大廈Central Central','+852 2140 6318'),
                            )
                        ),
                        array(
                            'type'=>'Steve Madden 店內',
                            'shops'=>array(
                                array('旺角朗豪坊商場','+852 3514 9491'),
                                array('觀塘apm','+852 3148 1378'),
                                array('沙田新城市廣場','+852 2603 2799'),
                                array('銅鑼灣時代廣場','+852 2506 3076'),
                            )
                        )
                    ),
                ),
                'Macau'=>array(
                    'customer_services'=>array('顧客服務熱線','+853 2838 9846'),    
                    'store'=>array(
                        array(       
                            'type'=>'直營店鋪',
                            'shops'=>array(
                                array('威尼斯人度假村酒店大運河購物中心','+853 2886 6879'),
                            )
                        ),
                        array(
                            'type'=>'CC Shop店內',
                            'shops'=>array(
                                array('金光大道金沙城中心金沙廣場','即將開幕'),
                            )
                        )
                    ),
                ),
                'Singapore'=>array(
                    'customer_services'=>array('顧客服務熱線','+65 6293 5322'),
                    'store'=>array(
                        array(
                            'type'=>'直營店鋪',
                            'shops'=>array(
                                array('Marina Bay Sands','+65 6688 7363'),
                            )
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
                        )
                    ),
                ),
            );

$bj_zh_country = array('China'=>'中國','Hong_Kong'=>'香港','Macau'=>'澳門','Singapore'=>'新加坡','Thailand'=>'泰國');

echo 'var store = ';
echo json_encode($bj_zh_store);
echo ';';
echo "\n\n";
echo 'var country = ';
echo json_encode($bj_zh_country);
echo ';';

/*

中国	顾客服务热线	+86 400 820 0006

直营店铺	成都国际金融中心	+86 28 8662 2409
北京三里屯	+86 10 6416 7552
广州太古汇	+86 20 3868 2953
array('上海国际金融中心商场','+86 28 8662 2409'),
array('重庆北城天街','-'),

Central Central店內	
array('北京国贸商城','+86 010 65059669'),
array('成都乐天百货','+86 028 65188673'),
array('上海尚嘉中心','+86 021 60746415'),

香港	顾客服务热线	+852 2480 2888

CENTRALCENTRAL店內	
array('中环毕打街3号中建大厦Central Central','+852 2140 6318'),

Steve Madden 店內	
array('旺角朗豪坊商场','+852 3514 9491'),
array('观塘apm','+852 3148 1378'),
array('沙田新城市广场','+852 2603 2799'),
array('铜锣湾时代广场','+852 2506 3076'),

澳门	
array('威尼斯人度假村酒店大运河购物中心','+853 2886 6879'),
array('顾客服务热线','+853 2838 9846'),
array(''),
array('金光大道金沙城中心金沙广场','即将开幕'),
array(''),
array('Marina Bay Sands','+65 6688 7363'),
array('顾客服务热线','+65 6293 5322'),
array(''),
array('顾客服务热线','+662 658 1428'),
array(''),
array('Central Embassy','即将开幕'),
array(''),

 */