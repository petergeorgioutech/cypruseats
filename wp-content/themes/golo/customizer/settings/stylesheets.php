<?php 

// Add style to style.css mytheme
function golo_add_customizer_styles() {
    wp_enqueue_style( 'golo_main-style', get_stylesheet_uri() );
    $custom_css = golo_get_customizer_css();
    wp_add_inline_style( 'golo_main-style', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'golo_add_customizer_styles', 99 );

function golo_get_customizer_css() {
	
	ob_start();

	// Variables --------------------------------------------------------------------------------------------
	$primary_color 		   = Golo_Helper::get_setting('primary_color');
	$text_color 		   = Golo_Helper::get_setting('text_color');
	$accent_color 	   	   = Golo_Helper::get_setting('accent_color');
	$body_background_color = Golo_Helper::get_setting('body_background_color');
	$bg_body_image 		   = Golo_Helper::get_setting('bg_body_image');
	$bg_body_size 		   = Golo_Helper::get_setting('bg_body_size');
	$bg_body_repeat 	   = Golo_Helper::get_setting('bg_body_repeat');
	$bg_body_position 	   = Golo_Helper::get_setting('bg_body_position');
	$bg_body_attachment    = Golo_Helper::get_setting('bg_body_attachment');

	$body_font_type		   = Golo_Helper::get_setting('body_font_type');
	$font_family 	 	   = $body_font_type['font-family'];
	$font_style 	 	   = $body_font_type['font-style'];
	$font_size 	 	       = $body_font_type['font-size'];
	$font_weight 	 	   = $body_font_type['variant'];

	$content_width 		   = Golo_Helper::get_setting('content_width');
	$sidebar_width 		   = Golo_Helper::get_setting('sidebar_width');

	$header_sticky_background = Golo_Helper::get_setting('header_sticky_background');

	$search_form_width     = Golo_Helper::get_setting('search_form_width');
	$logo_width            = Golo_Helper::get_setting('logo_width');
	$header_padding_top    = Golo_Helper::get_setting('header_padding_top');
	$header_padding_bottom = Golo_Helper::get_setting('header_padding_bottom');

	$blog_sidebar_width    = Golo_Helper::get_setting('blog_sidebar_width');

	$shop_sidebar_width    = Golo_Helper::get_setting('shop_sidebar_width');

	$page_title_bg_color       = Golo_Helper::get_setting('page_title_bg_color');
	$page_title_text_color     = Golo_Helper::get_setting('page_title_text_color');
	$page_title_bg_image       = Golo_Helper::get_setting('page_title_bg_image');
	$page_title_bg_size        = Golo_Helper::get_setting('page_title_bg_size');
	$page_title_bg_repeat      = Golo_Helper::get_setting('page_title_bg_repeat');
	$page_title_bg_position    = Golo_Helper::get_setting('page_title_bg_position');
	$page_title_bg_attachment  = Golo_Helper::get_setting('page_title_bg_attachment');
	$page_title_font_size      = Golo_Helper::get_setting('page_title_font_size');
	$page_title_letter_spacing = Golo_Helper::get_setting('page_title_letter_spacing');
	if( empty($page_title_letter_spacing) ){
		$page_title_letter_spacing = 'normal';
	}else{
		$page_title_letter_spacing = $page_title_letter_spacing.'px';
	}

	$style_page_title_blog          = Golo_Helper::get_setting('style_page_title_blog');
	$bg_page_title_blog             = Golo_Helper::get_setting('bg_page_title_blog');
	$color_page_title_blog          = Golo_Helper::get_setting('color_page_title_blog');
	$bg_image_page_title_blog       = Golo_Helper::get_setting('bg_image_page_title_blog');
	$bg_size_page_title_blog        = Golo_Helper::get_setting('bg_size_page_title_blog');
	$bg_repeat_page_title_blog      = Golo_Helper::get_setting('bg_repeat_page_title_blog');
	$bg_position_page_title_blog    = Golo_Helper::get_setting('bg_position_page_title_blog');
	$bg_attachment_page_title_blog  = Golo_Helper::get_setting('bg_attachment_page_title_blog');
	$font_size_page_title_blog      = Golo_Helper::get_setting('font_size_page_title_blog');
	$letter_spacing_page_title_blog = Golo_Helper::get_setting('letter_spacing_page_title_blog');
	if( empty($letter_spacing_page_title_blog) ){
		$letter_spacing_page_title_blog = 'normal';
	}else{
		$letter_spacing_page_title_blog = $letter_spacing_page_title_blog.'px';
	}

	$style_page_title_shop          = Golo_Helper::get_setting('style_page_title_shop');
	$bg_page_title_shop             = Golo_Helper::get_setting('bg_page_title_shop');
	$color_page_title_shop          = Golo_Helper::get_setting('color_page_title_shop');
	$bg_image_page_title_shop       = Golo_Helper::get_setting('bg_image_page_title_shop');
	$bg_size_page_title_shop        = Golo_Helper::get_setting('bg_size_page_title_shop');
	$bg_repeat_page_title_shop      = Golo_Helper::get_setting('bg_repeat_page_title_shop');
	$bg_position_page_title_shop    = Golo_Helper::get_setting('bg_position_page_title_shop');
	$bg_attachment_page_title_shop  = Golo_Helper::get_setting('bg_attachment_page_title_shop');
	$font_size_page_title_shop      = Golo_Helper::get_setting('font_size_page_title_shop');
	$letter_spacing_page_title_shop = Golo_Helper::get_setting('letter_spacing_page_title_shop');
	if( empty($letter_spacing_page_title_shop) ){
		$letter_spacing_page_title_shop = 'normal';
	}else{
		$letter_spacing_page_title_shop = $letter_spacing_page_title_shop.'px';
	}

	// Primary Color ----------------------------------------------------------------------------------------
	if ( !empty( $primary_color ) ) {
	    ?>
	    .block-heading .entry-title,.woocommerce .products .product .entry-detail .product-title .entry-title a,.woocommerce #reviews #review_form_wrapper .comment-reply-title,.woocommerce #reviews #comments .woocommerce-Reviews-title,#comments .comments-title,#comments .comment-author .entry-detail .author-name a,.products.related > h2, .products.upsells > h2,.woocommerce div.product .woocommerce-tabs ul.tabs li a,.woocommerce div.product .product_title,#respond .comment-reply-title,.single .post .inner-post-wrap .post-author .head-author .entry-title a,.mobile-menu .menu li a,.single .post .inner-post-wrap .post-title .entry-title,.archive-post .post .post-title a,.dropdown-select,header.site-header, .single-post .post-content dt,#comments dt,.single-post .post-content strong,#comments strong,.mobile-menu,.single-place .place-reviews .reviews-list .reply a,.archive .information .entry-detail strong,.archive .nav-categories .entry-categories ul li a,.single .post .inner-post-wrap .post-author .head-author .entry-title a,.woocommerce div.product .woocommerce-tabs ul.tabs li a,.woocommerce div.product form.cart .variations label,.woocommerce div.product p.price,.woocommerce div.product span.price,.woocommerce div.product div.summary p.price,.woocommerce div.product form.cart.grouped_form .woocommerce-grouped-product-list-item__price,.product-quantity input,.woocommerce #reviews #review_form_wrapper .comment-reply-title,.woocommerce #reviews #review_form_wrapper .comment-form-rating label {
	        color: <?php echo esc_attr($primary_color); ?>;
	    }
	    <?php
	}

	// Text Color ------------------------------------------------------------------------------------------
	if ( !empty( $text_color ) ) {
	    ?>
	    body,.woocommerce nav.woocommerce-pagination ul li .page-numbers,.posts-pagination ul li .page-numbers,.archive .block-heading.category-heading .entry-result,.golo-menu-filter ul.filter-control a,.woocommerce div.product .woocommerce-product-rating a,.woocommerce div.product div.summary .product_meta > span span,.woocommerce div.product div.summary .product_meta > span a {
	        color: <?php echo esc_attr($text_color); ?>;
	    }
	    <?php
	}

	// Highlight Color --------------------------------------------------------------------------------------
	if ( !empty( $accent_color ) ) {
	    ?>
	    .single-place .entry-heading > a,.block-heading .entry-count,.custom-checkbox:checked:before,#commentform #wp-comment-cookies-consent:checked:before,header.site-header .right-header .minicart a.toggle span.cart-count,.block-heading .entry-count,.woocommerce div.product .woocommerce-tabs ul.tabs li.active a, .single-place .site-layout .place-amenities .hidden-amenities > a {
			color: <?php echo esc_attr($accent_color); ?>;
		}
		.golo-menu-filter ul.filter-control a:before,.golo-menu-filter ul.filter-control li.active a:before,.woocommerce nav.woocommerce-pagination ul li .page-numbers.current, .posts-pagination ul li .page-numbers.current,.woocommerce span.onsale {
			background: <?php echo esc_attr($accent_color); ?>;
		}
		.place-item .btn-add-to-wishlist.added svg path {
			fill: <?php echo esc_attr($accent_color); ?>;
    		stroke: <?php echo esc_attr($accent_color); ?>;
		}
		.place-item .btn-add-to-wishlist .golo-dual-ring:after {
			border-color: <?php echo esc_attr($accent_color); ?> transparent <?php echo esc_attr($accent_color); ?> transparent;
		}
	    <?php
	}

	// Hover Color -----------------------------------------------------------------------------------------
	if ( !empty( $accent_color ) ) {
	    ?>
	    .popup-booking .list-group li.place-name a strong,.golo-table td.place-control a:hover,.account .user-control li.active a,.golo-marker .place-rating,.place-item.layout-02 .entry-head .place-city a:hover,.place-item.layout-03 .entry-head .place-city a:hover,.golo-nav-filter.active .golo-clear-filter,.place-item .btn-add-to-wishlist.added i,.agent-manager .agent-nav ul li.active a,.woocommerce-error:before,.woocommerce .checkout.woocommerce-checkout #order_review table tfoot tr.order-total td .amount,.woocommerce-info a.showcoupon,.woocommerce .woocommerce-message a.button, .woocommerce .woocommerce-info a.button, .woocommerce .woocommerce-error a.button,.woocommerce nav.woocommerce-pagination ul li .page-numbers:hover, .posts-pagination ul li .page-numbers:hover, .woocommerce nav.woocommerce-pagination ul li .page-numbers:focus, .posts-pagination ul li .page-numbers:focus,.archive-post .post .post-meta .post-author a:hover,.minicart .top-mb-menu .your-cart a .cart-count,.widget_categories li,.single-place .place-thumbnails.type-1 .single-place-thumbs .place-meta > div .rating-count,.author-rating .star.checked i,.single-place .site-layout.type-1 .place-map > a .redirect,.single-place .place-content a,.single-place .site-layout.type-1 .place-amenities .hidden-amenities > a,.single-place .single-place-thumbs .entry-nav .btn-add-to-wishlist.added,.dropdown-select ul li.active a,header.site-header .right-header .minicart a.toggle span.cart-count,.place-search .form-control.nice-select .current,.archive .nav-categories .entry-categories ul li.active a,.place-item .place-preview .place-rating,.single-place .place-reviews .entry-heading .rating-count,.woocommerce #reviews #review_form_wrapper .comment-form-rating .stars a,.woocommerce #reviews #comments ol.commentlist .star-rating > span,.product-quantity .btn-quantity:hover i,a:hover,.slick-arrow:hover,.widget_calendar tbody tr > td:hover,.widget ul > li a:hover,.city-item .entry-detail a:hover,input:checked ~ label:before,.woocommerce-info:before, label:hover:before, label:hover ~ label:before,.golo-filter-toggle:hover,.single-place .place-reviews .reviews-list .entry-nav a:hover, .golo-clear-filter:hover, .golo-filter-toggle.active, .golo-clear-filter.active,.place-search .btn-close:hover,.archive .nav-categories .entry-categories ul li a:hover,.archive .nav-categories .entry-categories ul li a:hover,.single-place .entry-categories a:hover,.place-item .place-title a:hover,.woocommerce div.product div.summary .product_meta > span a:hover,.product-quantity .btn-quantity:hover svg path,a.customize-unpreviewable:hover,.account a:hover,.mobile-menu .menu li a:hover,.archive-post .post .post-title a:hover,.single .post .inner-post-wrap .post-author .head-author .entry-title a:hover,.single .post .inner-post-wrap .post-meta .post-author a:hover,.single .post .inner-post-wrap .post-categories a:hover,.archive-post .post .post-categories li a:hover,.woocommerce .products .product .entry-detail .product-title .entry-title a:hover, div.elementor-slick-slider ul.slick-dots li.slick-active button:before,.golo-swiper .swiper-pagination-bullet:hover, .golo-swiper .swiper-pagination-bullet.swiper-pagination-bullet-active,.single-place .site-layout .place-meta > div .rating-count,.place-menu .toggle-desc a,.place-reviews .login-for-review a,.archive-layout.layout-column .entry-center a.active, .archive-layout.layout-top-filter .entry-center a.active, .archive-layout.layout-top-filter .nav-bar .archive-filter .golo-menu-filter .col .entry-filter.active > h4, .archive-layout.layout-top-filter .nav-bar .archive-filter .golo-menu-filter .col .entry-filter.open > h4, .archive.author .contact-info i, .archive.author .author-reviews .reviews-list .entry-bottom a, .popup-account .accept-account a, .popup-account .forgot-password a, .golo-reset-password-wrap .back-to-login {
			color: <?php echo esc_attr($accent_color); ?>;
		}
		.filter-place-search .btn-close,.archive-layout.layout-column .top-area .entry-right .btn-maps-filter a,.golo-page-title.layout-column.layout-01 .entry-detail,.golo-ldef-spinner span:after,.golo-ldef-grid span,.golo-ldef-ellipsis span,.golo-ldef-default span,.golo-ldef-roller span:after,.golo-ldef-heart span:after,.golo-ldef-heart span:before,.golo-ldef-heart span,.golo-ldef-facebook span,.golo-ldef-circle > span,.woocommerce .wc-proceed-to-checkout a.checkout-button:hover,.woocommerce .wc-proceed-to-checkout a.checkout-button,.golo-pagination .page-numbers.current,.gl-button, .wpcf7-submit,.woocommerce .checkout.woocommerce-checkout #order_review #payment .place-order .button:hover,.minicart .woocommerce-mini-cart__buttons a.elementor-button--checkout, .minicart .elementor-menu-cart__footer-buttons a.elementor-button--checkout,.widget_calendar caption,.widget_calendar tbody tr > td#today,.newsletter-area .submit-control,.woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button,.single-place .place-booking a.gl-button,.woocommerce nav.woocommerce-pagination ul li .page-numbers.current, .posts-pagination ul li .page-numbers.current, .woocommerce span.onsale,.woocommerce #respond input#submit, .woocommerce button.button, .woocommerce input.button,.woocommerce div.product form.cart .button,.btn-golo,.golo-button a,.woocommerce .products .product .entry-detail .button.add_to_cart_button, .woocommerce .products .product .entry-detail .button.product_type_external, .woocommerce .products .product .entry-detail .button.product_type_grouped, .woocommerce .products .product .entry-detail .button.product_type_variation,.archive .nav-categories .entry-categories ul li a:after,.archive .nav-categories .entry-categories ul li a:hover:after,.archive .nav-categories .entry-categories ul li.active a:after,.woocommerce nav.woocommerce-pagination ul li .page-numbers.current:hover, .posts-pagination ul li .page-numbers.current:hover,.archive-post .post .btn-readmore a:after, .block-search.layout-02 .location-field .icon-search, .block-search.layout-03 .location-field .icon-search, .block-search.layout-02 .type-field .icon-search, .block-search.layout-03 .type-field .icon-search, .golo-pagination .pagination.loadmore .page-numbers,.inner-content.has-map .btn-control .slider, .nav-bar.has-map .btn-control .slider,.golo-page-title.layout-01 .entry-detail,.custom-form-search button.search-submit {
			background: <?php echo esc_attr($accent_color); ?>;
		}
	    .btn-control input:checked + .slider,.filter-place-search .btn-close,.archive-layout.layout-column .top-area .entry-right .btn-maps-filter a,.golo-place-multi-step .golo-steps .listing-menu li.active a,.golo-place-multi-step .golo-steps .listing-menu li a:hover,.golo-ldef-ripple span,.woocommerce button.button:hover,.woocommerce .wc-proceed-to-checkout a.checkout-button:hover,.woocommerce .inner-action-form .coupon input,.gl-button, .wpcf7-submit,.place-manager-form .form-group .form-control, .place-manager-form .form-group .chosen-choices:focus, .golo-my-profile .form-group input.form-control:focus,.minicart .woocommerce-mini-cart__buttons a.elementor-button--checkout, .minicart .elementor-menu-cart__footer-buttons a.elementor-button--checkout,.block-search.search-input .input-search:focus,.block-search.search-input .input-search:focus,.woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button,.woocommerce-info,.post-categories li a, .single-place .entry-categories a, .single-place .entry-categories span,.single-place .place-booking a.gl-button,.woocommerce #respond input#submit, .woocommerce button.button, .woocommerce input.button,.woocommerce div.product form.cart .button,.btn-golo,.archive-post .post .post-categories li,.golo-button a,.single .post .inner-post-wrap .post-categories a,.woocommerce .products .product .entry-detail .button.add_to_cart_button,.woocommerce .products .product .entry-detail .button.product_type_external, .woocommerce .products .product .entry-detail .button.product_type_grouped, .woocommerce .products .product .entry-detail .button.product_type_variation, .golo-pagination .pagination.loadmore .page-numbers, .archive-layout.layout-top-filter .nav-bar .archive-filter .golo-menu-filter .col .entry-filter.active > h4, .archive-layout.layout-top-filter .nav-bar .archive-filter .golo-menu-filter .col .entry-filter.open > h4, .archive.author .author-info .info-area .btn-send-message > a:hover {
			border-color: <?php echo esc_attr($accent_color); ?>;
		}
		.gl-button:hover, .wpcf7-submit:hover,.minicart .woocommerce-mini-cart__buttons a.elementor-button--checkout:hover, .minicart .elementor-menu-cart__footer-buttons a.elementor-button--checkout:hover,.single-place .place-booking a.gl-button:hover,.woocommerce #respond input#submit:hover,.woocommerce button.button:hover, .woocommerce input.button:hover,.woocommerce div.product form.cart .button:hover,.btn-golo:hover,.golo-button a:hover,.woocommerce .products .product .entry-detail .button.add_to_cart_button:hover, .woocommerce .products .product .entry-detail .button.product_type_external:hover, .woocommerce .products .product .entry-detail .button.product_type_grouped:hover, .woocommerce .products .product .entry-detail .button.product_type_variation:hover,.archive.author .author-info .info-area .btn-send-message > a:hover {
			background: transparent;
			color: <?php echo esc_attr($accent_color); ?>;
		}
		.golo-dual-ring:after,.golo-ldef-hourglass:after,.golo-ldef-dual-ring:after {
			border-color: <?php echo esc_attr($accent_color); ?> transparent <?php echo esc_attr($accent_color); ?> transparent;
		}
		.golo-ldef-ring span {
			border-color: <?php echo esc_attr($accent_color); ?> transparent transparent transparent;
		}
		.golo-page-title.layout-column.block-left .entry-detail .after-image svg use, .golo-page-title.layout-01 .entry-detail .after-image svg use {
			fill: <?php echo esc_attr($accent_color); ?>;
		}
		.accent-color {
			color: <?php echo esc_attr($accent_color); ?>!important;
		}
		.xdsoft_datetimepicker .xdsoft_calendar td.xdsoft_default, .xdsoft_datetimepicker .xdsoft_calendar td.xdsoft_current {
			background: <?php echo esc_attr($accent_color); ?>;
			box-shadow: <?php echo esc_attr($accent_color); ?> 0 1px 3px 0 inset;
		}
		.xdsoft_datetimepicker .xdsoft_calendar td:hover {
			background: <?php echo esc_attr($accent_color); ?>!important;
		}
		.xdsoft_datetimepicker .xdsoft_timepicker .xdsoft_time_box > div > div.xdsoft_current,.xdsoft_datetimepicker .xdsoft_timepicker .xdsoft_time_box > div > div:hover {
			color: <?php echo esc_attr($accent_color); ?>!important;
		}
	    <?php
	}

	// Body Background Custom ------------------------------------------------------------------------------
	?>
	    body {
	    	<?php if( !empty( $body_background_color ) ) : ?>
	        background-color: <?php echo esc_attr($body_background_color); ?>;
	    	<?php endif; ?>
	    	<?php if( !empty( $bg_body_image ) ) : ?>
	        background-image: url(<?php echo esc_attr($bg_body_image); ?>);
	    	<?php endif; ?>
	    	<?php if( !empty( $bg_body_size ) ) : ?>
	        background-size: <?php echo esc_attr($bg_body_size); ?>;
	    	<?php endif; ?>
	    	<?php if( !empty( $bg_body_repeat ) ) : ?>
	        background-repeat: <?php echo esc_attr($bg_body_repeat); ?>;
	    	<?php endif; ?>
	    	<?php if( !empty( $bg_body_position ) ) : ?>
	        background-position: <?php echo esc_attr($bg_body_position); ?>;
	    	<?php endif; ?>
	    	<?php if( !empty( $bg_body_attachment ) ) : ?>
	        background-attachment: <?php echo esc_attr($bg_body_attachment); ?>;
	    	<?php endif; ?>
	    }
    <?php

    // Content Width ---------------------------------------------------------------------------------------
	if ( !empty( $content_width ) ) {
	    ?>
	    #page.fullwidth {
	        max-width: <?php echo esc_attr($content_width); ?>px;
	    }
	    <?php
	}

	// Sidebar Width ---------------------------------------------------------------------------------------
	if ( !empty( $sidebar_width ) ) {
	    ?>
	    .content-page .site-layout.has-sidebar aside#secondary {
	        flex: 0 0 <?php echo esc_attr($sidebar_width); ?>px;
	        max-width: <?php echo esc_attr($sidebar_width); ?>px;
	    }
	    .content-page .site-layout.has-sidebar #primary {
	        flex: 1;
	        max-width: calc(100% - <?php echo esc_attr($sidebar_width); ?>px);
	    }
	    <?php
	}

	// Header Sticky Background ---------------------------------------------------------------------------
	if ( !empty( $header_sticky_background ) ) {
	    ?>
	    .uxper-sticky.on {
	        background-color: <?php echo esc_attr($header_sticky_background); ?>!important;
	    }
	    <?php
	}

	// Search Form Width ---------------------------------------------------------------------------------------
	if ( !empty( $search_form_width ) ) {
	    ?>
	    header.site-header .block-search.search-input {
	        max-width: <?php echo esc_attr($search_form_width); ?>px;
	    }
	    header.site-header .block-search.search-form.layout-02 form,
	    header.site-header .block-search.search-form.layout-03 form{
	        width: <?php echo esc_attr($search_form_width); ?>px;
	    }
	    <?php
	}

	// Logo Width ---------------------------------------------------------------------------------------
	if ( !empty( $logo_width ) ) {
	    ?>
	    header.site-header .site-logo img {
	        max-width: <?php echo esc_attr($logo_width); ?>px;
	    }
	    <?php
	}

	// Header Padding Top ---------------------------------------------------------------------------------------
	if ( !empty( $header_padding_top ) ) {
	    ?>
	    header.site-header {
	        padding-top: <?php echo esc_attr($header_padding_top); ?>px;
	    }
	    <?php
	}

	// Header Padding Bottom ---------------------------------------------------------------------------------------
	if ( !empty( $header_padding_bottom ) ) {
	    ?>
	    header.site-header {
	        padding-bottom: <?php echo esc_attr($header_padding_bottom); ?>px;
	    }
	    <?php
	}

	if ( !empty( $blog_sidebar_width ) ) {
	    ?>
	    .content-blog .site-layout.has-sidebar aside#secondary {
	        flex: 0 0 <?php echo esc_attr($blog_sidebar_width); ?>px;
	        max-width: <?php echo esc_attr($blog_sidebar_width); ?>px;
	    }
	    .content-blog .site-layout.has-sidebar #primary {
	        flex: 1;
	        max-width: calc(100% - <?php echo esc_attr($blog_sidebar_width); ?>px);
	    }
	    <?php
	}

	if ( !empty( $shop_sidebar_width ) ) {
	    ?>
	    .site-layout.has-sidebar aside#secondary {
	        flex: 0 0 <?php echo esc_attr($shop_sidebar_width); ?>px;
	        max-width: <?php echo esc_attr($shop_sidebar_width); ?>px;
	    }
	    .site-layout.has-sidebar #primary {
	        flex: 1;
	        max-width: calc(100% - <?php echo esc_attr($shop_sidebar_width); ?>px);
	    }
	    <?php
	}

	// Page Title ------------------------------------------------------------------------------------------
	?>
	.page-title-blog {
		background-image: url(<?php echo esc_attr($bg_image_page_title_blog); ?>);
		background-color: <?php echo esc_attr($bg_page_title_blog); ?>;
		background-size: <?php echo esc_attr($bg_size_page_title_blog); ?>;
		background-repeat: <?php echo esc_attr($bg_repeat_page_title_blog); ?>;
		background-position: <?php echo esc_attr($bg_position_page_title_blog); ?>;
		background-attachment: <?php echo esc_attr($bg_attachment_page_title_blog); ?>
	}
    .page-title-blog,.page-title-blog .entry-detail .entry-title {
        font-style: <?php echo esc_attr($style_page_title_blog); ?>;
        color: <?php echo esc_attr($color_page_title_blog); ?>;
    }
    .page-title-blog .entry-title {
		font-size: <?php echo esc_attr($font_size_page_title_blog); ?>px;
        letter-spacing: <?php echo esc_attr($letter_spacing_page_title_blog); ?>;
	}

	.page-title-shop {
		background-image: url(<?php echo esc_attr($bg_image_page_title_shop); ?>);
		background-color: <?php echo esc_attr($bg_page_title_shop); ?>;
		background-size: <?php echo esc_attr($bg_size_page_title_shop); ?>;
		background-repeat: <?php echo esc_attr($bg_repeat_page_title_shop); ?>;
		background-position: <?php echo esc_attr($bg_position_page_title_shop); ?>;
		background-attachment: <?php echo esc_attr($bg_attachment_page_title_shop); ?>
	}
    .page-title-shop,.page-title-shop .entry-detail .entry-title {
        font-style: <?php echo esc_attr($style_page_title_shop); ?>;
        color: <?php echo esc_attr($color_page_title_shop); ?>;
    }
    .page-title-shop .entry-title {
		font-size: <?php echo esc_attr($font_size_page_title_shop); ?>px;
        letter-spacing: <?php echo esc_attr($letter_spacing_page_title_shop); ?>;
	}
	.page-title-orther,
	.page-title-other {
		background-image: url(<?php echo esc_attr($page_title_bg_image); ?>);
		background-color: <?php echo esc_attr($page_title_bg_color); ?>;
		background-size: <?php echo esc_attr($page_title_bg_size); ?>;
		background-repeat: <?php echo esc_attr($page_title_bg_repeat); ?>;
		background-position: <?php echo esc_attr($page_title_bg_position); ?>;
		background-attachment: <?php echo esc_attr($page_title_bg_attachment); ?>
	}
	.page-title-orther,
    .page-title-other,
    .page-title-orther .entry-detail .entry-title,
    .page-title-other .entry-detail .entry-title {
        color: <?php echo esc_attr($page_title_text_color); ?>;
    }
    .page-title-orther .entry-title,
    .page-title-other .entry-title {
		font-size: <?php echo esc_attr($page_title_font_size); ?>px;
        letter-spacing: <?php echo esc_attr($page_title_letter_spacing); ?>;
	}
    <?php

	$css = ob_get_clean();
	return $css;
}