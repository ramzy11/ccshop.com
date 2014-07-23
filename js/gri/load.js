if (jQuery) (function($) {
    $(document).ready(function() {
        $("SELECT").selectBox();
        var navWidth = $("#nav").width(), navSpace = navWidth, navItems = $("#nav li.level0"), itemSpacing = 0;
        navItems.each(function(index, obj) {
            navSpace -= $(obj).outerWidth();
        });
        if (navSpace > 0) {
            itemSpacing = parseInt(navSpace / navItems.length / 2);
            navItems.css({"margin-left": itemSpacing, "margin-right": itemSpacing});
        }
        if (window._gat) window.pageTracker = _gat._getTrackerByName();
    });
    $(".marquee").marquee("marquee");
    var v = $('.vjcarousel').jcarousel({vertical: true}),
        prev = v.siblings('.prev'),
        next = v.siblings('.next');
    prev.on('inactive.jcarouselcontrol', function() {$(this).addClass('inactive');})
        .on('active.jcarouselcontrol', function() {$(this).removeClass('inactive');})
        .jcarouselControl({target: '-=1'});
    next.on('inactive.jcarouselcontrol', function() {$(this).addClass('inactive');})
        .on('active.jcarouselcontrol', function() {$(this).removeClass('inactive');})
        .jcarouselControl({target: '+=1'});
    window.switchMainImage = function(element, src) {
        element = $(element);
        var mainImage = element.closest('.product-img-box').find('.product-image img');
        mainImage.is(':visible') ? mainImage.prop('src', src) : mainImage.attr('url', src);
        element.closest('.magiatecolorswatch-gallery').find(element.prop('tagName')).removeClass('zoomThumbActive');
        element.addClass('zoomThumbActive');
    }
})(jQuery);

setUrlParam = function(para_name,para_value,url)
{
    var strNewUrl=new String();
    var strUrl=url;
    if(strUrl.indexOf("?")!=-1)
    {
        strUrl=strUrl.substr(strUrl.indexOf("?")+1);
        if(strUrl.toLowerCase().indexOf(para_name.toLowerCase())==-1)
        {
            strNewUrl=url+"&"+para_name+"="+para_value;
            return strNewUrl;

        }else
        {
            var aParam=strUrl.split("&");
            for(var i=0;i<aParam.length;i++)
            {
                if(aParam[i].substr(0,aParam[i].indexOf("=")).toLowerCase()==para_name.toLowerCase())
                {
                    aParam[i]= aParam[i].substr(0,aParam[i].indexOf("="))+"="+para_value;
                }
            }

            strNewUrl=url.substr(0,url.indexOf("?")+1)+aParam.join("&");
            return strNewUrl;
        }

    }else
    {
        strUrl+="?"+para_name+"="+para_value;
        return strUrl
    }
};

jQuery(function(){
(window.initPriceFilter = function() {
    var obj = jQuery('#price-rangeslider-values'),
      minPrice = parseInt(obj.attr('min_price')),
      maxPrice = parseInt(obj.attr('max_price')),
      curMinPrice = parseInt(obj.attr('cur_min_price')),
      curMaxPrice = parseInt(obj.attr('cur_max_price')),
      symbol = obj.attr('currency_symbol'),
      step = parseInt(obj.attr('step')),
      url = obj.attr('url');
    jQuery("#price-rangeslider").slider({
        range: true,
        min: minPrice,
        max: maxPrice,
        step: step,
        values: [ curMinPrice , curMaxPrice ],
        slide: function( event, ui ) {
            jQuery( "#price-rangeslider-min" ).val( symbol + ui.values[ 0 ] );
            jQuery( "#price-rangeslider-max" ).val( symbol + ui.values[ 1 ] );
        },
        stop: function( event, ui ) {
            if (url.indexOf("?") == "-1") url += "?";
            if (url.indexOf("price=") == "-1"){
                url += "&price=" + ui.values[ 0 ] + "-" + ui.values[ 1 ];
            }
            else{
                var newValue = ui.values[ 0 ] + "-" + ui.values[ 1 ];
                url = setUrlParam('price',newValue,url)
            }

            if(ui.values[ 0 ] != curMinPrice || ui.values[ 1 ] != curMaxPrice){
                sendFilter(url);
            }
        }
    });
    jQuery( "#price-rangeslider-min" ).val( symbol + jQuery( "#price-rangeslider" ).slider( "values", 0 ) );
    jQuery( "#price-rangeslider-max" ).val( symbol + jQuery( "#price-rangeslider" ).slider( "values", 1 ) );
})();
})



arrayIntersection = function(a, b) {
    if(a == undefined || b == undefined){
        return '';
    }
    var ai = 0, bi = 0;
    var result = new Array();

    while( ai < a.length && bi < b.length )
    {
        if (a[ai] < b[bi] ){ ai++; }
        else if (a[ai] > b[bi] ){ bi++; }
        else /* they're equal */
        {
            result.push(a[ai]);
            ai++;
            bi++;
        }
    }
    a = b = '';

    return result;
};

(window.initGiftItemsBind = function(){
    var inputs = jQuery("input.gift-product");

    inputs.each(function(index, domElement){
        var productId = jQuery(domElement).val();
        var giftItemElementHtml = jQuery('#gift-icon-hover-parent-'+productId).html() ;
        jQuery('body').append( giftItemElementHtml );
        jQuery('#gift-icon-hover-parent-'+productId).remove();
        jQuery('#gift-icon-'+productId).live('mouseover', function(e) {
            var offset,
                height,
                top,
                left;

            offset = jQuery(this).find('img').offset();
            height = jQuery(this).find('img').height();

            left = offset.left - 6;
            top  = offset.top + height + 5;

            jQuery('#gift-icon-hover-'+productId).css({"position":"absolute", "top":top, "left":left})
                .show();
        });

        jQuery('#gift-icon-'+productId).live('mouseout', function(e){
            jQuery('#gift-icon-hover-'+productId).hide();
        });
    })

    jQuery("input.gift-product").remove();

})()

_(document).on('click', function(e) {
    var t = _(e.target);
    if (t.hasClass('zoomThumbActive')) return;
    _('.item .product_style').removeClass('hover');
    t.closest('.product_style').addClass('hover');
});

if (window.Validation) {
    Validation.isVisible = function (elm) {
        if (elm.hasAttribute('isVisible')) return elm.getAttribute('isVisible');
        while(elm.tagName != 'BODY') {
            if(!$(elm).visible()) return false;
            elm = elm.parentNode;
        }
        return true;
    };
}

/*(window.initTopMenu = function($) {
    var topMenus = $('#nav .level-top');

    topMenus.bind('mouseover', function(){
         var specialImageMenu = $(this).next().find('a.special-image'),
             shopByBrandMenu = $(this).next().find('a.shop-by-brand'),
             offset = shopByBrandMenu.offset(),
             height = shopByBrandMenu.height();

         left = offset.left + 20;
         top  = offset.top + height -20;

         //Special Image Position
         specialImageMenu.css({"position":"absolute", "top":top, "left":left}).show();
    })
})(jQuery);*/

(window.initSwatchOptions = function() {
    var swatchOptions = $$('.item .product_style ul.options li');
    swatchOptions.length && swatchOptions.each(function(obj) {
        if (obj.getAttribute('initialized')) return;

        obj.parentNode.getElementsBySelector('li:first')[0].addClassName('hover');
        var listImageNode = obj.parentNode.parentNode.parentNode;
        var firstImage = obj.parentNode.getElementsBySelector('li:first')[0].getAttribute('firstimage');
        var secondImage = obj.parentNode.getElementsBySelector('li:first')[0].getAttribute('secondimage');

        if(firstImage != '')
            listImageNode.getElementsBySelector('div.product_image img')[0].src = firstImage;

        if(secondImage != ''){
            listImageNode.getElementsBySelector('div.product_image img.rotatingImage')[0].src = secondImage;
        }

        if(secondImage != ''){
            listImageNode.getElementsBySelector('div.product_image img.rotatingImage')[0].src = secondImage;
        }else if(firstImage != ''){
            listImageNode.getElementsBySelector('div.product_image img.rotatingImage')[0].src = firstImage;
        }


        obj.observe('mouseover', function(event) {
            var item = $(this.parentNode.parentNode.parentNode),
                swatch = (this.getAttribute('option') || '-').split('-'),
                itemDescriptionSwatch = item.getElementsBySelector('.item-description .more-views div.swatch-' + swatch[1])[0];
            this.siblings().each(function(obj) {
                obj.removeClassName('hover');
            });
            this.addClassName('hover');

            item.getElementsBySelector('div.product_image img')[0].src = this.getAttribute('firstimage');
            if(this.getAttribute('secondimage') != '')
                item.getElementsBySelector('div.product_image img.rotatingImage')[0].src = this.getAttribute('secondimage');


            if (!item.getAttribute('skip_link_update')) {
                var link = item.getElementsBySelector('div.product_image a.product-image')[0];
                if (link) link.href = this.childElements('a')[0].href;
            }
            if (itemDescriptionSwatch) {
                itemDescriptionSwatch.siblings().each(function(o) {
                    o.hide();
                });
                itemDescriptionSwatch.show().select('.magiatecolorswatch-gallery li a:first')[0].click();
            }
        });

        obj.setAttribute('initialized', 1);
    });

//    var changeProductImages = $$('.catalog-category-view .item .product_image');
//    changeProductImages.length && changeProductImages.each(function(obj){
//        if (obj.getAttribute('initialized')) return;
//
//        obj.observe('mouseover', function(event) {
//            var item = $(this.parentNode);
//            var rotatingImage = item.getElementsBySelector('div.product_image img.rotatingImage')[0].src;
//            if(rotatingImage != ''){
//                item.getElementsBySelector('div.product_image img')[0].style.display = 'none';
//                item.getElementsBySelector('div.product_image img.rotatingImage')[0].style.display = 'block';
//            }
//        });
//        obj.observe('mouseout', function(event) {
//            var item = $(this.parentNode);
//            item.getElementsBySelector('div.product_image img')[0].style.display = 'block';
//            item.getElementsBySelector('div.product_image img.rotatingImage')[0].style.display = 'none';
//        });
//    });

    /*var swatches = $$('.item .product_style ul.options');
    swatches.length && swatches.each(function(obj) {
        if (obj.getAttribute('initialized')) return;
        var li = obj.getElementsBySelector('li:first')[0];
        li && (Element.prototype.triggerEvent.bind(li))('mouseover');
        obj.setAttribute('initialized', 1);
    });*/
})();

setFilterCookie = function(attribute)
{
    var value = Mage.Cookies.get('------'+attribute+'------');
    value = !parseInt(value) ? 1 : 0;
    Mage.Cookies.set('------'+attribute+'------', value);
}


