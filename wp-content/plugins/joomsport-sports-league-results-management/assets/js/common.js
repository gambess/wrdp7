// TABS
jQuery( document ).ready(function() {
    jQuery('ul.mgl-tabs').show();
    jQuery('div.panel-wrap').each(function(){
            jQuery(this).find('div.panel:not(:first)').hide();
    });
    jQuery('ul.mgl-tabs a').click(function(){
            var panel_wrap =  jQuery(this).closest('div.panel-wrap');
            jQuery('ul.mgl-tabs li', panel_wrap).removeClass('active');
            jQuery(this).parent().addClass('active');
            jQuery('div.panel', panel_wrap).hide();
            jQuery( jQuery(this).attr('href') ).show();
            return false;
    });
    jQuery('ul.mgl-tabs li:visible').eq(0).find('a').click();
});    

jQuery( document ).ready(function() {

    jQuery('div.mgl-panel-wrap').each(function(){
            jQuery(this).find('div.panel:not(:first)').hide();
    });
    jQuery('.mgl-tabs-big a').click(function(){
            var panel_wrap =  jQuery(this).closest('div.mgl-panel-wrap');
            jQuery('.mgl-tabs-big a').removeClass('nav-tab-active');
            jQuery(this).addClass('nav-tab-active');
            jQuery('div.mgl-panel-wrap div.panel').hide();
            jQuery( jQuery(this).attr('href') ).show();
            return false;
    });

});    

jQuery(document).ready(function(){
    
    
    jQuery("body.post-type-joomsport_season .wrap .page-title-action").on("click",function(e){
        e.preventDefault();
        jQuery("<div></div>").attr('id','jsTournSelect').appendTo('body');  
        var addnew = jQuery(this);
        var data = {
        'action': 'season_tournamentmodal',
        };

        jQuery.post(ajaxurl, data, function(response) {

           jQuery( "#jsTournSelect" ).html(response);

        });
        jQuery( "#jsTournSelect" ).dialog({modal: true,height: 250,width:450,
            buttons: {
              Next: function() {
                if(jQuery('#joomsport_tournament_modal_id').val()){
                    jQuery( this ).dialog( "close" );
                    
                    location.href = addnew.attr('href') + '&tid='+jQuery('#joomsport_tournament_modal_id').val() + '&iscomplex='+jQuery('input[name="joomsport_season_container"]:checked').val();
                }   
                
              }
            }


        });
        
    });
    
    jQuery('#mglMatchDay .mgl-add-button').click(function(){
        

        var tbl = jQuery("#mglMatchDay tbody");
        
        //check for mistakes
        if(jQuery('select[name=set_home_team] :selected').val() == '0' || jQuery('select[name=set_away_team] :selected').val() == '0'){
            alert('Select participant');
            return false;
        }
        if(jQuery('select[name=set_home_team] :selected').val() == jQuery('select[name=set_away_team] :selected').val()){
            alert('Select another participant');
            return false;
        }
        jQuery('#modalAj').show();
        
        var formdata = jQuery('#edittag').serialize();
        //console.log(formdata);
        var data = {
            'action': 'mday_savematch',
            'formdata': formdata,
        };

        jQuery.post(ajaxurl, data, function(res) {
            resObj = JSON.parse(res);
            if(resObj.error){
                alert(resObj.error);
                return;
            }
            var thisNewField = jQuery('#mglMatchDay tfoot').children('tr').clone();


            var td0 = '<a href="javascript:void(0);" onclick="javascript:(this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode));"><i class="fa fa-trash" aria-hidden="true"></i></a><input type="hidden" name="match_id[]" value="'+res+'" />';
            var td1 = jQuery('select[name=set_home_team] :selected').text() + '<input type="hidden" name="home_team[]" value="'+jQuery('select[name=set_home_team] :selected').val()+'" />';
            var td3 = jQuery('select[name=set_away_team] :selected').text() + '<input type="hidden" name="away_team[]" value="'+jQuery('select[name=set_away_team] :selected').val()+'" />';

            var td2 = '<input type="number" name="home_score[]" class="mglScore jsNumberNotNegative" value="' + jQuery('#set_score_home').val() + '" />:<input type="number" name="away_score[]" class="mglScore jsNumberNotNegative" value="' + jQuery('#set_score_away').val() + '" />';
            thisNewField.children('td:eq(0)').html(td0);
            var tdIndex = 1;

            if(jQuery('#js_groupid_add').val() !== undefined){

                var clone = jQuery('#js_groupid_add').clone();
                clone.prop({id:"", name:"group_id[]"});
                clone.val(jQuery('#js_groupid_add').val());
                thisNewField.children('td:eq('+tdIndex+')').html(clone);
                tdIndex++;
            }

            thisNewField.children('td:eq('+tdIndex+')').html(td1);
            tdIndex++;
            thisNewField.children('td:eq('+tdIndex+')').html(td2);
            tdIndex++;
            thisNewField.children('td:eq('+tdIndex+')').html(td3);
            tdIndex++;

            if(jQuery('#extra_timez').val() !== undefined){
                var clone = jQuery('#extra_timez').clone();
                clone.prop({id:"", name:"extra_time[]"});
                //clone.val(jQuery('#extra_timez').val());
                thisNewField.children('td:eq('+tdIndex+')').html(clone);
                tdIndex++;
            }
            if(jQuery('#m_played_foot').val() !== undefined){

                var clone = jQuery('#m_played_foot').clone();
                clone.prop({id:"", name:"m_played[]"});
                clone.val(jQuery('#m_played_foot').val());
                thisNewField.children('td:eq('+tdIndex+')').html(clone);
                tdIndex++;
            }
            if(jQuery('#m_date_foot').val() !== undefined){

                var clone = jQuery('#m_date_foot').clone();
                clone.prop({id:"", name:"m_date[]"});
                clone.val(jQuery('#m_date_foot').val());
                thisNewField.children('td:eq('+tdIndex+')').html(clone);
                jQuery('#m_date_foot').val('');

                tdIndex++;
            }
            if(jQuery('#m_time_foot').val() !== undefined){

                var clone = jQuery('#m_time_foot').clone();
                clone.prop({id:"", name:"m_time[]"});
                clone.val(jQuery('#m_time_foot').val());
                thisNewField.children('td:eq('+tdIndex+')').html(clone);
                jQuery('#m_time_foot').val('');
                tdIndex++;
            }
            if(jQuery('#venue_id_foot').val() !== undefined){

                var clone = jQuery('#venue_id_foot').clone();
                clone.prop({id:"", name:"venue_id[]"});
                clone.val(jQuery('#venue_id_foot').val());
                thisNewField.children('td:eq('+tdIndex+')').html(clone);
                jQuery('#venue_id_foot').val('0');
                tdIndex++;
            }

            jQuery('input[name^="jscef"]').each(function(){
                var efid = jQuery(this).val();
                var efname = 'ef_foot_'+efid;
                var clone = jQuery('#'+efname).clone();
                clone.prop({id:"", name:"ef_"+efid+"[]"});
                clone.val(jQuery('#'+efname).val());
                thisNewField.children('td:eq('+tdIndex+')').html(clone);
                jQuery('#'+efname).val('');
                tdIndex++;
            })

            thisNewField.children('td:eq('+tdIndex+')').html('<a href="post.php?post='+res+'&amp;action=edit"><input type="button" class="button" value="Details"></a>');
            jQuery("#mglMatchDay tbody").append(thisNewField);

            //set to default
            jQuery('#mglMatchDay tfoot input[type=text]').val('');
            jQuery('#mglMatchDay tfoot input[type=number]').val('');
            jQuery('#mglMatchDay tfoot select').val('0');
        }); 
        //recalcPartic();
        jQuery('#modalAj').hide();
    });


});
    

function jsFormMDVal(){
    jQuery("#season_id_inp").val(jQuery("#season_id").val());
    jQuery("#season_id").closest( '.form-invalid' ).removeClass( 'form-invalid' );;
}
function shide(){
    if(jQuery('select[name="field_type"]').val() == 3){
            jQuery("#seltable").show();
    }else{
            jQuery("#seltable").hide();
    }
    
    if(jQuery('select[name="field_type"]').val() == 5){
            jQuery(".jsw_personcat_ef").show();
    }else{
            jQuery(".jsw_personcat_ef").hide();
    }
    if(jQuery('select[name="field_type"]').val() == 6){
            jQuery(".jsw_dateage_ef").show();
    }else{
            jQuery(".jsw_dateage_ef").hide();
    }
    
    if(jQuery('select[name="field_type"]').val() == 5 && jQuery('select[name="type"]').val() == 1){
            jQuery(".jsw_personroster_ef").show();
    }else{
            jQuery(".jsw_personroster_ef").hide();
    }
    
}
function tblview_hide(){

    if(jQuery('select[name="type"]').val() < 2){
            jQuery("#tbl_fv_1").css('visibility','visible');
            jQuery("#tbl_fv_2").css('visibility','visible');
            jQuery("#tbl_seasr_1").css('visibility','visible');
            jQuery("#tbl_seasr_2").css('visibility','visible');
    }else{
            jQuery("#tbl_fv_1").css('visibility','hidden');
            jQuery("#tbl_fv_2").css('visibility','hidden');
            jQuery("#tbl_seasr_1").css('visibility','hidden');
            jQuery("#tbl_seasr_2").css('visibility','hidden');
    }
    if(jQuery('select[name="type"]').val() == 2){
            jQuery("#tbl_fv_11").css('display','table-cell');
            jQuery("#tbl_fv_12").css('display','table-cell');
           
    }else{
            jQuery("#tbl_fv_11").css('display','none');
            jQuery("#tbl_fv_12").css('display','none');
            
    }
    if(jQuery('select[name="type"]').val() == '0'){
        jQuery(".pllistdiv").show();
    }else{
        jQuery(".pllistdiv").hide();
    }   
    
    if(jQuery('select[name="field_type"]').val() == 5 && jQuery('select[name="type"]').val() == 1){
            jQuery(".jsw_personroster_ef").show();
    }else{
            jQuery(".jsw_personroster_ef").hide();
    }

}
function add_selval(){
    if(!jQuery("#addsel").val()){
            return false;
    }
    jQuery("#seltable>tbody").append('<tr class="ui-state-default"><td class="jsdadicon"><i class="fa fa-bars" aria-hidden="true"></i></td><td class="jsdadicondel"><input type="hidden" name="adeslid[]" value="0" /><a href="javascript:void(0);" title="Remove" onClick="javascript:delJoomSportSelRow(this);"><input type="hidden" value="0" name="selid[]" /><i class="fa fa-trash" aria-hidden="true"></i></a></td><td><input type="text" name="selnames[]" value="'+jQuery("#addsel").val()+'" /></td></tr>');
    jQuery("#addsel").val('');
}
function delJoomSportSelRow(element) {
        var del_index = element.parentNode.parentNode;
        del_index.parentNode.removeChild(del_index);

}
		
jQuery( document ).ready(function() {
    jQuery("#seltable>tbody").sortable(

    );

    //jQuery( "#seltable>tbody" ).disableSelection();
    jQuery("#id_column_seas").sortable(

    );
    jQuery("#jsGroupList").sortable(

    );
    
    
    
    if(jQuery('input[type="radio"][name="equalpts_chk"]').is(':checked') && jQuery('input[type="radio"][name="equalpts_chk"]:checked').val() == '1'){
        jQuery("#divrankingsbox").hide();
        jQuery("#divcririadescr").show();

    }else{
        jQuery("#divrankingsbox").show();
        jQuery("#divcririadescr").hide();
    }
    jQuery('input[type="radio"][name="equalpts_chk"]').on('click', function(){

        if(jQuery(this).is(':checked') && jQuery(this).val() == '1'){
            jQuery("#divrankingsbox").hide();
            jQuery("#divcririadescr").show();
            
        }else{
            jQuery("#divrankingsbox").show();
            jQuery("#divcririadescr").hide();
        }
    });

    if(jQuery('input[type="radio"][name="s_reg"]').is(':checked') && jQuery('input[type="radio"][name="s_reg"]:checked').val() == '1'){
        jQuery("#partRegDiv").show();
    }else{
        jQuery("#partRegDiv").hide();
    }
    if(jQuery('input[type="radio"][name="s_reg_to"]:checked').val() == '1'){
        jQuery(".dependonilmit").css("display","inline-block");
    }else{
        jQuery(".dependonilmit").hide();
    }
    jQuery('input[type="radio"][name="s_reg"]').on('click', function(){

        if(jQuery(this).is(':checked') && jQuery(this).val() == '1'){
            jQuery("#partRegDiv").show();
        }else{
            jQuery("#partRegDiv").hide();
        }
    });
    jQuery('input[type="radio"][name="s_reg_to"]').on('click', function(){

        if(jQuery(this).is(':checked') && jQuery(this).val() == '1'){
            jQuery(".dependonilmit").css("display","inline-block");
        }else{
            jQuery(".dependonilmit").hide();
        }
    });
    
    if(jQuery('input[type="radio"][name="jmscore[new_points]"]').is(':checked') && jQuery('input[type="radio"][name="jmscore[new_points]"]:checked').val() == '1'){
        jQuery(".jshideonNP").show();
    }else{
        jQuery(".jshideonNP").hide();
    }
    jQuery('input[type="radio"][name="jmscore[new_points]"]').on('click', function(){
        if(jQuery(this).is(':checked') && jQuery(this).val() == '1'){
            jQuery(".jshideonNP").show();
        }else{
            jQuery(".jshideonNP").hide();
        }
    });
    if(jQuery('input[type="radio"][name="is_extra"]').is(':checked') && jQuery('input[type="radio"][name="is_extra"]:checked').val() == '1'){
        jQuery(".js_match_et_addit").show();
    }else{
        jQuery(".js_match_et_addit").hide();
    }
    jQuery('input[type="radio"][name="is_extra"]').on('click', function(){
        if(jQuery(this).is(':checked') && jQuery(this).val() == '1'){
            jQuery(".js_match_et_addit").show();
        }else{
            jQuery(".js_match_et_addit").hide();
        }
    });
    if(jQuery('input[type="radio"][name="layouts[enbl_teamlinks]"]').is(':checked') && jQuery('input[type="radio"][name="layouts[enbl_teamlinks]"]:checked').val() == '1'){
        jQuery(".hdn_div_enblink").hide();
    }else{
        jQuery(".hdn_div_enblink").show();
    }
    jQuery('input[type="radio"][name="layouts[enbl_teamlinks]"]').on('click', function(){
        if(jQuery(this).is(':checked') && jQuery(this).val() == '1'){
            jQuery(".hdn_div_enblink").hide();
        }else{
            jQuery(".hdn_div_enblink").show();
        }
    });
    
    

});
function calctpfun(){
    
        if(jQuery('input[name="player_event"]:checked').val() == '1'){
                jQuery("#calctp").show();
                jQuery("#calctp_es").show();
                
        }else{
                jQuery("#calctp").hide();
                jQuery("#calctp_es").hide();
        }
        

}
function calcenblsumfun(){
    if(jQuery('input[name="events_sum"]:checked').val() == '1'){
            jQuery('.displ_subevents').show();
    }else{
            jQuery('.displ_subevents').hide();
    }
}
function showopt(){
        if(jQuery('input[name="s_enbl_extra"]:checked').val() == '1'){
                jQuery('#extraoptions').show();
        }else{
                jQuery('#extraoptions').hide();
        }
}

function add_colors(){
    var cell = document.createElement("div");
    cell.className = 'jscolordivcont';
    colors_count = Math.random();;
    var input_hidden = document.createElement("input");
    input_hidden.type = "text";
    input_hidden.name = 'color_field[]';
    input_hidden.id = 'input_field_'+colors_count;
    input_hidden.value = '';
    input_hidden.className = 'jscolorinp';
    input_hidden.size = 9;
    input_hidden.style.width = '100px';
    var input_hidden2 = document.createElement("input");
    input_hidden2.type = "text";
    input_hidden2.id = 'sample_'+colors_count;
    input_hidden2.value = '';
    input_hidden2.size = 1;
    input_hidden2.style.width = '30px';
    var input_hidden3 = document.createElement("input");
    input_hidden3.type = "text";
    input_hidden3.name = 'place[]';
    input_hidden3.value = '';
    input_hidden3.size = 5;
    input_hidden3.style.width = '30px';
    
    var input_hidden4 = document.createElement("input");
    input_hidden4.type = "text";
    input_hidden4.name = 'legend[]';

    input_hidden4.value = '';

    input_hidden4.style.width = '100px';
                        
			

    cell.innerHTML = '<input class="button" type="button" style="cursor:pointer;" onclick="showColorGrid2(\'input_field_'+colors_count+'\',\'sample_'+colors_count+'\');" value="..." class="color-kind">&nbsp;';

    var txtnode2 = document.createTextNode(" Place  ");
    var txtnode4 = document.createTextNode(" Legend  ");
    cell.appendChild(input_hidden);
    cell.appendChild(input_hidden2);
    cell.appendChild(txtnode2);

    cell.appendChild(input_hidden3);
    cell.appendChild(txtnode4);
    cell.appendChild(input_hidden4);
    jQuery('#app_newcol').append(cell);

}
jQuery(document).ready( function(){
    jQuery("body").on("click",".jsfw-enable",function(){
        var parent = jQuery(this).parents('.jsw_switch');
        jQuery('.jsfw-disable',parent).removeClass('selected');
        jQuery('.jsfw-enable',parent).removeClass('selected');
        jQuery(this).addClass('selected');
        jQuery('.checkbox',parent).attr('checked', true);
    });
    jQuery("body").on("click",".jsfw-disable",function(){
        var parent = jQuery(this).parents('.jsw_switch');
        jQuery('.jsfw-enable',parent).removeClass('selected');
        jQuery(this).addClass('selected');
        jQuery('.checkbox',parent).attr('checked', false);
    });
    
    jQuery(".jswf-chosen-select").chosen({disable_search_threshold: 10,width: "95%",disable_search:false});
    

    jQuery('.jscheckall').on('click', function(){
        
        var parent = jQuery(this).parent();

        var chk = parent.find('input[type="radio"][value="1"]');
        chk.each(function(){
            
            jQuery(this).prop("checked", true);
            jQuery(this).trigger('change');
            var inpid = jQuery(this).attr('id');
            var label = jQuery('label[for='+inpid+']');

			var input = jQuery('#' + label.attr('for'));
                        
                        //console.log(input.prop('checked'));
			//if (!input.prop('checked')) {
                            //console.log(input.val());
				label.closest('.jsw_switch').find('label').removeClass('selected');
				if (input.val() == '') {
					label.addClass('selected');
				} else if (input.val() == 0) {
					label.addClass('selected');
				} else if (input.val() == '2'){
                                    
                                        label.addClass('selected');
                                        
				}else{
					label.addClass('selected');
				}
				input.prop('checked', true);
				input.trigger('change');
			//}
            
        });
        getSubsLists('squadradio1');
        getSubsLists('squadradio2');
        
    })
    jQuery('.jscheckallnot').on('click', function(){
        
        var parent = jQuery(this).parent();

        var chk = parent.find('input[type="radio"][value="0"]');

        chk.each(function(){
            
            jQuery(this).prop("checked", true);
            jQuery(this).trigger('change');
            var inpid = jQuery(this).attr('id');
            var label = jQuery('label[for='+inpid+']');
            
			var input = jQuery('#' + label.attr('for'));
                        
                        //console.log(input.prop('checked'));
			//if (!input.prop('checked')) {
                            //console.log(input.val());
				label.closest('.jsw_switch').find('label').removeClass('selected');
				if (input.val() == '') {
					label.addClass('selected');
				} else if (input.val() == 0) {
					label.addClass('selected');
				} else if (input.val() == '2'){
                                    
                                        label.addClass('selected');
                                        
				}else{
					label.addClass('selected');
				}
				input.prop('checked', true);
				input.trigger('change');
			//}
            
        })
        getSubsLists('squadradio1');
        getSubsLists('squadradio2');
        
    });
    


   jQuery('body').on('click', '.jsaddtblscode', function(){

        var shortcode = '[jsStandings';
        
        shortcode += ' id ="'+jQuery('select[name="season_id"]').val()+'"';
        
        if(jQuery("#jsshrtgroup_id").val() && jQuery("#jsshrtgroup_id").val() != '0'){
            shortcode += ' group_id ="'+jQuery('#jsshrtgroup_id').val()+'"';
        }
        if(jQuery("#partic_id").val() && jQuery("#partic_id").val() != '0'){
            shortcode += ' partic_id ="'+jQuery('#partic_id').val()+'"';
        }
        
        if(jQuery("#jsshrtcplace").val() && jQuery("#jsshrtcplace").val() != '0'){
            shortcode += ' place ="'+jQuery('#jsshrtcplace').val()+'"';
        }
        if(jQuery("#jsshrtcolumns").val() && jQuery("#jsshrtcolumns").val() != '0'){
            var cols = jQuery("#jsshrtcolumns").val().join(";");
            
            shortcode += ' columns ="'+cols+'"';
        }
        
        shortcode += ']';

        // Send the shortcode to the editor
        window.send_to_editor( shortcode );
   });
   jQuery('body').on('click', '.jsaddmatchesscode', function(){

        var shortcode = '[jsMatches';
        
        shortcode += ' id ="'+jQuery('select[name="season_id"]').val()+'"';
        
        if(jQuery("#jsshrtgroup_id").val() && jQuery("#jsshrtgroup_id").val() != '0'){
            shortcode += ' group_id="'+jQuery('#jsshrtgroup_id').val()+'"';
        }
        if(jQuery("#partic_id").val() && jQuery("#partic_id").val() != '0'){
            shortcode += ' partic_id="'+jQuery('#partic_id').val()+'"';
        }
        
        if(jQuery("#jsshrtcquantity").val() && jQuery("#jsshrtcquantity").val() != '0'){
            shortcode += ' quantity="'+jQuery('#jsshrtcquantity').val()+'"';
        }
        
        if(jQuery("#jsshrtcodematchtype").val() && jQuery("#jsshrtcodematchtype").val() != '0'){
            shortcode += ' matchtype="'+jQuery('#jsshrtcodematchtype').val()+'"';
        }
        if(jQuery("input[name='display_embl']:checked").val()){
            shortcode += ' emblems="'+jQuery("input[name='display_embl']:checked").val()+'"';
        }
        if(jQuery("input[name='display_venue']:checked").val() ){
            shortcode += ' venue="'+jQuery("input[name='display_venue']:checked").val()+'"';
        }
        if(jQuery("input[name='display_seasname']:checked").val()){
            shortcode += ' season="'+jQuery("input[name='display_seasname']:checked").val()+'"';
        }
        if(jQuery("input[name='display_slider']:checked").val()){
            shortcode += ' slider="'+jQuery("input[name='display_slider']:checked").val()+'"';
        }
        if(jQuery("input[name='display_layout']:checked").val()){
            shortcode += ' layout="'+jQuery("input[name='display_layout']:checked").val()+'"';
        }
        if(jQuery("input[name='display_grbymd']:checked").val()){
            shortcode += ' groupbymd="'+jQuery("input[name='display_grbymd']:checked").val()+'"';
        }
        if(jQuery("input[name='display_order']:checked").val()){
            shortcode += ' morder="'+jQuery("input[name='display_order']:checked").val()+'"';
        }
        if(jQuery("input[name='drange_past']").val()){
            shortcode += ' drange_past="'+jQuery("input[name='drange_past']").val()+'"';
        }
        if(jQuery("input[name='drange_today']:checked").val()){
            shortcode += ' drange_today="'+jQuery("input[name='drange_today']:checked").val()+'"';
        }
        if(jQuery("input[name='drange_future']").val()){
            shortcode += ' drange_future="'+jQuery("input[name='drange_future']").val()+'"';
        }
        
        shortcode += ']';

        // Send the shortcode to the editor
        window.send_to_editor( shortcode );
   });
   jQuery('body').on('click', '.jsaddplayerscode', function(){

        var shortcode = '[jsPlayerStat';
        
        shortcode += ' id ="'+jQuery('select[name="season_id"]').val()+'"';
        
        if(jQuery("#jsshrtgroup_id").val() && jQuery("#jsshrtgroup_id").val() != '0'){
            shortcode += ' group_id="'+jQuery('#jsshrtgroup_id').val()+'"';
        }
        if(jQuery("#partic_id").val() && jQuery("#partic_id").val() != '0'){
            shortcode += ' partic_id="'+jQuery('#partic_id').val()+'"';
        }
        if(jQuery("#jsshrtcodeevid").val() && jQuery("#jsshrtcodeevid").val() != '0'){
            shortcode += ' event="'+jQuery('#jsshrtcodeevid').val()+'"';
        }
        
        
        if(jQuery("#jsshrtcquantity").val() && jQuery("#jsshrtcquantity").val() != '0'){
            shortcode += ' quantity="'+jQuery('#jsshrtcquantity').val()+'"';
        }

        if(jQuery("input[name^='display_embl']:checked").val()){
            shortcode += ' photo="'+jQuery("input[name^='display_embl']:checked").val()+'"';
        }
        if(jQuery("input[name^='display_teamname']:checked").val() ){
            shortcode += ' teamname="'+jQuery("input[name^='display_teamname']:checked").val()+'"';
        }
        

        
        shortcode += ']';

        // Send the shortcode to the editor
        window.send_to_editor( shortcode );
   });
   
   jQuery('body').on('click', '.jsaddmatchdaycode', function(){
        if(jQuery('#matchday_id').val()){
            var shortcode = '[jsMatchDayStat';

            shortcode += ' matchday_id ="'+jQuery('#matchday_id').val()+'"';

            shortcode += ']';

            // Send the shortcode to the editor
            window.send_to_editor( shortcode );
        }
   });
   
   jQuery('body').on('click', '.jsaddplayerlistcode', function(){
        if(jQuery('#partic_id').val() && jQuery('#partic_id').val()!='0'){
            var shortcode = '[jsMatchPlayerList';
            shortcode += ' season_id ="'+jQuery('#jsshrtcodesid').val()+'"';
            shortcode += ' team_id ="'+jQuery('#partic_id').val()+'"';
            shortcode += ' pview ="'+jQuery('input[name="pview"]:checked').val()+'"';
            shortcode += ' pgroup ="'+jQuery('select[name="pgroup"]').val()+'"';
            shortcode += ']';

            // Send the shortcode to the editor
            window.send_to_editor( shortcode );
        }else{
            alert('Season and Team need to be specified');
        }
   });
   
   jQuery('body').on('change', '#jsshrtcodesid', function(){

        var data = {
                'action': 'joomsport_group_shortcode',
                'season_id': jQuery(this).val()
        };

        jQuery.post(ajaxurl, data, function(response) {
            
            var res = jQuery.parseJSON( response );
            jQuery('#jsstandgroup').html(res.groups);
            jQuery('#jsstandpartic').html(res.partic);
        });
   });
   jQuery('body').on('change', '#jsshrtcodesidmd', function(){

        var data = {
                'action': 'joomsport_matchdaylist_shortcode',
                'season_id': jQuery(this).val()
        };

        jQuery.post(ajaxurl, data, function(response) {
            
            var res = jQuery.parseJSON( response );
            jQuery('#jsmatchdayseason').html(res.mday);
        });
   });
   jQuery('body').on('change', '#jsshrtgroup_id', function(){

        var data = {
                'action': 'joomsport_grouppart_shortcode',
                'season_id': jQuery('#jsshrtcodesid').val(),
                'group_id': jQuery(this).val()
        };

        jQuery.post(ajaxurl, data, function(response) {
            jQuery('#jsstandpartic').html(response);
        });
   });
   
   jQuery('body').on('change', '.jsshrtcodesid', function(){
        var parEnt = jQuery(this).closest( ".JSshrtPop" );
        var data = {
                'action': 'joomsport_group_shortcode',
                'season_id': jQuery(this).val()
        };
        var selname = jQuery(this).attr('name');
        jQuery.post(ajaxurl, data, function(response) {
            
            var res = jQuery.parseJSON( response );
            parEnt.find('.jsstandgroup').html(res.groups);
            parEnt.find('.jsstandpartic').html(res.partic);
            var part = parEnt.find('.jspartic_id');
            var newpart = selname.replace('season_id','partic_id');
            part.attr("name",newpart);
            part.attr("id","");
            
            var gr = parEnt.find(".jsshrtgroup_id");
            var newpart1 = selname.replace('season_id','group_id');
            gr.attr("name",newpart1);
            gr.attr("id","");
        });
   });
   jQuery('body').on('change', '.jsshrtgroup_id', function(){
       var parEnt = jQuery(this).closest( ".JSshrtPop" );
        var data = {
                'action': 'joomsport_grouppart_shortcode',
                'season_id': parEnt.find('.jsshrtcodesid').val(),
                'group_id': jQuery(this).val()
        };

        jQuery.post(ajaxurl, data, function(response) {
            parEnt.find('.jsstandpartic').html(response);
            var part = parEnt.find('.jspartic_id');
            var newpart = selname.replace('season_id','partic_id');
            part.attr("name",newpart);
            part.attr("id","");
        });
   });
   jQuery('body').on('focus',".jsdatefield", function(){
        jQuery(this).datepicker({ dateFormat: 'yy-mm-dd'});
   });
   jQuery('body').on('change', 'input[name="js_demotype"]', function(){
        
        var data = {
                'action': 'joomsport_demo_ttype',
                'ttype': jQuery(this).val()
        };
        
        jQuery.post(ajaxurl, data, function(response) {
            
            
        });
   });
    
    
});


function bl_add_event(){
    var cur_event = document.getElementsByName('event_id')[0];

    //var e_count = getObj('e_count').value;
    var e_minutes = document.getElementById('e_minutes').value;
    var e_player = document.getElementById('playerz_id');
    var re_count = document.getElementById('re_count').value;
    if (cur_event.value == 0) {
            alert("Select event");return;
    }
    if (e_player.value == 0) {
            alert("Select player");return;
    }

    var tbl_elem = document.getElementById('new_events');
    var row = tbl_elem.insertRow(tbl_elem.rows.length);
    row.className = 'ui-state-default';
    var cell0 = document.createElement("td");
    var cell1 = document.createElement("td");
    var cell2 = document.createElement("td");
    var cell3 = document.createElement("td");
    var cell4 = document.createElement("td");
    var cell5 = document.createElement("td");
    var cell6 = document.createElement("td");
    var cell7 = document.createElement("td");
    var cell8 = document.createElement("td");///

    cell0.innerHTML = '<span class="sortable-handler" style="cursor: move;"><span class="icon-menu"></span></span>';
    var input_hidden = document.createElement("input");
    input_hidden.type = "hidden";
    input_hidden.name = "em_id[]";
    input_hidden.value = 0;
    cell1.innerHTML = '<a href="javascript: void(0);" onClick="javascript:delJoomSportSelRow(this); return false;" title="Delete"><i class="fa fa-trash" aria-hidden="true"></i></a>';
    cell1.appendChild(input_hidden);

    var input_hidden = document.createElement("input");
    input_hidden.type = "hidden";
    input_hidden.name = "new_eventid[]";
    input_hidden.value = cur_event.value;
    cell2.innerHTML = cur_event.options[cur_event.selectedIndex].text;
    cell2.appendChild(input_hidden);


    var input_hidden = document.createElement("input");
    input_hidden.type = "text";
    input_hidden.name = "e_minuteval[]";
    input_hidden.value = e_minutes;
    //cell4.innerHTML = e_minutes;
    input_hidden.setAttribute("maxlength",5);
    input_hidden.setAttribute("size",5);
    input_hidden.style.width = '60px';
    input_hidden.className = 'jsNumberEventMinutes';

    cell4.appendChild(input_hidden);

    var input_player = document.createElement("input");
    input_player.type = "hidden";
    input_player.name = "new_player[]";
    input_player.value = e_player.value;
    if(e_player.value != 0){
            cell5.innerHTML = e_player.options[e_player.selectedIndex].text;
    }	
    cell5.appendChild(input_player);
    var input_hidden = document.createElement("input");
    input_hidden.type = "number";
    input_hidden.name = "e_countval[]";
    input_hidden.value = re_count;
    //cell4.innerHTML = e_minutes;
    input_hidden.setAttribute("maxlength",5);
    input_hidden.setAttribute("size",5);
input_hidden.style.width = '60px';

    cell6.appendChild(input_hidden); 

    row.appendChild(cell0);
    row.appendChild(cell1);
    row.appendChild(cell2);
    row.appendChild(cell5);
    row.appendChild(cell4);
    row.appendChild(cell6);
    row.appendChild(cell7);
    row.appendChild(cell8);
    document.getElementsByName('event_id')[0].value =  0;
    document.getElementById('playerz_id').value =  0;
    document.getElementById('e_minutes').value = '';
    document.getElementById('re_count').value =  1;


}
function js_add_subs(tblid,pl2,pl1,minutes){
    var tbl_elem = document.getElementById(tblid);
    if(document.getElementById(pl1).value == document.getElementById(pl2).value || document.getElementById(pl1).value == 0 ){
            return false;
    }
    var row = tbl_elem.insertRow(tbl_elem.rows.length - 1);
    var cell1 = document.createElement("td");
    var cell2 = document.createElement("td");
    var cell3 = document.createElement("td");
    var cell4 = document.createElement("td");
    var cell5 = document.createElement("td");


    cell1.innerHTML = '<a href="javascript: void(0);" onClick="javascript:delJoomSportSelRow(this);getSubsLists(\'squadradio1\');getSubsLists(\'squadradio2\'); return false;" title="Delete"><i class="fa fa-trash" aria-hidden="true"></i></a>';

    var input_hidden = document.createElement("input");
    input_hidden.type = "hidden";
    input_hidden.name = pl1+"_arr[]";
    input_hidden.value = document.getElementById(pl1).value;
    cell2.innerHTML = document.getElementById(pl1).options[document.getElementById(pl1).selectedIndex].text;
    cell2.appendChild(input_hidden);
    var input_hidden = document.createElement("input");
    input_hidden.type = "hidden";
    input_hidden.name = pl2+"_arr[]";
    input_hidden.value = document.getElementById(pl2).value;
    if(document.getElementById(pl2).value != 0){
        cell3.innerHTML = document.getElementById(pl2).options[document.getElementById(pl2).selectedIndex].text;
    }else{
        cell3.innerHTML = '';
    }
    cell3.appendChild(input_hidden);

    var input_hidden = document.createElement("input");
    input_hidden.type = "number";
    input_hidden.style.width = "50px";
    input_hidden.name = minutes+"_arr[]";
    input_hidden.value = document.getElementById(minutes).value;
    input_hidden.setAttribute("maxlength",5);
    input_hidden.setAttribute("size",5);
    cell4.appendChild(input_hidden);

    row.appendChild(cell1);
    row.appendChild(cell3);
    row.appendChild(cell2);

    row.appendChild(cell4);
    row.appendChild(cell5);

    document.getElementById(minutes).value =  0;
    getSubsLists('squadradio1');
    getSubsLists('squadradio2');
}

jQuery(document).ready(function(){
    var team1full = jQuery('#set_home_team option');
    var team2full = jQuery('#set_away_team option');

    jQuery('#set_home_team').on("change", function(){
        var team2 = jQuery('#set_away_team').val();
        jQuery('#set_away_team').html('');
        for(i=0;i<team2full.length;i++){
            var selected = team2full[i].value == team2 ? ' selected="selected"' : '';
            jQuery('#set_away_team').append('<option value="'+team2full[i].value+'" '+selected+'>'+team2full[i].text+'</option>');
        }    
        
        if(jQuery(this).val() != 0){
            jQuery("#set_away_team option[value='"+jQuery(this).val()+"']").remove();
        }    
        jQuery('#set_away_team').trigger("liszt:updated");
    });
    jQuery('#set_away_team').on("change", function(){
        var team1 = jQuery('#set_home_team').val();
        jQuery('#set_home_team').html('');
        for(i=0;i<team1full.length;i++){
            var selected = team1full[i].value == team1 ? ' selected="selected"' : '';
            jQuery('#set_home_team').append('<option value="'+team1full[i].value+'" '+selected+'>'+team1full[i].text+'</option>');
        }
        if(jQuery(this).val() != 0){
            jQuery("#set_home_team option[value='"+jQuery(this).val()+"']").remove();
        }    
        jQuery('#set_home_team').trigger("liszt:updated");
    });
    
    //js_selpartic
    var partfull = jQuery('#js_selpartic_0_0 option');
    var partfull_cur = [];
    function recalcPartic(){
        var partfull_cur = [];
        for(i=0;i<partfull.length;i++){
            var cur = partfull[i].value;
            var exist = 0;
            //console.log(partfull[i].value);
            jQuery('select.js_selpartic').each(function(){
                //console.log(jQuery(this).val());
                if(jQuery(this).val() == cur){
                    exist = 1;
                }
            });
            if(exist == 0 || cur < 1){
                partfull_cur.push(partfull[i].value);
            }
        };
        jQuery('select.js_selpartic').each(function(){
            
            var teamV = jQuery(this).val();
            
            jQuery(this).html('');
            for(i=0;i<partfull.length;i++){
                if(jQuery.inArray(partfull[i].value, partfull_cur) != -1 || teamV == partfull[i].value){
                    var selected = (partfull[i].value == teamV) ? ' selected="selected"' : '';
                    jQuery(this).append('<option value="'+partfull[i].value+'" '+selected+'>'+partfull[i].text+'</option>');
                }

            }
            
        });
        jQuery('select.js_selpartic').trigger("liszt:updated");
    }
    recalcPartic();

    jQuery('select.js_selpartic').on("change", function(){
        
        var team2 = jQuery(this).val();
        if(team2 == 0 || team2 == -1){
            return;
        }
        recalcPartic();
    });
    
    
});


//knockout
jQuery(document).ready(function(){
    jQuery(".jsproceednext").on("click", function(){
        var tdc = jQuery(this).closest("td");
        var intA = parseInt(tdc.attr("data-game"));
        var intB = parseInt(tdc.attr("data-level"));
        var home = tdc.find('.js_selpartichome');
        var away = tdc.find('.js_selparticaway');
        var is_final = jQuery(this).hasClass("jsknockfinal");
        if(intB == 0){
            var homeText = tdc.find('.js_selpartichome option:selected').text();
            var awayText = tdc.find('.js_selparticaway option:selected').text();
        }else{
            var homeText = jQuery("#knocktd_"+intA+"_"+(intB)).find(".knocktop .knwinner").html();
            var awayText = jQuery("#knocktd_"+intA+"_"+(intB)).find(".knockbot .knwinner").html();
            
        }
        var homeScore = tdc.find('.mglScoreHome');
        var awayScore = tdc.find('.mglScoreAway');
       
        if(home.val() != '0' && away.val() != '0'){
            if((home.val() == '-1' && away.val() != 0) || (home.val() != 0 && away.val() == '-1')){
                if(home.val() == '-1'){
                    var winner = awayText;
                    var winnerID = parseInt(away.val());
                }
                if(away.val() == '-1'){
                    var winner = homeText;
                    var winnerID = parseInt(home.val());
                }
                
                if(jQuery("#knocktd_"+intA+"_"+(intB+1)).length){
                    jQuery("#knocktd_"+intA+"_"+(intB+1)).find(".knocktop .knockplName").html('');
                    jQuery("#knocktd_"+intA+"_"+(intB+1)).find(".knocktop .knockplName").append("<div class='knwinner'>"+winner+"</div>");
                    jQuery("#knocktd_"+intA+"_"+(intB+1)).find(".knocktop .knockplName").append('<input type="hidden" class="js_selpartichome" name="set_home_team_'+intA+'_'+(intB+1)+'" value="'+parseInt(winnerID)+'">');
                }else{
                    jQuery("#knocktd_"+(intA - Math.pow(2,intB))+"_"+(intB+1)).find(".knockbot .knockplName").html('');
                    jQuery("#knocktd_"+(intA - Math.pow(2,intB))+"_"+(intB+1)).find(".knockbot .knockplName").append("<div class='knwinner'>"+winner+"</div>"); 
                    jQuery("#knocktd_"+(intA - Math.pow(2,intB))+"_"+(intB+1)).find(".knockbot .knockplName").append('<input type="hidden" class="js_selparticaway" name="set_away_team_'+(intA - Math.pow(2,intB))+'_'+(intB+1)+'" value="'+parseInt(winnerID)+'">');
                }
            }else
            if(homeScore.val() != '' && awayScore.val() != ''){
                var homewin = 0;
                var awaywin = 0;
                
                for(var i=0; i<homeScore.length;i++){
                    //console.log(awayScore[i]);
                    if(awayScore[i]){
                        if(awayScore[i].value > homeScore[i].value){
                            awaywin++;
                        }else
                        if(awayScore[i].value < homeScore[i].value){
                            homewin++;
                        }    
                    }
                }
                
                var winner = (homewin > awaywin) ? homeText : awayText;
                var winnerID = (homewin > awaywin) ? home.val() : away.val();
                
                if(homewin == awaywin){
                    jQuery( "#jsknock-selectwinner" ).html('<select id="jsselectw"><option value="0">'+homeText+'</option><option value="1">'+awayText+'</option></select>');
                    jQuery( "#jsknock-selectwinner" ).dialog({
                        modal: true,
                        buttons: {
                          Ok: function() {
                            jQuery( this ).dialog( "close" );
                            if(jQuery("#jsselectw").val() == '0'){
                                winner = homeText;
                                winnerID = home.val();
                                if(is_final){
                                    jsknockSetWinner(home);
                                }
                            }else if(jQuery("#jsselectw").val() == '1'){
                                winner = awayText;
                                winnerID = away.val();
                                if(is_final){
                                    jsknockSetWinner(away);
                                }
                            }
                            if(jQuery("#knocktd_"+intA+"_"+(intB+1)).length){
                                jQuery("#knocktd_"+intA+"_"+(intB+1)).find(".knocktop .knockplName").html('');
                                jQuery("#knocktd_"+intA+"_"+(intB+1)).find(".knocktop .knockplName").append("<div class='knwinner'>"+winner+"</div>");
                                jQuery("#knocktd_"+intA+"_"+(intB+1)).find(".knocktop .knockplName").append('<input type="hidden" class="js_selpartichome" name="set_home_team_'+intA+'_'+(intB+1)+'" value="'+parseInt(winnerID)+'">');
                            }else{
                                jQuery("#knocktd_"+(intA - Math.pow(2,intB))+"_"+(intB+1)).find(".knockbot .knockplName").html('');
                                jQuery("#knocktd_"+(intA - Math.pow(2,intB))+"_"+(intB+1)).find(".knockbot .knockplName").append("<div class='knwinner'>"+winner+"</div>"); 
                                jQuery("#knocktd_"+(intA - Math.pow(2,intB))+"_"+(intB+1)).find(".knockbot .knockplName").append('<input type="hidden" class="js_selparticaway" name="set_away_team_'+(intA - Math.pow(2,intB))+'_'+(intB+1)+'" value="'+parseInt(winnerID)+'">');
                            }
                          }
                        }
                      });
                    
                }else{
                    if(jQuery("#knocktd_"+intA+"_"+(intB+1)).length){
                        jQuery("#knocktd_"+intA+"_"+(intB+1)).find(".knocktop .knockplName").html('');
                        jQuery("#knocktd_"+intA+"_"+(intB+1)).find(".knocktop .knockplName").append("<div class='knwinner'>"+winner+"</div>");
                        jQuery("#knocktd_"+intA+"_"+(intB+1)).find(".knocktop .knockplName").append('<input type="hidden" class="js_selpartichome" name="set_home_team_'+intA+'_'+(intB+1)+'" value="'+parseInt(winnerID)+'">');
                    }else{
                        jQuery("#knocktd_"+(intA - Math.pow(2,intB))+"_"+(intB+1)).find(".knockbot .knockplName").html('');
                        jQuery("#knocktd_"+(intA - Math.pow(2,intB))+"_"+(intB+1)).find(".knockbot .knockplName").append("<div class='knwinner'>"+winner+"</div>"); 
                        jQuery("#knocktd_"+(intA - Math.pow(2,intB))+"_"+(intB+1)).find(".knockbot .knockplName").append('<input type="hidden" class="js_selparticaway" name="set_away_team_'+(intA - Math.pow(2,intB))+'_'+(intB+1)+'" value="'+parseInt(winnerID)+'">');
                    }
                    if(is_final){
                        if(winnerID == home.val()){
                            jsknockSetWinner(home);
                        }else{
                            jsknockSetWinner(away);
                        }
                    }
                }
                
                
            } 
        }
        chkKnockIcons();
    });
    function jsknockSetWinner(Obj){
        jQuery("#jsknock_winnerid").val(Obj.val());
        var parentObj = Obj.parent();
        jQuery('.jsknockwinnerDiv').remove();
        parentObj.append('<div class="jsknockwinnerDiv"></div>');
    }
    
    
    function chkKnockIcons(){
        jQuery(".jsproceednext").hide();
        jQuery(".jsmatchconf").hide();
        
        jQuery("#jsKnockTableBe td").each(function(){
            var tdc = jQuery(this);
            var home = tdc.find('.js_selpartichome');
            var away = tdc.find('.js_selparticaway');

            var homeScore = tdc.find('.mglScoreHome');
            var awayScore = tdc.find('.mglScoreAway');
            //console.log(home);
            if((home.val() > '0' && away.val() > '0') || home.length == '0' || away.length == '0'){
                tdc.find(".jsmatchconf").show();
            }
            
            if(home.val() != '0' && away.val() != '0'){
                if((home.val() == '-1' && away.val() != 0 && away.val() ) || (home.val() != 0 && away.val() == '-1' && home.val())){
                    tdc.find(".jsproceednext").show();
                }
                else if(homeScore.val() != '' && awayScore.val() != ''){
                    tdc.find(".jsproceednext").show();
                } 
            }   
            
        });
    }
    jQuery("#jsKnockTableBe").on("change",".mglScore", function(){
        chkKnockIcons();
    });
    
    
    jQuery(".js_selpartic").on('change',function(){
        chkKnockIcons();
    });
    
    chkKnockIcons();
    
    
    function JSKN_recheckConf(td){
        var intA = 0;
        td.find('.jsmatchconf2').each(function(){
            jQuery(this).attr('data-index',intA);
            intA++;
        });
    }
    
    jQuery("body").on('click', '.jsmatchconf2', function(){
        jQuery('#modalAj').show();
        var tdc = jQuery(this).closest("td");
        var intA = parseInt(tdc.attr("data-game"));
        var intB = parseInt(tdc.attr("data-level"));
        var formdata = jQuery('#edittag').serialize();
        var di = parseInt(jQuery(this).attr("data-index"));
        //console.log(formdata);
        var data = {
            'action': 'mday_saveknock',
            'formdata': formdata,
            'yLevel' : intA,
            'xLevel' : intB,
            'dIndex' : di
        };
        jQuery.post(ajaxurl, data, function(res) {
            jQuery('#modalAj').hide();
            if(res){
                location.href = 'post.php?post='+res+'&action=edit';
            }
        });
    });
    
    jQuery('#JSMD_matchday_type').on("change",function(){
       if(jQuery(this).val() == '1'){
           jQuery("#jsknock_only").show();
       }else{
           jQuery("#jsknock_only").hide();
       }
    });
    
    
    jQuery(".jsknockadd").on("click", function(){
        var tdc = jQuery(this).closest("td");
        var intA = parseInt(tdc.attr("data-game"));
        var intB = parseInt(tdc.attr("data-level"));
        var homeDIV = jQuery("#knocktd_"+intA+"_"+(intB)).find(".knocktop .knockscore");
        var awayDIV = jQuery("#knocktd_"+intA+"_"+(intB)).find(".knockbot .knockscore");
        
        var maximum = 0;

        jQuery("#knocktd_"+intA+"_"+(intB)).find('.knockscoreItem').each(function() {
            
          var value = parseFloat(jQuery(this).attr('data-index'));
          maximum = (value > maximum) ? value : maximum;
        });
        maximum++;
        
        var htmlHome = '<div class="knockscoreItem" data-index="'+maximum+'"><input type="text" class="mglScore mglScoreHome" value="" name="set_home_score_'+intA+'_'+intB+'[]" size="3" maxlength="3" /><input type="hidden" name="match_id_'+intA+'_'+intB+'[]" value="" /><i class="fa fa-cog jsmatchconf2" aria-hidden="true"></i><i class="jsknockdel fa fa-minus-square" aria-hidden="true"></i></div>';
        var htmlAway = '<div class="knockscoreItem" data-index="'+maximum+'"><input type="text" class="mglScore mglScoreAway" value="" name="set_away_score_'+intA+'_'+intB+'[]" size="3" maxlength="3" /></div>';
        
        homeDIV.append(htmlHome);
        awayDIV.append(htmlAway);
        JSKN_recheckConf(tdc);
    });
    
    jQuery("body").on("mouseover", ".knockscoreItem", function(){
        if(jQuery(this).find("i.jsmatchconf2").css("display") == "none"){
            var curI = jQuery(this);
            jQuery('.knockscoreItem').each(function(){
                if(curI !== jQuery(this)){
                    jQuery(this).find("i").hide('slow');
                }

            });
            var ccc = jQuery(this).closest('td.even').find('i.jsmatchconf2').length;
            if(ccc > 1){
                jQuery(this).find("i.jsknockdel").show('slow');
            }
            jQuery(this).find("i.jsmatchconf2").show('slow');
        }
        
        //jQuery(this).find("i").delay(5000).fadeIn();
    });
    jQuery("body").on("click", ".fa-minus-square", function(){
        var ind = jQuery(this).closest('.knockscoreItem').attr('data-index');
        var td = jQuery(this).closest('td.even');
        td.find('.knockscoreItem[data-index="'+ind+'"]').remove();
        JSKN_recheckConf(td);
    });
    
    jQuery(".jsknchange").on("click",function(){
        var div = jQuery(this).closest('div.jstable');
        
        div.children('div.jstable-row').each(function() {
            var hm = jQuery(this).find('.jsSpanHome').html();
            jQuery(this).find('.jsSpanHome').html(jQuery(this).find('.jsSpanAway').html());
            jQuery(this).find('.jsSpanAway').html(hm);

            var hmScore = jQuery(this).find('.jsSpanHomeScore').html();
            jQuery(this).find('.jsSpanHomeScore').html(jQuery(this).find('.jsSpanAwayScore').html());
            jQuery(this).find('.jsSpanAwayScore').html(hmScore);
            
            var hmval = jQuery(this).find('.jsScrHmV').val();
            var awval = jQuery(this).find('.jsScrAwV').val();
            jQuery(this).find('.jsScrHmV').val(awval);
            jQuery(this).find('.jsScrAwV').val(hmval);
        });    
        
        
    });
    getSubsLists('squadradio1');
    getSubsLists('squadradio2');
    jQuery('.jsgetcheckedSubs').on('click',function(){
        getSubsLists('squadradio1');
        getSubsLists('squadradio2');
    });
});
jQuery(document).ready(function(){
    jQuery("body").delegate('.jsNumberNotNegative', 'focusout', function(){
        if(jQuery(this).val() < 0){
            jQuery(this).val('0');
        }
    });
});

function extractNumber(obj, decimalPlaces, allowNegative)
{
	var temp = obj.value;
	
	// avoid changing things if already formatted correctly
	var reg0Str = '[0-9]*';
	if (decimalPlaces > 0) {
		reg0Str += '\\.?[0-9]{0,' + decimalPlaces + '}';
	} else if (decimalPlaces < 0) {
		reg0Str += '\\.?[0-9]*';
	}
	reg0Str = allowNegative ? '^-?' + reg0Str : '^' + reg0Str;
	reg0Str = reg0Str + '$';
	var reg0 = new RegExp(reg0Str);
	if (reg0.test(temp)) return true;

	// first replace all non numbers
	var reg1Str = '[^0-9' + (decimalPlaces != 0 ? '.' : '') + (allowNegative ? '-' : '') + ']';
	var reg1 = new RegExp(reg1Str, 'g');
	temp = temp.replace(reg1, '');

	if (allowNegative) {
		// replace extra negative
		var hasNegative = temp.length > 0 && temp.charAt(0) == '-';
		var reg2 = /-/g;
		temp = temp.replace(reg2, '');
		if (hasNegative) temp = '-' + temp;
	}
	
	if (decimalPlaces != 0) {
		var reg3 = /\./g;
		var reg3Array = reg3.exec(temp);
		if (reg3Array != null) {
			// keep only first occurrence of .
			//  and the number of places specified by decimalPlaces or the entire string if decimalPlaces < 0
			var reg3Right = temp.substring(reg3Array.index + reg3Array[0].length);
			reg3Right = reg3Right.replace(reg3, '');
			reg3Right = decimalPlaces > 0 ? reg3Right.substring(0, decimalPlaces) : reg3Right;
			temp = temp.substring(0,reg3Array.index) + '.' + reg3Right;
		}
	}
	
	obj.value = temp;
}
function extractNumber2(obj, decimalPlaces, allowNegative)
{
	var temp = obj.value;
	
	// avoid changing things if already formatted correctly
	var reg0Str = '[0-9,-]*';
	if (decimalPlaces > 0) {
		reg0Str += '\\.?[0-9]{0,' + decimalPlaces + '}';
	} else if (decimalPlaces < 0) {
		reg0Str += '\\.?[0-9]*';
	}
	reg0Str = allowNegative ? '^-?' + reg0Str : '^' + reg0Str;
	reg0Str = reg0Str + '$';
	var reg0 = new RegExp(reg0Str);
	if (reg0.test(temp)) return true;

	// first replace all non numbers
	var reg1Str = '[^0-9,-]';
	var reg1 = new RegExp(reg1Str, 'g');
	temp = temp.replace(reg1, '');

	if (allowNegative) {
		// replace extra negative
		var hasNegative = temp.length > 0 && temp.charAt(0) == '-';
		var reg2 = /-/g;
		temp = temp.replace(reg2, '');
		if (hasNegative) temp = '-' + temp;
	}
	
	if (decimalPlaces != 0) {
		var reg3 = /\./g;
		var reg3Array = reg3.exec(temp);
		if (reg3Array != null) {
			// keep only first occurrence of .
			//  and the number of places specified by decimalPlaces or the entire string if decimalPlaces < 0
			var reg3Right = temp.substring(reg3Array.index + reg3Array[0].length);
			reg3Right = reg3Right.replace(reg3, '');
			reg3Right = decimalPlaces > 0 ? reg3Right.substring(0, decimalPlaces) : reg3Right;
			temp = temp.substring(0,reg3Array.index) + '.' + reg3Right;
		}
	}
	
	obj.value = temp;
}
function blockNonNumbers(obj, e, allowDecimal, allowNegative)
{
	var key;
	var isCtrl = false;
	var keychar;
	var reg;
		
	if(window.event) {
		key = e.keyCode;
		isCtrl = window.event.ctrlKey
	}
	else if(e.which) {
		key = e.which;
		isCtrl = e.ctrlKey;
	}
	
	if (isNaN(key)) return true;
	
	keychar = String.fromCharCode(key);
	
	// check for backspace or delete, or if Ctrl was pressed
	if (key == 8 || isCtrl)
	{
		return true;
	}

	reg = /\d/;
	var isFirstN = allowNegative ? keychar == '-' && obj.value.indexOf('-') == -1 : false;
	var isFirstD = allowDecimal ? keychar == '.' && obj.value.indexOf('.') == -1 : false;
	
	return isFirstN || isFirstD || reg.test(keychar);
}
function blockNonNumbers2(obj, e, allowDecimal, allowNegative)
{
	var key;
	var isCtrl = false;
	var keychar;
	var reg;
		
	if(window.event) {
		key = e.keyCode;
		isCtrl = window.event.ctrlKey
	}
	else if(e.which) {
		key = e.which;
		isCtrl = e.ctrlKey;
	}
	
	if (isNaN(key)) return true;
	
	keychar = String.fromCharCode(key);
	
	// check for backspace or delete, or if Ctrl was pressed
	if (key == 8 || isCtrl || keychar == '-' || keychar == ',')
	{
		return true;
	}

	reg = /\d/;
	var isFirstN = allowNegative ? keychar == '-' && obj.value.indexOf('-') == -1 : false;
	var isFirstD = allowDecimal ? keychar == '.' && obj.value.indexOf('.') == -1 : false;
	
	return isFirstN || isFirstD || reg.test(keychar);
}
function disableEnterKey(e)
{
	 var key;
	 if(window.event)
		  key = window.event.keyCode;     //IE
	 else
		  key = e.which;     //firefox
	 if(key == 13)
		  return false;
	 else
		  return true;
}

jQuery(document).on('keydown', '[data-inputboxtype="float"]', function (e) {
    // Allow: backspace, delete, tab, escape, enter and . and -
    if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 109, 189, 45]) !== -1 ||
        // Allow: Ctrl+A
        (e.keyCode == 65 && e.ctrlKey === true) ||
        // Allow: home, end, left, right, down, up
        (e.keyCode >= 35 && e.keyCode <= 40)) {
        // let it happen, don't do anything
        return true;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
        return false;
    }
    return true;
});

function getSubsLists(labelId){
        if(labelId.substring(10,11) == '1'){
            parseSubsList('players_team1_in', 'players_team1_out', 'new_squard1', 't1_squard');
        }else if(labelId.substring(10,11) == '2'){
            
            parseSubsList('players_team2_in', 'players_team2_out', 'new_squard2', 't2_squard');
        }
    }
    
    
    function parseSubsList(subsin, subsout, tbl, hidden){
        var chk = jQuery('#'+tbl).find('input[type="radio"][value="1"]:checked');

        jQuery("#"+subsout).find('option').each(function(){
            if(jQuery(this).val() != '0'){
                jQuery(this).remove();
            }
        });
        chk.each(function(){
            //console.log(jQuery(this).parent().parent().parent().find('input[name^="t1_squard"]'));
            var plId = jQuery(this).parent().parent().find('input[name^="'+hidden+'"]').val();
            if(plId != 0){
                var plName = jQuery(this).parent().parent().parent().find('td:first').text();
                jQuery("#"+subsout).append("<option value='"+plId+"'>"+plName+"</option>");
            }
            //console.log("<option value='"+plId+"'>"+plName+"</option>");
        });
        var plrs = (subsout == 'players_team1_out')?'players_team1_in_arr':'players_team2_in_arr';

        jQuery('input[name="'+plrs+'\[\]"]').each(function(){
            var plName = jQuery(this).parent().text();
            
            var plId = jQuery(this).val();
            
            if(plId != 0){
                
                jQuery("#"+subsout).append("<option value='"+plId+"'>"+plName+"</option>");
            }
            
        });
        jQuery("#"+subsout).trigger("liszt:updated");
        
        
        
        var chk = jQuery('#'+tbl).find('input[type="radio"][value="2"]:checked');
        jQuery("#"+subsin).find('option').each(function(){
            if(jQuery(this).val() != '0'){
                jQuery(this).remove();
            }
        });
        chk.each(function(){
            //console.log(jQuery(this).parent().parent().parent().find('input[name^="t1_squard"]'));
            var plId = jQuery(this).parent().parent().find('input[name^="'+hidden+'"]').val();
            if(plId != 0){
                var plName = jQuery(this).parent().parent().parent().find('td:first').text();
                jQuery("#"+subsin).append("<option value='"+plId+"'>"+plName+"</option>");
            }
            //console.log("<option value='"+plId+"'>"+plName+"</option>");
        });
        jQuery("#"+subsin).trigger("liszt:updated");
    }
    
jQuery(document).ready(function(){    
    jQuery(document).on('keydown', '.jsNumberEventMinutes', function (e) {
        // Allow: backspace, delete, tab, escape, enter and . and -
        if(e.key == ':'){
            return true;
        }
        if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 109, 189]) !== -1 ||
            // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) ||
            // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
            // let it happen, don't do anything
            return true;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
            return false;
        }
        return true;
    });
});    
jQuery(document).ready(function(){  
    jQuery('body').on('click','.jsportPopUl input',function(){
        var id = jQuery(this).attr("id");
        jQuery('.jsportPopUl textarea').hide();
        jQuery('#'+id+'_text').show();
    });
    
    jQuery('body').on('click','#jsportPopSkip',function(event){
        event.preventDefault();
        if(jQuery('#jsDeactivateOpt1').is(':checked')){
           var disb = 1; 
        }else{
            disb = 0;
        }
        var data = {
            'action': 'joomsport-updoption',
            'option': disb,
        };
        var href = jQuery(this).attr('href');
        jQuery.post(ajaxurl, data, function(response) {
            window.location = href;
        });
    });
    jQuery('body').on('click','#jsportPopSend',function(event){
        event.preventDefault();
        var ch_type = jQuery('input[name="jsDeactivateReason"]:checked').val();
        if(ch_type){
            var ch_text = jQuery('#jsDeactivateReason'+ch_type+'_text').val();
            
             if(jQuery('#jsDeactivateOpt1').is(':checked')){
                var disb = 1; 
             }else{
                 disb = 0;
             }
             var data = {
                 'action': 'joomsport-updoption',
                 'option': disb,
             };
             jQuery.post(ajaxurl, data, function(response) {
             });
             
             var href = jQuery(this).attr('href');
             var data = {
                    'action': 'joomsport-senddeactivation',
                    'ch_type': ch_type,
                    'ch_text': ch_text,
                };
             jQuery.post(ajaxurl, data, function(response) {
                 window.location = href;

             });
            
            
        }
    });
});
