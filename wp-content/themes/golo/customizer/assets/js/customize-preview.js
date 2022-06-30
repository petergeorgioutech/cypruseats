(function ($) {
    "use strict";

    // CSS Pseudo Selected
	function pseudoStyle( selector, inline_css ) {
	  	var style  = document.querySelector('style[id="pseudo-css"]') || document.createElement('style');
	  	style.id   = 'pseudo-css';
	  	style.type = 'text/css';

	  	var css = selector + inline_css ;
	  	if (style.styleSheet){
	    	style.styleSheet.cssText = css;
	  	} else {
	    	style.appendChild(document.createTextNode(css));
	  	}
	  	document.querySelector('body').appendChild(style);
	}

    // Remove Class With Prefix --------------------------------------------------------------------
    function removeClassStartingWith(node, begin) {
	    node.removeClass (function (index, className) {
	        return (className.match ( new RegExp("\\b"+begin+"\\S+", "g") ) || []).join(' ');
	    });
	};

    // Color ---------------------------------------------------------------------------------------
    wp.customize( 'primary_color', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			var color_element = '.block-heading .entry-title,.woocommerce .products .product .entry-detail .product-title .entry-title a,.woocommerce #reviews #review_form_wrapper .comment-reply-title,.woocommerce #reviews #comments .woocommerce-Reviews-title,#comments .comments-title,#comments .comment-author .entry-detail .author-name a,.products.related > h2, .products.upsells > h2,.woocommerce div.product .woocommerce-tabs ul.tabs li a,.woocommerce div.product .product_title,#respond .comment-reply-title,.single .post .inner-post-wrap .post-author .head-author .entry-title a,.mobile-menu .menu li a,.single .post .inner-post-wrap .post-title .entry-title,.archive-post .post .post-categories li a,.archive-post .post .post-title a,.dropdown-select,header.site-header, .single-post .post-content dt,#comments dt,.single-post .post-content strong,#comments strong,.mobile-menu,.single-place .place-reviews .reviews-list .reply a,.archive .information .entry-detail strong,.archive .nav-categories .entry-categories ul li a,.single .post .inner-post-wrap .post-author .head-author .entry-title a,.woocommerce div.product .woocommerce-tabs ul.tabs li a,.woocommerce div.product form.cart .variations label,.woocommerce div.product p.price,.woocommerce div.product span.price,.woocommerce div.product div.summary p.price,.woocommerce div.product form.cart.grouped_form .woocommerce-grouped-product-list-item__price,.product-quantity input,.woocommerce #reviews #review_form_wrapper .comment-reply-title,.woocommerce #reviews #review_form_wrapper .comment-form-rating label';
			$(color_element).css('color', newval );
		} );
	} );

	wp.customize( 'text_color', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			var color_element = 'body,.woocommerce nav.woocommerce-pagination ul li .page-numbers,.posts-pagination ul li .page-numbers,.archive .block-heading.category-heading .entry-result,.golo-menu-filter ul.filter-control a,.woocommerce div.product .woocommerce-product-rating a,.woocommerce div.product div.summary .product_meta > span span,.woocommerce div.product div.summary .product_meta > span a';
			$(color_element).css( 'color', newval );
		} );
	} );

	wp.customize( 'accent_color', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			var color_element     = '.popup-booking .list-group li.place-name a strong,.account .user-control li.active a,.accent-color,.golo-marker .place-rating,.golo-nav-filter.active .golo-clear-filter,.place-item .btn-add-to-wishlist.added i,.agent-manager .agent-nav ul li.active a,.woocommerce .checkout.woocommerce-checkout #order_review table tfoot tr.order-total td .amount,.woocommerce-info a.showcoupon,.woocommerce .woocommerce-message a.button, .woocommerce .woocommerce-info a.button, .woocommerce .woocommerce-error a.button,.minicart .top-mb-menu .your-cart a .cart-count,.widget_categories li,.single-place .place-thumbnails.type-1 .single-place-thumbs .place-meta > div .rating-count,.author-rating .star.checked i,.single-place .site-layout.type-1 .place-map > a .redirect,.single-place .place-content a,.single-place .site-layout.type-1 .place-amenities .hidden-amenities > a,.single-place .single-place-thumbs .entry-nav .btn-add-to-wishlist.added,.dropdown-select ul li.active a,header.site-header .right-header .minicart a.toggle span.cart-count,.place-search .form-control.nice-select .current,.archive .nav-categories .entry-categories ul li.active a,.place-item .place-preview .place-rating,.single-place .place-reviews .entry-heading .rating-count,.woocommerce #reviews #review_form_wrapper .comment-form-rating .stars a,.woocommerce #reviews #comments ol.commentlist .star-rating > span, div.elementor-slick-slider ul.slick-dots li.slick-active button:before,.golo-swiper .swiper-pagination-bullet:hover, .golo-swiper .swiper-pagination-bullet.swiper-pagination-bullet-active,.single-place .site-layout .place-meta > div .rating-count,.place-menu .toggle-desc a,.place-reviews .login-for-review a,.archive-layout.layout-column .entry-center a.active, .archive-layout.layout-top-filter .entry-center a.active';
			var pseudo_element    = '.golo-table td.place-control a:hover,.golo-place-multi-step .golo-steps .listing-menu li a:hover,.place-item.layout-02 .entry-head .place-city a:hover,.place-item.layout-03 .entry-head .place-city a:hover,.woocommerce-error:before,.woocommerce nav.woocommerce-pagination ul li .page-numbers:hover, .posts-pagination ul li .page-numbers:hover, .woocommerce nav.woocommerce-pagination ul li .page-numbers:focus, .posts-pagination ul li .page-numbers:focus,.archive-post .post .post-meta .post-author a:hover,.product-quantity .btn-quantity:hover i,a:hover,.slick-arrow:hover,.widget_calendar tbody tr > td:hover,.widget ul > li a:hover,.city-item .entry-detail a:hover,input:checked ~ label:before,.woocommerce-info:before, label:hover:before, label:hover ~ label:before,.golo-filter-toggle:hover,.single-place .place-reviews .reviews-list .entry-nav a:hover, .golo-clear-filter:hover, .golo-filter-toggle.active, .golo-clear-filter.active,.place-search .btn-close:hover,.archive .nav-categories .entry-categories ul li a:hover,.archive .nav-categories .entry-categories ul li a:hover,.single-place .entry-categories a:hover,.place-item .place-title a:hover,.woocommerce div.product div.summary .product_meta > span a:hover,.product-quantity .btn-quantity:hover svg path,a.customize-unpreviewable:hover,.account a:hover,.mobile-menu .menu li a:hover,.archive-post .post .post-title a:hover,.single .post .inner-post-wrap .post-author .head-author .entry-title a:hover,.single .post .inner-post-wrap .post-meta .post-author a:hover,.single .post .inner-post-wrap .post-categories a:hover,.archive-post .post .post-categories li a:hover,.woocommerce .products .product .entry-detail .product-title .entry-title a:hover';
			var pseudo_bg_element = '.golo-ldef-spinner span:after,.golo-ldef-roller span:after,.golo-ldef-heart span:after,.golo-ldef-heart span:before,.woocommerce .wc-proceed-to-checkout a.checkout-button:hover,.woocommerce .checkout.woocommerce-checkout #order_review #payment .place-order .button:hover,.archive-post .post .btn-readmore a:after,.archive .nav-categories .entry-categories ul li a:after,.archive .nav-categories .entry-categories ul li a:hover:after,.archive .nav-categories .entry-categories ul li.active a:after,.woocommerce nav.woocommerce-pagination ul li .page-numbers.current:hover, .posts-pagination ul li .page-numbers.current:hover';
			var bg_element        = '.filter-place-search .btn-close,.archive-layout.layout-column .top-area .entry-right .btn-maps-filter a,.golo-page-title.layout-column.layout-01 .entry-detail,.golo-ldef-grid span,.golo-ldef-ellipsis span,.golo-ldef-default span,.golo-ldef-heart span,.golo-ldef-facebook span,.golo-ldef-circle > span,.woocommerce .wc-proceed-to-checkout a.checkout-button,.golo-pagination .page-numbers.current,.gl-button, .wpcf7-submit,.minicart .woocommerce-mini-cart__buttons a.elementor-button--checkout, .minicart .elementor-menu-cart__footer-buttons a.elementor-button--checkout,.widget_calendar caption,.widget_calendar tbody tr > td#today,.newsletter-area .submit-control,.woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button,.single-place .place-booking a.gl-button,.woocommerce nav.woocommerce-pagination ul li .page-numbers.current, .posts-pagination ul li .page-numbers.current, .woocommerce span.onsale,.woocommerce #respond input#submit, .woocommerce button.button, .woocommerce input.button,.woocommerce div.product form.cart .button,.btn-golo,.golo-button a,.woocommerce .products .product .entry-detail .button.add_to_cart_button, .woocommerce .products .product .entry-detail .button.product_type_external, .woocommerce .products .product .entry-detail .button.product_type_grouped, .woocommerce .products .product .entry-detail .button.product_type_variation,.block-search.layout-02 .location-field .icon-search, .block-search.layout-03 .location-field .icon-search, .block-search.layout-02 .type-field .icon-search, .block-search.layout-03 .type-field .icon-search,.golo-pagination .pagination.loadmore .page-numbers,.inner-content.has-map .btn-control .slider, .nav-bar.has-map .btn-control .slider';
			var border_element    = '.filter-place-search .btn-close,.archive-layout.layout-column .top-area .entry-right .btn-maps-filter a,.golo-place-multi-step .golo-steps .listing-menu li.active a,.golo-ldef-ripple span,.woocommerce .inner-action-form .coupon input,.gl-button, .wpcf7-submit,.place-manager-form .form-group .form-control, .place-manager-form .form-group .chosen-choices:focus, .golo-my-profile .form-group input.form-control:focus,.minicart .woocommerce-mini-cart__buttons a.elementor-button--checkout, .minicart .elementor-menu-cart__footer-buttons a.elementor-button--checkout,.woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button,.woocommerce-info,.post-categories li a, .single-place .entry-categories a, .single-place .entry-categories span,.single-place .place-booking a.gl-button,.woocommerce #respond input#submit, .woocommerce button.button, .woocommerce input.button,.woocommerce div.product form.cart .button,.btn-golo,.archive-post .post .post-categories li,.golo-button a,.single .post .inner-post-wrap .post-categories a,.woocommerce .products .product .entry-detail .button.add_to_cart_button,.woocommerce .products .product .entry-detail .button.product_type_external, .woocommerce .products .product .entry-detail .button.product_type_grouped, .woocommerce .products .product .entry-detail .button.product_type_variation,.golo-pagination .pagination.loadmore .page-numbers';
			$(color_element).css( 'color', newval );
			$(bg_element).css( 'background', newval );
			$(border_element).css( 'border-color', newval );
			pseudoStyle( pseudo_element, '{ color: ' + newval + '}' );
			pseudoStyle( pseudo_bg_element, '{ background: ' + newval + '}' );
			pseudoStyle( '.woocommerce button.button:hover,.gl-button, .wpcf7-submit,.minicart .woocommerce-mini-cart__buttons a.elementor-button--checkout, .minicart .elementor-menu-cart__footer-buttons a.elementor-button--checkout,.single-place .place-booking a.gl-button:hover,.woocommerce #respond input#submit:hover, .woocommerce button.button:hover, .woocommerce input.button:hover,.woocommerce div.product form.cart .button:hover,.btn-golo:hover,.golo-button a:hover,.woocommerce .products .product .entry-detail .button.add_to_cart_button:hover, .woocommerce .products .product .entry-detail .button.product_type_external:hover, .woocommerce .products .product .entry-detail .button.product_type_grouped:hover, .woocommerce .products .product .entry-detail .button.product_type_variation:hover', '{ background: transparent!important;color:' + newval + '}' );
			pseudoStyle( '.golo-dual-ring:after,.golo-ldef-hourglass:after,.golo-ldef-dual-ring:after', '{ border-color: ' + newval + ' transparent ' + newval + ' transparent}' );
			pseudoStyle( '.golo-ldef-ring span', '{ border-color: ' + newval + ' transparent transparent transparent}' );
			pseudoStyle( '.golo-page-title.layout-column.block-left .entry-detail .after-image svg use', '{ fill: ' + newval + '}' );
			pseudoStyle( '.xdsoft_datetimepicker .xdsoft_calendar td.xdsoft_default, .xdsoft_datetimepicker .xdsoft_calendar td.xdsoft_current', '{ background: ' + newval + ';box-shadow: ' + newval + ' 0 1px 3px 0 inset}' );
    
		} );
	} );

	wp.customize( 'body_background_color', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$('body').css( 'background-color', newval );
		} );
	} );

	wp.customize( 'bg_body_image', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$('body').css( 'background-image', newval );
		} );
	} );

	wp.customize( 'bg_body_size', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$('body').css( 'background-size', newval );
		} );
	} );

	wp.customize( 'bg_body_repeat', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$('body').css('background-repeat', newval );
		} );
	} );

	wp.customize( 'bg_body_position', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$('body').css( 'background-position', newval );
		} );
	} );

	wp.customize( 'bg_body_attachment', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$('body').css( 'background-attachment', newval );
		} );
	} );

	// Layout --------------------------------------------------------------------------------------
	wp.customize( 'layout_content', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$( '#wrapper' ).removeClass();
			$( '#wrapper' ).addClass( newval );
			$( '#wrapper' ).css( 'max-width', 'auto' );
		} );
	} );

	wp.customize( 'content_width', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			var newval = newval + 'px';
			$( '#wrapper' ).css( 'max-width', newval );
		} );
	} );

    wp.customize( 'layout_sidebar', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			if( $('.site-layout aside#secondary').length > 0 ) {
				$( '.site-layout' ).removeClass( 'left-sidebar right-sidebar no-sidebar has-sidebar' );
				if( newval != 'no-sidebar' ){
					$( '.site-layout' ).addClass( 'has-sidebar' );
				}
				$( '.site-layout' ).addClass( newval );
			}
		} );
	} );

	wp.customize( 'sidebar_width', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			var layout_width = $( '.site-layout #primary' ).outerWidth();
            var content_width = layout_width - newval;
            if( $('.site-layout aside#secondary').length > 0 ) {
				$('.site-layout aside#secondary').css('cssText', 'flex: 0 0 ' + newval + 'px!important; max-width:' + newval + 'px!important');
				$('.site-layout #primary').css('cssText', 'max-width: calc(100% - ' + content_width + ')px!important');
			}
		} );
	} );

	// Header --------------------------------------------------------------------------------------
	wp.customize( 'sticky_header', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			if( newval ){
				$( 'header.site-header' ).addClass( 'sticky-header' );
			}else{
				$( 'header.site-header' ).removeClass( 'sticky-header' );
			}
		} );
	} );

	wp.customize( 'float_header', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			if( newval ){
				$( 'header.site-header' ).addClass( 'float-header' );
			}else{
				$( 'header.site-header' ).removeClass( 'float-header' );
			}
		} );
	} );

	wp.customize( 'header_sticky_background', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			var bg_color_element = '.uxper-sticky.on';
			$(bg_color_element).css( 'background-color', newval );
		} );
	} );

	wp.customize( 'search_form_width', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			var newval = newval + 'px';

			$( 'header.site-header .block-search.search-input' ).css( 'max-width', newval );
			$( 'header.site-header .block-search.search-form.layout-02 form' ).css( 'width', newval );
			$( 'header.site-header .block-search.search-form.layout-03 form' ).css( 'width', newval );
		} );
	} );

	wp.customize( 'logo_width', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			var newval = newval + 'px';

			$( 'header.site-header .site-logo img' ).css( 'max-width', newval );
		} );
	} );

	wp.customize( 'header_padding_top', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			var newval = newval + 'px';

			$( 'header.site-header' ).css( 'padding-top', newval );
		} );
	} );

	wp.customize( 'header_padding_bottom', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			var newval = newval + 'px';

			$( 'header.site-header' ).css( 'padding-bottom', newval );
		} );
	} );

	wp.customize( 'show_destinations', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			if( newval ){
				$( '.site-header .dropdown-categories' ).show();
			}else{
				$( '.site-header .dropdown-categories' ).hide();
			}
		} );
	} );

	// Page Title -----------------------------------------------------------------------------------
	wp.customize( 'enable_page_title_blog', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			if( newval ) {
				$( '.page-title-blog' ).removeClass('hide');
			}else{
				$( '.page-title-blog' ).addClass('hide');
			}
		} );
	} );

	wp.customize( 'style_page_title_blog', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$( '.page-title-blog' ).css( 'font-style', newval );
		} );
	} );

	wp.customize( 'page_title_blog_name', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$( '.page-title-blog .entry-title' ).text( newval );
		} );
	} );

	wp.customize( 'page_title_blog_des', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$( '.page-title-blog .sub-title p' ).text( newval );
		} );
	} );

	wp.customize( 'bg_page_title_blog', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$( '.page-title-blog' ).css( 'background-color', newval );
		} );
	} );

	wp.customize( 'color_page_title_blog', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$( '.page-title-blog,.page-title-blog .entry-detail .entry-title' ).css( 'color', newval );
		} );
	} );

	wp.customize( 'bg_image_page_title_blog', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$( '.page-title-blog' ).css( 'background-image', newval );
		} );
	} );

	wp.customize( 'bg_size_page_title_blog', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$( '.page-title-blog' ).css( 'background-size', newval );
		} );
	} );

	wp.customize( 'bg_repeat_page_title_blog', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$( '.page-title-blog' ).css( 'background-repeat', newval );
		} );
	} );

	wp.customize( 'bg_position_page_title_blog', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$( '.page-title-blog' ).css( 'background-position', newval );
		} );
	} );

	wp.customize( 'bg_attachment_page_title_blog', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$( '.page-title-blog' ).css( 'background-attachment', newval );
		} );
	} );

	wp.customize( 'font_size_page_title_blog', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			var newval = newval + 'px';
			$( '.page-title-blog .entry-title' ).css( 'font-size', newval );
		} );
	} );

	wp.customize( 'letter_spacing_page_title_blog', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			var newval = newval + 'px';
			$( '.page-title-blog .entry-title' ).css( 'letter-spacing', newval );
		} );
	} );

    // Blog ----------------------------------------------------------------------------------------
    wp.customize( 'blog_sidebar', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			if( $('.content-blog .site-layout aside#secondary').length > 0 ) {
				$('.content-blog .site-layout').removeClass( 'left-sidebar right-sidebar no-sidebar has-sidebar' );
				if( newval != 'no-sidebar' ){
					$( '.site-layout' ).addClass( 'has-sidebar' );
				}
				$('.content-blog .site-layout').addClass( newval );
			}
		} );
	} );

	wp.customize( 'blog_sidebar_width', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			var layout_width = $( '.content-blog .site-layout #primary' ).outerWidth();
            var content_width = layout_width - newval;
            if( $('.content-blog .site-layout aside#secondary').length > 0 ) {
				$('.content-blog .site-layout aside#secondary').css('cssText', 'flex: 0 0 ' + newval + 'px!important; max-width:' + newval + 'px!important');
				$('.content-blog .site-layout #primary').css('cssText', 'max-width: calc(100% - ' + content_width + ')px!important');
			}
		} );
	} );

	wp.customize( 'blog_number_column', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			removeClassStartingWith( $('.archive-post'), 'columns-');
			$('.archive-post').addClass( newval );
		} );
	} );

	wp.customize( 'post_single_sidebar', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			if( $('.single-post .site-layout aside#secondary').length > 0 ) {
				$('.single-post .site-layout').removeClass( 'left-sidebar right-sidebar no-sidebar has-sidebar' );
				if( newval != 'no-sidebar' ){
					$( '.site-layout' ).addClass( 'has-sidebar' );
				}
				$('.single-post .site-layout').addClass( newval );
			}
		} );
	} );

	// Shop ----------------------------------------------------------------------------------------
	wp.customize( 'shop_layout_content', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$( '.woocommerce #content .main-content>.inner-content' ).removeClass( 'container fullwidth' );
			$( '.woocommerce #content .main-content>.inner-content' ).addClass( newval );
		} );
	} );

	wp.customize( 'shop_sidebar', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			if( $('.woocommerce .content-shop .site-layout aside#secondary').length > 0 ) {
				$('.woocommerce .content-shop .site-layout').removeClass( 'left-sidebar right-sidebar no-sidebar has-sidebar' );
				if( newval != 'no-sidebar' ){
					$( '.woocommerce .content-shop .site-layout' ).addClass( 'has-sidebar' );
				}
				$('.woocommerce .content-shop .site-layout').addClass( newval );
			}
		} );
	} );

	wp.customize( 'shop_sidebar_width', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			var layout_width = $( '.content-shop .site-layout #primary' ).outerWidth();
            var content_width = layout_width - newval;
            if( $('.woocommerce .content-shop .site-layout aside#secondary').length > 0 ) {
				$('.woocommerce .content-shop .site-layout aside#secondary').css('cssText', 'flex: 0 0 ' + newval + 'px!important; max-width:' + newval + 'px!important');
				$('.woocommerce .content-shop .site-layout #primary').css('cssText', 'max-width: calc(100% - ' + content_width + ')px!important');
			}
		} );
	} );

	wp.customize( 'shop_number_column', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			removeClassStartingWith( $('.woocommerce .archive-product'), 'columns-');
			$('.woocommerce .archive-product').addClass( newval );
		} );
	} );

	wp.customize( 'single_sidebar', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			if( $('.woocommerce .content-single .site-layout aside#secondary').length > 0 ) {
				$('.woocommerce .content-single .site-layout').removeClass( 'left-sidebar right-sidebar no-sidebar has-sidebar' );
				if( newval != 'no-sidebar' ){
					$( '.woocommerce .content-single .site-layout' ).addClass( 'has-sidebar' );
				}
				$('.woocommerce .content-single .site-layout').addClass( newval );
			}
		} );
	} );

	wp.customize( 'enable_page_title_shop', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			if( newval ) {
				$( '.page-title-shop' ).removeClass('hide');
			}else{
				$( '.page-title-shop' ).addClass('hide');
			}
		} );
	} );

	wp.customize( 'style_page_title_shop', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$( '.page-title-shop' ).css( 'font-style', newval );
		} );
	} );

	wp.customize( 'page_title_shop_name', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$( '.page-title-shop .entry-title' ).text( newval );
		} );
	} );

	wp.customize( 'page_title_shop_des', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$( '.page-title-shop .sub-title p' ).text( newval );
		} );
	} );

	wp.customize( 'bg_page_title_shop', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$( '.page-title-shop' ).css( 'background-color', newval );
		} );
	} );

	wp.customize( 'color_page_title_shop', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$( '.page-title-shop,.page-title-shop .entry-detail .entry-title' ).css( 'color', newval );
		} );
	} );

	wp.customize( 'bg_image_page_title_shop', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$( '.page-title-shop' ).css( 'background-image', newval );
		} );
	} );

	wp.customize( 'bg_size_page_title_shop', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$( '.page-title-shop' ).css( 'background-size', newval );
		} );
	} );

	wp.customize( 'bg_repeat_page_title_shop', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$( '.page-title-shop' ).css( 'background-repeat', newval );
		} );
	} );

	wp.customize( 'bg_position_page_title_shop', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$( '.page-title-shop' ).css( 'background-position', newval );
		} );
	} );

	wp.customize( 'bg_attachment_page_title_shop', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$( '.page-title-shop' ).css( 'background-attachment', newval );
		} );
	} );

	wp.customize( 'font_size_page_title_shop', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			var newval = newval + 'px';
			$( '.page-title-shop .entry-title' ).css( 'font-size', newval );
		} );
	} );

	wp.customize( 'letter_spacing_page_title_shop', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			var newval = newval + 'px';
			$( '.page-title-shop .entry-title' ).css( 'letter-spacing', newval );
		} );
	} );

	wp.customize( 'page_title_bg_color', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$( '.page-title-orther' ).css( 'background-color', newval );
		} );
	} );

	wp.customize( 'page_title_text_color', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$( '.page-title-orther,.page-title-orther .entry-detail .entry-title' ).css( 'color', newval );
		} );
	} );

	wp.customize( 'page_title_bg_image', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$( '.page-title-orther' ).css( 'background-image', newval );
		} );
	} );

	wp.customize( 'page_title_bg_size', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$( '.page-title-orther' ).css( 'background-size', newval );
		} );
	} );

	wp.customize( 'page_title_bg_repeat', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$( '.page-title-orther' ).css( 'background-repeat', newval );
		} );
	} );

	wp.customize( 'page_title_bg_position', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$( '.page-title-orther' ).css( 'background-position', newval );
		} );
	} );

	wp.customize( 'page_title_bg_attachment', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			$( '.page-title-orther' ).css( 'background-attachment', newval );
		} );
	} );

	wp.customize( 'page_title_font_size', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			var newval = newval + 'px';
			$( '.page-title-orther .entry-title' ).css( 'font-size', newval );
		} );
	} );

	wp.customize( 'page_title_letter_spacing', function( value ) {
		// When the value changes.
		value.bind( function( newval ) {
			// Add CSS to elements.
			var newval = newval + 'px';
			$( '.page-title-orther .entry-title' ).css( 'letter-spacing', newval );
		} );
	} );

})(jQuery);














