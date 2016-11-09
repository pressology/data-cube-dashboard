
jQuery.noConflict();

jQuery(document).ready(function($) {
	
	//DASHBOARD OVERVIEW
	
	//Individual "Send E-mail" Buttons on Dashboard Overview
	//These are written to allow the on click binding to apply to dynamically created buttons as well		
    $("#all-the-content").on('click', '.send-alert',function() {
								
	    $.post(
			ajaxurl,
				{
					'action':'dc_send_alert',
					'postid':$(this).attr('name'),
				}
			);
			$(this).css({'background':'green'});
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
			
});