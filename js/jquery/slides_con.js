// JavaScript Document

		
		$(window).load(function(){			
			if($("#main .bot_space .touchcarousel").length)
				$("#main .bot_space .touchcarousel").iosSlider({
					snapToChildren: true,
					desktopClickDrag: true,
					onSliderLoaded: sliderLoaded,
					onSlideChange: slideChange,
					infiniteSlider: true,
					autoSlide:true,
					autoSlideTransTimer:800,
					navSlideSelector: '.slideSelectors .item'
				});
			function sliderLoaded(args) {
				
				slideChange(args);
				
			}
			function slideChange(args) {
			
				$('.slideSelectors .item').removeClass('selected');
				$('.slideSelectors .item:eq(' + (args.currentSlideNumber-1) + ')').addClass('selected');
				
			}
			
			
			if($("#inner-slider .touchcarousel").legnth)	
				$("#inner-slider .touchcarousel").iosSlider({
					snapToChildren: true,
					desktopClickDrag: true,
					infiniteSlider: true,
					navNextSelector: $('#inner-slider .next'),
					navPrevSelector: $('#inner-slider .prev')
				});
				
		});
	