<?php
if ( !defined('ABSPATH') ) {
    exit;
}

if ( !class_exists('Golo_Booking') ) {
    /**
     * Class Golo_Booking
     */
    class Golo_Booking
    {
        private $golo_message = '';

        /**
         * Handle actions which need to be run before the shortcode
         */
        public function booking_action_handler()
        {
            global $post;
            if (is_page() && ( strstr($post->post_content, '[golo_my_booking]') || strstr($post->post_content, '[golo_bookings]') )) {
                $this->my_booking_handler();
            }
        }

        /**
         * Place Handler
         */
        public function my_booking_handler()
        {
            if (!empty($_REQUEST['action']) && !empty($_REQUEST['_wpnonce']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'golo_my_booking_actions')) {
                $action = sanitize_title($_REQUEST['action']);
                $booking_id = absint($_REQUEST['booking_id']);
                try {
                    $booking = get_post($booking_id);
                    $booking_meta_data = get_post_custom($booking_id);
                    $booking_item_id   = isset($booking_meta_data[GOLO_METABOX_PREFIX . 'booking_item_id']) ? $booking_meta_data[GOLO_METABOX_PREFIX . 'booking_item_id'][0] : '';

                    switch ($action) {
                        case 'approve' :
                            $data = array(
                                'ID' => $booking_id,
                                'post_type' => 'booking',
                                'post_status' => 'publish'
                            );
                            wp_update_post($data);

                            $author_id = get_post_field('post_author', $booking_id);
                            $user = get_user_by('id', $author_id);
                            $user_email = $user->user_email;

                            $args = array(
                                'booking_title' => get_the_title($booking_item_id),
                                'booking_url' => get_permalink($booking_item_id)
                            );
                            golo_send_email($user_email, 'mail_approved_booking', $args);

                            $this->golo_message = '<div class="golo-message alert alert-success" role="alert">' . sprintf(__('<strong>Success!</strong> %s has been approved', 'golo-framework'), $booking->post_title) . '</div>';
                            break;

                        case 'cancel' :
                            $data = array(
                                'ID' => $booking_id,
                                'post_type' => 'booking',
                                'post_status' => 'canceled'
                            );
                            wp_update_post($data);

                            $author_id = get_post_field('post_author', $booking_id);
                            $user = get_user_by('id', $author_id);
                            $user_email = $user->user_email;

                            $args = array(
                                'booking_title' => get_the_title($booking_item_id),
                                'booking_url' => get_permalink($booking_item_id)
                            );
                            golo_send_email($user_email, 'mail_canceled_booking', $args);

                            $this->golo_message = '<div class="golo-message alert alert-success" role="alert">' . sprintf(__('<strong>Success!</strong> %s has been cancel', 'golo-framework'), $booking->post_title) . '</div>';
                            break;

                        case 'delete' :
                            wp_delete_post($booking_id, true);

                            $this->golo_message = '<div class="golo-message alert alert-success" role="alert">' . sprintf(__('<strong>Success!</strong> %s has been deleted', 'golo-framework'), $booking->post_title) . '</div>';
                            break;

                        default :
                            do_action('golo_booking_do_action_' . $action);
                            break;
                    }

                    do_action('golo_my_booking_do_action', $action, $booking_id);

                } catch (Exception $e) {
                    $this->golo_message = '<div class="golo-message alert alert-danger" role="alert">' . $e->getMessage() . '</div>';
                }
            }
        }

    }
}