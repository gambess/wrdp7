function init() {
	tinyMCEPopup.resizeToInnerSize();
}

function insertSoccerInfo() {
	var tagtext;
	var tables 		 = document.getElementById('tables_panel');
	var fixtures	 = document.getElementById('fixtures_panel');
	var results		 = document.getElementById('results_panel');

	// Table mode
	if (tables.className.indexOf('current') != -1) {
		
		var type = 'table';
		
		var columns	 = document.getElementById('columns').value;
		
		var width		 = document.getElementById('width' +'_'+ type +'s').value;
		var limit		 = document.getElementById('limit' +'_'+ type +'s').value;
		var title		 = document.getElementById('title' +'_'+ type +'s').value;
		var highlight	 = document.getElementById('highlight' +'_'+ type +'s').value;
		var team 		 = document.getElementById('team' +'_'+ type +'s').value;
		var leagueId	 = document.getElementById('league_id' +'_'+ type +'s').value;
		var style		 = document.getElementById('style' +'_'+ type +'s').value;
		var icon		 = document.getElementById('icon' +'_'+ type +'s').value;
	}
	
	// Fixtures mode
	if (fixtures.className.indexOf('current') != -1) {
		
		var type = 'fixtures';
		
		var columns = '';
		
		var width		 = document.getElementById('width' +'_'+ type).value;
		var limit		 = document.getElementById('limit' +'_'+ type).value;
		var title		 = document.getElementById('title' +'_'+ type).value;
		var highlight	 = document.getElementById('highlight' +'_'+ type).value;
		var team 		 = document.getElementById('team' +'_'+ type).value;
		var leagueId	 = document.getElementById('league_id' +'_'+ type).value;
		var style		 = document.getElementById('style' +'_'+ type).value;
		var icon		 = document.getElementById('icon' +'_'+ type).value;
	}
	
	// Results mode
	if (results.className.indexOf('current') != -1) {
		
		var type = 'results';
		
		var columns = '';
		
		var width		 = document.getElementById('width' +'_'+ type).value;
		var limit		 = document.getElementById('limit' +'_'+ type).value;
		var title		 = document.getElementById('title' +'_'+ type).value;
		var highlight	 = document.getElementById('highlight' +'_'+ type).value;
		var team 		 = document.getElementById('team' +'_'+ type).value;
		var leagueId	 = document.getElementById('league_id' +'_'+ type).value;
		var style		 = document.getElementById('style' +'_'+ type).value;
		var icon		 = document.getElementById('icon' +'_'+ type).value;
	}
	
	if ( columns != '' ) columns = " columns='" + columns + "'";
	if ( width != '' ) width = " width='" + width + "'";
	if ( limit != '' ) limit = " limit='" + limit + "'";
	if ( title != '' ) title = " title='" + title + "'";
	if ( highlight != '' && highlight != '0||' )
		highlight = " highlight='" + highlight + "'";
	else
		highlight = '';
	if ( team != '' && team != '0||' )
		team = " team='" + team + "'";
	else
		team = '';
	if ( style != '' && style != 'general' )
		style = " style='" + style + "'";
	else
		style = '';
	if ( icon != '' && icon != '0' )
		icon = " icon='" + icon + "'";
	else
		icon = '';
	
	if (leagueId != 0)
		tagtext = "[soccer-info id='" + leagueId + "' type='" + type + "'" + style + columns + highlight + team + title + limit + width + icon + " /]";
	else {
		if ( typeof tinyMCEPopup != undefined)
			tinyMCEPopup.close();
	}
	
	if (window.tinyMCE) {
		
		/* get the TinyMCE version to account for API diffs */
    	var tmce_ver=window.tinyMCE.majorVersion;

		if ( tmce_ver >= "4") {
			window.tinyMCE.execCommand('mceInsertContent', false, tagtext);
		} else {
			window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
		}
		
		tinyMCEPopup.editor.execCommand('mceRepaint');
		tinyMCEPopup.close();
	}
	return;
}