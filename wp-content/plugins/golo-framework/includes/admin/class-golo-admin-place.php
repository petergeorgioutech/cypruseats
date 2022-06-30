<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!class_exists('Golo_Admin_Place')) {
    /**
     * Class Golo_Admin_Place
     */
    class Golo_Admin_Place
    {
        /**
         * Disable gutenberg for place
         * @param $columns
         * @return array
         */
        public function golo_disable_gutenberg_for_post_type( $is_enabled, $post_type ) {
            if ( 'place' == $post_type ) {
                return false;
            }

            return $is_enabled;
        }

        /**
         * Register custom columns
         * @param $columns
         * @return array
         */
        public function register_custom_column_titles($columns)
        {
            unset($columns['tags']);
            $columns['thumb'] = esc_html__('Image', 'golo-framework');
            $columns['title'] = esc_html__('Place Title', 'golo-framework');
            $columns['type'] =  esc_html__('Type', 'golo-framework');
            $columns['category'] =  esc_html__('Category', 'golo-framework');
            $columns['city'] =esc_html__('City', 'golo-framework');
            $columns['price'] = esc_html__('Price', 'golo-framework');
            $columns['featured'] = '<span data-tip="'.  esc_html__('Featured?', 'golo-framework') .'" class="tips dashicons dashicons-star-filled"></span>';
            $columns['author'] = esc_html__('Author', 'golo-framework');
            $new_columns = array();
            $custom_order = array('cb','thumb', 'title', 'type', 'category', 'city','price','featured','author','date');
            foreach ($custom_order as $colname){
                $new_columns[$colname] = $columns[$colname];
            }
            return $new_columns;
        }
        /**
         * Display custom column for places
         * @param $column
         */
        public function display_custom_column($column)
        {
            global $post;
            switch ($column) {
                case 'thumb':
                    if (has_post_thumbnail()) {
                        the_post_thumbnail('thumbnail', array(
                            'class' => 'attachment-thumbnail attachment-thumbnail-small',
                        ));
                    } else {
                        echo '&ndash;';
                    }
                    break;
                case 'type':
                    echo golo_admin_taxonomy_terms($post->ID, 'place-type', 'place');
                    break;
                case 'category':
                    echo golo_admin_taxonomy_terms($post->ID, 'place-categories', 'place');
                    break;
                case 'city':
                    echo golo_admin_taxonomy_terms($post->ID, 'place-city', 'place');
                    break;
                case 'price':
                    $price = get_post_meta($post->ID, GOLO_METABOX_PREFIX . 'place_price_range', true);
                    $currency_sign = golo_get_option('currency_sign', '$');
                    $low_price     = golo_get_option('low_price', '$');
                    $medium_price  = golo_get_option('medium_price', '$$');
                    $high_price    = golo_get_option('high_price', '$$$');
                    if (!empty($price)) {
                        if( $price == 1 ) {
                            $price = esc_html__('Free', 'golo-framework');
                        }
                        if( $price == 2 ) {
                            $price = $low_price;
                        }
                        if( $price == 3 ) {
                            $price = $medium_price;
                        }
                        if( $price == 4 ) {
                            $price = $high_price;
                        }
                        echo esc_html($price);
                    } else {
                        echo '&ndash;';
                    }
                    break;
                case 'featured':
                    $featured = get_post_meta($post->ID, GOLO_METABOX_PREFIX . 'place_featured', true);
                    if ($featured == 1) {
                        echo '<i data-tip="'.  esc_html__('Featured', 'golo-framework') .'" class="tips accent-color dashicons dashicons-star-filled"></i>';
                    } else {
                        echo '<i data-tip="'.  esc_html__('Not Featured', 'golo-framework') .'" class="tips dashicons dashicons-star-empty"></i>';
                    }
                    break;
                case 'author' :
                    echo '<a href="' . esc_url(add_query_arg('author', $post->post_author)) . '">' . get_the_author() . '</a>';
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
            if ( $post->post_type == 'place' ) {
                if (in_array($post->post_status, array('pending','expired')) && current_user_can('publish_places', $post->ID)) {
                    $actions['place-approve']='<a href="'.wp_nonce_url(add_query_arg('approve_listing', $post->ID), 'approve_listing').'">'.esc_html__('Approve', 'golo-framework').'</a>';
                }
                if (in_array($post->post_status, array('publish', 'pending')) && current_user_can('publish_places', $post->ID)) {
                    $actions['place-expired']='<a href="'.wp_nonce_url(add_query_arg('expire_listing', $post->ID), 'expire_listing').'">'.esc_html__('Expire', 'golo-framework').'</a>';
                }
                if (in_array($post->post_status, array('publish')) && current_user_can('publish_places', $post->ID)) {
                    $actions['place-hidden']='<a href="'.wp_nonce_url(add_query_arg('hidden_listing', $post->ID), 'hidden_listing').'">'.esc_html__('Hide', 'golo-framework').'</a>';
                }
                if (in_array($post->post_status, array('hidden')) && current_user_can('publish_places', $post->ID)) {
                    $actions['place-show']='<a href="'.wp_nonce_url(add_query_arg('show_listing', $post->ID), 'show_listing').'">'.esc_html__('Show', 'golo-framework').'</a>';
                }
            }
            return $actions;
        }

        /**
         * sortable_columns
         * @param $columns
         * @return mixed
         */
        public function sortable_columns($columns)
        {
            $columns['price'] = 'price';
            $columns['featured'] = 'featured';
            $columns['author'] = 'author';
            $columns['post_date'] = 'post_date';
            return $columns;
        }

        /**
         * @param $vars
         * @return array
         */
        public function column_orderby($vars) {
            if ( !is_admin() )
                return $vars;
            if ( isset($vars['orderby']) && 'price' == $vars['orderby'] ) {
                $vars = array_merge($vars, array(
                    'meta_key' => GOLO_METABOX_PREFIX. 'place_price',
                    'orderby' => 'meta_value_num',
                ));
            }
            if ( isset($vars['orderby']) && 'featured' == $vars['orderby'] ) {
                $vars = array_merge($vars, array(
                    'meta_key' => GOLO_METABOX_PREFIX. 'place_featured',
                    'orderby' => 'meta_value_num',
                ));
            }
            return $vars;
        }
        /**
         * Modify place slug
         * @param $existing_slug
         * @return string
         */
        public function modify_place_slug($existing_slug)
        {
            $place_url_slug = golo_get_option('place_url_slug');
            if ($place_url_slug) {
                return $place_url_slug;
            }
            return $existing_slug;
        }

        /**
         * Modify place type slug
         * @param $existing_slug
         * @return string
         */
        public function modify_place_type_slug($existing_slug)
        {
            $place_type_url_slug = golo_get_option('place_type_url_slug');
            if ($place_type_url_slug) {
                return $place_type_url_slug;
            }
            return $existing_slug;
        }

        /**
         * Modify place categories slug
         * @param $existing_slug
         * @return string
         */
        public function modify_place_categories_slug($existing_slug)
        {
            $place_categories_url_slug = golo_get_option('place_categories_url_slug');
            if ($place_categories_url_slug) {
                return $place_categories_url_slug;
            }
            return $existing_slug;
        }

        /**
         * Modify place feature slug
         * @param $existing_slug
         * @return string
         */
        public function modify_place_amenities_slug($existing_slug)
        {
            $place_amenities_url_slug = golo_get_option('place_amenities_url_slug');
            if ($place_amenities_url_slug) {
                return $place_amenities_url_slug;
            }
            return $existing_slug;
        }

        /**
         * Modify place city slug
         * @param $existing_slug
         * @return string
         */
        public function modify_place_city_slug($existing_slug)
        {
            $place_city_url_slug = golo_get_option('place_city_url_slug');
            if ($place_city_url_slug) {
                return $place_city_url_slug;
            }
            return $existing_slug;
        }

        /**
         * Modify place neighborhood slug
         * @param $existing_slug
         * @return string
         */
        public function modify_place_neighborhood_slug($existing_slug)
        {
            $place_neighborhood_url_slug = golo_get_option('place_neighborhood_url_slug');
            if ($place_neighborhood_url_slug) {
                return $place_neighborhood_url_slug;
            }
            return $existing_slug;
        }

        /**
         * Approve_place
         */
        public function approve_place()
        {
            if (!empty($_GET['approve_listing']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'approve_listing') && current_user_can('publish_post', $_GET['approve_listing'])) {
                $post_id = absint( golo_clean(wp_unslash($_GET['approve_listing'])) );
                $listing_data = array(
                    'ID' => $post_id,
                    'post_status' => 'publish'
                );
                wp_update_post($listing_data);

                $author_id = get_post_field('post_author', $post_id);
                $user = get_user_by('id', $author_id);
                $user_email = $user->user_email;

                $args = array(
                    'your_name' => $user->user_login,
                    'listing_title' => get_the_title($post_id),
                    'listing_url' => get_permalink($post_id)
                );
                golo_send_email($user_email, 'mail_approved_listing', $args);
                wp_redirect(remove_query_arg('approve_listing', add_query_arg('approve_listing', $post_id, admin_url('edit.php?post_type=place'))));
                exit;
            }
        }

        /**
         * Expire place
         */
        public function expire_place()
        {
            if (!empty($_GET['expire_listing']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'expire_listing') && current_user_can('publish_post', $_GET['expire_listing'])) {
                $post_id = absint( golo_clean(wp_unslash($_GET['expire_listing'])) );

                $listing_data = array(
                    'ID' => $post_id,
                    'post_status' => 'expired'
                );
                wp_update_post($listing_data);

                $author_id = get_post_field('post_author', $post_id);
                $user = get_user_by('id', $author_id);
                $user_email = $user->user_email;

                $args = array(
                    'listing_title' => get_the_title($post_id),
                    'listing_url' => get_permalink($post_id)
                );
                golo_send_email($user_email, 'mail_expired_listing', $args);

                wp_redirect(remove_query_arg('expire_listing', add_query_arg('expire_listing', $post_id, admin_url('edit.php?post_type=place'))));
                exit;
            }
        }

        /**
         * Hidden place
         */
        public function hidden_place()
        {
            if (!empty($_GET['hidden_listing']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'hidden_listing') && current_user_can('publish_post', $_GET['hidden_listing'])) {
                $post_id = absint( golo_clean(wp_unslash($_GET['hidden_listing'])) );
                $listing_data = array(
                    'ID' => $post_id,
                    'post_status' => 'hidden'
                );
                wp_update_post($listing_data);
                wp_redirect(remove_query_arg('hidden_listing', add_query_arg('hidden_listing', $post_id, admin_url('edit.php?post_type=place'))));
                exit;
            }
        }

        /**
         * Show place
         */
        public function show_place()
        {
            if (!empty($_GET['show_listing']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'show_listing') && current_user_can('publish_post', $_GET['show_listing'])) {
                $post_id = absint( golo_clean(wp_unslash($_GET['show_listing'])) );
                $listing_data = array(
                    'ID' => $post_id,
                    'post_status' => 'publish'
                );
                wp_update_post($listing_data);
                wp_redirect(remove_query_arg('show_listing', add_query_arg('show_listing', $post_id, admin_url('edit.php?post_type=place'))));
                exit;
            }
        }

        /**
         * filter_restrict_manage_place
         */
        public function filter_restrict_manage_place() {
            global $typenow;
            $post_type = 'place';
            if ($typenow == $post_type) {
                $taxonomy_arr  = array('place-city','place-type', 'place-categories');
                foreach($taxonomy_arr as $taxonomy){
                    $selected      = isset($_GET[$taxonomy]) ? golo_clean(wp_unslash($_GET[$taxonomy])) : '';
                    $info_taxonomy = get_taxonomy($taxonomy);
                    wp_dropdown_categories(array(
                        'show_option_all' => __("All {$info_taxonomy->label}"),
                        'taxonomy'        => $taxonomy,
                        'name'            => $taxonomy,
                        'orderby'         => 'name',
                        'selected'        => $selected,
                        'show_count'      => true,
                        'hide_empty'      => false,
                    ));
                }
                ?>
                <input type="text" placeholder="<?php esc_html_e('Author','golo-framework');?>" name="place_author" value="<?php echo (isset($_GET['place_author'])? golo_clean(wp_unslash($_GET['place_author'])) : '');?>">
                <input type="text" placeholder="<?php esc_html_e('Place ID','golo-framework');?>" name="place_identity" value="<?php echo (isset($_GET['place_identity'])? golo_clean(wp_unslash($_GET['place_identity'])) : '');?>">
                <?php
            };
        }

        /**
         * h_filter
         * @param $query
         */
        public function place_filter($query) {
            global $pagenow;
            $post_type = 'place';
            $q_vars    = &$query->query_vars;
            if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type)
            {
                $taxonomy_arr  = array('place-city','place-type','place-categories');
                foreach($taxonomy_arr as $taxonomy) {
                    if (isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0) {
                        $term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
                        $q_vars[$taxonomy] = $term->slug;
                    }
                }
                if(isset($_GET['place_author']) && $_GET['place_author'] != '')
                {
                    $q_vars['author_name'] = golo_clean(wp_unslash($_GET['place_author']));
                }
                if(isset($_GET['place_identity']) && $_GET['place_identity'] != '')
                {
                    $q_vars['meta_key'] = GOLO_METABOX_PREFIX . 'place_identity';
                    $q_vars['meta_value'] = golo_clean(wp_unslash($_GET['place_identity']));
                    $q_vars['meta_compare'] = '=';
                }
            }
        }

        public function get_city_by_country_ajax()
        {
            if (!isset($_POST['country'])) {
                return;
            }
            $country = golo_clean(wp_unslash($_POST['country'])) ;
            $type = isset($_POST['type']) ?  golo_clean(wp_unslash($_POST['type'])) : '';
            if (!empty($country)) {
                $taxonomy_terms = get_categories(
                    array(
                        'taxonomy' => 'place-city',
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'hide_empty' => false,
                        'parent' => 0,
                        'meta_query' => array(
                            array(
                                'key' => 'place_city_country',
                                'value' => $country,
                                'compare' => '=',
                            )
                        )
                    )
                );
            } else {
                $taxonomy_terms = get_categories(
                    array(
                        'taxonomy' => 'place-city',
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'hide_empty' => false,
                        'parent' => 0,
                    )
                );
            }

            $html = '';
            if ($type == 0) {
                $html = '<option value="">' . esc_html__('None', 'golo-framework') . '</option>';
            }
            if (!empty($taxonomy_terms)) {
                if (isset($_POST['is_slug']) && ($_POST['is_slug']=='0')) {
                    foreach ($taxonomy_terms as $term) {
                        $html .= '<option value="' . $term->term_id . '">' . $term->name . '</option>';
                    }
                }
                else
                {
                    foreach ($taxonomy_terms as $term) {
                        $html .= '<option value="' . $term->slug . '">' . $term->name . '</option>';
                    }
                }
            }
            if ($type == 1) {
                $html .= '<option value="" selected="selected">' . esc_html__('All Cities', 'golo-framework') . '</option>';
            }
            echo wp_kses($html,array(
                'option' => array(
                'value' => true,
                'selected' => true
            )));
            wp_die();
        }

        public function get_neighborhoods_by_city_ajax()
        {
            if (!isset($_POST['city'])) {
                return;
            }
            $city = golo_clean(wp_unslash($_POST['city']));
            $type = isset($_POST['type']) ? golo_clean(wp_unslash($_POST['type'])) : '';
            if (isset($_POST['is_slug']) && ($_POST['is_slug']=='0')) {
                $place_city = get_term_by('id', $city, 'place-city');
            }
            else{
                $place_city = get_term_by('slug', $city, 'place-city');
            }

            if (!empty($city) && $place_city) {
                $taxonomy_terms = get_categories(
                    array(
                        'taxonomy' => 'place-neighborhood',
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'hide_empty' => false,
                        'parent' => 0,
                        'meta_query' => array(
                            array(
                                'key' => 'place_neighborhood_city',
                                'value' => $place_city->term_id,
                                'compare' => '=',
                            )
                        )
                    )
                );
            } else {
                $taxonomy_terms = get_categories(
                    array(
                        'taxonomy' => 'place-neighborhood',
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'hide_empty' => false,
                        'parent' => 0,
                    )
                );
            }

            $html = '';
            if ($type == 0) {
                $html = '<option value="">' . esc_html__('None', 'golo-framework') . '</option>';
            }
            if (!empty($taxonomy_terms)) {
                if (isset($_POST['is_slug']) && ($_POST['is_slug']=='0')) {
                    foreach ($taxonomy_terms as $term) {
                        $html .= '<option value="' . $term->term_id . '">' . $term->name . '</option>';
                    }
                }
                else
                {
                    foreach ($taxonomy_terms as $term) {
                        $html .= '<option value="' . $term->slug . '">' . $term->name . '</option>';
                    }
                }
            }
            if ($type == 1) {
                $html .= '<option value="" selected="selected">' . esc_html__('All Neighborhoods', 'golo-framework') . '</option>';
            }
            echo wp_kses($html,array(
                'option' => array(
                    'value' => true,
                    'selected' => true
                )));
            wp_die();
        }

        public function action_claim_listing()
        {
            $status              = isset($_POST['status']) ? golo_clean(wp_unslash($_POST['status'])) : '';
            $place_id            = isset($_POST['place_id']) ? golo_clean(wp_unslash($_POST['place_id'])) : '';

            $place_title         = get_the_title($place_id);
            $headers = array('Content-Type: text/html; charset=UTF-8');
            update_post_meta($place_id, GOLO_METABOX_PREFIX . 'cd_status', $status);
            $cd_your_email = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'cd_your_email', true);

            if ($status == 'accept') {
                update_post_meta($place_id, GOLO_METABOX_PREFIX . 'verified_listing', 1);
                // SEND EMAIL
                $message = esc_html__('Hi there,', 'golo-framework') . "\r\n\r\n";
                $message .= sprintf(esc_html__("Your request to claim ownership has been confirmed. You can check it.", 'golo-framework'), $place_title) . "\r\n\r\n";
                wp_mail($cd_your_email,
                    sprintf(esc_html__('Claim Listing', 'golo-framework'), 
                    $place_title),
                    $message,
                    $headers
                );
            } else if ($status == 'refuse') {
                update_post_meta($place_id, GOLO_METABOX_PREFIX . 'verified_listing', 0);
                // SEND EMAIL
                $message = esc_html__('Hi there,', 'golo-framework') . "\r\n\r\n";
                $message .= sprintf(esc_html__("Your request to claim ownership has been declined. You can check it.", 'golo-framework'), $place_title) . "\r\n\r\n";
                wp_mail($cd_your_email,
                    sprintf(esc_html__('Claim Listing', 'golo-framework'), 
                    $place_title),
                    $message,
                    $headers
                );
            } else if ($status == 'delete') {
                update_post_meta($place_id, GOLO_METABOX_PREFIX . 'cd_status', '');
                update_post_meta($place_id, GOLO_METABOX_PREFIX . 'verified_listing', '');
                update_post_meta($place_id, GOLO_METABOX_PREFIX . 'claim_request', '');
                // SEND EMAIL
                $message = esc_html__('Hi there,', 'golo-framework') . "\r\n\r\n";
                $message .= sprintf(esc_html__("Your request to claim ownership has been declined. You can check it.", 'golo-framework'), $place_title) . "\r\n\r\n";
                wp_mail($cd_your_email,
                    sprintf(esc_html__('Claim Listing', 'golo-framework'), 
                    $place_title),
                    $message,
                    $headers
                );
            }
            

            echo json_encode(array('success' => true, 'stt'  => $status));
            wp_die();
        }
    }
}