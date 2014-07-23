<?php return array(
    'about-ninewest' => array(
        'title' => 'Nine West - 关于Nine West',
        'identifier' => 'about-ninewest',
        'content' => '<p>关于 Nine West</p>',
        'is_active' => '0',
    ),
    'about-stevemadden' => array(
        'title' => 'Steve Madden - 关于Steve Madden',
        'identifier' => 'about-stevemadden',
        'content' => '<p>关于 Steve Madden</p>',
        'is_active' => '0',
    ),
    'accessories-best-sellers' => array(
        'title' => '配饰 - 畅销款: 主横额（不可删除）',
        'identifier' => 'accessories-best-sellers',
        'content' => '<div class="page-title category-title">
<h2>配饰 畅销款</h2>
</div>',
        'is_active' => '1',
    ),
    'accessories-editor' => array(
        'title' => '配饰 - 推荐款: 主横额（不可删除）',
        'identifier' => 'accessories-editor',
        'content' => '<div class="page-title category-title">
<h2>配饰推荐款</h2>
</div>',
        'is_active' => '1',
    ),
    'accessories-new-arrivals' => array(
        'title' => '配饰 - 新品: 主横额 （不可删除）',
        'identifier' => 'accessories-new-arrivals',
        'content' => '<div class="page-title category-title">
<h2>配饰 新品</h2>
</div>',
        'is_active' => '1',
    ),
    'accessories_nav' => array(
        'title' => '配饰 - 菜单（不可删除）',
        'identifier' => 'accessories_nav',
        'content' => '<div class="left-static-nav">
<ul>
<li class="new-arrivals"><a href="{{store_url=\'accessories/new-arrivals.html\'}}">新品</a></li>
<li class="best_seller"><a href="{{store_url=\'accessories/best-sellers.html\'}}">畅销款</a></li>
<li class="editors_pick"><a href="{{store_url=\'accessories/editor.html\'}}"><span>推荐</span>款</a></li>
</ul>
</div>',
        'is_active' => '1',
    ),
    'as-seen-in-celebrity' => array(
        'title' => '媒体  - 名流风采',
        'identifier' => 'as-seen-in-celebrity',
        'content' => '<p>名流风采</p>',
        'is_active' => '0',
    ),
    'as-seen-in-editorials' => array(
        'title' => '媒体  - 编辑评价',
        'identifier' => 'as-seen-in-editorials',
        'content' => '<p>编辑评价</p>',
        'is_active' => '0',
    ),
    'best_seller' => array(
        'title' => '畅销款',
        'identifier' => 'best_seller',
        'content' => '
<div>{{block type="gri_catalogcustom/category_header" name="category_header"}}</div>
<div>{{block type="catalog/product_list" name="product_list" as="product_list" template="catalog/product/list.phtml" }}</div>',
        'is_active' => '1',
    ),
    'carolinnaespinosa' => array(
        'title' => 'Carolinna Espinosa',
        'identifier' => 'carolinnaespinosa',
        'content' => '<div class="slider bot_space carolinnaespinosa-slide-img clearer">{{block type="interaktingslider/interaktingslider" name="interaktingslider" group="carolinnaespinosa"}}</div>',
        'is_active' => '1',
    ),
    'carolinnaespinosa-shop' => array(
        'title' => 'Carolinna Espinosa Shop',
        'identifier' => 'carolinnaespinosa-shop',
        'content' => '<div class="banner">
<p class="spacer">{{block type="gri_catalogcustom/category_group" name="carolinnaespinosa-shop"}}</p>
</div>',
        'is_active' => '1',
    ),
    'carolinnaespinosa-shop-accessories' => array(
        'title' => 'Carolinna Espinosa Shop Accessories',
        'identifier' => 'carolinnaespinosa-shop-accessories',
        'content' => '<div class="banner">
<p class="spacer">{{block type="gri_catalogcustom/category_group" name="carolinnaespinosa-shop-accessories"}}</p>
</div>',
        'is_active' => '1',
    ),
    'carolinnaespinosa-shop-clothing' => array(
        'title' => 'Carolinna Espinosa Shop Clothing',
        'identifier' => 'carolinnaespinosa-shop-clothing',
        'content' => '<div class="banner">
<p class="spacer">{{block type="gri_catalogcustom/category_group" name="carolinnaespinosa-shop-clothing"}}</p>
</div>',
        'is_active' => '1',
    ),
    'carolinnaespinosa-shop-shoes' => array(
        'title' => 'Carolinna Espinosa Shop Shoes',
        'identifier' => 'carolinnaespinosa-shop-shoes',
        'content' => '<div class="banner">
<p class="spacer">{{block type="gri_catalogcustom/category_group" name="carolinnaespinosa-shop-shoes"}}</p>
</div>',
        'is_active' => '1',
    ),
    'carolinnaespinosa_banner' => array(
        'title' => 'Carolinna Espinosa Banner',
        'identifier' => 'carolinnaespinosa_banner',
        'content' => '<div class="brand-banner">
<div class="brand-image"><img title="Carolinna Espinosa" src="{{media url="wysiwyg/Jan_28_Soft_Launch/NineWest/B2.jpg"}}" alt="Carolinna Espinosa" /></div>
<div id="sub_menu">
<div class="container">
<ul>
<li class="first"><a class="shop" href="{{store_url=\'carolinnaespinosa/shop.html\'}}">购物</a>
<div class="child_menu">
<ul>
<li><a href="{{store_url=\'carolinnaespinosa/shop/new-arrivals.html\'}}">新品</a></li>
<li><a href="{{store_url=\'carolinnaespinosa/shop/editor.html\'}}">推荐款</a></li>
<li><a href="{{store_url=\'carolinnaespinosa/shop/best-sellers.html\'}}">畅销款</a></li>
</ul>
<ul>
<li><a href="{{store_url=\'carolinnaespinosa/shop/shoes.html\'}}">鞋履</a></li>
<li><a href="{{store_url=\'carolinnaespinosa/shop/clothing.html\'}}">服装</a></li>
<li><a href="{{store_url=\'carolinnaespinosa/shop/accessories.html\'}}">配饰</a></li>
</ul>
</div>
</li>
<li><a href="{{store_url=\'carolinnaespinosa/about\'}}">关于 Carolinna Espinosa</a></li>
<li><a href="{{store_url=\'carolinnaespinosa/store\'}}">店铺地址</a></li>
<li>&nbsp;</li>
</ul>
<div class="clear">&nbsp;</div>
</div>
</div>
<div>&nbsp;</div>
</div>',
        'is_active' => '1',
    ),
    'carolinnaespinosa_nav' => array(
        'title' => 'Carolinna Espinosa Nav',
        'identifier' => 'carolinnaespinosa_nav',
        'content' => '<div class="left-static-nav">
<ul>
<li class="new-arrivals"><a href="{{store_url=\'carolinnaespinosa/shop/new-arrivals.html\'}}">新品</a></li>
<li class="editors_pick"><a href="{{store_url=\'carolinnaespinosa/shop/editor.html\'}}">推荐款</a></li>
<li class="editors_pick"><a href="{{store_url=\'carolinnaespinosa/shop/best-sellers.html\'}}">畅销款</a></li>
</ul>
</div>',
        'is_active' => '1',
    ),
    'cart-banner' => array(
        'title' => '购物车 - 主横额（不可删除） - P1',
        'identifier' => 'cart-banner',
        'content' => '<p><img title="满600元包邮" src="{{media url="wysiwyg/Jan_28_Soft_Launch/ShoppingCart/P1.jpg"}}" alt="满600元包邮" /></p>',
        'is_active' => '1',
    ),
    'checkout-package' => array(
        'title' => '购物车 - 包装（不可删除） - Delivery Box',
        'identifier' => 'checkout-package',
        'content' => '<div class="con_title"><span class="rig_space">精美的包装盒</span></div>
<div class="pay_met ship_table">
<table style="width: 100%;" border="0" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td width="200"><img title="精美的包装盒" src="{{media url="wysiwyg/Jan_28_Soft_Launch/ShoppingCart/DeliveryBox.jpg"}}" alt="精美的包装盒" /></td>
<td class="ship_bag"><strong>精美的包装盒</strong>
<p>熙熙网为您准备了精美的高品质包装盒以确保您的货品能够安全送达，同时出于环保的目的，我们也建议您循环使用此包装盒</p>
</td>
</tr>
</tbody>
</table>
<div class="clear">&nbsp;</div>
</div>',
        'is_active' => '1',
    ),
    'clothing-best-sellers' => array(
        'title' => '服装 - 畅销款: 主横额 (不可删除)',
        'identifier' => 'clothing-best-sellers',
        'content' => '<div class="page-title category-title">
<h2>服装 畅销款</h2>
</div>',
        'is_active' => '1',
    ),
    'clothing-editor' => array(
        'title' => '服装 - 推荐款: 主横额 (不可删除)',
        'identifier' => 'clothing-editor',
        'content' => '<div class="page-title category-title">
<h2>服装推荐款</h2>
</div>',
        'is_active' => '1',
    ),
    'clothing-new-arrivals' => array(
        'title' => '服装 - 新品: 主横额 (不可删除)',
        'identifier' => 'clothing-new-arrivals',
        'content' => '<div class="page-title category-title">
<h2>服装 新品</h2>
</div>',
        'is_active' => '1',
    ),
    'clothing_nav' => array(
        'title' => '服饰 - 菜单（不可删除）',
        'identifier' => 'clothing_nav',
        'content' => '<div class="left-static-nav">
<ul>
<li class="new-arrivals"><a href="{{store_url=\'clothing/new-arrivals.html\'}}">新品</a></li>
<li class="best_seller"><a href="{{store_url=\'clothing/best-sellers.html\'}}">畅销款</a></li>
<li class="editors_pick"><a href="{{store_url=\'clothing/editor.html\'}}"><span>推荐</span>款</a></li>
</ul>
</div>',
        'is_active' => '1',
    ),
    'editors_pick' => array(
        'title' => '促销款',
        'identifier' => 'editors_pick',
        'content' => '
<div>{{block type="gri_catalogcustom/category_header" name="category_header"}}</div>
<div>{{block type="catalog/product_list" name="product_list" as="product_list" template="catalog/product/list.phtml" }}</div>',
        'is_active' => '1',
    ),
    'events' => array(
        'title' => '活动',
        'identifier' => 'events',
        'content' => '<p>活动</p>',
        'is_active' => '0',
    ),
    'events-archives' => array(
        'title' => '活动 - 活动 1',
        'identifier' => 'events-archives',
        'content' => '<p>档案馆</p>',
        'is_active' => '0',
    ),
    'footer_links' => array(
        'title' => '页脚 - 链接',
        'identifier' => 'footer_links',
        'content' => '<div class="footer_col"><span class="fotTitle footer-brand-title">品牌</span>
<ul class="footer-brand">
<li><a href="{{store_url=\'ninewest.html\'}}">NINE WEST</a></li>
<li><a href="{{store_url=\'stevemadden.html\'}}">STEVE MADDEN</a></li>
<li><a href="{{store_url=\'carolinnaespinosa.html\'}}">CAROLINNA ESPINOSA</a></li>
</ul>
<p class="footer-brand">&nbsp;</p>
<span class="fotTitle">会员</span>
<ul>
<li><a href="{{store_url=\'vip\'}}">VIP</a></li>
</ul>
</div>
<div class="footer_col"><span class="fotTitle">帮助中心</span>
<ul>
<li><a href="{{store_url=\'shopping-flow\'}}">购物流程</a></li>
<li><a href="{{store_url=\'payment\'}}">支付方式</a></li>
<li><a href="{{store_url=\'customer-service/shipping-delivery\'}}">配送说明</a></li>
<li><a href="{{store_url=\'customer-service/returns\'}}">退换货政策</a></li>
<li><a href="{{store_url=\'customer-service/faq\'}}">常见问题</a></li>
</ul>
</div>
<div class="footer_col"><span class="fotTitle">购物小贴士</span>
<ul>
<li><a href="/shopping-tips/size-chart/" target="_self">尺码表</a></li>
<li><a href="/shopping-tips/care-cleaning-tips/clothing1" target="_self">清洁保养</a></li>
<li><a href="/shopping-tips/glossary/shoes-glossary" target="_self">鞋类术语</a></li>
<li><a href="/shopping-tips/glossary/clothing-glossary" target="_self">服装术语</a></li>
<li><a href="/shopping-tips/glossary/bags-glossary" target="_self">包术语</a></li>
</ul>
</div>
<div class="footer_col"><span class="fotTitle">关于我们</span></div>
<div class="footer_col">
<ul>
<li><a href="{{store_url=\'about-us/central-central\'}}">关于熙熙网</a></li>
<li><a href="{{store_url=\'about-us/store-locator\'}}">店铺地址</a></li>
<li><a href="{{store_url=\'contact\'}}">联系我们</a></li>
</ul>
</div>',
        'is_active' => '1',
    ),
    'home_bottom_banner_1' => array(
        'title' => '主頁橫額 - Bottom Banner 1 (不可刪除) - H8',
        'identifier' => 'home_bottom_banner_1',
        'content' => '<p><img src="{{media url="wysiwyg/Home_Banner/Free_Shipping.JPG"}}" alt="Free Shipping" width="305" height="140" /></p>',
        'is_active' => '1',
    ),
    'home_bottom_banner_2' => array(
        'title' => '主頁橫額 - Bottom Banner 2 (不可刪除) - H9',
        'identifier' => 'home_bottom_banner_2',
        'content' => '<p><a title="VIP尊享礼遇" href="{{store_url="vip/"}}" target="_blank"><img title="VIP尊享礼遇" src="{{media url="wysiwyg/Home_Banner/vip1.JPG"}}" alt="VIP尊享礼遇" /></a></p>',
        'is_active' => '1',
    ),
    'home_bottom_banner_3' => array(
        'title' => '主页横额 - Bottom Banner 3 - H10',
        'identifier' => 'home_bottom_banner_3',
        'content' => '<p><a title="购物指南" href="{{store_url=\'shopping-flow\'}}" target="_blank"><img title="购物指南" src="{{media url="wysiwyg/Home_Banner/shopping.JPG"}}" alt="购物指南" /></a></p>',
        'is_active' => '1',
    ),
    'home_bottom_left' => array(
        'title' => '主页横额 -  Bottom Left - H4',
        'identifier' => 'home_bottom_left',
        'content' => '<p><img title="熙熙网在线购物" src="{{media url="wysiwyg/Jan_28_Soft_Launch/HomePage/H4.jpg"}}" alt="熙熙网在线购物" /></p>',
        'is_active' => '1',
    ),
    'home_center' => array(
        'title' => '主页横额 - Center - H5',
        'identifier' => 'home_center',
        'content' => '<p><object id="cc_46CD45EC7571E507" onClick="_gaq.push([\'_trackEvent\', \'Videos\', \'Play\', \'Central/Central周年庆\']); _gaq.push([\'_trackEvent\', \'Videos\', \'Stop\', \'Central/Central周年庆\']); _gaq.push([\'_trackEvent\', \'Videos\', \'Pause\', \'Central/Central周年庆\']); " width="275" height="175" data="http://union.bokecc.com/flash/single/DB99B1FEA9708B8E_46CD45EC7571E507_false_7A1880655F48CD0B_1/player.swf" type="application/x-shockwave-flash"><param name="allowFullScreen" value="true" /><param name="allowScriptAccess" value="always" /><param name="src" value="http://union.bokecc.com/flash/single/DB99B1FEA9708B8E_46CD45EC7571E507_false_7A1880655F48CD0B_1/player.swf" /><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="pluginspage" value="http://www.macromedia.com/go/getflashplayer" /><param name="wmode" value="transparent" /></object></p>
<div class="video_tit">Central/Central周年庆</div>
<p>精彩片段</p>
<p><a href="http://union.bokecc.com/flash/single/DB99B1FEA9708B8E_46CD45EC7571E507_false_7A1880655F48CD0B_1/player.swf" target="_blank"><span style="color: #ff0000;"><span style="color: #ff0000;">立即观看 </span></span></a></p>
<script type="text/javascript">// <![CDATA[
  var _gaq = _gaq || [];
  _gaq.push([\'_setAccount\', \'UA-17257669-7\']);
  _gaq.push([\'_trackPageview\']);

  (function() {
    var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;
    ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';
    var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);
  })();
// ]]></script>',
        'is_active' => '1',
    ),
    'home_middle_right' => array(
        'title' => '主页横额 - Middle Right - H6',
        'identifier' => 'home_middle_right',
        'content' => '<p><a title="编辑精选推荐" href="{{store_url="shoes/editor.html"}}" target="_blank"><img title="编辑精选推荐" src="{{media url="wysiwyg/Jan_28_Soft_Launch/HomePage/H6-3-15.jpg"}}" alt="编辑精选推荐" /></a></p>',
        'is_active' => '1',
    ),
    'home_notice' => array(
        'title' => '首页 - 滚动提示',
        'identifier' => 'home_notice',
        'content' => '<p class="marquee">尊敬的客户您好！春节期间（2013年2月13日 ~ 2013年2月15日）所有付款订单，CENTRAL/CENTRAL（熙熙网）将于2月16号开始安排发货，敬请谅解。</p>',
        'is_active' => '0',
    ),
    'home_shop_now' => array(
        'title' => '主页横额 - 立即选购 (不可删除) - H7',
        'identifier' => 'home_shop_now',
        'content' => '<div>
<p class="shop-now" align="center"><strong>立即选购！</strong></p>
{{block type="gri_catalogcustom/home_shopNow" name="home_shop_now" category_ids="6,17,35,5" labels="服装,单鞋,凉鞋,包包/配件"}}</div>',
        'is_active' => '1',
    ),
    'home_top_left' => array(
        'title' => '主頁橫額 -  Top Left - H2',
        'identifier' => 'home_top_left',
        'content' => '<p><a title="造型推荐" href="{{store_url="ninewest/shop/shop-by-look.html"}}" target="_blank" onClick="_gaq.push([\'_trackEvent\', \'Links\', \'Click\', \'Home Top Left\']);"><img title="造型推荐" src="{{media url="wysiwyg/Jan_28_Soft_Launch/HomePage/H2.jpg"}}" alt="造型推荐" /></a></p>
<script type="text/javascript">// <![CDATA[
  var _gaq = _gaq || [];
  _gaq.push([\'_setAccount\', \'UA-17257669-7\']);
  _gaq.push([\'_trackPageview\']);

  (function() {
    var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;
    ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';
    var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);
  })();
// ]]></script>',
        'is_active' => '1',
    ),
    'home_top_right' => array(
        'title' => '主頁橫額 - Top Right - H3',
        'identifier' => 'home_top_right',
        'content' => '<p><a title="潮流前沿" onclick="_gaq.push([\'_trackEvent\', \'Links\', \'Click\', \'Home Top Right\']);" href="{{store_url="stevemadden.html"}}" target="_blank"><img title="潮流前沿" src="{{media url="wysiwyg/Jan_28_Soft_Launch/HomePage/H3.jpg"}}" alt="潮流前沿" /></a></p>
<script type="text/javascript">// <![CDATA[
  var _gaq = _gaq || [];
  _gaq.push([\'_setAccount\', \'UA-17257669-7\']);
  _gaq.push([\'_trackPageview\']);

  (function() {
    var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;
    ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';
    var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);
  })();
// ]]></script>',
        'is_active' => '1',
    ),
    'huodongtuiguang' => array(
        'title' => '活动推广',
        'identifier' => 'huodongtuiguang',
        'content' => '<p>{{block type="gri_catalogcustom/category_group" name="ninewest-shop"}}</p>',
        'is_active' => '1',
    ),
    'need_help' => array(
        'title' => '购物车 - 客服热线 (不可删除)',
        'identifier' => 'need_help',
        'content' => '<p><img src="{{skin_url=\'images/cart_q.jpg\'}}" alt="Help" width="24" height="34" /> <span class="up_c">需要帮助？</span> 请拨打 +400-8976-988</p>',
        'is_active' => '1',
    ),
    'new-arrivals' => array(
        'title' => '新品',
        'identifier' => 'new-arrivals',
        'content' => '
<div>{{block type="gri_catalogcustom/category_header" name="category_header"}}</div>
<div>{{block type="catalog/product_list" name="product_list" as="product_list" template="catalog/product/list.phtml" }}</div>',
        'is_active' => '1',
    ),
    'ninewest' => array(
        'title' => 'Nine West - 主页: 主横额 (不可删除) - N1 - Slide',
        'identifier' => 'ninewest',
        'content' => '<div class="slider bot_space ninewest-slide-img clearer">{{block type="interaktingslider/interaktingslider" name="interaktingslider" group="nine-west"}}</div>',
        'is_active' => '1',
    ),
    'ninewest-shop' => array(
        'title' => 'Nine West - 购物页: 主横额 (不可删除)',
        'identifier' => 'ninewest-shop',
        'content' => '<div class="banner">
<p class="spacer">{{block type="gri_catalogcustom/category_group" name="ninewest-shop" category_ids="186,61,89,95"}}</p>
</div>',
        'is_active' => '1',
    ),
    'ninewest-shop-accessories' => array(
        'title' => 'Nine West - 配饰页: 主横额 (不可删除)',
        'identifier' => 'ninewest-shop-accessories',
        'content' => '<div>&nbsp;</div>
<div>&nbsp;</div>
<div>{{block type="gri_catalogcustom/category_group" name="ninewest-shop-accessories" category_ids="90"}}</div>',
        'is_active' => '1',
    ),
    'ninewest-shop-best-sellers' => array(
        'title' => 'Nine West - 畅销款: 主横额 (不可删除)',
        'identifier' => 'ninewest-shop-best-sellers',
        'content' => '<div class="page-title category-title">
<h2>Nine West 畅销款</h2>
</div>',
        'is_active' => '1',
    ),
    'ninewest-shop-clothing' => array(
        'title' => 'Nine West - 服装页: 主横额 (不可删除)',
        'identifier' => 'ninewest-shop-clothing',
        'content' => '<div>&nbsp;&nbsp;</div>
<div>{{block type="gri_catalogcustom/category_group" name="ninewest-shop-clothing" category_ids="98,96,99,100,97,101"}}</div>',
        'is_active' => '1',
    ),
    'ninewest-shop-editor' => array(
        'title' => 'Nine West - 推荐款: 主横额 (不可删除)',
        'identifier' => 'ninewest-shop-editor',
        'content' => '<div class="page-title category-title">
<h2>Nine West 推荐款</h2>
</div>',
        'is_active' => '1',
    ),
    'ninewest-shop-new-arrivals' => array(
        'title' => 'Nine West - 新品: 主横额 (不可删除)',
        'identifier' => 'ninewest-shop-new-arrivals',
        'content' => '<div class="page-title category-title">
<h2>Nine West 新品</h2>
</div>',
        'is_active' => '1',
    ),
    'ninewest-shop-shoes' => array(
        'title' => 'Nine West - 鞋履页: 主横额 (不可删除)',
        'identifier' => 'ninewest-shop-shoes',
        'content' => '<div>&nbsp;</div>
<div>&nbsp;</div>
<div>{{block type="gri_catalogcustom/category_group" name="ninewest-shop-shoes" category_ids="62,85,80,74,69,87,88"}}</div>',
        'is_active' => '1',
    ),
    'ninewest-store' => array(
        'title' => 'Nine West - 店铺地址',
        'identifier' => 'ninewest-store',
        'content' => '<p>Nine West 店铺地址</p>',
        'is_active' => '0',
    ),
    'ninewest_banner' => array(
        'title' => 'Nine West - 品牌横额 (不可删除) - B2',
        'identifier' => 'ninewest_banner',
        'content' => '<p>&nbsp;</p>
<div class="brand-banner">
<div class="brand-image"><a href="baidu.com" target="_self"><img src="{{media url="wysiwyg/Jan_28_Soft_Launch/NineWest/B2.jpg"}}" alt="banner" /></a></div>
<div id="sub_menu">
<div class="container">
<ul>
<li class="first"><a class="shop" href="{{store_url=\'ninewest/shop.html\'}}">购物</a>
<div class="child_menu">
<ul>
<li><a href="{{store_url=\'ninewest/shop/new-arrivals.html\'}}">新品</a></li>
<li><a href="{{store_url=\'ninewest/shop/best-sellers.html\'}}">畅销款</a></li>
<li><a href="{{store_url=\'ninewest/shop/editor.html\'}}">推荐款</a></li>
<li><a href="{{store_url=\'ninewest/shop/shop-by-look.html\'}}">造型推荐</a></li>
</ul>
<ul>
<li><a href="{{store_url=\'ninewest/shop/shoes.html\'}}">鞋履</a></li>
<li><a href="{{store_url=\'ninewest/shop/clothing.html\'}}">服装</a></li>
<li><a href="{{store_url=\'ninewest/shop/accessories.html\'}}">配饰</a></li>
</ul>
</div>
</li>
<li><a href="{{store_url=\'ninewest/about\'}}">关于Nine West</a></li>
<li><a href="{{store_url=\'ninewest/store\'}}">店铺地址</a></li>
<li>&nbsp;</li>
</ul>
<div class="clear">&nbsp;</div>
</div>
</div>
<div>&nbsp;</div>
</div>',
        'is_active' => '1',
    ),
    'ninewest_lookbook' => array(
        'title' => 'Nine West - 造型集',
        'identifier' => 'ninewest_lookbook',
        'content' => '<p>造型集</p>',
        'is_active' => '0',
    ),
    'ninewest_nav' => array(
        'title' => 'Nine West - 菜单 (不可删除)',
        'identifier' => 'ninewest_nav',
        'content' => '<div class="left-static-nav">
<ul>
<li class="new-arrivals"><a href="{{store_url=\'ninewest/shop/new-arrivals.html\'}}">新品</a></li>
<li class="best_seller"><a href="{{store_url=\'ninewest/shop/best-sellers.html\'}}">畅销款</a></li>
<li class="editors_pick"><a href="{{store_url=\'ninewest/shop/editor.html\'}}">推荐款</a></li>
<li class="exclusives"><a href="{{store_url=\'ninewest/shop/shop-by-look.html\'}}">造型推荐</a></li>
</ul>
</div>',
        'is_active' => '1',
    ),
    'pre-order' => array(
        'title' => 'Pre Order',
        'identifier' => 'pre-order',
        'content' => '<p>{{block type="catalog/product_list" name="product_list" as="product_list" template="catalog/product/list.phtml" }}</p>',
        'is_active' => '1',
    ),
    'pre-sales' => array(
        'title' => 'Pre Sales',
        'identifier' => 'pre-sales',
        'content' => '<p>{{block type="catalog/product_list" name="product_list" as="product_list" template="catalog/product/list.phtml" }}</p>',
        'is_active' => '1',
    ),
    'product_optional_promotion_block' => array(
        'title' => '商品页: 促销讯息 (不可删除)',
        'identifier' => 'product_optional_promotion_block',
        'content' => '<p>{{product_name}}</p>',
        'is_active' => '1',
    ),
    'product_promotion_rule_block' => array(
        'title' => '商品页: 系统促销讯息 (不可删除)',
        'identifier' => 'product_promotion_rule_block',
        'content' => '<p>{{rule_name}}</p>',
        'is_active' => '1',
    ),
    'safe_payment' => array(
        'title' => '购物车 - 安全支付（不可删除）',
        'identifier' => 'safe_payment',
        'content' => '<p><img src="{{skin_url=\'images/cart_lock.jpg\'}}" alt="Lock" width="28" height="32" /> 安全支付 <a href="javascript:;"><img src="{{skin_url=\'images/norton.jpg\'}}" alt="norton" width="60" height="36" /></a> <img src="{{skin_url=\'images/cart_d.jpg\'}}" alt="|" width="14" height="36" /> &nbsp;<a href="javascript:;"><img src="{{skin_url=\'images/allpay.jpg\'}}" alt="alipay" width="42" height="36" /></a></p>',
        'is_active' => '1',
    ),
    'shoes-best-sellers' => array(
        'title' => '鞋履 - 畅销款：主横额（不可删除）',
        'identifier' => 'shoes-best-sellers',
        'content' => '<div class="page-title category-title">
<h2>鞋履畅销款</h2>
</div>',
        'is_active' => '1',
    ),
    'shoes-editor' => array(
        'title' => '鞋履 - 推荐款：主横额（不可删除）',
        'identifier' => 'shoes-editor',
        'content' => '<div class="page-title category-title">
<h2>鞋履 推荐款</h2>
</div>',
        'is_active' => '1',
    ),
    'shoes-new-arrivals' => array(
        'title' => '鞋履 - 新品：主横额（不可删除）',
        'identifier' => 'shoes-new-arrivals',
        'content' => '<div class="page-title category-title">
<h2>鞋履 新品</h2>
</div>',
        'is_active' => '1',
    ),
    'shoes_nav' => array(
        'title' => '鞋履 - 菜单 （不可删除）',
        'identifier' => 'shoes_nav',
        'content' => '<div class="left-static-nav">
<ul>
<li class="new-arrivals"><a href="{{store_url=\'shoes/new-arrivals.html\'}}">新品</a></li>
<li class="best_seller"><a href="{{store_url=\'shoes/best-sellers.html\'}}">畅销款</a></li>
<li class="editors_pick"><a href="{{store_url=\'shoes/editor.html\'}}">推荐款</a></li>
</ul>
</div>',
        'is_active' => '1',
    ),
    'shop-accessories' => array(
        'title' => '配饰 - 主页: 主横额（不可删除）',
        'identifier' => 'shop-accessories',
        'content' => '<h2 style="text-align: center;"><span style="font-size: x-large;">配饰</span></h2>
<div>&nbsp;</div>
<div>{{block type="gri_catalogcustom/category_group" name="shop-accessories"}}</div>',
        'is_active' => '1',
    ),
    'shop-clothing' => array(
        'title' => '服装 - 主页（不可删除）',
        'identifier' => 'shop-clothing',
        'content' => '<h2 style="text-align: center;"><span style="font-size: x-large;"><strong></strong>服装</span>&nbsp;</h2>
<div>&nbsp;</div>
<div>{{block type="gri_catalogcustom/category_group" name="shop-clothing" category_ids="60,57,265,264,56,266,277,278,279"}}</div>',
        'is_active' => '1',
    ),
    'shop-shoes' => array(
        'title' => '鞋履 - 主页：主横额（不可删除）',
        'identifier' => 'shop-shoes',
        'content' => '<h2 style="text-align: center;"><span style="font-size: x-large;">鞋履</span></h2>
<div>&nbsp;</div>
<div>{{block type="gri_catalogcustom/category_group" name="shop-shoes" category_ids="17,40,35,29,24,42,43,43"}}</div>',
        'is_active' => '1',
    ),
    'stevemadden' => array(
        'title' => 'Steve Madden - 主页: 主横额 (不可删除) - S1 - Slide',
        'identifier' => 'stevemadden',
        'content' => '<div class="slider stevemadden_slider clearer">{{block type="interaktingslider/interaktingslider" name="interaktingslider" group="stevemadden"}}</div>
<div class="block_list">{{block type="cms/block" name="cms_test_block" block_id="stevemadden_block" }}</div>',
        'is_active' => '1',
    ),
    'stevemadden-shop' => array(
        'title' => 'Steve Madden - 购物页: 主横额 (不可删除)',
        'identifier' => 'stevemadden-shop',
        'content' => '<div>&nbsp;</div>
<div>&nbsp;</div>
<div>{{block type="gri_catalogcustom/category_group" name="stevemadden-shop" category_ids="102,116"}}</div>',
        'is_active' => '1',
    ),
    'stevemadden-shop-accessories' => array(
        'title' => 'Steve Madden - 配饰页: 主横额 (不可删除)',
        'identifier' => 'stevemadden-shop-accessories',
        'content' => '<div>&nbsp;</div>
<div>&nbsp;</div>
<div>{{block type="gri_catalogcustom/category_group" name="stevemadden-shop-accessories" category_ids="117"}}</div>',
        'is_active' => '1',
    ),
    'stevemadden-shop-best-sellers' => array(
        'title' => 'Steve Madden - 畅销款: 主横额 (不可删除)',
        'identifier' => 'stevemadden-shop-best-sellers',
        'content' => '<div class="page-title category-title">
<h2>Steve Madden 畅销款</h2>
</div>',
        'is_active' => '1',
    ),
    'stevemadden-shop-editor' => array(
        'title' => 'Steve Madden - 推荐款: 主横额 (不可删除)',
        'identifier' => 'stevemadden-shop-editor',
        'content' => '<div class="page-title category-title">
<h2>Steve Madden 推荐款</h2>
</div>',
        'is_active' => '1',
    ),
    'stevemadden-shop-new-arrivals' => array(
        'title' => 'Steve Madden - 新品: 主横额 (不可删除)',
        'identifier' => 'stevemadden-shop-new-arrivals',
        'content' => '<div class="page-title category-title">
<h2>Steve Madden 新品</h2>
</div>',
        'is_active' => '1',
    ),
    'stevemadden-shop-shoes' => array(
        'title' => 'Steve Madden - 鞋履页: 主横额 (不可删除)',
        'identifier' => 'stevemadden-shop-shoes',
        'content' => '<div class="banner">
<p class="spacer">&nbsp;{{block type="gri_catalogcustom/category_group" name="stevemadden-shop-shoes" category_ids="103,112,111,105,104,115,113"}}</p>
</div>',
        'is_active' => '1',
    ),
    'stevemadden-store' => array(
        'title' => 'Steve Madden - 店铺地址',
        'identifier' => 'stevemadden-store',
        'content' => '<p>SteveMadden 店铺地址</p>',
        'is_active' => '0',
    ),
    'stevemadden_banner' => array(
        'title' => 'Steve Madden - 品牌横额 (不可删除) - B2',
        'identifier' => 'stevemadden_banner',
        'content' => '<div class="brand-banner">
<div class="brand-image"><img src="{{media url="wysiwyg/Jan_28_Soft_Launch/SteveMadden/B2.jpg"}}" alt="Steve Madden" /></div>
<div id="sub_menu">
<div class="container">
<ul>
<li class="first"><a class="shop" href="{{store_url=\'stevemadden/shop.html\'}}">购物</a>
<div class="child_menu">
<ul>
<li><a href="{{store_url=\'stevemadden/shop/new-arrivals.html\'}}">新品</a></li>
<li><a href="{{store_url=\'stevemadden/shop/best-sellers.html\'}}">畅销款</a></li>
<li><a href="{{store_url=\'stevemadden/shop/editor.html\'}}">推荐款</a></li>
</ul>
<ul>
<li><a href="{{store_url=\'stevemadden/shop/shoes.html\'}}">鞋履</a></li>
</ul>
</div>
</li>
<li><a href="{{store_url=\'stevemadden/about\'}}">关于Steve Madden</a></li>
<li><a href="{{store_url=\'stevemadden/store\'}}">店铺地址</a></li>
</ul>
<div class="clear">&nbsp;</div>
</div>
</div>
<div>&nbsp;</div>
</div>',
        'is_active' => '1',
    ),
    'stevemadden_block' => array(
        'title' => 'Steve Madden - 主頁: 小橫額 (不可刪除)  - S2-S3-S4',
        'identifier' => 'stevemadden_block',
        'content' => '<ul>
<li class="first"><a href="#"><img src="{{media url="wysiwyg/Jan_28_Soft_Launch/SteveMadden/S2.jpg"}}" alt="编辑推荐" /></a></li>
<li><a href="#"><img src="{{media url="wysiwyg/Jan_28_Soft_Launch/SteveMadden/S3.jpg"}}" alt="风格" /></a></li>
<li class="last"><a href="#"><img src="{{media url="wysiwyg/Jan_28_Soft_Launch/SteveMadden/S4.jpg"}}" alt="热卖单品" /></a></li>
</ul>',
        'is_active' => '1',
    ),
    'stevemadden_nav' => array(
        'title' => 'Steve Madden - 自定品类 (不可删除)',
        'identifier' => 'stevemadden_nav',
        'content' => '<div class="left-static-nav">
<ul>
<li class="new-arrivals"><a href="{{store_url=\'stevemadden/shop/new-arrivals.html\'}}">新品</a></li>
<li class="best_seller"><a href="{{store_url=\'stevemadden/shop/best-sellers.html\'}}">畅销款</a></li>
<li class="editor"><a href="{{store_url=\'stevemadden/shop/editor.html\'}}">推荐款</a></li>
</ul>
</div>',
        'is_active' => '1',
    ),
    'steve_madden_lookbook' => array(
        'title' => 'Steve Madden - 造型集',
        'identifier' => 'steve_madden_lookbook',
        'content' => '<p>Steve Madden 造型集</p>',
        'is_active' => '0',
    ),
    'terms_conditions' => array(
        'title' => 'VIP - 条款细则',
        'identifier' => 'terms_conditions',
        'content' => '<div class="fs_tc">
<div class="fs_tc_title">
<p><strong>条款细则</strong></p>
<ol>
<li>升级需要：每天花HK $ 2,000在3个月内赚取3000点</li>
<li>每年更新需要在一年内赚取3000点</li>
</ol></div>
</div>',
        'is_active' => '1',
    ),
    'vip-grading' => array(
        'title' => '我的帐户 - VIP 等级',
        'identifier' => 'vip-grading',
        'content' => '<div class="das_tb_tit">VIP 等级</div>
<div class="das_tit_btn"><a href="#">使用条款</a></div>
<div class="request_info">
<div class="con_table">
<div class="padding_20 no_padding">
<table style="width: 100%;" border="0" cellspacing="0 " cellpadding="0">
<tbody>
<tr>
<td style="text-align: center;" width="275"><img src="{{skin_url=\'images/vip_silver.jpg\'}}" alt="VIP Silver" width="250" /></td>
<td>
<table class="vip_table" style="width: 100%;" border="0" cellspacing="0 " cellpadding="0">
<tbody>
<tr>
<td class="vip_sil_tit" colspan="2" nowrap="nowrap">熙熙网VIP等级: 银卡</td>
</tr>
<tr>
<td nowrap="nowrap">
<p>尊贵礼遇:</p>
<ul class="silver_check">
<li>VIP购物专享85折</li>
<li>累积积分</li>
<li>免费礼品</li>
<li>VIP先享折扣</li>
<li>VIP预售</li>
</ul>
</td>
<td nowrap="nowrap">
<p>升级要求:<br /> <strong>一天内购物满$2,000<br /><span style="margin-left: 65px; font-weight: bold;">或</span><br />3个月内累积3000分</strong><br /><br /></p>
<p>续会要求:<br /> <strong>一年内累积3000分</strong></p>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<div class="clear bot_space">&nbsp;</div>
<table style="width: 100%;" border="0" cellspacing="0 " cellpadding="0">
<tbody>
<tr>
<td style="text-align: center;" width="275"><img src="{{skin_url=\'images/vip_gold.jpg\'}}" alt="VIP Gold" width="250" /></td>
<td>
<table class="vip_table" style="width: 100%;" border="0" cellspacing="0 " cellpadding="0">
<tbody>
<tr>
<td class="vip_gol_tit" colspan="2" nowrap="nowrap">熙熙网VIP等级: 金卡</td>
</tr>
<tr>
<td nowrap="nowrap">
<p>尊贵礼遇:</p>
<ul class="gold_check">
<li>全国免邮</li>
<li>VIP购物专享85折</li>
<li>累积积分</li>
<li>免费礼品</li>
<li>VIP先享折扣</li>
<li>VIP预售</li>
</ul>
</td>
<td nowrap="nowrap">
<p>金卡升級要求:<br /> <strong>一天內購物滿$8,000<br /><span style="margin-left: 65px; font-weight: bold;">或</span><br />3個月內累積10,000分</strong></p>
<br />
<p>金卡續會要求:<br /> <strong> 一年內累積10,000分</strong></p>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<div class="clear bot_space_m">&nbsp;</div>
</div>
</div>
</div>',
        'is_active' => '1',
    ),
    'vip_upgrade' => array(
        'title' => 'VIP升级 - A1',
        'identifier' => 'vip_upgrade',
        'content' => '<p><a href="{{store_url=""}}"><img src="{{media url="wysiwyg/Jan_28_Soft_Launch/My_Account/A1.jpg"}}" alt="vip upgrade" /></a></p>',
        'is_active' => '1',
    ),
);
