<?php 

if ( !defined( 'ABSPATH' ) ){
	exit;
}

if ( !class_exists('Golo_Enqueue') ){
	
    /**
     *  Class Golo_Enqueue
     */
    class Golo_Enqueue {

    	/**
		 * The constructor.
		 */
		function __construct() {
			add_action('wp_enqueue_scripts',array( $this, 'enqueue_styles' ) );
			add_action('wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		/**
         * Register the stylesheets for the public-facing side of the site.
         */
        public function enqueue_styles()
        {
        	$map_type = Golo_Helper::golo_get_option('map_type', '');
        	/*
			 * Enqueue Third Party Styles
			 */

			wp_enqueue_style('font-awesome-all', GOLO_THEME_URI . '/assets/fonts/font-awesome/css/fontawesome-all.min.css', array(), '5.10.0', 'all');

			wp_enqueue_style('line-awesome', GOLO_THEME_URI . '/assets/fonts/line-awesome/css/line-awesome.min.css', array(), '1.1.0', 'all');

        	wp_enqueue_style('slick', GOLO_THEME_URI . '/assets/libs/slick/slick.css', array(), '1.8.1', 'all');

        	wp_enqueue_style('swiper', GOLO_THEME_URI . '/assets/libs/swiper/css/swiper.css', array(), '5.3.8', 'all');

            wp_enqueue_style('slick-theme', GOLO_THEME_URI . '/assets/libs/slick/slick-theme.css', array(), '1.8.1', 'all');

            wp_enqueue_style('nice-select', GOLO_THEME_URI . '/assets/libs/jquery-nice-select/css/nice-select.css', array(), '1.1.0', 'all');

            if( $map_type == 'mapbox' ){

            	wp_enqueue_style('mapbox-gl', GOLO_THEME_URI . '/assets/libs/mapbox/mapbox-gl.css', array(), '1.0.0', 'all');
            	wp_enqueue_style('mapbox-gl-geocoder', GOLO_THEME_URI . '/assets/libs/mapbox/mapbox-gl-geocoder.css', array(), '1.0.0', 'all');

            }
            
            if( $map_type == 'openstreetmap' ){
            
            wp_enqueue_style('leaflet', GOLO_THEME_URI . '/assets/libs/leaflet/leaflet.css', array(), '1.7.1', 'all');
            wp_enqueue_style('esri-leaflet', GOLO_THEME_URI . '/assets/libs/leaflet/esri-leaflet-geocoder.css', array(), '1.7.1', 'all');

            }
            

			/*
			 * Enqueue Theme Styles
			 */
			wp_enqueue_style( 'golo_font', GOLO_THEME_URI . '/assets/fonts/font.css' );

			$enable_rtl_mode  = Golo_Helper::golo_get_option('enable_rtl_mode', 0);
			if ( is_rtl() || $enable_rtl_mode ) {
				wp_enqueue_style( 'golo_bootstrap-rtl', GOLO_THEME_URI . '/assets/libs/bootstrap-rtl/bootstrap.min.css', array());
				wp_enqueue_style( 'golo_minify-style', GOLO_THEME_URI . '/rtl.min.css', array());
			} else {
				wp_enqueue_style( 'golo_minify-style', GOLO_THEME_URI . '/style.min.css', array());
			}
			$time = time();
			wp_enqueue_style( 'golo_main-style', get_stylesheet_uri() . '?v='. $time );
        }

        /**
         * Register the JavaScript for the admin area.
         */
		public function enqueue_scripts() {

			$map_type = Golo_Helper::golo_get_option('map_type', '');

			/*
			 * Enqueue Third Party Scripts
			 */

			wp_enqueue_script('waypoints', GOLO_THEME_URI . '/assets/libs/waypoints/jquery.waypoints.js', array( 'jquery' ), '4.0.1', true);

			wp_enqueue_script('matchheight', GOLO_THEME_URI . '/assets/libs/matchHeight/jquery.matchHeight-min.js', array( 'jquery' ), '0.7.0', true);

			wp_enqueue_script('imagesloaded', GOLO_THEME_URI . '/assets/libs/imagesloaded/imagesloaded.min.js', array( 'jquery' ), null, true);

			wp_enqueue_script('isotope-masonry', GOLO_THEME_URI . '/assets/libs/isotope/js/isotope.pkgd.min.js', array( 'jquery' ), '3.0.6', true);

			wp_enqueue_script('packery-mode', GOLO_THEME_URI . '/assets/libs/packery-mode/packery-mode.pkgd.min.js', array( 'jquery' ), '3.0.6', true);

			wp_enqueue_script('slick', GOLO_THEME_URI . '/assets/libs/slick/slick.min.js', array( 'jquery' ), '1.8.1', true);

			wp_enqueue_script('swiper', GOLO_THEME_URI . '/assets/libs/swiper/js/swiper.min.js', array( 'jquery', 'imagesloaded' ), '5.3.8', true);

            wp_enqueue_script('nice-select', GOLO_THEME_URI . '/assets/libs/jquery-nice-select/js/jquery.nice-select.min.js', array( 'jquery' ), '1.1.0', true);

            if( $map_type == 'mapbox' ){
            
            	wp_enqueue_script('mapbox-gl', GOLO_THEME_URI . '/assets/libs/mapbox/mapbox-gl.js', array( 'jquery' ), '1.0.0', true);
            	wp_enqueue_script('mapbox-gl-geocoder', GOLO_THEME_URI . '/assets/libs/mapbox/mapbox-gl-geocoder.min.js', array( 'jquery' ), '1.0.0', true);
	            wp_enqueue_script('es6-promisel', GOLO_THEME_URI . '/assets/libs/mapbox/es6-promise.min.js', array( 'jquery' ), '1.0.0', true);
	            wp_enqueue_script('es6-promise', GOLO_THEME_URI . '/assets/libs/mapbox/es6-promise.auto.min.js', array( 'jquery' ), '1.0.0', true);

            }

            if( $map_type == 'openstreetmap' ){
            	
	            wp_enqueue_script('leaflet', GOLO_THEME_URI . '/assets/libs/leaflet/leaflet.js', array( 'jquery' ), '1.7.1', true);
	            wp_enqueue_script('leaflet-src', GOLO_THEME_URI . '/assets/libs/leaflet/leaflet-src.js', array( 'jquery' ), '1.7.1', true);
	            wp_enqueue_script('esri-leaflet', GOLO_THEME_URI . '/assets/libs/leaflet/esri-leaflet.js', array( 'jquery' ), '1.7.1', true);
	            wp_enqueue_script('esri-leaflet-geocoder', GOLO_THEME_URI . '/assets/libs/leaflet/esri-leaflet-geocoder.js', array( 'jquery' ), '1.7.1', true);
      
            }
            wp_enqueue_script('validate', GOLO_THEME_URI . '/assets/libs/validate/jquery.validate.min.js', array( 'jquery' ), '1.17.0', true);

            if( class_exists('Golo_Framework') ) {
            	wp_enqueue_script('google-api', 'https://apis.google.com/js/platform.js', array( 'jquery' ), GOLO_THEME_VER, true);

            	wp_enqueue_script('facebook-api');
            }
            
            if( $map_type == 'google_map' ){
            	wp_enqueue_script('gmap3', GOLO_THEME_URI . '/assets/libs/gmap3/gmap3.min.js', array( 'jquery' ), '5.3.8', true);
            	wp_register_script( 'gmap3', GOLO_THEME_URI . '/assets/libs/gmap3/gmap3.min.js', array( 'jquery' ), GOLO_THEME_VER, true );
            	$googlemap_api_key = Golo_Helper::golo_get_option('googlemap_api_key', 'AIzaSyBvPDNG6pePr9iFpeRKaOlaZF_l0oT3lWk');
                if ( is_ssl() ) {
                	wp_register_script( 'gmap-api', 'https://maps.google.com/maps/api/js?key=' . $googlemap_api_key . '&amp;language=en' );
               	}else{
               		wp_register_script( 'gmap-api', 'http://maps.google.com/maps/api/js?key=' . $googlemap_api_key . '&amp;language=en' );
               	}
            }
            
			/*
			 * Enqueue Theme Scripts
			 */
			wp_enqueue_script( 'golo-swiper-wrapper', GOLO_THEME_URI . '/assets/js/swiper-wrapper.js', array( 'swiper' ), GOLO_THEME_VER, true );

			$golo_swiper_js = array(
                'prevText' => esc_html__( 'Prev', 'golo' ),
                'nextText' => esc_html__( 'Next', 'golo' ),
            );
            wp_localize_script( 'golo-swiper-wrapper', '$goloSwiper', $golo_swiper_js );

            wp_enqueue_script( 'golo-grid-layout', GOLO_THEME_URI . '/assets/js/grid-layout.js', array(
				'jquery',
				'imagesloaded',
				'matchheight',
				'isotope-masonry',
				'packery-mode',
			), GOLO_THEME_VER, true );

			wp_enqueue_script( 'golo-main-js', GOLO_THEME_URI . '/assets/js/main.js', array( 'jquery' ), GOLO_THEME_VER, true );

		    $ajax_url     = admin_url( 'admin-ajax.php' );
			$current_lang = apply_filters( 'wpml_current_language', null );

			if ( $current_lang ) {
				$ajax_url = add_query_arg( 'lang', $current_lang, $ajax_url );
			}

			$google_id = Golo_Helper::golo_get_option('google_login_api', '406259942299-s0m5o0ecdf8khdiittl1r6cd3pdjqsum.apps.googleusercontent.com');
			$sticky_header          = Golo_Helper::get_setting('sticky_header');
			$sticky_header_homepage = Golo_Helper::get_setting('sticky_header_homepage');
			$float_header           = Golo_Helper::get_setting('float_header');
			$float_header_homepage  = Golo_Helper::get_setting('float_header_homepage');

		    wp_localize_script( 'golo-main-js', 'theme_vars', 
		    	array(
					'ajax_url'  => esc_url( $ajax_url ),
					'google_id' => $google_id,
					'send_user_info' => esc_html__('Sending user info,please wait...', 'golo'),
					'forget_password' => esc_html__('Checking your email,please wait...', 'golo'),
					'change_password' => esc_html__('Checking your password,please wait...', 'golo'),
					'sticky_header' => $sticky_header,
					'sticky_header_homepage' => $sticky_header_homepage,
					'float_header' => $float_header,
					'float_header_homepage' => $float_header_homepage,
				) 
			);

			/*
			 * The comment-reply script.
			 */
			if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
				wp_enqueue_script( 'comment-reply' );
			}
		}

    }
}