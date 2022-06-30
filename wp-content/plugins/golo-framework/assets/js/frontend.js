(function($) {
	"use strict";

	var submit_form = $('#submit_place_form'),
		place_title_error 	= submit_form.data( 'titleerror' ),
        place_des_error 	= submit_form.data( 'deserror' ),
        place_cat_error 	= submit_form.data( 'caterror' ),
        place_type_error 	= submit_form.data( 'typeerror' ),
        place_map_error 	= submit_form.data( 'maperror' ),
        place_img_error 	= submit_form.data( 'imgerror' );

	var ajax_url  = golo_submit_vars.ajax_url,
		my_places = golo_submit_vars.my_places,
		additional_fields = golo_submit_vars.additional_fields;

	$(document).ready( function() {

		$('#booking .field-radio label').on('click', function(){
			$('#booking .field-radio .form-field').removeClass('checked');
			$(this).parents('.form-field').addClass('checked');
			var id = $('#booking .field-radio .form-field.checked').attr('data-id');
			$('#booking .tab-content .inner-content').removeClass('active');
			$('#' + id).addClass('active');
		});

		$.validator.setDefaults({ ignore: ":hidden:not(select)" });

		submit_form.validate({
			ignore: [],
            rules: {
                place_title: {
                    required: true,
                },
                place_categories: {
                    required: true,
                },
                place_type: {
                    required: true,
                },
                place_map_address: {
                    required: true,
                },
                place_featured_image_url: {
                    required: true,
                },
            },
            messages: {
                place_title: place_title_error,
                place_des: place_des_error,
                place_categories: place_cat_error,
                place_type: place_type_error,
                place_map_address: place_map_error,
                place_featured_image_url: place_img_error,
            },
            submitHandler: function(form) {
                ajax_load();
            },
            errorPlacement: function(error, element) {
	            if (element.is('select.nice-select:hidden')) {
	                error.insertAfter(element.next('.nice-select'));
	            } else {
	                error.insertAfter(element);
	            }
	        },
	        invalidHandler: function() {
		    	if( $('.error:visible').length > 0 ) {
					$('html, body').animate({
					    scrollTop: ($('.error:visible').offset().top - 100)
					}, 500);
				}
		    }
        });

        $('.nice-select,.golo-select2').on('change', function() {
	        $(this).valid();
	    });
	    
		function ajax_load() {

			var place_form           	 = submit_form.find('input[name="place_form"]').val(),
				place_action             = submit_form.find('input[name="place_action"]').val(),
				place_id                 = submit_form.find('input[name="place_id"]').val(),
				place_title              = submit_form.find('input[name="place_title"]').val(),
				place_price_short        = submit_form.find('input[name="place_price_short"]').val(),
				place_price_unit         = submit_form.find('select[name="place_price_unit"]').val(),
				place_price_range        = submit_form.find('select[name="place_price_range"]').val(),
				place_des                = tinymce.get('place_des').getContent(),
				place_categories         = submit_form.find('select[name="place_categories"]').val(),
				place_type               = submit_form.find('select[name="place_type"]').val(),
				place_map_address        = submit_form.find('input[name="place_map_address"]').val(),
				place_map_location       = submit_form.find('input[name="place_map_location"]').val(),
				place_city               = submit_form.find('select[name="place_city"]').val(),
				new_place_city        	 = submit_form.find('input[name="new_place_city"]').val(),
				custom_place_city        = submit_form.find('input[name="custom_place_city"]').val(),
				place_timezone			 = submit_form.find('select[name="place_timezone"').val(),
				place_postal_code        = submit_form.find('input[name="place_postal_code"]').val(),
				place_email              = submit_form.find('input[name="place_email"]').val(),
				place_phone              = submit_form.find('input[name="place_phone"]').val(),
				place_phone2             = submit_form.find('input[name="place_phone2"]').val(),
				place_website            = submit_form.find('input[name="place_website"]').val(),
				place_facebook           = submit_form.find('input[name="place_facebook"]').val(),
				place_instagram          = submit_form.find('input[name="place_instagram"]').val(),
				place_twitter          	 = submit_form.find('input[name="place_twitter"]').val(),
				opening_monday           = submit_form.find('input[name="opening_monday"]').val(),
				opening_monday_time      = submit_form.find('input[name="opening_monday_time"]').val(),
				opening_tuesday          = submit_form.find('input[name="opening_tuesday"]').val(),
				opening_tuesday_time     = submit_form.find('input[name="opening_tuesday_time"]').val(),
				opening_wednesday        = submit_form.find('input[name="opening_wednesday"]').val(),
				opening_wednesday_time   = submit_form.find('input[name="opening_wednesday_time"]').val(),
				opening_thursday         = submit_form.find('input[name="opening_thursday"]').val(),
				opening_thursday_time    = submit_form.find('input[name="opening_thursday_time"]').val(),
				opening_friday           = submit_form.find('input[name="opening_friday"]').val(),
				opening_friday_time      = submit_form.find('input[name="opening_friday_time"]').val(),
				opening_saturday         = submit_form.find('input[name="opening_saturday"]').val(),
				opening_saturday_time    = submit_form.find('input[name="opening_saturday_time"]').val(),
				opening_sunday           = submit_form.find('input[name="opening_sunday"]').val(),
				opening_sunday_time      = submit_form.find('input[name="opening_sunday_time"]').val(),
				place_featured_image_url = submit_form.find('input[name="place_featured_image_url"]').val(),
				place_featured_image_id  = submit_form.find('input[name="place_featured_image_id"]').val(),
				place_image_ids          = submit_form.find('input[name="place_image_ids[]"]').map(function(){return $(this).val();}).get(),
				place_video_url          = submit_form.find('input[name="place_video_url"]').val(),
				place_booking_type       = submit_form.find('input[name="place_booking_type"]:checked').val(),
				place_booking            = submit_form.find('input[name="place_booking"]').val(),
				place_booking_site       = submit_form.find('input[name="place_booking_site"]').val(),
				place_booking_2          = submit_form.find('input[name="place_booking_2"]').val(),
				place_booking_site_2     = submit_form.find('input[name="place_booking_site_2"]').val(),
				place_booking_image_url  = submit_form.find('input[name="place_booking_image_url"]').val(),
				place_booking_image_id   = submit_form.find('input[name="place_booking_image_id"]').val(),
				place_booking_banner_url = submit_form.find('input[name="place_booking_banner_url"]').val(),
				place_booking_form       = submit_form.find('select[name="place_booking_form"]').val(),
				menu_name       		 = submit_form.find('input[name="menu_name[]"]').map(function(){return $(this).val();}).get(),
				menu_price       		 = submit_form.find('input[name="menu_price[]"]').map(function(){return $(this).val();}).get(),
				menu_type       		 = submit_form.find('select[name="menu_type[]"]').map(function(){return $(this).val();}).get(),
				item_desc       		 = submit_form.find('textarea[name="item_desc[]"]').map(function(){return $(this).val();}).get(),
				faqs_title       		 = submit_form.find('input[name="faqs_title[]"]').map(function(){return $(this).val();}).get(),
				faqs_desc       		 = submit_form.find('textarea[name="faqs_desc[]"]').map(function(){return $(this).val();}).get(),
				place_menu_image_url     = submit_form.find('input[name="place_menu_image_url[]"]').map(function(){return $(this).val();}).get(),
				place_menu_image_id      = submit_form.find('input[name="place_menu_image_id[]"]').map(function(){return $(this).val();}).get();

			var place_amenities = [];
            $("input[name='place_amenities']:checked").each(function() {   
                place_amenities.push(parseInt($(this).val()));
            });

            var additional = {};
            $( '#additional' ).each( function() {
	            $.each( additional_fields, function(index, value){
	            	var val = $('.form-control[name='+ value.id +']').val();
	            	if( value.type == 'radio' ) {
	            		val = $('input[name='+ value.id +']:checked').val();
	            	}
	            	if( value.type == 'checkbox_list' ) {
	            		var arr_checkbox = [];
	            		$('input[name="'+ value.id +'[]"]:checked').each(function() {
			                arr_checkbox.push($(this).val());
			            });
	            		val = arr_checkbox;
	            	}
	            	additional[value.id] = val;
	            });
	        });

            if ( custom_place_city == '' && new_place_city ) {
            	custom_place_city = new_place_city;
            }

			$.ajax({
	            dataType: 'json',
	            url: ajax_url,
	            data: {
	                'action': 'place_submit_ajax',
	                'place_form': place_form,
	                'place_action': place_action,
	                'place_id': place_id,
	                'place_title': place_title,
	                'place_price_short': place_price_short,
	                'place_price_unit': place_price_unit,
	                'place_price_range': place_price_range,
	                'place_des': place_des,
	                'place_categories': place_categories,
	                'place_type': place_type,
	                'place_amenities': place_amenities,
	                'place_map_address': place_map_address,
	                'place_map_location': place_map_location,
	                'place_city': place_city,
	                'custom_place_city': custom_place_city,
	                'place_timezone': place_timezone,
	                'place_postal_code': place_postal_code,
	                'place_email': place_email,
	                'place_phone': place_phone,
	                'place_phone2': place_phone2,
	                'place_website': place_website,
	                'place_facebook': place_facebook,
	                'place_instagram': place_instagram,
	                'place_twitter': place_twitter,
	                'opening_monday': opening_monday,
	                'opening_monday_time': opening_monday_time,
	                'opening_tuesday': opening_tuesday,
	                'opening_tuesday_time': opening_tuesday_time,
	                'opening_wednesday': opening_wednesday,
	                'opening_wednesday_time': opening_wednesday_time,
	                'opening_thursday': opening_thursday,
	                'opening_thursday_time': opening_thursday_time,
	                'opening_friday': opening_friday,
	                'opening_friday_time': opening_friday_time,
	                'opening_saturday': opening_saturday,
	                'opening_saturday_time': opening_saturday_time,
	                'opening_sunday': opening_sunday,
	                'opening_sunday_time': opening_sunday_time,
	                'place_featured_image_url': place_featured_image_url,
	                'place_featured_image_id': place_featured_image_id,
	                'place_image_ids': place_image_ids,
	                'place_video_url': place_video_url,
	                'place_booking_type': place_booking_type,
	                'place_booking': place_booking,
	                'place_booking_site': place_booking_site,
	                'place_booking_2': place_booking_2,
	                'place_booking_site_2': place_booking_site_2,
	                'place_booking_image_url': place_booking_image_url,
	                'place_booking_image_id': place_booking_image_id,
	                'place_booking_banner_url': place_booking_banner_url,
	                'place_booking_form': place_booking_form,
	                'additional_fields': additional,
	                'menu_name': menu_name,
	                'menu_price': menu_price,
	                'menu_type': menu_type,
	                'item_desc': item_desc,
	                'faqs_title': faqs_title,
	                'faqs_desc': faqs_desc,
	                'place_menu_image_url': place_menu_image_url,
	                'place_menu_image_id': place_menu_image_id,
	            },
	            beforeSend: function () {
	                $('.btn-submit-place .btn-loading').fadeIn();
	            },
	            success: function (data) {
	            	$('.btn-submit-place .btn-loading').fadeOut();
		           	if( data.success === true ) {
		           		window.location.href = my_places;
		           	}
	            },
	       	});
		}
	});
})(jQuery);