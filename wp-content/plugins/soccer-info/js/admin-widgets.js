
		function get_soccer_info_teams_go(ajax_url, league_id, select_id, team_id, copy_to_id, copy_to_team_id){
			jQuery('#img_'+select_id.substring(1)).show();
			jQuery('#img_'+select_id.substring(1)).css('visibility', 'visible');
			jQuery('#img_'+copy_to_id.substring(1)).show();
			jQuery('#img_'+copy_to_id.substring(1)).css('visibility', 'visible');
			
			this.get_soccer_info_teams_ajax_query = jQuery.ajax({
                url: ajax_url,
				data: { 'league_id': league_id, 'new_id': '1', 'team_id': team_id, 'action': 'get_soccer_info_teams' },
				cache: false,
				dataType: 'json',
                success: function(data) {
					if(data.teams == null){
						// no teams found
					}
					else {
						// user found
						jQuery(select_id).html(data.teams);
						jQuery(copy_to_id).html(data.teams);
						jQuery(copy_to_id).val(copy_to_team_id);
					}
					
					jQuery('#img_'+select_id.substring(1)).hide();
					jQuery('#img_'+select_id.substring(1)).css('visibility', 'hidden');
					jQuery('#img_'+copy_to_id.substring(1)).hide();
					jQuery('#img_'+copy_to_id.substring(1)).css('visibility', 'hidden');
				}
            });
		}

	