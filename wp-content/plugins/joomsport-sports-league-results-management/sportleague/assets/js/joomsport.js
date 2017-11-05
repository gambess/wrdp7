function delCom(num){
    
        jQuery.post(
            'index.php?tmpl=component&option=com_joomsport&controller=users&task=del_comment&format=row&cid='+num,
            function( result ) { 
                if(result){
                    alert(result);
                } else {
                    var d = document.getElementById('divcomb_'+num).parentNode;
                    d.removeChild(jQuery('#divcomb_'+num).get(0));
                }
        });

}


function componentPopup(){
    var href = window.location.href;
    var regex = new RegExp("[&\\?]" + name + "=");
    
    if(href.indexOf("tmpl=component") > -1){
        window.print();
    }
    
    
    if(href.indexOf("?") > -1)
          var hrefnew = href + "&tmpl=component";
    else
          var hrefnew = href + "?tmpl=component";
    
    window.open(hrefnew,'jsmywindow','width=750,height=700,scrollbars=1,resizable=1');
    
}

function fSubmitwTab(e){
    if(jQuery('#joomsport-container').find('div.tabs').find('li.active').find('a').attr('href')){
        jQuery('input[name="jscurtab"]').val(jQuery('#joomsport-container').find('div.tabs').find('li.active').find('a').attr('href'));
    }
    e.form.submit();
}

jQuery(document).ready(function(){
   jQuery('#comForm').on('submit', function(e) {
    e.preventDefault();
        if(jQuery('#addcomm').val()){
            var submcom = jQuery('#submcom').get(0);
            //submcom.disabled = true;
            jQuery.ajax({
                url: jQuery('#comForm').attr('action'),                                                           
                type: "post",
                data: jQuery('#comForm').serialize(),
                success: function(result){
                    
                    if(result){		
                        result = JSON.parse(result);
                        if(result.error){
                            alert(result.error);
                        }else
                        if(result.id){
                            var li = jQuery("<li>");
                            li.attr("id", 'divcomb_'+result.id);
                            
                            
                            
                            var div = jQuery("<div>");
                            div.attr("class", "comments-box-inner");
                            var divInner = jQuery("<div>");
                            divInner.attr("class","jsOverflowHidden");
                            divInner.css("position", "relative");
                            divInner.appendTo(div);
                            jQuery('<div class="date">'+result.datetime+' '+result.delimg+'</div>').appendTo(divInner);
                            jQuery(result.photo).appendTo(divInner);
                            
                            jQuery('<h4 class="nickname">'+result.name+'</h4>').appendTo(divInner);
                            jQuery('<div class="jsCommentBox">'+result.posted+'</div>').appendTo(div);
                            div.appendTo(li);
                            li.appendTo("#all_comments");
                            //var allc = jQuery('#all_comments').get(0);
                            //allc.innerHTML = allc.innerHTML + result;
                            
                            
                            submcom.disabled = false;
                            jQuery('#addcomm').val('');
                        }

                    }
                    jQuery('#comForm').get(0).reset();
                }                                                            
             });
        }
    }); 
    jQuery('div[class^="knockplName knockHover"]').hover( 
        function(){
            var hclass = jQuery(this).attr("class");
            var tbody = jQuery(this).closest('tbody');
            
            tbody.find('[class^="knockplName knockHover"]').each(function(){
                if(jQuery(this).hasClass(hclass)){
                    jQuery(this).addClass("knIsHover");
                }
            });
            //console.log('div.'+hclass);
            //jQuery('div.'+hclass).addClass("knIsHover");
        },
        function(){
            var tbody = jQuery(this).closest('tbody');
            tbody.find('[class^="knockplName knockHover"]').each(function(){
                if(jQuery(this).hasClass("knIsHover")){
                    jQuery(this).removeClass("knIsHover");
                }
            });
        }
    );
    
    jQuery("#aSearchFieldset").on("click",function(){
        if(jQuery("#jsFilterMatches").css("display") == 'none'){
            jQuery("#jsFilterMatches").css("display","block");
        }else{
            jQuery("#jsFilterMatches").css("display","none");
        }
    });
    jQuery('#joomsport-container select').select2({minimumResultsForSearch: 20});
    
        var $select = jQuery('#mapformat select').select2();
    //console.log($select);
    $select.each(function(i,item){
      //console.log(item);
      jQuery(item).select2("destroy");
    });
    var jsDivwMinHg = 0;
    jQuery('.jsDivwMinHg').each(function(){
        if(jQuery(this).height() > jsDivwMinHg){
            jsDivwMinHg = jQuery(this).height();
        }
    })
    jQuery('.jsDivwMinHg').css('height', jsDivwMinHg);
    
    
});

jQuery(document).ready(function() {

  
    jQuery("body").tooltip(
            { 
                selector: '[data-toggle2=tooltip]',
                html:true
            });
    jQuery('body').on('focus',".jsdatefield", function(){
        jQuery(this).datepicker({ dateFormat: 'yy-mm-dd'});
   });

            
});
jQuery(function() {
    jQuery( '.jstooltip' ).tooltip({
        html:true,
      position: {
        my: "center bottom-20",
        at: "center top",
        using: function( position, feedback ) {
          jQuery( this ).css( position );
          jQuery( "<div>" )
            .addClass( "arrow" )
            .addClass( feedback.vertical )
            .addClass( feedback.horizontal )
            .appendTo( this );
        }
      }
    });
  });

jQuery(window).on('load',function() {
    var maxwidth = 200;
    var maxheight = 200;
    var maxheightWC = 200;
    
    var divwidth = jQuery('#jsPlayerListContainer').parent().width();
    var cols = Math.floor(parseInt(divwidth)/255);
    if(!cols){
        cols = 1;
    }
    
    var widthCols = Math.round(100/cols);
    var widthColsPix = Math.round(divwidth/cols);
    
    jQuery('.jsplayerCart').css({'width': widthCols+'%'});
    //jQuery('.jsplayerCart').width(parseInt(widthCols)+'%');
    
    jQuery('.imgPlayerCart').each(function(){
        //console.log(jQuery(this).find('img').prop('naturalHeight'));
        if(jQuery(this).find('img').prop('naturalWidth') > maxwidth){
            maxwidth = jQuery(this).find('img').prop('naturalWidth');
        }
        var widthNatural = parseInt(jQuery(this).find('img').prop('naturalWidth'));
        if(widthNatural < widthColsPix){
            coeff = 1;
        }else{
            if(widthNatural > 0){
                var coeff = (widthColsPix/(widthNatural+32));
            }else{
                coeff = 1;
            }
        }
        
        if(jQuery(this).find('img').prop('naturalHeight') > maxheight){
            maxheight = jQuery(this).find('img').prop('naturalHeight');
            maxheightWC = maxheight*coeff;
            console.log(widthColsPix+':'+widthNatural);
            console.log(maxheight+':'+coeff+':'+maxheightWC);
        }
    });
    maxheightWC = maxheightWC - 10;
    console.log(maxheightWC);
    //jQuery('.imgPlayerCart').width(maxwidth);
    jQuery('.imgPlayerCart').height(maxheightWC);
    jQuery('.imgPlayerCart > .innerjsplayerCart').height(maxheightWC);
    jQuery('.imgPlayerCart > .innerjsplayerCart').css({'line-height':maxheightWC+'px'});
    
    
});

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
    jQuery('#joomsport-container .page-content-js .tabs > ul.nav').each( function() {
        var jstabsul = jQuery(this).width();
        var jstabsli = jQuery(this).find('li');
        var jstabssum = 0;
        jstabsli.each(function(){ jstabssum+=jQuery(this).innerWidth(); });
        if (jstabssum > jstabsul) {jstabsli.addClass('jsmintab');}
    });
    
}

jQuery(window).on('load',JSwindowSize);

jQuery('#jsMatchViewID .tabs .jsPaddingBottom30 .jsInline').ready( function() {
    var highestBox = 0;
    jQuery('#jsMatchViewID .tabs .jsPaddingBottom30 .jsInline > div ', this).each(function(){
        if(jQuery(this).height() > highestBox) { highestBox = jQuery(this).height(); }
    });
    jQuery('#jsMatchViewID .tabs .jsPaddingBottom30 .jsInline > div ',this).height(highestBox);
});