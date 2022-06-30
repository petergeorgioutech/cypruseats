<?php 

if ( !defined( 'ABSPATH' ) ){
    exit;
}

if ( !class_exists('Golo_Core') ){
    /**
     *  The core plugin class
     *  Class Golo_Core
     */
    class Golo_Core {

        /**
         * Instance variable for singleton pattern
         */
        private static $instance = null;
        /**
         * Return class instance
         */
        public static function instance()
        {
            if (null == self::$instance) {
                self::$instance = new self;
            }
            return self::$instance;
        }

    	/**
         * Define the core functionality of the plugin
         */
        public function __construct()
        {
            $this->include_library();
            $this->template_hooks();
            $this->admin_hooks();
        }

        /**
         * Load the required dependencies for this plugin
         */
        private function include_library() 
        {
            require_once GOLO_PLUGIN_DIR . 'includes/golo-helper.php';

            require_once GOLO_PLUGIN_DIR . 'includes/class-golo-capability.php';
            require_once GOLO_PLUGIN_DIR . 'includes/class-golo-template-loader.php';
            require_once GOLO_PLUGIN_DIR . 'includes/class-golo-shortcodes.php';
            require_once GOLO_PLUGIN_DIR . 'includes/class-golo-ajax.php';

            // Mega Menu
            require_once GOLO_PLUGIN_DIR . 'includes/mega-menu/class-mega-menu.php';

            // Google Review
            include_once GOLO_PLUGIN_DIR . 'includes/google-review/class-google-review.php';

            // Yelp Review
            include_once GOLO_PLUGIN_DIR . 'includes/yelp-review/class-yelp-review.php';

            // Export
            require_once GOLO_PLUGIN_DIR . 'includes/import/class-exporter.php';

            // Import
            require_once ABSPATH . '/wp-admin/includes/class-wp-importer.php';
            require_once GOLO_PLUGIN_DIR . 'includes/import/wp-importer/WXRImporter.php';
            require_once GOLO_PLUGIN_DIR . 'includes/import/wp-importer/WPImporterLogger.php';
            require_once GOLO_PLUGIN_DIR . 'includes/import/wp-importer/WPImporterLoggerCLI.php';
            require_once GOLO_PLUGIN_DIR . 'includes/import/class-wxrimporter.php';
            require_once GOLO_PLUGIN_DIR . 'includes/import/class-import-logger.php';
            require_once GOLO_PLUGIN_DIR . 'includes/import/class-importer.php';
            require_once GOLO_PLUGIN_DIR . 'includes/import/class-content-importer.php';
            require_once GOLO_PLUGIN_DIR . 'includes/import/class-widgets-importer.php';
            require_once GOLO_PLUGIN_DIR . 'includes/admin/class-golo-import.php';
            Golo_Importer::instance();

            // Update
            require_once GOLO_PLUGIN_DIR . 'includes/admin/class-golo-updater.php';

            // Admin
            require_once GOLO_PLUGIN_DIR . 'includes/admin/class-golo-plugins.php';
            require_once GOLO_PLUGIN_DIR . 'includes/admin/class-golo-admin-setup.php';
            require_once GOLO_PLUGIN_DIR . 'includes/admin/class-golo-admin.php';
            require_once GOLO_PLUGIN_DIR . 'includes/admin/class-golo-admin-place.php';
            require_once GOLO_PLUGIN_DIR . 'includes/admin/class-golo-admin-booking.php';
            require_once GOLO_PLUGIN_DIR . 'includes/admin/class-golo-admin-package.php';
            require_once GOLO_PLUGIN_DIR . 'includes/admin/class-golo-admin-user-package.php';
            require_once GOLO_PLUGIN_DIR . 'includes/admin/class-golo-admin-invoice.php';
            require_once GOLO_PLUGIN_DIR . 'includes/admin/class-golo-metaboxes.php';
            require_once GOLO_PLUGIN_DIR . 'includes/admin/class-golo-profile.php';
            require_once GOLO_PLUGIN_DIR . 'includes/admin/class-golo-location.php';
            require_once GOLO_PLUGIN_DIR . 'includes/admin/class-golo-schedule.php';
            require_once GOLO_PLUGIN_DIR . 'includes/admin/class-golo-rest-api.php';

            // Partials
            include_once GOLO_PLUGIN_DIR . 'includes/partials/place/class-golo-place.php';
            include_once GOLO_PLUGIN_DIR . 'includes/partials/package/class-golo-package.php';
            include_once GOLO_PLUGIN_DIR . 'includes/partials/payment/class-golo-payment.php';
            include_once GOLO_PLUGIN_DIR . 'includes/partials/payment/class-golo-trans-log.php';
            include_once GOLO_PLUGIN_DIR . 'includes/partials/invoice/class-golo-invoice.php';
            include_once GOLO_PLUGIN_DIR . 'includes/partials/booking/class-golo-booking.php';

        }

        /**
         * Register all of the hooks related to the admin area functionality
         */
        private function admin_hooks() 
        {   
            /**
             * Hook Golo_Admin_Setup
             */
            if( is_admin() ) {
                $setup_page = new Golo_Admin_Setup();
                add_action('admin_menu', array( $setup_page, 'admin_menu' ), 12);
            }

            /**
             * Hook Golo_Admin
             */
        	$golo_admin = new Golo_Admin();
            add_filter('glf_meta_box_config', array( $golo_admin, 'register_meta_boxes' ) );
            add_filter('glf_register_post_type', array( $golo_admin, 'register_post_type' ) );
            add_filter('glf_register_taxonomy', array( $golo_admin, 'register_taxonomy' ) );
            add_filter('glf_register_term_meta', array( $golo_admin, 'register_term_meta' ) );

            add_filter('glf_option_config', array( $golo_admin, 'register_options_config') );
            add_action('init', array( $golo_admin, 'register_post_status') );
            add_action('admin_menu', array( $golo_admin, 'admin_menu') );
            add_action('after_setup_theme', array( $golo_admin, 'remove_admin_bar') );
            

            /**
             * Hook Golo_Admin_Place
             */
            $golo_admin_place = new Golo_Admin_Place();
            add_filter('golo_place_slug', array( $golo_admin_place, 'modify_place_slug' ) );
            add_filter('golo_place_type_slug', array( $golo_admin_place, 'modify_place_type_slug' ) );
            add_filter('golo_place_categories_slug', array( $golo_admin_place, 'modify_place_categories_slug' ) );
            add_filter('golo_place_amenities_slug', array( $golo_admin_place, 'modify_place_amenities_slug' ) );
            add_filter('golo_place_city_slug', array( $golo_admin_place, 'modify_place_city_slug' ) );
            add_filter('golo_place_neighborhood_slug', array( $golo_admin_place, 'modify_place_neighborhood_slug' ) );
            add_action('restrict_manage_posts', array( $golo_admin_place, 'filter_restrict_manage_place') );
            add_filter('parse_query', array( $golo_admin_place, 'place_filter') );
            add_action('admin_init', array( $golo_admin_place, 'approve_place') );
            add_action('admin_init', array( $golo_admin_place, 'expire_place') );
            add_action('admin_init', array( $golo_admin_place, 'hidden_place') );
            add_action('admin_init', array( $golo_admin_place, 'show_place') );

            add_action('wp_ajax_golo_get_city_by_country_ajax', array( $golo_admin_place, 'get_city_by_country_ajax') );
            add_action('wp_ajax_nopriv_golo_get_city_by_country_ajax', array( $golo_admin_place, 'get_city_by_country_ajax') );
            add_action('wp_ajax_golo_action_claim_listing', array( $golo_admin_place, 'action_claim_listing') );
            add_action('wp_ajax_nopriv_golo_action_claim_listing', array( $golo_admin_place, 'action_claim_listing') );

            add_action('wp_ajax_golo_get_neighborhoods_by_city_ajax', array( $golo_admin_place, 'get_neighborhoods_by_city_ajax') );
            add_action('wp_ajax_nopriv_golo_get_neighborhoods_by_city_ajax', array( $golo_admin_place, 'get_neighborhoods_by_city_ajax') );

            // Disable gutenberg for place
            $enable_gutenberg_edit_place = golo_get_option('enable_gutenberg_edit_place', 0);
            if( $enable_gutenberg_edit_place == 0 ) {
                if ( version_compare($GLOBALS['wp_version'], '5.0-beta', '>') ) {
                    // WP > 5 beta
                    add_filter( 'use_block_editor_for_post_type', array( $golo_admin_place, 'golo_disable_gutenberg_for_post_type'), 10, 2 );
                } else {
                    // WP < 5 beta
                    add_filter( 'gutenberg_can_edit_post_type', array( $golo_admin_place, 'golo_disable_gutenberg_for_post_type'), 10, 2 );
                }
            }

            /**
             * Hook Golo_Package_Admin
             */
            $golo_admin_package = new Golo_Admin_Package();
            add_filter('golo_package_slug', array( $golo_admin_package, 'modify_package_slug' ) );

            // Agent Packages Post Type
            $golo_user_package_admin = new Golo_User_Package_Admin();
            add_filter('golo_user_package_slug', array( $golo_user_package_admin, 'modify_user_package_slug') );
            add_action('restrict_manage_posts', array( $golo_user_package_admin, 'filter_restrict_manage_user_package') );
            add_action( 'before_delete_post', array($golo_user_package_admin, 'action_delete_post') );
            add_filter('parse_query', array( $golo_user_package_admin, 'user_package_filter') );

            /**
             * Hook Golo_Invoice_Admin
             */
            $golo_admin_invoice = new Golo_Admin_Invoice();
            add_action('golo_invoice_slug', array( $golo_admin_invoice, 'modify_invoice_slug') );
            add_action('restrict_manage_posts', array( $golo_admin_invoice, 'filter_restrict_manage_invoice') );
            add_action('parse_query', array( $golo_admin_invoice, 'invoice_filter') );

            /**
             * Hook Golo_Booking_Admin
             */
            $golo_admin_booking = new Golo_Admin_Booking();
            add_action('admin_init', array( $golo_admin_booking, 'approve_booking') );
            add_action('admin_init', array( $golo_admin_booking, 'cancel_booking') );

            /**
             * Hook Golo_Booking_Admin
             */
            $golo_booking = new Golo_Booking();
            add_action('wp', array( $golo_booking, 'booking_action_handler') );

            /**
             * Hook Golo_Rest_API
             */
            $golo_rest_api = new Golo_Rest_API();
            add_action('rest_api_init', array( $golo_rest_api, 'register_fields_api' ) );

            // add_filter( 'rest_authentication_errors', function( $result ) {
            //     if ( ! empty( $result ) ) {
            //         return $result;
            //     }
            //     if ( ! is_user_logged_in() ) {
            //         return new WP_Error( 'rest_not_logged_in', 'You are not currently logged in.', array( 'status' => 401 ) );
            //     }
            //     return $result;
            // });

            $profile = new Golo_Profile();
            add_filter('show_user_profile', array( $profile, 'custom_user_profile_fields') );
            add_filter('edit_user_profile', array( $profile, 'custom_user_profile_fields') );
            add_action('edit_user_profile_update', array( $profile, 'update_custom_user_profile_fields') );
            add_action('personal_options_update', array( $profile, 'update_custom_user_profile_fields') );
            add_action('admin_head', array( $profile, 'my_profile_upload_js') );
            
            /**
             * Hook Golo_Plugins
             */
            $golo_plugins = new Golo_Plugins();
            add_action('wp_ajax_process_plugin_actions', array( $golo_plugins, 'process_plugin_actions' ) );
            add_action('wp_ajax_nopriv_process_plugin_actions', array( $golo_plugins, 'process_plugin_actions' ) );

            /**
             * Hook Golo_Metaboxes
             */
            $golo_metaboxes = new Golo_Metaboxes();
            add_action('load-post.php', array( $golo_metaboxes, 'meta_boxes_setup' ) );
            add_action('load-post-new.php', array( $golo_metaboxes, 'meta_boxes_setup' ) );

            /**
             * Hook Golo_Location
             */
            $golo_location = new Golo_Location();
            add_action('admin_menu', array( $golo_location, 'countries_create_menu' ) );
            add_filter('admin_init', array( $golo_location, 'countries_register_setting' ) );

            //City
            add_action('place-neighborhood_add_form_fields',  array( $golo_location, 'add_form_fields_place_neighborhood'), 10, 2 );
            add_action('created_place-neighborhood',  array( $golo_location, 'save_place_neighborhood_meta'), 10, 2 );
            add_action('place-neighborhood_edit_form_fields',  array( $golo_location, 'edit_form_fields_place_neighborhood'), 10, 2 );
            add_action('edited_place-neighborhood',  array( $golo_location, 'update_place_neighborhood_meta'), 10, 2 );
            add_filter('manage_edit-place-neighborhood_columns', array( $golo_location,  'add_columns_place_neighborhood') );
            add_filter('manage_place-neighborhood_custom_column', array( $golo_location,  'add_columns_place_neighborhood_content'), 10, 3 );
            add_filter('manage_edit-place-neighborhood_sortable_columns',  array( $golo_location, 'add_columns_place_neighborhood_sortable') );

            $golo_schedule = new Golo_Schedule();
            register_deactivation_hook(__FILE__, array( $golo_schedule, 'golo_per_listing_check_expire') );
            add_action('init', array( $golo_schedule, 'scheduled_hook') );
            add_action('golo_per_listing_check_expire', array( $golo_schedule, 'per_listing_check_expire') );

            if ( is_admin() ) {
                global $pagenow;

                // place custom columns
                if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'place') {
                    add_filter('manage_edit-place_columns', array( $golo_admin_place, 'register_custom_column_titles') );
                    add_action('manage_posts_custom_column', array( $golo_admin_place, 'display_custom_column') );
                    add_filter('manage_edit-place_sortable_columns', array( $golo_admin_place, 'sortable_columns') );
                    add_filter('request', array( $golo_admin_place, 'column_orderby') );
                    add_filter('post_row_actions', array( $golo_admin_place, 'modify_list_row_actions' ), 10, 2 );
                }

                // booking custom columns
                if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'booking') {
                    add_filter('manage_edit-booking_columns', array( $golo_admin_booking, 'register_custom_column_titles') );
                    add_action('manage_posts_custom_column', array( $golo_admin_booking, 'display_custom_column') );
                    add_filter('post_row_actions', array( $golo_admin_booking, 'modify_list_row_actions' ), 10, 2 );
                }

                // package custom columns
                if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'package') {
                    add_filter('manage_edit-package_columns', array( $golo_admin_package, 'register_custom_column_titles') );
                    add_action('manage_posts_custom_column', array( $golo_admin_package, 'display_custom_column') );
                    add_filter('post_row_actions', array( $golo_admin_package, 'modify_list_row_actions' ), 10, 2 );
                }

                // agent package custom columns
                if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'user_package') {
                    add_filter('manage_edit-user_package_columns', array($golo_user_package_admin, 'register_custom_column_titles') );
                    add_action('manage_posts_custom_column', array($golo_user_package_admin, 'display_custom_column') );
                    add_action( 'before_delete_post', array($golo_user_package_admin, 'action_delete_post') ); 
                    add_filter('post_row_actions', array($golo_user_package_admin, 'modify_list_row_actions'), 10, 2);
                }

                // Invoice custom columns
                if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'invoice') {
                    add_filter('manage_edit-invoice_columns', array( $golo_admin_invoice, 'register_custom_column_titles') );
                    add_action('manage_posts_custom_column', array( $golo_admin_invoice, 'display_custom_column') );
                    add_filter('manage_edit-invoice_sortable_columns', array( $golo_admin_invoice, 'sortable_columns') );
                    add_filter('request', array( $golo_admin_invoice, 'column_orderby') );
                    add_filter('post_row_actions', array( $golo_admin_invoice, 'modify_list_row_actions'), 10, 2 );
                }
            }
        }

        /**
         * Register all of the hooks related to the public-facing functionality
         */
        private function template_hooks()
        {   
            /**
             * Hook Golo_Template_Loader
             */
            $golo_template_loader = new Golo_Template_Loader();
            add_filter('template_include', array( $golo_template_loader, 'template_loader' ) );
            add_action('admin_enqueue_scripts', array( $golo_template_loader, 'admin_enqueue' ) );
            add_action('wp_enqueue_scripts', array( $golo_template_loader, 'enqueue_styles' ) );
            add_action('wp_enqueue_scripts', array( $golo_template_loader, 'enqueue_scripts' ) );

            /**
             * Hook Golo_Ajax
             */
            $golo_ajax = new Golo_Ajax();
            add_action('wp_ajax_golo_pagination_ajax', array( $golo_ajax, 'golo_pagination_ajax' ) );
            add_action('wp_ajax_nopriv_golo_pagination_ajax', array( $golo_ajax, 'golo_pagination_ajax' ) );

            add_action('wp_ajax_golo_place_search_map_ajax', array( $golo_ajax, 'golo_place_search_map_ajax' ) );
            add_action('wp_ajax_nopriv_golo_place_search_map_ajax', array( $golo_ajax, 'golo_place_search_map_ajax' ) );

            add_action('wp_ajax_golo_filter_my_place', array( $golo_ajax, 'golo_filter_my_place' ) );
            add_action('wp_ajax_nopriv_golo_filter_my_place', array( $golo_ajax, 'golo_filter_my_place' ) );

            add_action('wp_ajax_golo_update_profile_ajax', array( $golo_ajax, 'golo_update_profile_ajax' ) );
            add_action('wp_ajax_nopriv_golo_update_profile_ajax', array( $golo_ajax, 'golo_update_profile_ajax' ) );

            add_action('wp_ajax_golo_change_password_ajax', array( $golo_ajax, 'golo_change_password_ajax' ) );
            add_action('wp_ajax_nopriv_golo_change_password_ajax', array( $golo_ajax, 'golo_change_password_ajax' ) );

            // Add to wishlist
            add_action('wp_ajax_golo_add_to_wishlist', array( $golo_ajax, 'golo_add_to_wishlist') );
            add_action('wp_ajax_nopriv_golo_add_to_wishlist', array( $golo_ajax, 'golo_add_to_wishlist' ) );

            // Ajax search
            add_action('wp_ajax_golo_search_ajax', array( $golo_ajax, 'golo_search_ajax') );
            add_action('wp_ajax_nopriv_golo_search_ajax', array( $golo_ajax, 'golo_search_ajax' ) );

            add_action('wp_ajax_golo_search_location_ajax', array( $golo_ajax, 'golo_search_location_ajax') );
            add_action('wp_ajax_nopriv_golo_search_location_ajax', array( $golo_ajax, 'golo_search_location_ajax' ) );

            // Ajax booking form
            add_action('wp_ajax_golo_booking_form_ajax', array( $golo_ajax, 'golo_booking_form_ajax') );
            add_action('wp_ajax_nopriv_golo_booking_form_ajax', array( $golo_ajax, 'golo_booking_form_ajax' ) );
            add_action('wp_ajax_golo_claim_form_ajax', array( $golo_ajax, 'golo_claim_form_ajax') );
            add_action('wp_ajax_nopriv_golo_claim_form_ajax', array( $golo_ajax, 'golo_claim_form_ajax' ) );

            // Ajax notification
            // add_action('wp_ajax_golo_load_unseen_notification_ajax', array( $golo_ajax, 'golo_load_unseen_notification_ajax') );
            // add_action('wp_ajax_nopriv_golo_load_unseen_notification_ajax', array( $golo_ajax, 'golo_load_unseen_notification_ajax' ) );

            /**
             * Hook Golo_Place
             */
            $golo_place = new Golo_Place();
            add_filter('golo_single_place_before', array( $golo_place, 'golo_set_place_view' ) );

            add_action('wp_ajax_golo_place_img_upload_ajax', array( $golo_place, 'place_img_upload_ajax' ) );
            add_action('wp_ajax_nopriv_golo_place_img_upload_ajax', array( $golo_place, 'place_img_upload_ajax' ) );

            add_action('wp_ajax_remove_place_img_ajax', array( $golo_place, 'remove_place_img_ajax' ) );
            add_action('wp_ajax_nopriv_remove_place_img_ajax', array( $golo_place, 'remove_place_img_ajax' ) );

            add_action('wp_ajax_place_submit_ajax', array( $golo_place, 'place_submit_ajax' ) );
            add_action('wp_ajax_nopriv_place_submit_ajax', array( $golo_place, 'place_submit_ajax' ) );

            add_action('wp_ajax_golo_place_submit_review_ajax', array( $golo_place, 'submit_review_ajax' ) );
            add_action('wp_ajax_nopriv_golo_place_submit_review_ajax', array( $golo_place, 'submit_review_ajax' ) );

            add_filter('golo_place_rating_meta', array( $golo_place, 'rating_meta_filter' ), 4, 9 );

            add_action('wp_ajax_golo_place_submit_reply_ajax', array( $golo_place, 'submit_reply_ajax' ) );
            add_action('wp_ajax_nopriv_golo_place_submit_reply_ajax', array( $golo_place, 'submit_reply_ajax' ) );

            add_action('wp_ajax_golo_contact_agent_ajax', array( $golo_place, 'contact_agent_ajax') );
            add_action('wp_ajax_nopriv_golo_contact_agent_ajax', array( $golo_place, 'contact_agent_ajax') );

            /**
             * Hook Golo_Payment
             */
            $golo_payment = new Golo_Payment();
            add_action('wp_ajax_golo_paypal_payment_per_package_ajax', array( $golo_payment, 'paypal_payment_per_package_ajax') );
            add_action('wp_ajax_nopriv_golo_paypal_payment_per_package_ajax', array( $golo_payment, 'paypal_payment_per_package_ajax') );

            add_action('wp_ajax_golo_woocommerce_payment_per_package_ajax', array( $golo_payment, 'woocommerce_payment_per_package_ajax') );
            add_action('wp_ajax_nopriv_golo_woocommerce_payment_per_package_ajax', array( $golo_payment, 'woocommerce_payment_per_package_ajax') );

            add_action('wp_ajax_golo_wire_transfer_per_package_ajax', array( $golo_payment, 'wire_transfer_per_package_ajax') );
            add_action('wp_ajax_nopriv_golo_wire_transfer_per_package_ajax', array( $golo_payment, 'wire_transfer_per_package_ajax') );

            add_action('wp_ajax_golo_free_package_ajax', array( $golo_payment, 'free_package_ajax') );
            add_action('wp_ajax_nopriv_golo_free_package_ajax', array( $golo_payment, 'free_package_ajax') );
        }

        /**
         * Get template path
         */
        public function template_path()
        {
            return apply_filters('golo_template_path', 'golo-framework/');
        }
    }
}

if( !function_exists('GOLO') )
{
    function GOLO() {
        return Golo_Core::instance();
    }
}
// Global for backwards compatibility.
$GLOBALS['Golo_Core'] = GOLO();