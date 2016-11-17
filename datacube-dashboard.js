
jQuery.noConflict();

jQuery(document).ready(function($) {
	
	//DASHBOARD OVERVIEW
	
	//Individual "Send E-mail" Buttons on Dashboard Overview
	//These are written to allow the on click binding to apply to dynamically created buttons as well		
    $("#all-the-content").on('click', '.send-alert',function() {

		var button = $(this);

	    $.post(
			ajaxurl,
				{
					'action':'dc_email_handler',
					'command':$(this).attr('name'),
					'data':$(this).attr('value')
				}
			).done( function( data ) {

			//alert( data );
			$(button).parent().html('Alert sent.');

			});

	});
	
	//NAV and Page Building. NOT dynamically bound.
	$("#brands_btn").click( function() {
		
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: {
				action: "dc_build_view",
				view: "brand"
			}
		}).done( function( data ) {
			//alert( "Return = " + data);
			$("#all-the-content").html(data);
		})
		
	});
	
	$("#overview_btn").click( function() {
		
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: {
				action: "dc_build_view",
				view: "overview"
			}
		}).done( function( data ) {
			//alert( "Return = " + data);
			$("#all-the-content").html(data);
		})
		
	});

	$("#refresh_btn").click( function() {

		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: {
				action: "dc_refresh_alerts",
			}
		}).done( function( data ) {
			alert( "All brands alert status reset." );
		})

	});
			
});