jQuery( document ).ready(function($) {
	
	// Initialize slick slider
	$( '.sp_wpspwpost_slider' ).each(function( index ) {
        
		var slider_id   = $(this).attr('id');
		var slider_conf = $.parseJSON( $(this).parent('.wpspw-slider-wrp').find('.wpspw-slider-conf').text() );

		if( typeof(slider_id) != 'undefined' && slider_id != '' ) {

				var blogdesign          = parseInt(slider_conf.slides_column);
				var slides_to_scroll    = parseInt(slider_conf.slides_scroll);

                // Slider responsive breakpoints
                var slider_res = [{
                	breakpoint: 1023,
                	settings: {
                		slidesToShow: (blogdesign > 3) ? 3 : blogdesign,
                		slidesToScroll: 1,
                		infinite: true,
                		dots: false
                	}
                },{
                	breakpoint: 768,
                	settings: {
                		slidesToShow: (blogdesign > 2) ? 2 : blogdesign,
                		slidesToScroll: 1
                	}
                },
                {
                	breakpoint: 479,
                	settings: {
                		slidesToShow: 1,
                		slidesToScroll: 1,
                		dots: false
                	}
                },
                {
                	breakpoint: 319,
                	settings: {
                		slidesToShow: 1,
                		slidesToScroll: 1,
                		dots: false
                	}
                }]

            jQuery('#'+slider_id).slick({
            	dots            : (slider_conf.dots) == "true" ? true : false,
            	infinite        : true,
            	arrows          : (slider_conf.arrows) == "true" ? true : false,
            	speed           : parseInt(slider_conf.speed),
            	autoplay        : (slider_conf.autoplay) == "true" ? true : false,
            	autoplaySpeed   : parseInt(slider_conf.autoplay_interval),
            	slidesToShow    : blogdesign,
            	slidesToScroll  : slides_to_scroll,
            	rtl             : (Bdpw.is_rtl == "1") ? true : false,
            	mobileFirst     : (Bdpw.is_mobile == "1") ? true : false,
            	responsive      : slider_res
            });
        }
    });
});