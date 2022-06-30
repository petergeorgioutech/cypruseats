<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if (!class_exists('Golo_Admin_Booking')) {
    /**
     * Class Golo_Admin_Booking
     */
    class Golo_Admin_Booking
    {
        /**
         * Register custom column titles
         * @param $columns
         * @return array
         */
        public function register_custom_column_titles($columns)
        {
            $columns['cb'] = "<input type=\"checkbox\" />";
            $columns['title'] = esc_html__('Name', 'golo-framework');
            $columns['adults'] = esc_html__('Adults', 'golo-framework');
            $columns['childrens'] = esc_html__('Childrens', 'golo-framework');
            $columns['time'] = esc_html__('Time', 'golo-framework');
            $columns['coupon'] = esc_html__('Coupon', 'golo-framework');
            $columns['date_booking'] = esc_html__('Date', 'golo-framework');
            $columns['activate_date'] = esc_html__('Activate Date', 'golo-framework');
            $new_columns = array();
            $custom_order = array('cb', 'title', 'adults', 'childrens', 'time', 'date_booking', 'activate_date', 'coupon');
            foreach ($custom_order as $colname){
                $new_columns[$colname] = $columns[$colname];
            }
            return $new_columns;
        }

        /**
         * Display custom column
         * @param $column
         */
        public function display_custom_column($column)
        {
            global $post;
            switch ($column) {
                case 'adults':
                    $booking_adults = get_post_meta($post->ID, GOLO_METABOX_PREFIX . 'booking_adults', true);
                    echo esc_html($booking_adults);

                    break;

                case 'childrens':
                    $booking_childrens = get_post_meta($post->ID, GOLO_METABOX_PREFIX . 'booking_childrens', true);
                    echo esc_html($booking_childrens);

                    break;

                case 'time':
                    $booking_time = get_post_meta($post->ID, GOLO_METABOX_PREFIX . 'booking_time', true);
                    echo esc_html($booking_time);

                    break;
                case 'coupon':
                    $place_coupon = get_post_meta($post->ID, GOLO_METABOX_PREFIX . 'place_coupon', true);
                    echo esc_html($place_coupon);

                    break;
                case 'date_booking':
                    $booking_date = get_post_meta($post->ID, GOLO_METABOX_PREFIX . 'booking_date', true);
                    echo esc_html($booking_date);
                    
                    break;

                case 'activate_date':
                    $booking_activate_date = get_post_meta($post->ID, GOLO_METABOX_PREFIX . 'booking_activate_date', true);
                    echo esc_html($booking_activate_date);
                    
                    break;
            }
        }

        /**
         * @param $actions
         * @param $post
         * @return mixed
         */
        public function modify_list_row_actions( $actions, $post ) {
            // Check for your post type.
            if ( $post->post_type == 'booking' ) {
                unset( $actions[ 'view' ] );
                if (in_array($post->post_status, array('pending','canceled')) && current_user_can('publish_post', $post->ID)) {
                    $actions['booking-approve']='<a href="'.wp_nonce_url(add_query_arg('approve_booking', $post->ID), 'approve_booking').'">'.esc_html__('Approve', 'golo-framework').'</a>';
                }
                if (in_array($post->post_status, array('publish', 'pending')) && current_user_can('publish_post', $post->ID)) {
                    $actions['booking-cancel']='<a href="'.wp_nonce_url(add_query_arg('cancel_booking', $post->ID), 'cancel_booking').'">'.esc_html__('Cancel', 'golo-framework').'</a>';
                }
            }
            return $actions;
        }

        /**
         * Approve booking
         */
        public function approve_booking()
        {
            if (!empty($_GET['approve_booking']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'approve_booking') && current_user_can('publish_post', $_GET['approve_booking'])) {
                $post_id = absint( golo_clean(wp_unslash($_GET['approve_booking'])) );
                $booking_data = array(
                    'ID' => $post_id,
                    'post_status' => 'publish'
                );
                wp_update_post($booking_data);

                $author_id = get_post_field('post_author', $post_id);
                $user = get_user_by('id', $author_id);
                $user_email = $user->user_email;

                $booking_meta_data = get_post_custom($post_id);
                $booking_item_id   = isset($booking_meta_data[GOLO_METABOX_PREFIX . 'booking_item_id']) ? $booking_meta_data[GOLO_METABOX_PREFIX . 'booking_item_id'][0] : '';

                $args = array(
                    'booking_title' => get_the_title($booking_item_id),
                    'booking_url' => get_permalink($booking_item_id)
                );
                golo_send_email($user_email, 'mail_approved_booking', $args);
                wp_redirect(remove_query_arg('approve_booking', add_query_arg('approve_booking', $post_id, admin_url('edit.php?post_type=booking'))));
                exit;
            }
        }

        /**
         * Cancel booking
         */
        public function cancel_booking()
        {
            if (!empty($_GET['cancel_booking']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'cancel_booking') && current_user_can('publish_post', $_GET['cancel_booking'])) {
                $post_id = absint( golo_clean(wp_unslash($_GET['cancel_booking'])) );
                $booking_data = array(
                    'ID' => $post_id,
                    'post_status' => 'canceled'
                );
                wp_update_post($booking_data);

                $author_id = get_post_field('post_author', $post_id);
                $user = get_user_by('id', $author_id);
                $user_email = $user->user_email;

                $booking_meta_data = get_post_custom($post_id);
                $booking_item_id   = isset($booking_meta_data[GOLO_METABOX_PREFIX . 'booking_item_id']) ? $booking_meta_data[GOLO_METABOX_PREFIX . 'booking_item_id'][0] : '';

                $args = array(
                    'booking_title' => get_the_title($booking_item_id),
                    'booking_url' => get_permalink($booking_item_id)
                );
                golo_send_email($user_email, 'mail_canceled_booking', $args);

                wp_redirect(remove_query_arg('cancel_booking', add_query_arg('cancel_booking', $post_id, admin_url('edit.php?post_type=booking'))));
                exit;
            }
        }
    }
}