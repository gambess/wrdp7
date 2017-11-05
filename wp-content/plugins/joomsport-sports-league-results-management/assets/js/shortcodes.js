( function () {
	tinymce.PluginManager.add( 'joomsport_shortcodes_button', function( editor, url ) {
		var ed = tinymce.activeEditor;
		editor.addButton( 'joomsport_shortcodes_button', {
			title: 'Joomsport',
			text: false,
			icon: false,
			type: 'menubutton',
			menu: [
				
				{
					text: 'Standings',
					onclick : function() {
                                            // triggers the thickbox
                                            var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
                                            W = W - 80;
                                            H = H - 84;
                                            tb_show( 'Standings', 'admin-ajax.php?action=joomsport_standings_shortcode&width=' + W + '&height=' + H );
                                        }
				},
                                {
					text: 'Matches',
					onclick : function() {
                                            // triggers the thickbox
                                            var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
                                            W = W - 80;
                                            H = H - 84;
                                            tb_show( 'Matches', 'admin-ajax.php?action=joomsport_matches_shortcode&width=' + W + '&height=' + H );
                                        }
				},
                                {
					text: 'Player Statistic',
					onclick : function() {
                                            // triggers the thickbox
                                            var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
                                            W = W - 80;
                                            H = H - 84;
                                            tb_show( 'Player Statistic', 'admin-ajax.php?action=joomsport_plstat_shortcode&width=' + W + '&height=' + H );
                                        }
				},
                                {
					text: 'Matchday',
					onclick : function() {
                                            // triggers the thickbox
                                            var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
                                            W = W - 80;
                                            H = H - 84;
                                            tb_show( 'Matchday', 'admin-ajax.php?action=joomsport_matchday_shortcode&width=' + W + '&height=' + H );
                                        }
				},
                                {
					text: 'Player list',
					onclick : function() {
                                            // triggers the thickbox
                                            var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
                                            W = W - 80;
                                            H = H - 84;
                                            tb_show( 'Player list', 'admin-ajax.php?action=joomsport_playerlist_shortcode&width=' + W + '&height=' + H );
                                        }
				}
				
			]
		});
	});
        
})();
