<?php 

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists('Golo_Template_Loader') ) {
    /**
     * Golo_Template_Loader
     */
    class Golo_Template_Loader {
    	/**
		 * Constructor
		 * *******************************************************
		 */
		public function __construct()
		{	
			$this->template_hooks();
            $this->includes();
		}

        /**
         * Includes library for plugin
         * *******************************************************
         */
        private function includes() 
        {
            require_once GOLO_PLUGIN_DIR . 'includes/golo-template-hooks.php';
        }

        /**
         * Register the stylesheets for the public-facing side of the site.
         */
        public function admin_enqueue() {
            $min_suffix = golo_get_option('enable_min_css', 0) == 1 ? '.min' : '';

            wp_enqueue_style('line-awesome', GOLO_PLUGIN_URL . 'assets/libs/line-awesome/css/line-awesome.min.css', array(), '1.1.0', 'all');

            wp_enqueue_style('hint', GOLO_PLUGIN_URL . 'assets/libs/hint/hint.min.css', array(), '2.6.0', 'all');

            wp_enqueue_script('lottie', GOLO_PLUGIN_URL . 'assets/libs/lottie/lottie.min.js', array('jquery'), false, true);

            wp_enqueue_script('magnific-popup', GOLO_PLUGIN_URL . 'assets/libs/magnific-popup/jquery.magnific-popup.min.js', array('jquery'), false, true);

            wp_enqueue_style('magnific-popup', GOLO_PLUGIN_URL . 'assets/libs/magnific-popup/magnific-popup.css', array(), GOLO_PLUGIN_VER, 'all');
            
            wp_enqueue_style(GOLO_PLUGIN_PREFIX . '-admin', GOLO_PLUGIN_URL . 'assets/css/_admin' . $min_suffix . '.css', array(), GOLO_PLUGIN_VER, 'all');

            wp_enqueue_script(GOLO_PLUGIN_PREFIX . 'import', GOLO_PLUGIN_URL . 'assets/js/import' . $min_suffix . '.js', array('jquery'), GOLO_PLUGIN_VER, true);

            wp_localize_script(GOLO_PLUGIN_PREFIX . 'import', 'golo_import_vars',
                array(
                    'ajax_url'      => GOLO_AJAX_URL,
                    'animation_url' => GOLO_PLUGIN_URL . 'assets/animation/',
                )
            );

            wp_enqueue_script(GOLO_PLUGIN_PREFIX . 'admin', GOLO_PLUGIN_URL . 'assets/js/admin' . $min_suffix . '.js', array('jquery'), GOLO_PLUGIN_VER, true);

            wp_localize_script(GOLO_PLUGIN_PREFIX . 'admin', 'golo_admin_vars',
                array(
                    'ajax_url' => GOLO_AJAX_URL,
                )
            );
        }

        /**
         * Register the JavaScript for the admin area.
         */
        public function enqueue_scripts()
        {
            $min_suffix = golo_get_option('enable_min_js', 0) == 1 ? '.min' : '';
            $datetimepicker_language            = golo_get_option('datetimepicker_language', '' );
            
            wp_register_script('stripe-checkout','https://checkout.stripe.com/checkout.js', array(), null, true);

            wp_enqueue_script('slick', GOLO_PLUGIN_URL . 'assets/libs/slick/slick.min.js', array('jquery'), '1.8.1', true);

            wp_enqueue_script('lightgallery', GOLO_PLUGIN_URL . 'assets/libs/lightgallery/js/lightgallery.min.js', array('jquery'), false, true);

            wp_enqueue_script('waypoints', GOLO_PLUGIN_URL . 'assets/libs/waypoints/jquery.waypoints.js', array( 'jquery' ), '4.0.1', true);

            wp_enqueue_script('nice-select', GOLO_PLUGIN_URL . 'assets/libs/jquery-nice-select/js/jquery.nice-select.min.js', array('jquery'), '1.1.0', true);

            wp_enqueue_script('select2', GOLO_PLUGIN_URL . 'assets/libs/select2/js/select2.min.js', array('jquery'), '4.0.13', true);

            wp_enqueue_script('mojs', GOLO_PLUGIN_URL . 'assets/libs/mojs/js/mo.min.js', array('jquery'), '0.288.2', true);

            wp_enqueue_script('lity', GOLO_PLUGIN_URL . 'assets/libs/lity/js/lity.min.js', array('jquery'), false, true);

            wp_enqueue_script('datetimepicker', GOLO_PLUGIN_URL . 'assets/libs/datetimepicker/jquery.datetimepicker.full.min.js', array('jquery'), false, true);

            wp_register_script('jquery-validate', GOLO_PLUGIN_URL . 'assets/libs/validate/jquery.validate.min.js', array('jquery'), '1.17.0', true);

            wp_register_script(GOLO_PLUGIN_PREFIX . 'frontend', GOLO_PLUGIN_URL . 'assets/js/frontend' . $min_suffix . '.js', array('jquery'), GOLO_PLUGIN_VER, true);

            wp_register_script(GOLO_PLUGIN_PREFIX . 'my-place', GOLO_PLUGIN_URL . 'assets/js/my-place' . $min_suffix . '.js', array('jquery'), GOLO_PLUGIN_VER, true);

            wp_register_script(GOLO_PLUGIN_PREFIX . 'my-profile', GOLO_PLUGIN_URL . 'assets/js/my-profile' . $min_suffix . '.js', array('jquery'), GOLO_PLUGIN_VER, true);

            wp_enqueue_script(GOLO_PLUGIN_PREFIX . 'payment', GOLO_PLUGIN_URL . 'assets/js/payment/payment' . $min_suffix . '.js', array('jquery', 'wp-util'), GOLO_PLUGIN_VER, true);

            wp_enqueue_script(GOLO_PLUGIN_PREFIX . 'invoice', GOLO_PLUGIN_URL . 'assets/js/invoice/invoice' . $min_suffix . '.js', array('jquery'), GOLO_PLUGIN_VER, true);

            $payment_data = array(
                'ajax_url'        => GOLO_AJAX_URL,
                'processing_text' => esc_html__('Processing, Please wait...', 'golo-framework')
            );
            wp_localize_script(GOLO_PLUGIN_PREFIX . 'payment', 'golo_payment_vars', $payment_data);

            wp_enqueue_script(GOLO_PLUGIN_PREFIX . 'template', GOLO_PLUGIN_URL . 'assets/js/template' . $min_suffix . '.js', array('jquery'), GOLO_PLUGIN_VER, true);

            wp_add_inline_script(GOLO_PLUGIN_PREFIX . 'template', 'var Golo_Inline_Style = document.getElementById( \'golo_main-style-inline-css\' );' );

            $archive_place_items_amount = golo_get_option('archive_place_items_amount', '12');
            $archive_city_items_amount  = golo_get_option('archive_city_items_amount', '12');
            $map_zoom_level             = golo_get_option('map_zoom_level', '12');
            $map_pin_cluster            = golo_get_option('map_pin_cluster', 1);
            $map_type                   = golo_get_option('map_type', 'google_map');
            $google_map_type            = 'roadmap';
            if( $map_type == 'google_map' ){
                $google_map_style           = golo_get_option('googlemap_style', '');
                $google_map_type            = golo_get_option('googlemap_type', 'roadmap');
            } else {
                $google_map_style           = golo_get_option('mapbox_style', 'streets-v11');
            }
            $google_map_needed          = 'true';
            $map_marker_icon_url        = GOLO_PLUGIN_URL . 'assets/images/map-marker-icon.png';
            $map_cluster_icon_url       = GOLO_PLUGIN_URL . 'assets/images/cluster-icon.png';
            $enable_filter_location     = golo_get_option('enable_filter_location', 1);
            $enable_city_map            = golo_get_option('enable_city_map', 1);
            $enable_archive_map         = golo_get_option('enable_archive_map', 1);

            $item_amount = $archive_place_items_amount;
            $taxonomy_name = get_query_var('taxonomy');
            if( $taxonomy_name == 'place-city' ) {
                $item_amount = $archive_city_items_amount;
            }
            
            $wishlist_color = '#23d3d3';
            if( class_exists('Golo_Helper') ) {
                $wishlist_color = Golo_Helper::get_setting('accent_color');
            }
            wp_localize_script(GOLO_PLUGIN_PREFIX . 'template', 'golo_template_vars',
                array(
                    'ajax_url'               => GOLO_AJAX_URL,
                    'not_found'              => esc_html__("We didn't find any results, you can retry with other keyword.", 'golo-framework'),
                    'not_place'              => esc_html__('No place found', 'golo-framework'),
                    'no_results'             => esc_html__('No Results', 'golo-framework'),
                    'item_amount'            => $item_amount,
                    'wishlist_color'         => $wishlist_color,
                    'wishlist_save'          => esc_html__('Save', 'golo-framework'),
                    'wishlist_saved'         => esc_html__('Saved', 'golo-framework'),
                    'marker_image_size'      => '100x100',
                    'googlemap_default_zoom' => $map_zoom_level,
                    'map_pin_cluster'        => $map_pin_cluster,
                    'marker_default_icon'    => $map_marker_icon_url,
                    'clusterIcon'            => $map_cluster_icon_url,
                    'google_map_needed'      => $google_map_needed,
                    'google_map_style'       => $google_map_style,
                    'google_map_type'        => $google_map_type,
                    'enable_filter_location' => $enable_filter_location,
                    'enable_city_map'        => $enable_city_map,
                    'enable_archive_map'     => $enable_archive_map,
                    'booking_success'        => esc_html__('You successfully created your booking', 'golo-framework'),
                    'booking_error'          => esc_html__('Please check your form booking', 'golo-framework'),
                    'sending_text'           => esc_html__('Sending email, Please wait...', 'golo-framework'),
                )
            );

            // Google map API
            $map_ssl        = golo_get_option('map_ssl', 0);
            $map_type       = golo_get_option('map_type', '');
            
            if( $map_type == 'google_map' ){
            
                $googlemap_api_key = golo_get_option('googlemap_api_key', 'AIzaSyBvPDNG6pePr9iFpeRKaOlaZF_l0oT3lWk');
                if (esc_html($map_ssl) == 1 || is_ssl()) {
                    wp_register_script('google-map', 'https://maps-api-ssl.google.com/maps/api/js?libraries=places&language=' . get_locale() . '&key=' . esc_html($googlemap_api_key), array('jquery'), GOLO_PLUGIN_VER, true);
                } else {
                    wp_register_script('google-map', 'http://maps.googleapis.com/maps/api/js?libraries=places&language=' . get_locale() . '&key=' . esc_html($googlemap_api_key), array('jquery'), GOLO_PLUGIN_VER, true);
                }
            }

            if ($map_pin_cluster != 0) {
                wp_register_script( 'markerclusterer', GOLO_PLUGIN_URL . 'assets/libs/markerclusterer/markerclusterer.js', array('jquery'), false, true);
            }

            // Mapbox
            if( $map_type == 'mapbox' ) {
                wp_enqueue_script('mapbox', 'https://api.mapbox.com/mapbox-gl-js/v1.8.1/mapbox-gl.js', array('jquery'), '1.8.1', false);
                wp_enqueue_style('mapbox','https://api.mapbox.com/mapbox-gl-js/v1.8.1/mapbox-gl.css', array(), '1.8.1', 'all');
            }

            // Facebook API
            $facebook_app_id = golo_get_option('facebook_app_id', '697200430787915');
            if ($facebook_app_id) {
                if ( is_ssl() ) {
                    wp_register_script('facebook-api', 'https://connect.facebook.net/'. get_locale() .'/sdk.js#xfbml=1&version=v4.0&appId=' . $facebook_app_id . '&autoLogAppEvents=1', array('jquery'), GOLO_PLUGIN_VER, true);
                } else {
                    wp_register_script('facebook-api', 'http://connect.facebook.net/'. get_locale() .'/sdk.js#xfbml=1&version=v4.0&appId=' . $facebook_app_id . '&autoLogAppEvents=1', array('jquery'), GOLO_PLUGIN_VER, true);
                }
            }
        }

        /**
         * Register the stylesheets for the public-facing side of the site.
         */
        public function enqueue_styles()
        {
            $min_suffix                         = golo_get_option('enable_min_css', 0) == 1 ? '.min' : '';

            $url_font_awesome = GOLO_PLUGIN_URL . 'assets/libs/font-awesome/css/fontawesome.min.css';
            $cdn_font_awesome = golo_get_option('cdn_font_awesome', '');
            if ($cdn_font_awesome) {
                $url_font_awesome = $cdn_font_awesome;
            }
            
            wp_register_style('font-awesome-pro', $url_font_awesome);

            wp_enqueue_style('font-awesome-pro');

            wp_enqueue_style('hint', GOLO_PLUGIN_URL . 'assets/libs/hint/hint.min.css', array(), '2.6.0', 'all');

            wp_enqueue_style('line-awesome', GOLO_PLUGIN_URL . 'assets/libs/line-awesome/css/line-awesome.min.css', array(), '1.1.0', 'all');

            wp_enqueue_style('select2', GOLO_PLUGIN_URL . 'assets/libs/select2/css/select2.min.css', array(), '4.0.13', 'all');

            wp_enqueue_style('slick', GOLO_PLUGIN_URL . 'assets/libs/slick/slick.css', array(), GOLO_PLUGIN_VER, 'all');

            wp_enqueue_style('lightgallery', GOLO_PLUGIN_URL . 'assets/libs/lightgallery/css/lightgallery.min.css', array(), false, 'all');

            wp_enqueue_style('slick-theme', GOLO_PLUGIN_URL . 'assets/libs/slick/slick-theme.css', array(), GOLO_PLUGIN_VER, 'all');

            wp_enqueue_style('nice-select', GOLO_PLUGIN_URL . 'assets/libs/jquery-nice-select/css/nice-select.css', array(), GOLO_PLUGIN_VER, 'all');

            wp_enqueue_style('lity', GOLO_PLUGIN_URL . 'assets/libs/lity/css/lity.min.css', array(), GOLO_PLUGIN_VER, 'all');

            wp_enqueue_style('datetimepicker', GOLO_PLUGIN_URL . 'assets/libs/datetimepicker/jquery.datetimepicker.min.css', array(), GOLO_PLUGIN_VER, 'all');

            wp_enqueue_style(GOLO_PLUGIN_PREFIX . '-frontend', GOLO_PLUGIN_URL . 'assets/css/_frontend' . $min_suffix . '.css', array(), GOLO_PLUGIN_VER, 'all');

            wp_enqueue_style(GOLO_PLUGIN_PREFIX . '-general', GOLO_PLUGIN_URL . 'assets/css/_general' . $min_suffix . '.css', array(), GOLO_PLUGIN_VER, 'all');

            wp_enqueue_style(GOLO_PLUGIN_PREFIX . '-grid', GOLO_PLUGIN_URL . 'assets/css/_grid' . $min_suffix . '.css', array(), GOLO_PLUGIN_VER, 'all');
        }

		/**
         * @return bool
         */
        function is_place_taxonomy()
        {
            return is_tax(get_object_taxonomies('place'));
        }

		/**
         * @param $template
         * @return string
         */
        public function template_loader($template)
        {
            $find = array();
            $file = '';

            if ( is_embed() ) {
                return $template;
            }

            if ( is_single() && ( get_post_type() == 'place' ) ) {
                if (get_post_type() == 'place') {
                    $file = 'single-place.php';
                }
                $find[] = $file;
                $find[] = GOLO()->template_path() . $file;

            } elseif ($this->is_place_taxonomy()) {
                $term = get_queried_object();

                if ( is_tax('place-type') || is_tax('place-categories') || is_tax('place-amenities') || is_tax('place-city') ) {
                    $file = 'taxonomy-' . $term->taxonomy . '.php';
                } else {
                    $file = 'archive-place.php';
                }

                $find[] = 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
                $find[] = GOLO()->template_path() . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
                $find[] = 'taxonomy-' . $term->taxonomy . '.php';
                $find[] = GOLO()->template_path() . 'taxonomy-' . $term->taxonomy . '.php';
                $find[] = $file;
                $find[] = GOLO()->template_path() . $file;
            } elseif (is_post_type_archive('place') || is_page('places')) {
                $file = 'archive-place.php';
                $find[] = $file;
                $find[] = GOLO()->template_path() . $file;
            }

            if( golo_page_shortcode('[golo_dashboard]') || golo_page_shortcode('[golo_my_profile]') || golo_page_shortcode('[golo_my_places]') || golo_page_shortcode('[golo_submit_place]') || golo_page_shortcode('[golo_my_wishlist]') || golo_page_shortcode('[golo_my_booking]') || golo_page_shortcode('[golo_bookings]') || golo_page_shortcode('[golo_country]')) {
                $file = 'page-control.php';
                $find[] = $file;
                $find[] = GOLO()->template_path() . $file;
            }

            if ( is_author() ) {
                $file = 'author.php';
                $find[] = $file;
                $find[] = GOLO()->template_path() . $file;
            }

            if ($file) {
                $template = locate_template(array_unique($find));
                if (!$template) {
                    $template = GOLO_PLUGIN_DIR . 'templates/' . $file;
                }
            }

            return $template;
        }

        /**
         * Register all of the hooks related to the admin area functionality
         */
        private function template_hooks()
        {   
            // Global
            add_action( 'golo_layout_wrapper_start', 'layout_wrapper_start' );
            add_action( 'golo_layout_wrapper_end', 'layout_wrapper_end' );
            add_action( 'golo_output_content_wrapper_start', 'output_content_wrapper_start' );
            add_action( 'golo_output_content_wrapper_end', 'output_content_wrapper_end' );
            add_action( 'golo_sidebar_place', 'sidebar_place' );

            // Taxonomy City & Categories
            $archive_city_layout_style = golo_get_option('archive_city_layout_style', 'layout-default' );
            $layout = !empty($_GET['layout']) ? golo_clean(wp_unslash($_GET['layout'])) : '';
            if( !empty($layout) ){
                $archive_city_layout_style = $layout;
            }

            switch ($archive_city_layout_style) {
                case 'layout-column':

                    add_action( 'golo_archive_place_before', 'archive_page_title', 5 );
                    add_action( 'golo_archive_place_before', 'archive_place_post', 5 );

                    add_action( 'golo_tax_categories_before', 'archive_page_title', 5 );
                    add_action( 'golo_tax_categories_before', 'archive_categories', 10 );

                    break;

                case 'layout-top-filter':

                    add_action( 'golo_archive_place_before', 'archive_page_title', 5 );
                    add_action( 'golo_archive_place_before', 'archive_place_post', 5 );

                    add_action( 'golo_tax_categories_before', 'archive_page_title', 5 );
                    add_action( 'golo_tax_categories_before', 'archive_categories', 10 );

                    break;

                case 'layout-default':

                    add_action( 'golo_archive_place_before', 'archive_page_title', 5 );
                    add_action( 'golo_archive_place_before', 'archive_information', 10 );
                    add_action( 'golo_archive_place_before', 'archive_categories', 15 );
                    add_action( 'golo_archive_place_before', 'archive_place_post', 20 );

                    add_action( 'golo_tax_categories_before', 'archive_page_title', 5 );
                    add_action( 'golo_tax_categories_before', 'archive_information', 10 );
                    add_action( 'golo_tax_categories_before', 'archive_categories', 20 );

                    break;

                default:
                    # code...
                    break;
            }

            add_action( 'golo_archive_map_filter', 'archive_map_filter');
            add_action( 'golo_archive_heading_filter', 'archive_heading_filter', 10, 3 );

            add_action( 'golo_archive_place_after', 'archive_related_city' );
            add_action( 'golo_tax_categories_after', 'archive_related_city' );

            // Single Place
            add_action( 'golo_single_place_before', 'gallery_place');

            $place_details_order_default = array(
                'sort_order'                            => 'enable_sp_amenities|enable_sp_description|enable_sp_menu|enable_sp_location|enable_sp_contact|enable_sp_additional_fields|enable_sp_time_opening|enable_sp_video|enable_sp_faqs|enable_sp_nearby_yelp_review|enable_sp_google_review|enable_sp_author_info|enable_sp_review|enable_sp_product',
                'enable_sp_amenities'                   => 'enable_sp_amenities',
                'enable_sp_description'                 => 'enable_sp_description',
                'enable_sp_menu'                        => 'enable_sp_menu',
                'enable_sp_location'                    => 'enable_sp_location',
                'enable_sp_contact'                     => 'enable_sp_contact',
                'enable_sp_additional_fields'           => 'enable_sp_additional_fields',
                'enable_sp_time_opening'                => 'enable_sp_time_opening',
                'enable_sp_video'                       => 'enable_sp_video',
                'enable_sp_faqs'                        => 'enable_sp_faqs',
                'enable_sp_nearby_yelp_review'          => 'enable_sp_nearby_yelp_review',
                'enable_sp_google_review'               => 'enable_sp_google_review',
                'enable_sp_author_info'                 => 'enable_sp_author_info',
                'enable_sp_review'                      => 'enable_sp_review',
                'enable_sp_product'                     => 'enable_sp_product',
            );

            $type_single_place                  = golo_get_option('type_single_place', 'type-1' );
            $place_details_order                = golo_get_option( 'place_details_order', $place_details_order_default );

            $amenities_nb_order = $description_nb_order = $menu_nb_order = $location_nb_order = $contact_nb_order = $additional_nb_order = $time_nb_order = $video_nb_order = $faqs_nb_order = $yelp_nb_order = $google_nb_order = $author_nb_order = $review_nb_order = $product_nb_order = 0;

            if (!empty($place_details_order)) {
                $place_details_sort_order           = explode('|',$place_details_order['sort_order']);

                foreach ($place_details_sort_order as $key => $value) {
                    switch ($value) {
                        case 'enable_sp_amenities':
                            $amenities_nb_order = $key;
                            break;

                        case 'enable_sp_description':
                            $description_nb_order = $key;
                            break;

                        case 'enable_sp_menu':
                            $menu_nb_order = $key;
                            break;

                        case 'enable_sp_location':
                            $location_nb_order = $key;
                            break;

                        case 'enable_sp_contact':
                            $contact_nb_order = $key;
                            break;

                        case 'enable_sp_additional_fields':
                            $additional_nb_order = $key;
                            break;

                        case 'enable_sp_time_opening':
                            $time_nb_order = $key;
                            break;

                        case 'enable_sp_video':
                            $video_nb_order = $key;
                            break;

                        case 'enable_sp_faqs':
                            $faqs_nb_order = $key;
                            break;

                        case 'enable_sp_nearby_yelp_review':
                            $yelp_nb_order = $key;
                            break;

                        case 'enable_sp_google_review':
                            $google_nb_order = $key;
                            break;

                        case 'enable_sp_author_info':
                            $author_nb_order = $key;
                            break;

                        case 'enable_sp_review':
                            $review_nb_order = $key;
                            break;

                        case 'enable_sp_product':
                            $product_nb_order = $key;
                            break;
                        
                        default:
                            # code...
                            break;
                    }
                }
            }

            $type_single_place = !empty($_GET['layout']) ? golo_clean(wp_unslash($_GET['layout'])) : $type_single_place;
            
            switch ($type_single_place) {
                case 'type-1':

                    if( in_array('enable_sp_amenities', $place_details_order ) ) {
                        add_action( 'golo_single_place_summary', 'single_place_amenities', $amenities_nb_order);
                    }

                    if( in_array('enable_sp_description', $place_details_order ) ) {
                        add_action( 'golo_single_place_summary', 'single_place_description', $description_nb_order);
                    }

                    if( in_array('enable_sp_menu', $place_details_order ) ) {
                        add_action( 'golo_single_place_summary', 'single_place_menu', $menu_nb_order);
                    }

                    if( in_array('enable_sp_location', $place_details_order ) ) {
                        add_action( 'golo_single_place_summary', 'single_place_map', $location_nb_order);
                    }
                    
                    if( in_array('enable_sp_contact', $place_details_order ) ) {
                        add_action( 'golo_single_place_summary', 'single_place_contact', $contact_nb_order);
                    }

                    if( in_array('enable_sp_additional_fields', $place_details_order ) ) {
                        add_action( 'golo_single_place_summary', 'single_place_additional', $additional_nb_order);
                    }

                    if( in_array('enable_sp_time_opening', $place_details_order ) ) {
                        add_action( 'golo_single_place_summary', 'single_place_time_opening', $time_nb_order);
                    }

                    if( in_array('enable_sp_video', $place_details_order ) ) {
                        add_action( 'golo_single_place_summary', 'single_place_video', $video_nb_order);
                    }

                    if( in_array('enable_sp_faqs', $place_details_order ) ) {
                        add_action( 'golo_single_place_summary', 'single_place_faqs', $faqs_nb_order);
                    }

                    if( in_array('enable_sp_nearby_yelp_review', $place_details_order ) ) {
                        add_action( 'golo_single_place_summary', 'single_place_review_yelp', $yelp_nb_order);
                    }

                    if( in_array('enable_sp_google_review', $place_details_order ) ) {
                        add_action( 'golo_single_place_summary', 'single_place_google_review', $google_nb_order);
                    }

                    if( in_array('enable_sp_author_info', $place_details_order ) ) {
                        add_action( 'golo_single_place_summary', 'single_place_author', $author_nb_order);
                    }
                    
                    if( in_array('enable_sp_review', $place_details_order ) ) {
                        add_action( 'golo_single_place_summary', 'single_place_review', $review_nb_order);
                    }

                    if( in_array('enable_sp_product', $place_details_order ) ) {
                        add_action( 'golo_single_place_summary', 'product_related_place', $product_nb_order);
                    }

                    break;

                case 'type-2':
                    
                    add_action( 'golo_single_place_summary', 'single_place_head', 0);
                    
                    add_action( 'golo_single_place_summary', 'single_place_meta', 0);

                    if( in_array('enable_sp_amenities', $place_details_order ) ) {
                        add_action( 'golo_single_place_summary', 'single_place_amenities', $amenities_nb_order);
                    }

                    if( in_array('enable_sp_description', $place_details_order ) ) {
                        add_action( 'golo_single_place_summary', 'single_place_description', $description_nb_order);
                    }

                    if( in_array('enable_sp_menu', $place_details_order ) ) {
                        add_action( 'golo_single_place_summary', 'single_place_menu', $menu_nb_order);
                    }

                    if( in_array('enable_sp_location', $place_details_order ) ) {
                        add_action( 'golo_single_place_summary', 'single_place_map', $location_nb_order);
                    }

                    if( in_array('enable_sp_contact', $place_details_order ) ) {
                        add_action( 'golo_single_place_summary', 'single_place_contact', $contact_nb_order);
                    }

                    if( in_array('enable_sp_additional_fields', $place_details_order ) ) {
                        add_action( 'golo_single_place_summary', 'single_place_additional', $additional_nb_order);
                    }

                    if( in_array('enable_sp_time_opening', $place_details_order ) ) {
                        add_action( 'golo_single_place_summary', 'single_place_time_opening', $time_nb_order);
                    }

                    if( in_array('enable_sp_video', $place_details_order ) ) {
                        add_action( 'golo_single_place_summary', 'single_place_video', $video_nb_order);
                    }

                    if( in_array('enable_sp_faqs', $place_details_order ) ) {
                        add_action( 'golo_single_place_summary', 'single_place_faqs', $faqs_nb_order);
                    }

                    if( in_array('enable_sp_nearby_yelp_review', $place_details_order ) ) {
                        add_action( 'golo_single_place_summary', 'single_place_review_yelp', $yelp_nb_order);
                    }

                    if( in_array('enable_sp_google_review', $place_details_order ) ) {
                        add_action( 'golo_single_place_summary', 'single_place_google_review', $google_nb_order);
                    }

                    if( in_array('enable_sp_author_info', $place_details_order ) ) {
                        add_action( 'golo_single_place_summary', 'single_place_author', $author_nb_order);
                    }

                    if( in_array('enable_sp_review', $place_details_order ) ) {
                        add_action( 'golo_single_place_summary', 'single_place_review', $review_nb_order);
                    }

                    if( in_array('enable_sp_product', $place_details_order ) ) {
                        add_action( 'golo_single_place_summary', 'product_related_place', $product_nb_order);
                    }

                    break;
                
                default:
                    # code...
                    break;
            }
            add_action( 'golo_single_place_after', 'related_place');

            // Single Author
            add_action( 'golo_single_author_head', 'author_info');

            add_action( 'golo_single_author_summary', 'author_place', 5);
            add_action( 'golo_single_author_summary', 'author_review', 10);

            add_action( 'golo_single_author_sidebar', 'author_about');
        }
    }

}