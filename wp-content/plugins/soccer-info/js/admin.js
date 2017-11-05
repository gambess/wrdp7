	jQuery(document).ready(function($){
		$("input[name='si_date_format']").click(function(){
			if ( "si_date_format_custom_radio" != $(this).attr("id") )
				$("input[name='si_date_format_custom']").val( $(this).val() ).siblings('.example').text( $(this).siblings('span').text() );
		});
		$("input[name='si_date_format_custom']").focus(function(){
			$("#si_date_format_custom_radio").attr("checked", "checked");
		});
		$("input[name='si_date_format_custom']").change( function() {
			var format = $(this);
			format.siblings('img').css('visibility','visible');
			$.post(ajaxurl, {
					action: 'si_date_format_custom' == format.attr('name') ? 'si_date_format' : 'time_format',
					date : format.val()
				}, function(d) { format.siblings('img').css('visibility','hidden'); format.siblings('.example').text(d); } );
		});
	});