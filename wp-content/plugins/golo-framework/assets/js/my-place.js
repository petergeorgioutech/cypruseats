(function($) {
	"use strict";

	var my_places = $('.golo-my-places');

	var ajax_url 			= golo_my_place_vars.ajax_url,
		not_place 			= golo_my_place_vars.not_place,
		item_amount 		= golo_my_place_vars.item_amount,
		delete_confirmation = golo_my_place_vars.delete_confirmation;

	function createDeleteModal( delete_id ) {
		var modal = `
			<div id="dialog-confirm">
				<p>
					` + delete_confirmation + `					
				</p>
			</div>`;

		return modal;
	}

	$(document).ready( function() {

		$('select.search-control').on('change', function() {
			$('.golo-pagination').find('input[name="paged"]').val(1);
            ajax_load();
        });

        $('input.search-control').on('input', function() {
        	$('.golo-pagination').find('input[name="paged"]').val(1);
            ajax_load();
        });

        $('body').on('click', '.place-control .btn-delete', function(e) {
        	e.preventDefault();
        	var delete_id = $(this).attr('place-id');
			var delete_name = $(this).parent().siblings('.place-name').find('a').text();
			delete_name = $.trim(delete_name);
			var modal = createDeleteModal(delete_id);
			$('#wrapper').append(modal);
			
			$('#dialog-confirm').dialog({
				resizable: false,
				height: "auto",
				width: 300,
				modal: true,
				title: delete_name,
				buttons: [
					{
						icon: 'fas fa-check',
						click: function() {
							ajax_load(delete_id, 'delete');
							$('#dialog-confirm').dialog( "close" );
						}
					},
					{
						icon: 'fas fa-times',
						click: function() {
							$('#dialog-confirm').dialog( "close" );
						}
					}
				]
			});
        });

        $('body').on('click', '.place-control .btn-mark-featured', function(e) {
        	e.preventDefault();
        	var item_id = $(this).attr('place-id');
        	ajax_load(item_id, 'mark-featured');
        });

        $('body').on('click', '.place-control .btn-reactivate-place', function(e) {
        	e.preventDefault();
        	var item_id = $(this).attr('place-id');
        	ajax_load(item_id, 'reactivate-place');
        });

        $('body').on('click', '.place-control .btn-show', function(e) {
        	e.preventDefault();
        	var item_id = $(this).attr('place-id');
        	ajax_load(item_id, 'show');
        });

        $('body').on('click', '.place-control .btn-hide', function(e) {
        	e.preventDefault();
        	var item_id = $(this).attr('place-id');
        	ajax_load(item_id, 'hidden');
        });

        $('body').on('click','.golo-pagination a.page-numbers', function(e) {
            e.preventDefault();
            $('.golo-pagination li .page-numbers').removeClass('current');
            $(this).addClass('current');
            var paged = $(this).text();
            var current_page = 1;
            if( $('.golo-pagination').find('input[name="paged"]').val() ) {
                current_page = $('.golo-pagination').find('input[name="paged"]').val();
            }
            if( $(this).hasClass('next') ){
                paged = parseInt(current_page) + 1;
            }
            if( $(this).hasClass('prev') ){
                paged = parseInt(current_page) - 1;
            }
            $('.golo-pagination').find('input[name="paged"]').val(paged);

            ajax_load();
        });

		function ajax_load(item_id = '', action_click = '') {
			var paged = 1;

			var place_search 	 = my_places.find('input[name="place_search"]').val(),
				place_city       = my_places.find('select[name="place_city"]').val(),
				place_categories = my_places.find('select[name="place_categories"]').val();

			paged = $('.golo-pagination').find('input[name="paged"]').val();

			var height = my_places.find('#my-places').height();

			$.ajax({
	            dataType: 'json',
	            url: ajax_url,
	            data: {
	                'action': 'golo_filter_my_place',
	                'item_amount': item_amount,
	                'paged': paged,
	                'place_search': place_search,
	                'place_city': place_city,
	                'place_categories': place_categories,
	                'item_id': item_id,
	                'action_click': action_click,
	            },
	            beforeSend: function () {
	                my_places.find('.golo-loading-effect').addClass('loading').fadeIn();
	                my_places.find('#my-places').height(height);
	            },
	            success: function (data) {
	            	if( data.success === true ) {
	            		$('.golo-pagination .pagination').html(data.pagination);
	            		my_places.find('#my-places tbody').fadeOut('fast', function(){
	            			my_places.find('#my-places tbody').html(data.place_html);
					        my_places.find('#my-places tbody').fadeIn(300);
					    });
	            		my_places.find('#my-places').css('height', 'auto');
	            	}else{
	            		my_places.find('#my-places tbody').html('<span>' + not_place + '</span>');
	            	}
	            	my_places.find('.golo-loading-effect').removeClass('loading').fadeOut();
	            },
	       	});
		}
	});
})(jQuery);