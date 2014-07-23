/*******    Size Chart & Glossary & Faq  ****/
jQuery(function(){
    jQuery("#cms-size-submenu a").each(function(i){
        jQuery(this).mouseover(function(){
            jQuery("#cms-size-submenu a").css({"color":"#808080","text-decoration":"none"})
            jQuery(this).css({"color":"#000","text-decoration":"underline"})
            jQuery(".cms-size-chart").eq(i).css("display","block").siblings(".cms-size-chart").css("display","none")
        }).mouseout(function(){
            //jQuery(this).css({"text-decoration":"none"})
        })
    })
	
    jQuery("#cms-glossary-submenu a").each(function(i){
        jQuery(this).mouseover(function(){
            jQuery("#cms-glossary-submenu a").css({"color":"#808080","text-decoration":"none"})
            jQuery(this).css({"color":"#000","text-decoration":"underline"})
            jQuery(".cms-glossary").eq(i).css("display","block").siblings(".cms-glossary").css("display","none")
        }).mouseout(function(){
            //jQuery(this).css({"text-decoration":"none"})
        })
    })
	
    jQuery("#cms-faq-submenu a").each(function(i){
        jQuery(this).mouseover(function(){
            jQuery("#cms-faq-submenu a").css({"color":"#808080","text-decoration":"none"})
            jQuery(this).css({"color":"#000","text-decoration":"underline"})
            jQuery(".cms-faq").eq(i).css("display","block").siblings(".cms-faq").css("display","none")
        }).mouseout(function(){
            //jQuery(this).css({"text-decoration":"none"})
        })
    })
})

jQuery(document).ready(function(){
	jQuery("#cms-size-submenu a").eq(0).trigger('mouseenter');
	jQuery("#cms-faq-submenu a").eq(0).trigger('mouseenter');
	jQuery("#cms-glossary-submenu a").eq(0).trigger('mouseenter');
})