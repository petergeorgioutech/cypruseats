<?php
if ( !defined('ABSPATH') ) {
    exit;
}

if ( !class_exists('Golo_Place') ) {
    /**
     * Class Golo_Place
     */
    class Golo_Place
    {
        public function golo_set_place_view() {
            $place_id = get_the_ID();
            $count = (int) get_post_meta( $place_id, GOLO_METABOX_PREFIX . 'place_views_count', true);
            $count++;
            update_post_meta( $place_id, GOLO_METABOX_PREFIX . 'place_views_count', $count );
        }

    	/**
         * upload place img
         */
        public function place_img_upload_ajax()
        {
            $nonce = isset($_REQUEST['nonce']) ? golo_clean(wp_unslash($_REQUEST['nonce'])) : '';
            if (!wp_verify_nonce($nonce, 'place_allow_upload')) {
                $ajax_response = array('success' => false, 'reason' => esc_html__('Security check failed!', 'golo-framework'));
                echo json_encode($ajax_response);
                wp_die();
            }

            $submitted_file = $_FILES['place_upload_file']; // WPCS: sanitization ok, input var ok.

            $uploaded_image = wp_handle_upload($submitted_file, array('test_form' => false));

            if (isset($uploaded_image['file'])) {
                $file_name = basename($submitted_file['name']);
                $file_type = wp_check_filetype($uploaded_image['file']);
                $attachment_details = array(
                    'guid'           => $uploaded_image['url'],
                    'post_mime_type' => $file_type['type'],
                    'post_title'     => preg_replace('/\.[^.]+$/', '', basename($file_name)),
                    'post_content'   => '',
                    'post_status'    => 'inherit'
                );

                $attach_id     = wp_insert_attachment($attachment_details, $uploaded_image['file']);
                $attach_data   = wp_generate_attachment_metadata($attach_id, $uploaded_image['file']);
                wp_update_attachment_metadata($attach_id, $attach_data);
                $thumbnail_url = wp_get_attachment_thumb_url($attach_id);
                $fullimage_url = wp_get_attachment_image_src($attach_id, 'full');

                $ajax_response = array(
                    'success'       => true,
                    'url'           => $thumbnail_url,
                    'attachment_id' => $attach_id,
                    'full_image'    => $fullimage_url[0]
                );
                echo json_encode($ajax_response);
                wp_die();

            } else {
                $ajax_response = array('success' => false, 'reason' => esc_html__('Image upload failed!', 'golo-framework'));
                echo json_encode($ajax_response);
                wp_die();
            }
        }

        /**
         * Remove place img
         */
        public function remove_place_img_ajax()
        {
            $nonce = isset($_POST['removeNonce']) ? golo_clean(wp_unslash($_POST['removeNonce'])) : '';
            $user_id = isset($_POST['user_id']) ? golo_clean(wp_unslash($_POST['user_id'])) : '';
            if ( !wp_verify_nonce($nonce, 'place_allow_upload') ) {
                $json_response = array(
                    'success' => false,
                    'reason'  => esc_html__('Security check fails', 'golo-framework')
                );
                echo json_encode($json_response);
                wp_die();
            }
            $success = false;
            if (isset($_POST['place_id']) && isset($_POST['attachment_id'])) {
                $place_id      = absint(wp_unslash($_POST['place_id'])) ;
                $type          = isset($_POST['type']) ? golo_clean(wp_unslash($_POST['type'])) : '';
                $attachment_id = absint(wp_unslash($_POST['attachment_id']));
                if ($place_id > 0) {
                    if ($type === 'gallery') {
                        delete_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_images', $attachment_id);
                    } else {
                        delete_post_meta($place_id, GOLO_METABOX_PREFIX . '_thumbnail_id', $attachment_id);
                    }
                    $success = true;
                }
                if ($attachment_id > 0) {
                    wp_delete_attachment($attachment_id);
                    $success = true;
                }
            }
            if ($user_id) {
                update_user_meta($user_id, 'author_avatar_image_url', GOLO_THEME_URI . '/assets/images/default-user-image.png');
            }
            $ajax_response = array(
                'success' => $success,
                'url'        => get_the_author_meta('author_avatar_image_url', $user_id),
            );

            echo json_encode($ajax_response);
            wp_die();
        }

        /**
        * Place submit
        */
        public function place_submit_ajax() {
            $place_form               = isset($_REQUEST['place_form']) ? golo_clean(wp_unslash($_REQUEST['place_form'])) : '';
            $place_action             = isset($_REQUEST['place_action']) ? golo_clean(wp_unslash($_REQUEST['place_action'])) : '';
            $place_id                 = isset($_REQUEST['place_id']) ? golo_clean(wp_unslash($_REQUEST['place_id'])) : '';
            $place_title              = isset($_REQUEST['place_title']) ? golo_clean(wp_unslash($_REQUEST['place_title'])) : '';
            $place_price_short        = isset($_REQUEST['place_price_short']) ? golo_clean(wp_unslash($_REQUEST['place_price_short'])) : '';
            $place_price_unit        = isset($_REQUEST['place_price_unit']) ? golo_clean(wp_unslash($_REQUEST['place_price_unit'])) : '';
            $place_price_range        = isset($_REQUEST['place_price_range']) ? golo_clean(wp_unslash($_REQUEST['place_price_range'])) : 'none';
            $place_des                = isset($_REQUEST['place_des']) ? wp_filter_post_kses($_REQUEST['place_des']) : '';
            $place_categories         = isset($_REQUEST['place_categories']) ? golo_clean(wp_unslash($_REQUEST['place_categories'])) : '';
            $place_type               = isset($_REQUEST['place_type']) ? golo_clean(wp_unslash($_REQUEST['place_type'])) : '';
            $place_amenities          = isset($_REQUEST['place_amenities']) ? golo_clean(wp_unslash($_REQUEST['place_amenities'])) : '';
            $place_map_address        = isset($_REQUEST['place_map_address']) ? golo_clean(wp_unslash($_REQUEST['place_map_address'])) : '';
            $place_map_location       = isset($_REQUEST['place_map_location']) ? golo_clean(wp_unslash($_REQUEST['place_map_location'])) : '';
            $place_city               = isset($_REQUEST['place_city']) ? golo_clean(wp_unslash($_REQUEST['place_city'])) : '';
            $custom_place_city        = isset($_REQUEST['custom_place_city']) ? golo_clean(wp_unslash($_REQUEST['custom_place_city'])) : '';
            $place_timezone           = isset($_REQUEST['place_timezone']) ? golo_clean(wp_unslash($_REQUEST['place_timezone'])) : '';
            $place_postal_code        = isset($_REQUEST['place_postal_code']) ? golo_clean(wp_unslash($_REQUEST['place_postal_code'])) : '';
            $place_email              = isset($_REQUEST['place_email']) ? golo_clean(wp_unslash($_REQUEST['place_email'])) : '';
            $place_phone              = isset($_REQUEST['place_phone']) ? golo_clean(wp_unslash($_REQUEST['place_phone'])) : '';
            $place_phone2             = isset($_REQUEST['place_phone2']) ? golo_clean(wp_unslash($_REQUEST['place_phone2'])) : '';
            $place_website            = isset($_REQUEST['place_website']) ? golo_clean(wp_unslash($_REQUEST['place_website'])) : '';
            $place_facebook           = isset($_REQUEST['place_facebook']) ? golo_clean(wp_unslash($_REQUEST['place_facebook'])) : '';
            $place_instagram          = isset($_REQUEST['place_instagram']) ? golo_clean(wp_unslash($_REQUEST['place_instagram'])) : '';
            $place_twitter            = isset($_REQUEST['place_twitter']) ? golo_clean(wp_unslash($_REQUEST['place_twitter'])) : '';
            $opening_monday           = isset($_REQUEST['opening_monday']) ? golo_clean(wp_unslash($_REQUEST['opening_monday'])) : '';
            $opening_monday_time      = isset($_REQUEST['opening_monday_time']) ? golo_clean(wp_unslash($_REQUEST['opening_monday_time'])) : '';
            $opening_tuesday          = isset($_REQUEST['opening_tuesday']) ? golo_clean(wp_unslash($_REQUEST['opening_tuesday'])) : '';
            $opening_tuesday_time     = isset($_REQUEST['opening_tuesday_time']) ? golo_clean(wp_unslash($_REQUEST['opening_tuesday_time'])) : '';
            $opening_wednesday        = isset($_REQUEST['opening_wednesday']) ? golo_clean(wp_unslash($_REQUEST['opening_wednesday'])) : '';
            $opening_wednesday_time   = isset($_REQUEST['opening_wednesday_time']) ? golo_clean(wp_unslash($_REQUEST['opening_wednesday_time'])) : '';
            $opening_thursday         = isset($_REQUEST['opening_thursday']) ? golo_clean(wp_unslash($_REQUEST['opening_thursday'])) : '';
            $opening_thursday_time    = isset($_REQUEST['opening_thursday_time']) ? golo_clean(wp_unslash($_REQUEST['opening_thursday_time'])) : '';
            $opening_friday           = isset($_REQUEST['opening_friday']) ? golo_clean(wp_unslash($_REQUEST['opening_friday'])) : '';
            $opening_friday_time      = isset($_REQUEST['opening_friday_time']) ? golo_clean(wp_unslash($_REQUEST['opening_friday_time'])) : '';
            $opening_saturday         = isset($_REQUEST['opening_saturday']) ? golo_clean(wp_unslash($_REQUEST['opening_saturday'])) : '';
            $opening_saturday_time    = isset($_REQUEST['opening_saturday_time']) ? golo_clean(wp_unslash($_REQUEST['opening_saturday_time'])) : '';
            $opening_sunday           = isset($_REQUEST['opening_sunday']) ? golo_clean(wp_unslash($_REQUEST['opening_sunday'])) : '';
            $opening_sunday_time      = isset($_REQUEST['opening_sunday_time']) ? golo_clean(wp_unslash($_REQUEST['opening_sunday_time'])) : '';
            $place_featured_image_url = isset($_REQUEST['place_featured_image_url']) ? golo_clean(wp_unslash($_REQUEST['place_featured_image_url'])) : '';
            $place_featured_image_id  = isset($_REQUEST['place_featured_image_id']) ? golo_clean(wp_unslash($_REQUEST['place_featured_image_id'])) : '';
            $place_image_ids          = isset($_REQUEST['place_image_ids']) ? golo_clean(wp_unslash($_REQUEST['place_image_ids'])) : '';
            $place_video_url          = isset($_REQUEST['place_video_url']) ? golo_clean(wp_unslash($_REQUEST['place_video_url'])) : '';
            $place_booking_type       = isset($_REQUEST['place_booking_type']) ? golo_clean(wp_unslash($_REQUEST['place_booking_type'])) : '';
            $place_booking            = isset($_REQUEST['place_booking']) ? golo_clean(wp_unslash($_REQUEST['place_booking'])) : '';
            $place_booking_site       = isset($_REQUEST['place_booking_site']) ? golo_clean(wp_unslash($_REQUEST['place_booking_site'])) : '';
            $place_booking_2            = isset($_REQUEST['place_booking_2']) ? golo_clean(wp_unslash($_REQUEST['place_booking_2'])) : '';
            $place_booking_site_2       = isset($_REQUEST['place_booking_site_2']) ? golo_clean(wp_unslash($_REQUEST['place_booking_site_2'])) : '';
            $place_booking_image_url  = isset($_REQUEST['place_booking_image_url']) ? golo_clean(wp_unslash($_REQUEST['place_booking_image_url'])) : '';
            $place_booking_image_id   = isset($_REQUEST['place_booking_image_id']) ? golo_clean(wp_unslash($_REQUEST['place_booking_image_id'])) : '';
            $place_booking_banner_url = isset($_REQUEST['place_booking_banner_url']) ? golo_clean(wp_unslash($_REQUEST['place_booking_banner_url'])) : '';
            $place_booking_form       = isset($_REQUEST['place_booking_form']) ? golo_clean(wp_unslash($_REQUEST['place_booking_form'])) : '';
            $additional_fields        = isset($_REQUEST['additional_fields']) ? golo_clean(wp_unslash($_REQUEST['additional_fields'])) : '';
            $menu_name                = isset($_REQUEST['menu_name']) ? golo_clean(wp_unslash($_REQUEST['menu_name'])) : '';
            $menu_price               = isset($_REQUEST['menu_price']) ? golo_clean(wp_unslash($_REQUEST['menu_price'])) : '';
            $menu_type                = isset($_REQUEST['menu_type']) ? golo_clean(wp_unslash($_REQUEST['menu_type'])) : '';
            $item_desc                = isset($_REQUEST['item_desc']) ? golo_clean(wp_unslash($_REQUEST['item_desc'])) : '';
            $faqs_title               = isset($_REQUEST['faqs_title']) ? golo_clean(wp_unslash($_REQUEST['faqs_title'])) : '';
            $faqs_desc                = isset($_REQUEST['faqs_desc']) ? golo_clean(wp_unslash($_REQUEST['faqs_desc'])) : '';
            $place_menu_image_url     = isset($_REQUEST['place_menu_image_url']) ? golo_clean(wp_unslash($_REQUEST['place_menu_image_url'])) : '';
            $place_menu_image_id      = isset($_REQUEST['place_menu_image_id']) ? golo_clean(wp_unslash($_REQUEST['place_menu_image_id'])) : '';

            $new_place = array();
            if ( $place_action ) {
                $new_place['post_type'] = 'place';
                global $current_user;
                wp_get_current_user();
                $user_id = $current_user->ID;
                $new_place['post_author'] = $user_id;
                $auto_publish         = golo_get_option('auto_publish', 1);
                $auto_publish_edited  = golo_get_option('auto_publish_edited', 1);
                $paid_submission_type = golo_get_option('paid_submission_type','no');

                if (isset($place_title)) {
                    $new_place['post_title'] = $place_title;
                }

                if (isset($place_des)) {
                    $new_place['post_content'] = $place_des;
                }

                $submit_action = $place_form;
                if ($submit_action == 'submit-place') {
                    $place_id = 0;
                    if ($auto_publish == 1) {
                        $new_place['post_status'] = 'publish';
                    } else {
                        $new_place['post_status'] = 'pending';
                    }
                    if( !empty($new_place['post_title']) ) {
                        $place_id = wp_insert_post($new_place, true);
                    }
                    if ($place_id > 0) {
                        if ( $paid_submission_type == 'per_package' ) {
                            $package_key = get_the_author_meta(GOLO_METABOX_PREFIX . 'package_key', $user_id);
                            update_post_meta( $place_id, GOLO_METABOX_PREFIX . 'package_key', $package_key );
                            $package_num_places = get_the_author_meta( GOLO_METABOX_PREFIX . 'package_number_listings', $user_id );
                            if ( $package_num_places - 1 >= 0 ) {
                                update_user_meta( $user_id, GOLO_METABOX_PREFIX . 'package_number_listings', $package_num_places - 1 );
                            }
                        }
                        // do_action( 'wp_insert_post', 'wp_insert_post' );
                    }
                    echo json_encode(array('success' => true));
                } elseif($submit_action == 'edit-place') {
                    $place_id        = absint(wp_unslash($place_id));
                    $place = get_post($place_id);
                    $new_place['ID'] = intval($place_id);

                    if( $place->post_status == 'pending' ) {
                        $new_place['post_status'] = 'pending';
                    }

                    if( $place->post_status == 'publish' ) {
                        if( $auto_publish_edited == 1 ) {
                            $new_place['post_status'] = 'publish';
                        }else{
                            $new_place['post_status'] = 'pending';
                        }
                    }
                    
                    if ($paid_submission_type == 'per_package') {
                        $current_package_key = get_the_author_meta(GOLO_METABOX_PREFIX . 'package_key', $user_id);
                        $place_package_key = get_post_meta($new_place['ID'], GOLO_METABOX_PREFIX . 'package_key', true);
                        $golo_profile = new Golo_Profile();
                        $check_package = $golo_profile->user_package_available($user_id);
                        if( ($check_package == -1) || ($check_package == 0 ) )
                        {
                            return -1;
                        }
                    }

                    $place_id = wp_update_post($new_place);
                    echo json_encode(array('success' => true));
                }
            }
            if($place_id > 0) {

                update_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_featured', 0);

                update_post_meta($place_id, GOLO_METABOX_PREFIX . 'verified_listing', 0);

                if (!empty($menu_name)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'menu_enable', '1');
                    $menu_data = $menu_types = array();

                    for ($i=0; $i < count($menu_name); $i++) { 
                        if (isset($place_menu_image_url[$i]) && isset($place_menu_image_id[$i])) {
                            $place_menu_image = array(
                                'id'  => $place_menu_image_id[$i],
                                'url' => $place_menu_image_url[$i],
                            );
                        }
                        $menu_data[] = array(
                            GOLO_METABOX_PREFIX . 'menu_title'          => $menu_name[$i],
                            GOLO_METABOX_PREFIX . 'menu_price'          => $menu_price[$i],
                            GOLO_METABOX_PREFIX . 'menu_item_type'      => $menu_type[$i],
                            GOLO_METABOX_PREFIX . 'menu_description'    => $item_desc[$i],
                            GOLO_METABOX_PREFIX . 'menu_image'          => $place_menu_image,
                        );
                        $menu_types[$i] = $menu_type[$i];
                    }
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'menu_tab', $menu_data);
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'menu_types_name', $menu_types);
                }

                if (!empty($faqs_title)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'faqs_enable', '1');
                    $faqs_data = array();
                    for ($i=0; $i < count($faqs_title); $i++) { 
                        $faqs_data[] = array(
                            GOLO_METABOX_PREFIX . 'faqs_title'          => $faqs_title[$i],
                            GOLO_METABOX_PREFIX . 'faqs_description'    => $faqs_desc[$i],
                        );
                    }
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'faqs_tab', $faqs_data);
                }

                if (isset($place_price_short)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_price_short', $place_price_short);
                }

                if (isset($place_price_unit)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_price_unit', $place_price_unit);
                }

                if (isset($place_price_range)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_price_range', $place_price_range);
                }

                if (isset($place_categories)) {
                    $place_categories = array_map('intval', $place_categories);
                    wp_set_object_terms($place_id, $place_categories, 'place-categories');
                }

                if (isset($place_type)) {
                    $place_type = array_map('intval', $place_type);
                    wp_set_object_terms($place_id, $place_type, 'place-type');
                }

                if (!empty($place_amenities)) {
                    $place_amenities = array_map('intval', $place_amenities);
                    wp_set_object_terms($place_id, $place_amenities, 'place-amenities');
                }

                if (isset($place_city)) {
                    wp_set_object_terms($place_id, $place_city, 'place-city');
                }

                if( !empty($custom_place_city) ){
                    $custom_place_city = trim($custom_place_city);
                    $custom_place_city_slug = strtolower($custom_place_city);
                    $custom_place_city_slug = str_replace(' ', '-', $custom_place_city_slug);
                    if( !term_exists( $custom_place_city, 'place-city' ) ) {
                        wp_insert_term(
                            $custom_place_city,
                            'place-city',
                            array(
                                'slug' => $custom_place_city,
                            )
                        );
                    }
                    wp_set_object_terms($place_id, $custom_place_city, 'place-city');
                }

                if (isset($place_map_address)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_address', $place_map_address);
                }

                if (isset($place_timezone)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_timezone', $place_timezone);
                }

                if (isset($place_map_location)) {
                    $lat_lng = $place_map_location;
                    $address = $place_map_address;
                    $timezone = $place_timezone;
                    $arr_location = array(
                        'location' => $lat_lng,
                        'address' => $address,
                        'timezone' => $timezone
                    );
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_location', $arr_location);
                }

                if (isset($place_postal_code)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_zip', $place_postal_code);
                }

                if (isset($place_email)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_email', $place_email);
                }

                if (isset($place_phone)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_phone', $place_phone);
                }

                if (isset($place_phone2)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_phone2', $place_phone2);
                }

                if (isset($place_website)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_website', $place_website);
                }

                if (isset($place_facebook)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_facebook', $place_facebook);
                }

                if (isset($place_instagram)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_instagram', $place_instagram);
                }

                if (isset($place_twitter)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_twitter', $place_twitter);
                }

                $get_additional = golo_render_additional_fields();
                if (count($get_additional) > 0) {
                    foreach ($get_additional as $key => $field) {
                        if (count($additional_fields) > 0) {
                            if ($field['type'] == 'checkbox_list') {
                                $arr = array();
                                foreach ($additional_fields[$field['id']] as $v) {
                                    $arr[] = $v;
                                }
                                update_post_meta($place_id, $field['id'], $arr);
                            } else {
                                update_post_meta($place_id, $field['id'], $additional_fields[$field['id']]);
                            }
                        }
                    }

                }



                if (isset($place_booking_url)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_booking', $place_booking_url);
                }

                if (isset($opening_monday)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_monday', $opening_monday);
                }

                if (isset($opening_monday)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_monday_time', $opening_monday_time);
                }

                if (isset($opening_tuesday)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_tuesday', $opening_tuesday);
                }

                if (isset($opening_tuesday_time)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_tuesday_time', $opening_tuesday_time);
                }

                if (isset($opening_wednesday)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_wednesday', $opening_wednesday);
                }

                if (isset($opening_wednesday_time)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_wednesday_time', $opening_wednesday_time);
                }

                if (isset($opening_thursday)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_thursday', $opening_thursday);
                }

                if (isset($opening_thursday_time)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_thursday_time', $opening_thursday_time);
                }

                if (isset($opening_friday)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_friday', $opening_friday);
                }

                if (isset($opening_friday_time)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_friday_time', $opening_friday_time);
                }

                if (isset($opening_saturday)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_saturday', $opening_saturday);
                }

                if (isset($opening_saturday_time)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_saturday_time', $opening_saturday_time);
                }

                if (isset($opening_sunday)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_sunday', $opening_sunday);
                }

                if (isset($opening_sunday_time)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'opening_sunday_time', $opening_sunday_time);
                }

                if (isset($place_featured_image_url) && isset($place_featured_image_id)) {
                    $place_featured_image = array(
                        'id'  => $place_featured_image_id,
                        'url' => $place_featured_image_url,
                    );
                    update_post_meta($place_id, '_thumbnail_id', $place_featured_image_id);
                }

                if (isset($place_image_ids)) {
                    if (!empty($place_image_ids) && is_array($place_image_ids)) {
                        $str_img_ids = '';
                        foreach ($place_image_ids as $place_img_id) {
                            $place_image_ids[] = intval($place_img_id);
                            $str_img_ids .= '|' . intval($place_img_id);
                        }
                        $str_img_ids = substr($str_img_ids, 1);
                        update_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_images', $str_img_ids);
                    }
                }

                if (isset($place_video_url)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_video_url', $place_video_url);
                }

                if (isset($place_booking_type)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_booking_type', $place_booking_type);
                }

                if (isset($place_booking)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_booking', $place_booking);
                }

                if (isset($place_booking_site)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_booking_site', $place_booking_site);
                }

                if (isset($place_booking_2)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_booking_2', $place_booking_2);
                }

                if (isset($place_booking_site_2)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_booking_site_2', $place_booking_site_2);
                }

                if (isset($place_booking_image_url) && isset($place_booking_image_id)) {
                    $place_booking_image = array(
                        'id'  => $place_booking_image_id,
                        'url' => $place_booking_image_url,
                    );
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_booking_banner', $place_booking_image);
                }

                if (isset($place_booking_banner_url)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_booking_banner_url', $place_booking_banner_url);
                }

                if (isset($place_booking_form)) {
                    update_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_booking_form', $place_booking_form);
                }
            }

            wp_die();
        }
    	
    	/**
         * submit review
         */
        public function submit_review_ajax()
        {
            check_ajax_referer('golo_submit_review_ajax_nonce', 'golo_security_submit_review');
            global $wpdb, $current_user;
            wp_get_current_user();
            $user_id                    = $current_user->ID;
            $user                       = get_user_by('id', $user_id);
            $place_id                   = isset($_POST['place_id']) ? golo_clean(wp_unslash($_POST['place_id'])) : '';
            $rating_service_value       = isset($_POST['rating_service']) ? golo_clean(wp_unslash($_POST['rating_service'])) : '';
            $rating_money_value         = isset($_POST['rating_money']) ? golo_clean(wp_unslash($_POST['rating_money'])) : '';
            $rating_location_value      = isset($_POST['rating_location']) ? golo_clean(wp_unslash($_POST['rating_location'])) : '';
            $rating_cleanliness_value   = isset($_POST['rating_cleanliness']) ? golo_clean(wp_unslash($_POST['rating_cleanliness'])) : '';
            $my_review    = $wpdb->get_row("SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $place_id AND comment.user_id = $user_id  AND meta.meta_key = 'place_rating' AND meta.comment_id = comment.comment_ID ORDER BY comment.comment_ID DESC");
            $comment_approved = 1;
            $auto_publish_review_place = get_option( 'comment_moderation' );
            if ($auto_publish_review_place == 1) {
                $comment_approved = 0;
            }
            if ( $my_review == null ) {
                $data = Array();
                $user = $user->data;

                $data['comment_post_ID']      = $place_id;
                $data['comment_content']      = isset($_POST['message']) ?  wp_filter_post_kses($_POST['message']) : '';
                $data['comment_date']         = current_time('mysql');
                $data['comment_approved']     = $comment_approved;
                $data['comment_author']       = $user->user_login;
                $data['comment_author_email'] = $user->user_email;
                $data['comment_author_url']   = $user->user_url;
                $data['user_id']              = $user_id;

                $comment_id = wp_insert_comment($data);

                add_comment_meta($comment_id, 'place_service_rating', $rating_service_value);
                add_comment_meta($comment_id, 'place_money_rating', $rating_money_value);
                add_comment_meta($comment_id, 'place_location_rating', $rating_location_value);
                add_comment_meta($comment_id, 'place_cleanliness_rating', $rating_cleanliness_value);

                $place_rating = ($rating_service_value + $rating_money_value + $rating_location_value + $rating_cleanliness_value) / 4;
                $place_rating = number_format((float)$place_rating, 2, '.', '');

                add_comment_meta($comment_id, 'place_rating', $place_rating);

                if ($comment_approved == 1) {
                    apply_filters('golo_place_rating_meta', $place_id, $place_rating);
                }

                $countfiles = count($_FILES['files']['name']);

                $submitted_file = '';

                $comment_thumb = array();

                for($i = 0; $i < $countfiles; $i++) {

                    $submitted_file = array(
                        'error'         => $_FILES['files']['error'][$i],
                        'name'          => $_FILES['files']['name'][$i],
                        'size'          => $_FILES['files']['size'][$i],
                        'tmp_name'      => $_FILES['files']['tmp_name'][$i],
                        'type'          => $_FILES['files']['type'][$i],
                    );

                    // File name
                    $filename = $_FILES['files']['name'][$i];

                    $upload_overrides = array(
                        'test_form' => false
                    );

                    $movefile = wp_handle_upload( $submitted_file, $upload_overrides );

                    if (isset($movefile['file'])) {
                        $filetype = wp_check_filetype( $movefile['file'], null );
                        $attachment_details = array(
                            'guid'           => $movefile['url'],
                            'post_mime_type' => $filetype['type'],
                            'post_title'     => preg_replace('/\.[^.]+$/', '', basename($filename)),
                            'post_content'   => '',
                            'post_status'    => 'inherit'
                        );

                        $attach_id     = wp_insert_attachment($attachment_details, $movefile['file']);
                        $attach_data   = wp_generate_attachment_metadata($attach_id, $movefile['file']);
                        wp_update_attachment_metadata($attach_id, $attach_data);
                        $thumbnail_url = wp_get_attachment_thumb_url($attach_id);
                        $fullimage_url = wp_get_attachment_image_src($attach_id, 'full');

                        array_push($comment_thumb,$attach_id);

                        $ajax_response = array(
                            'success'       => true,
                            'url'           => $thumbnail_url,
                            'attachment_id' => $attach_id,
                            'full_image'    => $fullimage_url[0]
                        );

                    } else {
                        $ajax_response = array('success' => false, 'reason' => esc_html__('Image upload failed!', 'golo-framework'));
                    }

                }

                add_comment_meta($comment_id, 'comment_thumb', $comment_thumb);
            } else {
                $data = Array();
                
                $data['comment_ID']       = $my_review->comment_ID;
                $data['comment_post_ID']  = $place_id;
                $data['comment_content']  = isset($_POST['message']) ? wp_filter_post_kses($_POST['message']) : '';
                $data['comment_date']     = current_time('mysql');
                $data['comment_approved'] = $comment_approved;

                wp_update_comment($data);
                update_comment_meta($my_review->comment_ID, 'place_service_rating', $rating_service_value);
                update_comment_meta($my_review->comment_ID, 'place_money_rating', $rating_money_value);
                update_comment_meta($my_review->comment_ID, 'place_location_rating', $rating_location_value);
                update_comment_meta($my_review->comment_ID, 'place_cleanliness_rating', $rating_cleanliness_value);

                $place_rating = ($rating_service_value + $rating_money_value + $rating_location_value + $rating_cleanliness_value) / 4;
                $place_rating = number_format((float)$place_rating, 2, '.', '');

                update_comment_meta($my_review->comment_ID, 'place_rating', $place_rating, $my_review->meta_value);

                if ($comment_approved == 1) {
                    apply_filters('golo_place_rating_meta', $place_id, $place_rating, false, $my_review->meta_value);
                }

                $countfiles = count($_FILES['files']['name']);

                $submitted_file = '';

                $comment_thumb = array();

                for($i = 0; $i < $countfiles; $i++) {

                    $submitted_file = array(
                        'error'         => $_FILES['files']['error'][$i],
                        'name'          => $_FILES['files']['name'][$i],
                        'size'          => $_FILES['files']['size'][$i],
                        'tmp_name'      => $_FILES['files']['tmp_name'][$i],
                        'type'          => $_FILES['files']['type'][$i],
                    );

                    // File name
                    $filename = $_FILES['files']['name'][$i];

                    $upload_overrides = array(
                        'test_form' => false
                    );

                    $movefile = wp_handle_upload( $submitted_file, $upload_overrides );

                    if (isset($movefile['file'])) {
                        $filetype = wp_check_filetype( $movefile['file'], null );
                        $attachment_details = array(
                            'guid'           => $movefile['url'],
                            'post_mime_type' => $filetype['type'],
                            'post_title'     => preg_replace('/\.[^.]+$/', '', basename($filename)),
                            'post_content'   => '',
                            'post_status'    => 'inherit'
                        );

                        $attach_id     = wp_insert_attachment($attachment_details, $movefile['file']);
                        $attach_data   = wp_generate_attachment_metadata($attach_id, $movefile['file']);
                        wp_update_attachment_metadata($attach_id, $attach_data);
                        $thumbnail_url = wp_get_attachment_thumb_url($attach_id);
                        $fullimage_url = wp_get_attachment_image_src($attach_id, 'full');

                        array_push($comment_thumb,$attach_id);

                        $ajax_response = array(
                            'success'       => true,
                            'url'           => $thumbnail_url,
                            'attachment_id' => $attach_id,
                            'full_image'    => $fullimage_url[0]
                        );

                    } else {
                        $ajax_response = array('success' => false, 'reason' => esc_html__('Image upload failed!', 'golo-framework'));
                    }

                }

                update_comment_meta($my_review->comment_ID, 'comment_thumb', $comment_thumb);
            }

            echo json_encode(array('success' => true));

            wp_die();
        }

        /**
         * @param $place_id
         * @param $rating_value
         * @param bool|true $comment_exist
         * @param int $old_rating_value
         */
        public function rating_meta_filter($place_id, $rating_value, $comment_exist = true, $old_rating_value = 0)
        {
            update_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_rating', $rating_value);
        }

        /**
         * submit review
         */
        public function submit_reply_ajax()
        {
            check_ajax_referer('golo_submit_reply_ajax_nonce', 'golo_security_submit_reply');
            global $wpdb, $current_user;
            wp_get_current_user();
            $user_id  = $current_user->ID;
            $user     = get_user_by('id', $user_id);
            $place_id = isset($_POST['place_id']) ? golo_clean(wp_unslash($_POST['place_id'])) : '';
            $comment_approved = 1;
            $auto_publish_review_place = get_option( 'comment_moderation' );
            if ($auto_publish_review_place == 1) {
                $comment_approved = 0;
            }
            $data = Array();
            $user = $user->data;

            $data['comment_post_ID']      = $place_id;
            $data['comment_content']      = isset($_POST['message']) ? wp_filter_post_kses($_POST['message']) : '';
            $data['comment_date']         = current_time('mysql');
            $data['comment_approved']     = $comment_approved;
            $data['comment_author']       = $user->user_login;
            $data['comment_author_email'] = $user->user_email;
            $data['comment_author_url']   = $user->user_url;
            $data['comment_parent']       = isset($_POST['comment_id']) ? golo_clean(wp_unslash($_POST['comment_id'])) : '';
            $data['user_id']              = $user_id;

            $comment_id = wp_insert_comment($data);

            echo json_encode(array('success' => true));

            wp_die();
        }

        /**
         * True if an the user can edit a place.
         */
        public function user_can_edit_place($place_id)
        {
            $can_edit = true;

            if (!is_user_logged_in() || !$place_id) {
                $can_edit = false;
            } else {
                $place = get_post($place_id);

                if (!$place || (absint($place->post_author) !== get_current_user_id() && !current_user_can('edit_post', $place_id))) {
                    $can_edit = false;
                }
            }

            return apply_filters('golo_user_can_edit_place', $can_edit, $place_id);
        }

        /**
         * Contact agent
         */
        public function contact_agent_ajax()
        {
            check_ajax_referer('golo_contact_agent_ajax_nonce', 'golo_security_contact_agent');
            $sender_phone = isset($_POST['sender_phone']) ? golo_clean(wp_unslash($_POST['sender_phone'])) : '';
            $target_email = isset($_POST['target_email']) ?  sanitize_email(wp_unslash($_POST['target_email'])) : '';
            $place_url    = isset($_POST['place_url']) ?  esc_url_raw(wp_unslash($_POST['place_url'])) : '';
            $target_email = is_email($target_email);

            if (!$target_email) {
                echo json_encode(array('success' => false, 'message' => esc_html__('Target Email address is not properly configured!', 'golo-framework')));
                wp_die();
            }

            $sender_email  = isset($_POST['sender_email']) ? sanitize_email(wp_unslash($_POST['sender_email'])) : '';
            $sender_name   = isset($_POST['sender_name']) ?  golo_clean(wp_unslash($_POST['sender_name'])) : '';
            $sender_msg    = isset($_POST['sender_msg']) ?  wp_filter_post_kses($_POST['sender_msg']) : '';
            $email_subject = sprintf(esc_html__('New message sent by %s using contact form at %s', 'golo-framework'), $sender_name, get_bloginfo('name'));
            $email_body    = esc_html__('You have received a message from: ', 'golo-framework') . $sender_name . " <br/>";
            
            if (!empty($sender_phone)) {
                $email_body .= esc_html__('Phone Number : ', 'golo-framework') . $sender_phone . " <br/>";
            }
            if (!empty($place_url)) {
                $email_body .= esc_html__('Place Url: ', 'golo-framework') . '<a href="' . $place_url . '">' . $place_url . '</a><br/>';
            }
            $email_body .= esc_html__('Additional message is as follows.', 'golo-framework') . " <br/>";
            $email_body .= wpautop($sender_msg) . " <br/>";
            $email_body .= sprintf(esc_html__('You can contact %s via email %s', 'golo-framework'), $sender_name, $sender_email);

            $header = 'Content-type: text/html; charset=utf-8' . "\r\n";
            $header = apply_filters("golo_contact_agent_mail_header", $header);
            $header .= 'From: ' . $sender_name . " <" . $sender_email . "> \r\n";

            if (wp_mail($target_email, $email_subject, $email_body, $header)) {
                echo json_encode(array('success' => true, 'message' => esc_html__('Message Sent Successfully!', 'golo-framework')));
            } else {
                echo json_encode(array('success' => false, 'message' => esc_html__('Server Error: WordPress mail function failed!', 'golo-framework')));
            }
            wp_die();
        }

    }
}