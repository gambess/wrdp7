if(typeof JSwindowSize !== 'function'){
    function jsToggleTH() {
        jQuery('table[id^="jstable_"] th').each( function(){
            var alternate = true;
            jQuery(this).click(function() {
                jQuery(this).find("span").each(function() {
                    if (alternate) { var shrtname = jQuery(this).attr("jsattr-full"); var text = jQuery(this).text(shrtname); } else { var shrtname = jQuery(this).attr("jsattr-short"); var text = jQuery(this).text(shrtname); }
                });
                alternate = !alternate;
            });	
        });
    }
    function JSwindowSize() {
        jQuery('table[id^="jstable_"]').each( function() {
            var conths = jQuery(this).parent().width();
            var thswdth = jQuery(this).find('th');
            var scrlths = 0;
            thswdth.each(function(){ scrlths+=jQuery(this).innerWidth(); });
            jQuery(this).find("span").each(function() {
                if (scrlths > conths) { var shrtname = jQuery(this).attr("jsattr-short"); var text = jQuery(this).text(shrtname).addClass("short"); return jsToggleTH(); }
            });
        });
    }
    jQuery(window).on('load',JSwindowSize);
}