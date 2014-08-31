/*Element.prototype.triggerEvent = function (eventName) {
    if (document.createEvent) {
        var evt = document.createEvent('HTMLEvents');
        evt.initEvent(eventName, true, true);
        return this.dispatchEvent(evt);
    }
    if (this.fireEvent) return this.fireEvent('on' + eventName);
};
Validation.hideAdvice = function(elm, advice) {
    if (advice != null) {
        advice.hide();
    }
};*/

(function($){$.fn.hoverIntent=function(f,g){var cfg={sensitivity:7,interval:100,timeout:0};cfg=$.extend(cfg,g?{over:f,out:g}:f);var cX,cY,pX,pY;var track=function(ev){cX=ev.pageX;cY=ev.pageY};var compare=function(ev,ob){ob.hoverIntent_t=clearTimeout(ob.hoverIntent_t);if((Math.abs(pX-cX)+Math.abs(pY-cY))<cfg.sensitivity){$(ob).unbind("mousemove",track);ob.hoverIntent_s=1;return cfg.over.apply(ob,[ev])}else{pX=cX;pY=cY;ob.hoverIntent_t=setTimeout(function(){compare(ev,ob)},cfg.interval)}};var delay=function(ev,ob){ob.hoverIntent_t=clearTimeout(ob.hoverIntent_t);ob.hoverIntent_s=0;return cfg.out.apply(ob,[ev])};var handleHover=function(e){var ev=jQuery.extend({},e);var ob=this;if(ob.hoverIntent_t){ob.hoverIntent_t=clearTimeout(ob.hoverIntent_t)}if(e.type=="mouseenter"){pX=ev.pageX;pY=ev.pageY;$(ob).bind("mousemove",track);if(ob.hoverIntent_s!=1){ob.hoverIntent_t=setTimeout(function(){compare(ev,ob)},cfg.interval)}}else{$(ob).unbind("mousemove",track);if(ob.hoverIntent_s==1){ob.hoverIntent_t=setTimeout(function(){delay(ev,ob)},cfg.timeout)}}};return this.bind('mouseenter',handleHover).bind('mouseleave',handleHover)}})(jQuery);

(function($){$.fn.hoverDropDown = function(dropDown, options, handler){
    $.fn.hoverDropDownDefaults = $.fn.hoverDropDownDefaults || {
        slideDown: 300,
        slideUp: 300,
        slideUpDelay: 300
    };
    options = $.extend({}, $.fn.hoverDropDownDefaults, options);
    (handler || this).hover(function(){
        var subject = $(this).find(dropDown);
        if (!subject.length) subject = dropDown;
        if (subject.length != 1) return;
        clearTimeout(subject.data("timer"));
        if (dropDown.length > 1) dropDown.each(function(){
            if (subject.length == 1 && this === subject[0]) return;
            $(this).stop(true, true).hide();
        });
        if (subject.queue() && subject.queue().length) return;
        subject.slideDown(options.slideDown);
    }, function(){
        var subject = $(this).find(dropDown) || dropDown;
        subject.data("timer", setTimeout(function(){
            subject.slideUp(options.slideUp);
        }, options.slideUpDelay));
    });
    return this;
}})(jQuery);

/**
 * author Remy Sharp
 * url http://remysharp.com/tag/marquee
 */
(function ($) {
    $.fn.marquee = function (klass) {
        var newMarquee = [],
            last = this.length;

        // works out the left or right hand reset position, based on scroll
        // behavior, current direction and new direction
        function getReset(newDir, marqueeRedux, marqueeState) {
            var behavior = marqueeState.behavior, width = marqueeState.width, dir = marqueeState.dir;
            var r = 0;
            if (behavior == 'alternate') {
                r = newDir == 1 ? marqueeRedux[marqueeState.widthAxis] - (width*2) : width;
            } else if (behavior == 'slide') {
                if (newDir == -1) {
                    r = dir == -1 ? marqueeRedux[marqueeState.widthAxis] : width;
                } else {
                    r = dir == -1 ? marqueeRedux[marqueeState.widthAxis] - (width*2) : 0;
                }
            } else {
                r = newDir == -1 ? marqueeRedux[marqueeState.widthAxis] : 0;
            }
            return r;
        }

        // single "thread" animation
        function animateMarquee() {
            var i = newMarquee.length,
                marqueeRedux = null,
                $marqueeRedux = null,
                marqueeState = {},
                newMarqueeList = [],
                hitedge = false;

            while (i--) {
                marqueeRedux = newMarquee[i];
                $marqueeRedux = $(marqueeRedux);
                marqueeState = $marqueeRedux.data('marqueeState');

                if ($marqueeRedux.data('paused') !== true) {
                    // TODO read scrollamount, dir, behavior, loops and last from data
                    marqueeRedux[marqueeState.axis] += (marqueeState.scrollamount * marqueeState.dir);

                    // only true if it's hit the end
                    hitedge = marqueeState.dir == -1 ? marqueeRedux[marqueeState.axis] <= getReset(marqueeState.dir * -1, marqueeRedux, marqueeState) : marqueeRedux[marqueeState.axis] >= getReset(marqueeState.dir * -1, marqueeRedux, marqueeState);

                    if ((marqueeState.behavior == 'scroll' && marqueeState.last == marqueeRedux[marqueeState.axis]) || (marqueeState.behavior == 'alternate' && hitedge && marqueeState.last != -1) || (marqueeState.behavior == 'slide' && hitedge && marqueeState.last != -1)) {
                        if (marqueeState.behavior == 'alternate') {
                            marqueeState.dir *= -1; // flip
                        }
                        marqueeState.last = -1;

                        $marqueeRedux.trigger('stop');

                        marqueeState.loops--;
                        if (marqueeState.loops === 0) {
                            if (marqueeState.behavior != 'slide') {
                                marqueeRedux[marqueeState.axis] = getReset(marqueeState.dir, marqueeRedux, marqueeState);
                            } else {
                                // corrects the position
                                marqueeRedux[marqueeState.axis] = getReset(marqueeState.dir * -1, marqueeRedux, marqueeState);
                            }

                            $marqueeRedux.trigger('end');
                        } else {
                            // keep this marquee going
                            newMarqueeList.push(marqueeRedux);
                            $marqueeRedux.trigger('start');
                            marqueeRedux[marqueeState.axis] = getReset(marqueeState.dir, marqueeRedux, marqueeState);
                        }
                    } else {
                        newMarqueeList.push(marqueeRedux);
                    }
                    marqueeState.last = marqueeRedux[marqueeState.axis];

                    // store updated state only if we ran an animation
                    $marqueeRedux.data('marqueeState', marqueeState);
                } else {
                    // even though it's paused, keep it in the list
                    newMarqueeList.push(marqueeRedux);
                }
            }

            newMarquee = newMarqueeList;

            if (newMarquee.length) {
                setTimeout(animateMarquee, 10);
            }
        }

        // TODO consider whether using .html() in the wrapping process could lead to loosing predefined events...
        this.each(function (i) {
            var $marquee = $(this),
                width = $marquee.attr('width') || $marquee.width(),
                height = $marquee.attr('height') || $marquee.height(),
                $marqueeRedux = $marquee.after('<div ' + (klass ? 'class="' + klass + '" ' : '') + 'style="width:' + width + 'px;height:' + height + 'px;overflow:hidden;"><div style="float:left; white-space:nowrap;">' + $marquee.html() + '</div></div>').next(),
                marqueeRedux = $marqueeRedux.get(0),
                hitedge = 0,
                direction = ($marquee.attr('direction') || 'left').toLowerCase(),
                marqueeState = {
                    dir : /down|right/.test(direction) ? -1 : 1,
                    axis : /left|right/.test(direction) ? 'scrollLeft' : 'scrollTop',
                    widthAxis : /left|right/.test(direction) ? 'scrollWidth' : 'scrollHeight',
                    last : -1,
                    loops : $marquee.attr('loop') || -1,
                    scrollamount : $marquee.attr('scrollamount') || this.scrollAmount || 1,
                    behavior : ($marquee.attr('behavior') || 'scroll').toLowerCase(),
                    width : /left|right/.test(direction) ? width : height
                };

            // corrects a bug in Firefox - the default loops for slide is -1
            if ($marquee.attr('loop') == -1 && marqueeState.behavior == 'slide') {
                marqueeState.loops = 1;
            }

            $marquee.remove();

            // add padding
            if (/left|right/.test(direction)) {
                $marqueeRedux.find('> div').css('padding', '0 ' + width + 'px');
            } else {
                $marqueeRedux.find('> div').css('padding', height + 'px 0');
            }

            // events
            $marqueeRedux.bind('stop', function () {
                $marqueeRedux.data('paused', true);
            }).bind('pause', function () {
                    $marqueeRedux.data('paused', true);
                }).bind('start', function () {
                    $marqueeRedux.data('paused', false);
                }).bind('unpause', function () {
                    $marqueeRedux.data('paused', false);
                }).data('marqueeState', marqueeState); // finally: store the state

            // todo - rerender event allowing us to do an ajax hit and redraw the marquee

            newMarquee.push(marqueeRedux);

            marqueeRedux[marqueeState.axis] = getReset(marqueeState.dir, marqueeRedux, marqueeState);
            $marqueeRedux.trigger('start');

            // on the very last marquee, trigger the animation
            if (i+1 == last) {
                animateMarquee();
            }
        });

        return $(newMarquee);
    };
}(jQuery));

jQuery(document).ready(function(){
	var topControlIcon = jQuery ('.top-icon-menu, .shadow, ' +
        '.block-cart-header, ' +
        '.top-search, ' +
        '.page, ' +
        'body, ' +
        '.header-button,' +
        '.swipe-menu .sf-menu-phone li a' );

	var blockSliderMarker = jQuery('.products-grid, .products-list, .catalog-product-view');
 	if(blockSliderMarker.length===0 ) {
   		jQuery(".sidebar .block-slider-sidebar").remove();
 	}else {
  		jQuery(".sidebar .block-slider-sidebar").addClass('block-slider-start');
  	};

	/*************************************************************** Superfish Menu *********************************************************************/
	/* toggle nav */
	jQuery("#menu-icon").on("click", function(){
		jQuery(".sf-menu-phone").slideToggle();
		jQuery(this).toggleClass("active");
	});

	jQuery('.sf-menu-phone').find('li.parent').append('<strong></strong>');
	jQuery('.sf-menu-phone li.parent strong').on("click", function(){
		if (jQuery(this).attr('class') == 'opened') {
            jQuery(this).removeClass().parent('li.parent').find('> ul').slideToggle();
        } else {
		    jQuery(this).addClass('opened').parent('li.parent').find('> ul').slideToggle();
	    }
	});

	jQuery('.icon-reorder, .block-cart-header, .top-search').on("click", function(){
		jQuery('.sf-menu-phone').slideUp();
		jQuery('#menu-icon').removeClass('active');
        jQuery(".swipe .menu-lists").addClass("no-display")
	});

	/***************************************************************** Cart Truncated *********************************************************************/

		jQuery('.truncated span').click(function(){
			jQuery(this).parent().find('.truncated_full_value').stop().slideToggle();
		});
		function truncateOptions() {
		    $$('.truncated').each(function(element){
		        Event.observe(element, 'mouseover', function(){
		            if (element.down('div.truncated_full_value')) {
		                element.down('div.truncated_full_value').removeClassName('show')
		            }
		        });
		    });
		}

		Event.observe(window, 'load', function(){
		   truncateOptions();
		});

		jQuery(".price-box.map-info a, .tier-price a").click(function() {
	        jQuery(".map-popup").toggleClass("displayblock");
	    });

	    jQuery('.map-popup-close').on('click',function(){
	    	jQuery('.map-popup').removeClass('displayblock');
	    });
	/********************************************************** Product View Accordion *********************************************************************/
		jQuery.fn.slideFadeToggle = function(speed, easing, callback) {
		  return this.animate({opacity: 'toggle', height: 'toggle'}, speed, easing, callback);
		};
		jQuery('.box-collateral').not('.box-up-sell').find('h2').append('<span class="toggle"></span>');
		jQuery('.form-add').find('.box-collateral-content').css({'display':'block'}).parents('.form-add').find('> h2 > span').addClass('opened');

		jQuery('.box-collateral > h2').click(function(){
			OpenedClass = jQuery(this).find('> span').attr('class');
			if (OpenedClass == 'toggle opened') {
                jQuery(this).find('> span').removeClass('opened');
            }else {
                jQuery(this).find('> span').addClass('opened');
            }

			jQuery(this).parents('.box-collateral').find(' > .box-collateral-content').slideFadeToggle()
		});
	/*************************************************************** Sidebar Accordion *********************************************************************/
		jQuery('.sidebar .block .block-title').append('<span class="toggle"></span>');
		jQuery('.sidebar .block .block-title').on("click", function(){
			if (jQuery(this).find('> span').attr('class') == 'toggle opened') {
                jQuery(this).find('> span').removeClass('opened').parents('.block').find('.block-content').slideToggle();
            } else {
				jQuery(this).find('> span').addClass('opened').parents('.block').find('.block-content').slideToggle();
			}
		});

	/**************************************************************** Footer Accordion *********************************************************************/
		jQuery('.footer .footer-col > h4').append('<span class="toggle"></span>');
		jQuery('.footer h4').on("click", function(){
			if (jQuery(this).find('span').attr('class') == 'toggle opened') {
                jQuery(this).find('span').removeClass('opened').parents('.footer-col').find('.footer-col-content').slideToggle();
            } else {
				jQuery(this).find('span').addClass('opened').parents('.footer-col').find('.footer-col-content').slideToggle();
			}
		});

	/******************************************************************** Header Buttons *********************************************************************/
    jQuery('.header-button, .switch-show').not('.top-login').on("click", function(e){
	    var ul = jQuery(this).find('ul')
	    if(ul.is(':hidden'))
	     ul.slideDown()
	     ,jQuery(this).addClass('active')
	    else
	     ul.slideUp()
	     ,jQuery(this).removeClass('active')
	     jQuery('.header-button, .switch-show').not(this).removeClass('active'),
	     jQuery('.header-button, .switch-show').not(this).find('ul').slideUp()
	     jQuery('.header-button ul li, .switch-show ul li').click(function(e){
	      	  e.stopPropagation();
	     });

	     return false
		});

        jQuery('.header-buttons .top-search-link').on('click',function(){
            jQuery('.search-content').slideToggle(300);
        });

        jQuery(document).on('click',function(){
		    jQuery('.header-button, .switch-show').removeClass('active').find('ul').slideUp()
		});
		jQuery('.block-cart-header, .top-search').on('click',function(){
		    jQuery('.header-button').removeClass('active').find('ul').slideUp()
		});


    jQuery(document).click(function(event){
        if(jQuery(event.target).parents("#ships-to-popup").length==0){
            jQuery("#ships-to-popup").hide();
        }
    })


    /********************************************************************* swipe *****************************************************************************/
		function swipe_animate_true(){
			jQuery('.icon-reorder').addClass('active');
			jQuery('.swipe').stop(true).animate({'left':'0'},300);
		}

		function swipe_animate_false(){
			jQuery('.icon-reorder').removeClass('active');
			jQuery('.swipe').stop(true).animate({'left':'-257px'},400);
		}

	    jQuery('.icon-reorder').click(function(){
	    	swipe_animate_true();
	    	mini_form_hide();
		    if(jQuery(this).parents('body').hasClass('ind')){
		    	jQuery(this).parents('body').removeClass('ind');

		    	swipe_animate_false()
		    	return false
		    }
		    else{
			    jQuery(this).parents('body').addClass('ind');

			    swipe_animate_true()
			    return false
		    }
	    })


	    jQuery(topControlIcon).not('.page').click(function(){
	    	//swipe_animate_false();
		   // if(jQuery(this).parents('body').hasClass('ind')){
		    //	jQuery(this).parents('body').removeClass('ind');
		    	//swipe_animate_false();
		    	//return false
		    //}
		});
/***
	    jQuery('.swipe').height( jQuery(window).height() );

	    jQuery(window).resize(function() {
	        jQuery('.swipe').height(jQuery(window).height());
	    });
***/
	    var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent),
	    $flag
        if(isMobile) {
			jQuery('body').removeClass('ps-static');
			jQuery('body').addClass('ps-phone');
			jQuery('.page').click(function(){
			   	jQuery('body').removeClass('ind');
			   	swipe_animate_false();
			});
		};

		var isiPhone = (navigator.userAgent.match(/iPhone/i) != null);
		if(!isMobile) {
			jQuery(function () {
				var headerHeight = jQuery('.header-container').outerHeight()
				var navContainerHeight = jQuery('.nav-container').outerHeight()
				var navContainerClass = jQuery('.nav-container')
				if (!isMobile) {
					jQuery(window).scroll(function () {
					 	  var scrollTop = jQuery(window).scrollTop()
						  if(scrollTop>headerHeight)
						  	jQuery(navContainerClass).addClass('fixed')
						  	,jQuery('body').css({'padding-top':navContainerHeight});
						  if(scrollTop <= headerHeight)
						  	jQuery(navContainerClass).removeClass('fixed')
						  	,jQuery('body').css({'padding-top':0})
					 });
				}
			});
		}

/********************************************************************* Swipe Back Button ***********************************************************************/


/********************************************************************* top-icon-menu ***********************************************************************/
		function mini_form_hide(){
			if(!$flag){	return false}
				jQuery('#search_mini_form').animate({height: 'hide', opacity:0}, 300);
				jQuery('.top-search').removeClass('active');

		}

		function mini_form_show(){
			jQuery('#search_mini_form').animate({height: 'show', opacity:1}, 300);
			jQuery('.top-search').addClass('active');
			jQuery('.form-search .input-text').trigger('focus');
			if (isiPhone) {
				jQuery('#search_mini_form').css({'top':'55px'});
			}
		};

		jQuery('.top-search').on("click", function(){
			if ( jQuery('#search_mini_form').css('display') == 'none' ) {
				mini_form_show()
			} else {
				mini_form_hide()
			}
		});

	/********************************************************************** Header Cart *********************************************************************/
		jQuery('.top-icon-menu .block-cart-header ').click(function() {
			jQuery('.block-cart-header .cart-content').stop(true, true).slideToggle(300);
			jQuery(this).toggleClass('active');
			return false
		});
//        jQuery(".top-menu-cart").click(function(){
//            jQuery('.top-cart-container #topCartContentSwipe').slideToggle(300);
//            jQuery(this).toggleClass('active');
//           // return false;
//        })
//
//        jQuery('.header-buttons li.last').click(function() {
//            jQuery('.top-cart-container #topCartContent').slideToggle(300);
//            jQuery(this).toggleClass('active');
//            return false;
//        });

    jQuery(".header-buttons .mini-cart-qty a").live("click",function(){
        jQuery("#topCartContent").slideToggle(300)
        jQuery(".header-buttons .mini-cart-qty a").die("click")
        return false;
    })
//    jQuery(".top-icon-menu .mini-cart-qty a").live("click",function(e){
//        e.preventDefault()
//        jQuery("#topCartContentSwipe").slideToggle(300)
//    })


        jQuery('.header .block-cart-header ').click(function() {
            jQuery('.block-cart-header .cart-content').stop(true, true).slideToggle(300);
            jQuery(this).toggleClass('active');
            return false
        });

		jQuery(topControlIcon).not('.block-cart-header').on('click',function(){
			jQuery('.block-cart-header .cart-content').slideUp();
			jQuery('.block-cart-header').removeClass('active');
		});

		jQuery('.icon-reorder').on('click',function(){
			jQuery('.block-cart-header .cart-content').slideUp();
			jQuery('.block-cart-header').removeClass('active');
		});

		jQuery('.header .block-cart-header a').on('click touchend', function(e) {
		    var el = jQuery(this);
		    var link = el.attr('href');
		    window.location = link;
		});

        !function($){
            var top_search = $('.top-search')
            $(window).bind('load resize',function(){
                    var bodyWidth=$('.container').width()
                    if(bodyWidth>=767){
                        if($flag===true)
  		                    $('#search_mini_form').show().css({opacity:1})
  	                        $flag = false;
                        }else{
                            if($flag===false&&!top_search.hasClass('active'))
  		                    $('#search_mini_form').hide().css({opacity:0})
  	                        $flag = true;
                        }
            })
        }(jQuery);
    });

/************************** Banner **************************************/
jQuery(function(){
        jQuery(".shop-banner").click(function(){
            if(jQuery(window).width() < 701){
                jQuery(".vertnav-container").slideToggle(300);
            }
        })
})

jQuery(function(){
    jQuery(".brand-banner").click(function(){
        if(jQuery(window).width() < 701){
            jQuery(".vertnav-container").slideToggle(300);
        }
    })
})
/************************** Bottom Contact Us**************************************/
jQuery(function(){
    jQuery("#bottom-contact-us .bottom-contactus-title").click(function(){
        jQuery("#bottom-contact-us").slideToggle("slow")
    })
})
/**********************************************************************back-top*****************************************************************************/
    jQuery(function () {
        jQuery(window).scroll(function () {
            if (jQuery(this).scrollTop() > 100) {
                jQuery('#back-top').fadeIn();
            } else {
                jQuery('#back-top').fadeOut();
            }
        });

        // scroll body to 0px on click
        jQuery('#back-top a').click(function () {
            jQuery('body,html').stop(false, false).animate({
                scrollTop: 0
            }, 800);

            return false;
        });
    });

/***************************************************************************************************** Magento class **************************************************************************/
    jQuery(document).ready(function() {
	    jQuery('.sidebar .block').last().addClass('last_block');
	    jQuery('.sidebar .block').first().addClass('first');
	    jQuery('.box-up-sell li').eq(2).addClass('last');
	    jQuery('.form-alt li:last-child').addClass('last');
	    jQuery('.product-collateral #customer-reviews dl dd, #cart-sidebar .item').last().addClass('last');
	    jQuery('#checkout-progress-state li:odd').addClass('odd');
	    jQuery('.product-view .product-img-box .product-image').append('<span></span>');
        jQuery('.links a.top-link-cart').parent().addClass('top-car');
        jQuery('.footer-cols-wrapper .footer-col').last().addClass('last');
        if(jQuery('.footer .facebook-fanbox')){ jQuery('.footer .footer-col').addClass('footer-col-ex')};
        jQuery('.input-box select, .input-box input, input.qty, .data-table textarea, .input-box textarea, .advanced-search .input-range input').not('input.radio, input.checkbox').addClass('form-control');
        if(jQuery('.new + .sale').each(function(index){
    	    jQuery(this).parent('.label-product').addClass('label-indent');
        }));

	    /*if (jQuery('.container').width() < 766) {
            if(jQuery(".my-account").length){
             jQuery('.my-account table td.order-id').prepend('<strong>Order No.:</strong>');
             jQuery('.my-account table td.order-date').prepend('<strong>Date: </strong>');
             jQuery('.my-account table td.order-ship').prepend('<strong>Recipient: </strong>');
             jQuery('.my-account table td.order-total').prepend('<strong>Order Total: </strong>');
             jQuery('.my-account table td.order-status').prepend('<strong>Status: </strong>');
             jQuery('.my-account table td.order-sku').prepend('<strong>SKU: </strong>');
             jQuery('.my-account table td.order-price').prepend('<strong>Price: </strong>');
             jQuery('.my-account table td.order-subtotal').prepend('<strong>Subtotal: </strong>');
             };

		    if(jQuery(".multiple-checkout").length){
			    jQuery('.multiple-checkout td.order-qty, .multiple-checkout th.order-qty').prepend('<strong>Qty: </strong>');
			    jQuery('.multiple-checkout td.order-shipping, .multiple-checkout th.order-shipping, ').prepend('<strong>Send To: </strong>');
			    jQuery('.multiple-checkout td.order-subtotal, .multiple-checkout th.order-subtotal').prepend('<strong>Subtotal: </strong>');
			    jQuery('.multiple-checkout td.order-price, .multiple-checkout th.order-price').prepend('<strong>Price: </strong>');
		    };
	    }*/

        /* Dashboard Menu */
        jQuery(".my-account .page-title,.awrma-account .page-title").on("click", function(){
            jQuery(".col-left .block-content").slideToggle('slow');
        });

        /* Dashboard Order */
        jQuery('.view-order-confirmation').on("click", function(){
            jQuery('.account-order-left').hide();
            jQuery(".account-order-right").show();
        });

        /* Rma Request */
        jQuery('.awrma-account-left-detail .view-order a').on("click", function(){
            jQuery('.awrma-account-left').hide();
            jQuery('.awrma-account-right .awrma-leave-message').hide();
            jQuery('.awrma-account-right .awrma-account-right-bottom').hide();
            jQuery(".awrma-account-right .awrma-account-right-top").show();
        });

		jQuery(function() {
		    //	Scrolled by user interaction
			if(jQuery(".up-sell-carousel").length){
				jQuery('.up-sell-carousel').carouFredSel({
					responsive: true,
					width: '100%',
					prev: '.carousel-prev',
					next: '.carousel-next',
					scroll: 1,
					auto	: {
			    		play	: 1,
				    	timeoutDuration :15000
				    },
					items: {
						visible: {
							min: 1,
							max: 3
						},
						width:260
					},
					mousewheel: true,
					swipe: {
						onMouse: false,
						onTouch: true
					}
				});
			};

			if(jQuery(".tumbSlider").length){
				jQuery('.tumbSlider').carouFredSel({
					responsive: true,
					width: '100%',
					width: 'auto',
					prev: '.tumbSlider-prev',
					next: '.tumbSlider-next',
					scroll: 1,
					auto	:false,
					items: {
						visible: {
							min: 1,
							max: 3
						},
						width:97
					},
					mousewheel: true,
					swipe: {
						onMouse: false,
						onTouch: true
					}
				});
			};

			if(jQuery(".slider-sidebar").length){
				jQuery('.slider-sidebar').carouFredSel({
					responsive: true,
					width: '100%',
					prev: '.slider-sidebar-prev',
					next: '.slider-sidebar-next',
					pagination:'.slider-sidebar-pager',
					scroll: 1,
					auto: {
			    		play: 1,
				    	timeoutDuration: 15000
				    },
					items: {
						visible: {
							min: 1,
							max: 1
						},
						width:270
					},
					mousewheel: true,
					swipe: {
						onMouse: true,
						onTouch: true
					}
				});
			};
		});
		if(jQuery("#gallery-swipe").length){
			jQuery('#gallery-swipe').bxSlider({
				pager:false,
				controls:true,
				minSlides: 1,
				maxSlides: 1,
				infiniteLoop:false,
				moveSlides:1
				});
		};

		if(jQuery("#gallery-swipe").length){
			var myPhotoSwipe = jQuery("#gallery-swipe a").photoSwipe({ enableMouseWheel: false , enableKeyboard: false, captionAndToolbarAutoHideDelay:0 });
		};

});

(function(doc) {
	var addEvent = 'addEventListener',
	    type = 'gesturestart',
	    qsa = 'querySelectorAll',
	    scales = [1, 1],
	    meta = qsa in doc ? doc[qsa]('meta[name=viewport]') : [];

	function fix() {
		meta.content = 'width=device-width,minimum-scale=' + scales[0] + ',maximum-scale=' + scales[1];
		doc.removeEventListener(type, fix, true);
	}

	if ((meta = meta[meta.length - 1]) && addEvent in doc) {
		fix();
		scales = [.25, 1.6];
		doc[addEvent](type, fix, true);
	}
}(document));

/**********  Footer Contact Us    ******/
jQuery(function(){
    jQuery(".contact_us").click(function(){
        jQuery("#bottom-contact-us").slideToggle("slow")
        return false;
    })
})



/*************    Mobile Menu   ****************/


jQuery(function(){
    jQuery(".swipe .sf-menu-phone li.level0 a.level-top").click(function(){
        var jQuerythis = jQuery(this).parent()
        var topmenu = jQuery(".top-icon-menu").height()
        if(jQuerythis.children(".menu-lists").length > 0){
            if(jQuerythis.children("strong").length > 0){
                var top = jQuerythis.offset().top;
                var top2 = jQuery(window).scrollTop()
                var top3 = jQuery(".swipe").scrollTop()
                jQuery(".swipe-menu").css("position","absolute").animate({"left":"-257px"},1)
                jQuery(".swipe-menu>a,.swipe-menu>form,.swipe-menu>ul,.swipe-menu>.language-list").hide()
                jQuerythis.children(".menu-lists").animate({"left":"240px"},500).css("top",(-(top - top2 - topmenu + top3) ) + "px").removeClass("no-display")
                jQuery(".icon-reorder").click(function(){
                    if(jQuery(".swipe-menu").css("left") == "-257px"){
                        jQuery(".swipe-menu").animate({"left":"0px"},1)
                        jQuery(".swipe-menu>a,.swipe-menu>form,.swipe-menu>ul,.swipe-menu>.language-list").show()
                    }
                })
                return false
                jQuery('.block-cart-header .cart-content').slideUp();
                jQuery(".icon-reorder").removeClass("active")
                jQuery(this).parents('body').removeClass('ind');
            }
        }
    })

    jQuery(".swipe-menu-bottom>div a").click(function(){
        var jQuerythis = jQuery(this).parent()
        var topmenu = jQuery(".top-icon-menu").height()
        if(jQuerythis.children(".menu-lists").length > 0){
            if(jQuerythis.children("strong").length > 0){
                var top = jQuerythis.offset().top;
                var top2 = jQuery(window).scrollTop()
                var top3 = jQuery(".swipe").scrollTop()
                jQuery(".swipe-menu").css("position","absolute").animate({"left":"-257px"},1)
                jQuerythis.children(".menu-lists").animate({"left":"240px"},500).removeClass("no-display")
                jQuery(".icon-reorder").click(function(){
                    if(jQuery(".swipe-menu").css("left") == "-257px"){
                        jQuery(".swipe-menu").animate({"left":"0px"},1)
                        jQuery(".swipe-menu>a,.swipe-menu>form,.swipe-menu>ul,.swipe-menu>.language-list").show()
                    }
                })
                return false;
                jQuery('.block-cart-header .cart-content').slideUp();
                jQuery(".icon-reorder").removeClass("active")
                jQuery(this).parents('body').removeClass('ind');
            }

        }
    })




    jQuery(".top-icon-menu .top-menu-search").click(function(){
        jQuery(".top-icon-menu .search-content").slideToggle(300)
    })
})


jQuery(function(){
    jQuery(".menu-lists-title").click(function(){
        jQuery(this).next("ul").slideToggle(300)
        jQuery(this).toggleClass("menu-lists-title-open")
    })

    jQuery(".icon-reorder").click(function(){
        if(jQuery(".swipe").css("position") == "static"){
            jQuery(".swipe").css({"position":"fixed","marginTop":"0px","marginLeft":"0px"})
            jQuery(".main-container").css({"position":"static"})
            jQuery(".nav-container").css("marginTop","0px")
        }
    })
})






    jQuery(function(){
        var width = jQuery(window).width();
//        jQuery(".sf-sortby-value").css("left",-(286 - width * 0.44))
        if(width < 687){
            jQuery(".products-grid .product_image").height("auto")
            if(width > 486){
                jQuery(".catalog-category-view .products-grid .item,.catalogsearch-advanced-result .products-grid .item,.flash-sale-list .products-grid .item").css("width","36%")
                jQuery(".catalog-category-view .product_image,.catalogsearch-advanced-result .product_image,.flash-sale-list .product_image").css("width","100%")
            }else if(width < 487){
                jQuery(".catalogsearch-advanced-result .products-grid .item,.catalogsearch-advanced-result .product_image").width((jQuery(".page-title").width() / 2 ) - 30)
                jQuery(".flash-sale-list .products-grid .item,.flash-sale-list .product_image").width((jQuery(".banner-title").width() / 2 ) - 30)
                jQuery(".catalog-category-view .products-grid .item,.catalog-category-view .product_image").width((jQuery(".sf-refine-name").width() / 2 ) - 30)
            }
        }else if(width > 687){
            jQuery(".catalog-category-view .product_image,.catalogsearch-advanced-result .product_image,.flash-sale-list .product_image").height(330)
            jQuery(".catalog-category-view .item,.catalogsearch-advanced-result .products-grid .item,.flash-sale-list .products-grid .item").css("width","220px")
        }

jQuery(window).resize(function(){
        var width = jQuery(window).width();
        jQuery(".sf-sortby-value").css("left",-(286 - width * 0.44))
        if(width < 687){
            var w = jQuery(".products-grid  .product_image").width()
            jQuery(".products-grid .product_image").height(w * 1.5)
            if(width > 486){
                jQuery(".catalog-category-view .products-grid .item,.catalogsearch-advanced-result .products-grid .item,.flash-sale-list .products-grid .item").css("width","36%")
                jQuery(".catalog-category-view .product_image,.catalogsearch-advanced-result .product_image,.flash-sale-list .product_image").css("width","100%")
            }else{
                jQuery(".catalogsearch-advanced-result .products-grid .item,.catalogsearch-advanced-result .product_image").width((jQuery(".page-title").width() / 2 ) - 20)
                jQuery(".flash-sale-list .products-grid .item,.flash-sale-list .product_image").width((jQuery(".banner-title").width() / 2 ) - 20)
                jQuery(".catalog-category-view .products-grid .item,.catalog-category-view .product_image").width((jQuery(".sf-refine-name").width() / 2 ) - 20)
            }
        }else{
            jQuery(".catalog-category-view .product_image,.catalogsearch-advanced-result .product_image,.flash-sale-list .product_image").height(330)
            jQuery(".catalog-category-view .item,.catalogsearch-advanced-result .products-grid .item,.flash-sale-list .products-grid .item").css("width","220px")
        }
    /* Menu */
    var li = jQuery(".page>.nav-container #nav.sf-menu>li")
    var n =li.length
    var sum = 0
    jQuery(".page>.nav-container #nav.sf-menu").find("li.level0").each(function(){
        sum += jQuery(this).width()
    })
    var padding = (1180 - sum) / (n * 2)
    li.css("padding-left",padding - 5).css("padding-right",padding - 5)



    if(width > 700){
        jQuery(".sf-menu-block").addClass("no-display")
        jQuery(".main-container").css("position","relative")
        jQuery(".nav-container").css("marginTop","0")
        jQuery(".top-icon-menu").addClass("no-display")
        jQuery(".nav-container .container").css("min-width","1180px")
        jQuery(".cms-index-index .slider .slides_container .slide img,.cms-index-index .slider .slides_control").css("height","600px")
    }else if(width < 700){
        jQuery(".main-container").css({"position":"static"})
        jQuery(".sf-menu-block").removeClass("no-display")
        jQuery(".top-icon-menu").removeClass("no-display")
    }
})
})


jQuery(function(){
    if(jQuery(window).width()>700){
        jQuery(".sf-menu-block").addClass("no-display")
        jQuery(".top-icon-menu").addClass("no-display")
    }else if (jQuery(window).width()<700){
        jQuery(".sf-menu-block").removeClass("no-display")
        jQuery(".top-icon-menu").removeClass("no-display")
    }
})

/****  Home Message   *****/
jQuery(function(){
    jQuery(".home_message_close").click(function(){
        jQuery(".home_top_message").animate({"marginTop":"-30px"},200)
    })
})



jQuery(function(){
    //for mobile
    jQuery(".ui-slider-handle").draggable({
        axis:"x"
    })
})


/******   header  login message   ********/
jQuery(function(){
    jQuery(".welcome .uline").click(function(){
        jQuery(".logged_content ul").slideToggle(300)
        jQuery(".welcome").toggleClass("welcomeactive")
    })
    jQuery(document).click(function(event){
        if(jQuery(event.target).parents(".welcome").length==0){
            jQuery(".logged_content ul").slideUp();
            jQuery(".welcome").removeClass("welcomeactive")
        }
    })
})


/*******  newcomer popup  *********/
jQuery(function(){
   // if(jQuery("#newcomer_popup")){
       jQuery(document).click(function(event){
           if(jQuery(event.target).parents("#fancybox-content").length == 0 ){
               jQuery.fancybox.close()
           }
        })
   // }
})
/*******   Nav    *******/
jQuery(function(){
    var li = jQuery(".page>.nav-container #nav.sf-menu>li")
    var n =li.length
    var sum = 0
    jQuery(".page>.nav-container #nav.sf-menu").find("li.level0").each(function(){
        sum += jQuery(this).width()
    })
    var padding = (1180 - sum) / (n * 2)
    li.css("padding-left",padding - 5).css("padding-right",padding - 5)
})



/*******   CMS-PAGE-HOW-TO-SHOP   ********/
jQuery(function(){
    jQuery(".cms-page-right h2").click(function(){
        jQuery(this).next(".cms-page-details").slideToggle(300)
        jQuery(this).toggleClass("cms-page-filter-open")
    })
})


/***   Judge the Mobile  *****/
jQuery(function() {
    var isMobile = {
        Android: function() {
            return navigator.userAgent.match(/Android/i) ? true : false;
        },
        BlackBerry: function() {
            return navigator.userAgent.match(/BlackBerry/i) ? true : false;
        },
        iOS: function() {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i) ? true : false;
        },
        Windows: function() {
            return navigator.userAgent.match(/IEMobile/i) ? true : false;
        },
        any: function() {
            return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Windows());
        }
    };
    if( isMobile.any() )
    {
        var width = jQuery(window).width()
        var height = jQuery(window).height()
        jQuery('meta[name=viewport]').attr('content','width=' + width + ', user-scalable=no');
        jQuery(".menu-back").click(function(){
            jQuery(".swipe .menu-lists").addClass("no-display")
            jQuery(".swipe-menu").animate({"left":"0px"},1).css({"position":"static","marginTop":"-10px","marginLeft":"10px","height":"690px"})
            jQuery(".swipe").css({"position":"static","marginLeft":"-20px","marginTop":"-30px","height":height})
            jQuery(".main-container").css({"position":"fixed"})
            jQuery(".swipe-menu>a,.swipe-menu>form,.swipe-menu>ul,.swipe-menu>.language-list").show()
            jQuery(".nav-container").css("marginTop","50px")
            jQuery(".page").css({"position":"fixed","height":height})
            jQuery(".swipe-menu-bottom>div .menu-lists").css({"position":"absolute","top":"50px"})
        })
        jQuery(".icon-reorder").click(function(){
            jQuery(".swipe-menu").css({"marginTop":"20px","marginLeft":0,"height":"auto"})
            if(!jQuery("body").hasClass("ind")){
                jQuery(".page").css({"position":"static","height":"auto"})
            }
        })
        jQuery(".swipe-menu-bottom>div a").click(function(){
            jQuery(".swipe-menu-bottom>div .menu-lists").css({"marginTop":"0px"})
        })


    }else{
        jQuery(".catalog-category-view .products-grid div.item,.catalogsearch-advanced-result .products-grid div.item,.flash-sale-list .products-grid div.item").live("mouseover",function(){
            jQuery(this).children(".product_style,.actions,.quick-view").show()
            jQuery(this).find("a.product-image img:first-child").hide()
            jQuery(this).find(".rotatingImage").show()
        })
        jQuery(".catalog-category-view .products-grid div.item,.catalogsearch-advanced-result .products-grid div.item,.flash-sale-list .products-grid div.item").live("mouseout",function(){
            jQuery(this).children(".product_style,.actions,.quick-view").hide()
            jQuery(this).find("a.product-image img:first-child").show()
            jQuery(this).find(".rotatingImage").hide()
        })
        jQuery(".menu-back").click(function(){
            jQuery(".swipe .menu-lists").addClass("no-display")
            jQuery(".swipe-menu").animate({"left":"0px"},1)
            jQuery(".swipe-menu>a,.swipe-menu>form,.swipe-menu>ul,.swipe-menu>.language-list").show()
        })
        jQuery(".swipe .sf-menu-phone li.level0 a.level-top,.swipe-menu-bottom>div a").click(function(){
            jQuery(".swipe").css({"position":"fixed","marginLeft":"0"})
            jQuery(".main-container").css("position","static")
        })
    }
});





/*******    Judge mobile or Pad     *****/
jQuery(function(){
    if(/AppleWebKit.*mobile/i.test(navigator.userAgent) || (/MIDP|SymbianOS|NOKIA|SAMSUNG|LG|NEC|TCL|Alcatel|BIRD|DBTEL|Dopod|PHILIPS|HAIER|LENOVO|MOT-|Nokia|SonyEricsson|SIE-|Amoi|ZTE/.test(navigator.userAgent)))
        { if(window.location.href.indexOf("?mobile")<0)
            {
                try{
                    if(/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)){
                        /*******    Mobile *********/


                    }else if(/iPad/i.test(navigator.userAgent)){
                        /*********    Tablet    *********/
                        jQuery(".catalog-category-view .products-grid div.item,.flash-sale-list .products-grid div.item").one("click",function(){
                            jQuery(this).children(".product_style,.actions,.quick-view").show()
                            jQuery(this).find("a.product-image img:first-child").hide()
                            jQuery(this).find(".rotatingImage").show()
                            return false
                        })
                    }
                }
                catch(e){}
            }
    }
})



/****** swipe bottom information   ********/
jQuery(function(){
    var url = window.location.href
    jQuery(".footer_swipe_content ul li a").each(function(){
        var jQuerythis = jQuery(this)
        if(jQuerythis.attr("href") == url){
            jQuery(this).addClass("swipe_info_active")
        }
    })
    jQuery(window).resize(function(){
        if(jQuery(window).width() > 700){
            jQuery(".footer_swipe_content ul li a").removeClass("swipe_info_active")
        }else if(jQuery(window).width() < 700){
            var url = window.location.href
            jQuery(".footer_swipe_content ul li a").each(function(){
                var jQuerythis = jQuery(this)
                if(jQuerythis.attr("href") == url){
                    jQuery(this).addClass("swipe_info_active")
                }
            })
        }
    })
})



/********   Shopping Cart   ********/
jQuery(function(){
    var height = jQuery(window).height()
    jQuery(".top-icon-menu #topCartContentSwipe").css("max-height",(height - 60))
})



/******     Product Details You May Also Like   ******/
jQuery(function() {
    var width = jQuery(".catalog-product-view #scroll-upsell .jcarousel>ul>li").width()
    jQuery(".catalog-product-view #scroll-upsell .jcarousel>ul>li,.catalog-product-view #scroll-upsell .jcarousel").height(width * 1.5)
    jQuery(window).resize(function(){
        var width = jQuery(".catalog-product-view #scroll-upsell .jcarousel>ul>li").width()
        jQuery(".catalog-product-view #scroll-upsell .jcarousel>ul>li,.catalog-product-view #scroll-upsell .jcarousel").height(width * 1.5)
    })
})


/* Product Details ------ Wear This With */
jQuery(function(){
    var width = jQuery(".product-view .product-detail-right .related .jcarousel>ul>li").width()
    jQuery(" .product-view .product-detail-right .related .jcarousel>ul>li, .product-view .product-detail-right .related .jcarousel").height(width * 1.5)
    jQuery(window).resize(function(){
        var width = jQuery(" .product-view .product-detail-right .related .jcarousel>ul>li").width()
        jQuery(" .product-view .product-detail-right .related .jcarousel>ul>li, .product-view .product-detail-right .related .jcarousel").height(width * 1.5)
    })
})


jQuery(function(){
    jQuery("#newsletter-validate-detail a.signup_btn").click(function(){
        jQuery.fancybox.close()
    })
})



/**** cmspage ---- contact us   ****/
jQuery(function(){
    jQuery(".leave-us-message").click(function(){
        jQuery(".cms-contact-us-html .cms-page-right").toggle()
    })
})


/********  Colors Watch    *******/
jQuery(function(){
    var width = jQuery(window).width()
        jQuery(".catalog-category-view .products-grid .item .product_style ul.options").each(function(){
            var jQuerythis = jQuery(this)
            var len = jQuerythis.find("li").length
            if (len > 4){
                jQuerythis.children("li:gt(3)").addClass("no-display")
                jQuerythis.children("li:last-child").removeClass("no-display")
            }
        })
})



/* Flash sale --- Refine */
jQuery(function(){
    jQuery(".flash-sale-list .sf-refine-name").live("click",function(){
        jQuery(".flash-sale-list .block-layered-nav .block-content").slideToggle("300");
    })
})



/* Tips */
jQuery(function(){
    jQuery(".tips-content .tips").click(function(){
        jQuery(this).siblings().toggleClass("no-display");
    })
})


/*******   CMS-PAGE-MEMBERSHIP-PRIVILEGES   ********/
jQuery(function(){
    jQuery(".cms-cc-club-membership-privileges-phone").click(function(){
        jQuery(this).parent().find(".cms-cc-club-membership-privileges-detail").slideToggle(300)
        jQuery(this).toggleClass("cms-cc-club-membership-privileges-phone-open")
    })
})


/* telecom country/area code*/
var D1 = D1 || {};
D1.teleJson = eval('({"HK":"852","MO":"853","MY":"60","SG":"65","TW":"886","TH":"66","CN":"86","JP":"81"})');
D1.telecomCode = {
    get:function(){
        if(jQuery('form#form-validate').length>0) this.form = jQuery('form#form-validate');
        if(jQuery('form#co-billing-form').length>0) this.form = jQuery('form#co-billing-form');
        if(jQuery('form#register-form').length>0) this.form = jQuery('form#register-form');
        this.country = this.form.find('#customer_country');
        this.sCountry = this.form.find('.field-country select');
        this.teleCode = this.form.find('#customer_area_code');
        if(jQuery('#area_code').length>0) this.sTeleCode = this.form.find('#area_code');
        if(jQuery('.billing_area_code').length>0) this.sTeleCode = this.form.find('.billing_area_code');
    },
    _ini:function(){
        var key;
        for (key in D1.teleJson){
            if(key == this.country.val()){
                this.teleCode.val(D1.teleJson[key]);
            }
            if(key == this.sCountry.val()){
                this.sTeleCode.val(D1.teleJson[key]);
            }
        }
    },
    bind:function(){
        var that = this,
            key,
            c;
        this.country.change(function(){
            c = jQuery(this).val();
            for (key in D1.teleJson){
                if(key == c){
                    that.teleCode.val(D1.teleJson[key]);
                    break;
                }
            }
        });
        this.sCountry.change(function(){
            c = jQuery(this).val();
            for (key in D1.teleJson){
                if(key == c){
                    that.sTeleCode.val(D1.teleJson[key]);
                    break;
                }
            }
        });
    },
    ini:function(){
        this.get();
        this._ini();
        this.bind();
    }
}
jQuery(function(){
    if((jQuery('form#form-validate').length>0) || (jQuery('form#co-billing-form').length>0) || (jQuery('form#register-form').length>0)) D1.telecomCode.ini();
});