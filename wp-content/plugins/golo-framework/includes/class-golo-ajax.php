<?php
if ( !defined('ABSPATH') ) {
    exit;
}

if ( !class_exists('Golo_Ajax') ) {
    /**
     * Class Golo_Ajax
     */
    class Golo_Ajax
    {
        public function golo_pagination_ajax()
        {   

            global $wpdb;

            $title        = isset($_REQUEST['title']) ? golo_clean(wp_unslash($_REQUEST['title'])) : '';
            $item_amount  = isset($_REQUEST['item_amount']) ? golo_clean(wp_unslash($_REQUEST['item_amount'])) : '12';
            $paged        = isset($_REQUEST['paged']) ? golo_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $price        = isset($_REQUEST['price']) ? golo_clean(wp_unslash($_REQUEST['price'])) : '';
            $sort_by      = isset($_REQUEST['sort_by']) ? golo_clean(wp_unslash($_REQUEST['sort_by'])) : '';
            $cities       = isset($_REQUEST['cities']) ? golo_clean(wp_unslash($_REQUEST['cities'])) : '';
            $categories   = isset($_REQUEST['categories']) ? golo_clean(wp_unslash($_REQUEST['categories'])) : '';
            $types        = isset($_REQUEST['types']) ? golo_clean(wp_unslash($_REQUEST['types'])) : '';
            $amenities    = isset($_REQUEST['amenities']) ? golo_clean(wp_unslash($_REQUEST['amenities'])) : '';
            $neighborhood = isset($_REQUEST['neighborhood']) ? golo_clean(wp_unslash($_REQUEST['neighborhood'])) : array();
            $current_term = isset($_REQUEST['current_term']) ? golo_clean(wp_unslash($_REQUEST['current_term'])) : '';
            $type_term    = isset($_REQUEST['type_term']) ? golo_clean(wp_unslash($_REQUEST['type_term'])) : '';
            $city         = isset($_REQUEST['city']) ? golo_clean(wp_unslash($_REQUEST['city'])) : '';
            $location     = isset($_REQUEST['location']) ? golo_clean(wp_unslash($_REQUEST['location'])) : '';
            $place_type   = isset($_REQUEST['place_type']) ? golo_clean(wp_unslash($_REQUEST['place_type'])) : '';
            $place_layout = isset($_REQUEST['place_layout']) ? golo_clean(wp_unslash($_REQUEST['place_layout'])) : '';
            $open_now     = isset($_REQUEST['open_now']) ? golo_clean(wp_unslash($_REQUEST['open_now'])) : '';
            
            if( $city ){
                $current_city = $city;
            }else{
                $tax   = get_term_by('id', $current_term, $type_term);
                
                if ($tax) {
                    $current_city   = $tax->slug;
                }
            }

            $meta_query = array();
            $tax_query  = array();

            if( is_author() ) {
                $item_amount = 6;
            }

            // Get Default Order
            $search_order = golo_get_option('default_search_order', 'place_featured');
            $meta_key_search_list = array('place_featured', 'average_rating');

            if ( in_array( $search_order, $meta_key_search_list ) ) {
                $meta_key = 'golo-' . $search_order;
            } 
            
            if (isset($meta_key)) {
                $order_by = 'meta_value';
                $order_by .= ' ' . 'date';
            } else {
                $order_by = $search_order;
            }
            
            // If fitler "open_now", load all places to check open_now

            $args = array(
                'posts_per_page'        => ($open_now != 1 && $item_amount > 0) ? $item_amount : -1,
                'post_type'             => 'place',
                'paged'                 => $open_now != 1 ? $paged : '',
                'post_status'           => 'publish',
                'meta_key'              => isset($meta_key)? $meta_key : '',
                'orderby'               => $order_by
            );

            if (!empty($title)) {
                $args['s'] = $title;
            }

            $location_check_id = get_term_by('name', $title, 'place-city');


            if ($location_check_id) {
                $tax_query[] = array(
                    'taxonomy' => 'place-city',
                    'field'    => 'id',
                    'terms'    => $location_check_id->term_id
                );
                $args['s'] = '';
            }

            //tax query place cities
            if (!empty($cities)) {
                $tax_query[] = array(
                    'taxonomy' => 'place-city',
                    'field'    => 'term_id',
                    'terms'    => $cities
                );
            }

            //tax query place location
            if( !empty($location) && empty($cities) && empty($city) ) {
                $location_id = get_term_by('name', $title, 'place-city');

                $tax_query[] = array(
                    'taxonomy' => 'place-city',
                    'field'    => 'term_id',
                    'terms'    => $location_id->term_id
                );
            }

            if( !empty($place_type) && $place_type == $types ) {
                $type_check_id = get_term_by('name', $place_type, 'place-type');
                $tax_query[] = array(
                    'taxonomy' => 'place-type',
                    'field'    => 'term_id',
                    'terms'    => $type_check_id->term_id
                );
            }

            //tax query place city
            if (!empty($city)) {
                $tax_query[] = array(
                    'taxonomy' => 'place-city',
                    'field'    => 'slug',
                    'terms'    => $city
                );
            }

            //tax query current term
            if( !empty($current_term) && !empty($type_term) ) {
                $tax_query[] = array(
                    'taxonomy' => $type_term,
                    'field'    => 'id',
                    'terms'    => $current_term
                );
            }

            //tax query place categories
            if (!empty($categories)) {
                $tax_query[] = array(
                    'taxonomy' => 'place-categories',
                    'field'    => 'term_id',
                    'terms'    => $categories
                );
            }

            //tax query place types
            if (!empty($types)) {
                $tax_query[] = array(
                    'taxonomy' => 'place-type',
                    'field'    => 'term_id',
                    'terms'    => $types
                );
            }

            //tax query place amenities
            if (!empty($amenities)) {
                $tax_query[] = array(
                    'taxonomy' => 'place-amenities',
                    'field'    => 'term_id',
                    'terms'    => $amenities
                );
            }

            //tax query place neighborhood
            if (!empty($neighborhood)) {
                $tax_query[] = array(
                    'taxonomy' => 'place-neighborhood',
                    'field'    => 'term_id',
                    'terms'    => $neighborhood
                );
            }

            //meta query place price
            if (!empty($price)) {
                $price      = sanitize_text_field($price);
                $price_free = array();

                if( $price == 'free' ) {
                    $price_free[] = array(
                        'key'     => GOLO_METABOX_PREFIX. 'place_price_short',
                        'value'   => '',
                        'type'    => 'NUMERIC',
                        'compare' => '=',
                    );
                }

                $meta_query[] = array(
                    'relation' => 'AND',
                    array(
                        'key'     => GOLO_METABOX_PREFIX. 'place_price_range',
                        'value'   => $price,
                        'type'    => 'CHAR',
                        'compare' => '=',
                    ),
                    $price_free
                );
            }

            //meta query place sort_by            
            if (!empty($sort_by)) {
                if( $sort_by == 'featured' ) {
                    $meta_query[] = array(
                        'key'     => GOLO_METABOX_PREFIX. 'place_featured',
                        'value'   => 1,
                        'type'    => 'NUMERIC',
                        'compare' => '=',
                    );
                }
                
                if( $sort_by == 'rating' ) {
                    $args['meta_key'] = GOLO_METABOX_PREFIX . 'average_rating';
                    $args['orderby']  = 'meta_value_num';
                    $args['order']    = 'DESC';
                }

                if( $sort_by == 'newest' ) {
                    $args['orderby'] = array(
                        'menu_order' => 'ASC',
                        'date'       => 'DESC',
                    );
                }
            }

            $args['meta_query'] = array(
                'relation' => 'AND',
                $meta_query
            );

            $tax_count = count($tax_query);
            if ($tax_count > 0) {
                $args['tax_query'] = array(
                    'relation' => 'AND',
                    $tax_query
                );
            }

            $args_map = $args;
            $args_map['paged'] = '';
            $args_map['posts_per_page'] = '-1';

            $data       = new WP_Query($args);
            $data_map   = new WP_Query($args_map);
            $total_post = $data->found_posts;
            $place_html = $filter_html ='';
            $places     = array();
            
            $max_num_pages = $data->max_num_pages;

            $final_total_post = $total_post;
            
            // Filter all open places when open_now is set
            if ( $total_post > 0 && $open_now == 1 ) {

                $open_place_id = array();
                $status = '';

                while ( $data->have_posts() ) : $data->the_post();
                    $stored_id = get_the_ID();
                    $place_timezone = get_post_meta(get_the_ID(), GOLO_METABOX_PREFIX . 'place_timezone', true);
                    
                    $tzstring = convert_place_timezone($place_timezone);
                    
                    $currentTime = new DateTime("now", new DateTimeZone($tzstring) );
                    $timestamp = strtotime($currentTime->format("Y-m-d H:i:s"));
            
                    $long_current_day = date('l', $timestamp);
                    $short_current_day = date('D', $timestamp);
                    
                    $opening_time    = get_post_meta($stored_id, GOLO_METABOX_PREFIX . 'opening_' .strtolower($long_current_day) .'_time', true);

                    $storeSchedule = [
                        $short_current_day => $opening_time
                    ];
                    
                    $result_status = golo_status_time_place($storeSchedule);
                    
                    if ( stripos( $result_status, 'close' ) ) {
                        continue;
                    } else {
                        $open_place_id[] = $stored_id;
                        if (!empty($status)) {
                            $status = $result_status;
                        }
                    }
                endwhile;

                $final_total_post = count($open_place_id);                
                $open_place_id = array_slice( $open_place_id, (int)($paged - 1) * (int)$item_amount, $item_amount );
            }
            wp_reset_postdata();

            // Pagination if open_now
            if ( $open_now == 1 ) {
                $max_num_pages = ceil( $final_total_post / $item_amount );
            }

            $pagination_type = golo_get_option('pagination_type', 'loadmore');
            if( $pagination_type == 'number' ) {
                $pagination = paginate_links( apply_filters( 'golo_pagination_args', array(
                    'total'     => $max_num_pages,
                    'current'   => $paged,
                    'mid_size'  => 1,
                    'type'      => 'array',
                    'prev_text' => __('<i class="fal fa-chevron-left"></i>', 'golo-framework'),
                    'next_text' => __('<i class="fal fa-chevron-right"></i>', 'golo-framework'),
                ) ));
            }else{
                $pagination = '<a class="page-numbers next" href="#"><span>' . __('Load More', 'golo-framework') . '</span><i class="far fa-spinner-third fa-spin icon-large"></i></a>';
            }

            $hidden_pagination = '';
            if( $paged == $max_num_pages ){
                $hidden_pagination = 1;
            }
            
            $place_html = ob_start();
            
            if( $total_post > 0 && ( $open_now == '' || ($open_now == 1 && ! empty($open_place_id)) ) ){
                $custom_place_image_size = golo_get_option('custom_place_image_size', '540x480' );                
                
                while ( $data->have_posts() ) : $data->the_post();
                    
                    if ( $open_now == 1 && ! in_array( get_the_ID(), $open_place_id ) ) {
                        continue;
                    }
                    
                    golo_get_template('content-place.php', array(
                        'place_layout' => $place_layout,
                        'custom_place_image_size' => $custom_place_image_size,
                        'status' => !empty($status) ? $status : ''
                    ));
                    
                endwhile;
            }
            wp_reset_postdata();
            
            $place_html = ob_get_clean();
                        
            // Use the final_post of open places when "open_now" is set
            if( !empty($total_post) ) {
                $count_post = sprintf( _n( '%s Result', '%s Results', $final_total_post, 'golo-framework' ), '<span class="count">' . esc_html( $final_total_post ) . '</span>' );
            }else{
                $count_post = esc_html__('We not found place available for you', 'golo-framework');
            }

            if ( !empty($current_term) ) {
                $count_post = sprintf( _n( '%s Place', '%s Places', $final_total_post, 'golo-framework' ), '<span class="count">' . esc_html( $final_total_post ) . '</span>' );
                if( empty($total_post) ){
                    $count_post = sprintf( __( '%s Place', 'golo-framework' ), $final_total_post);
                }
            }
            
            if( $total_post > 0 ){

                while ( $data_map->have_posts() ) : $data_map->the_post();

                    $place_id = get_the_ID();

                    if ( ! empty($open_place_id) && ! in_array( $place_id, $open_place_id ) ) {
                        continue;
                    }
                    
                    $place_title = get_the_title();

                    $place_meta_data = get_post_custom( $place_id );

                    $map_zoom_level = golo_get_option('map_zoom_level', '15');

                    $price_short    = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_price_short']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_price_short'][0] : '';
                    $price_range    = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_price_range']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_price_range'][0] : '';
                    $place_address  = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_address']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_address'][0] : '';
                    $place_location = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_location', true);

                    if ( !empty($place_location['location']) ) {
                        $lat_lng = explode(',', $place_location['location']);
                    } else {
                        $lat_lng = array();
                    }

                    // Rating
                    $rating = $total_reviews = $total_stars = 0;
                    $comments_query = "SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $place_id AND meta.meta_key = 'place_rating' AND meta.comment_id = comment.comment_ID AND ( comment.comment_approved = 1 )";
                    $my_review      = $wpdb->get_row("SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $place_id  AND meta.meta_key = 'place_rating' AND meta.comment_id = comment.comment_ID ORDER BY comment.comment_ID DESC");
                    $get_comments   = $wpdb->get_results($comments_query);

                    if (!is_null($get_comments)) {
                        foreach ($get_comments as $comment) {
                            if ($comment->comment_approved == 1) {
                                if( !empty($comment->meta_value) ){
                                    $total_reviews++;
                                }
                                if( $comment->meta_value > 0 ){
                                    $total_stars += $comment->meta_value;
                                }
                            }
                        }

                        if ($total_reviews != 0) {
                            $rating = number_format($total_stars / $total_reviews, 1);
                        }
                    }

                    $primary_term               = get_primary_taxonomy_id($place_id, 'place-categories');

                    $marker_icon = '';
                    $place_categories = get_the_terms( $place_id, 'place-categories');
                    if( $place_categories ) {
                        foreach ($place_categories as $cate) {
                            $cate_id     = $cate->term_id;
                            $icon_marker = get_term_meta( $cate_id, 'place_categories_icon_marker', true );
                            if( !empty($icon_marker) ) {
                                $marker_icon = $icon_marker['url'];
                                break;
                            }
                            
                        }
                        
                        
                        if($primary_term){
                            $icon_marker = get_term_meta( $primary_term, 'place_categories_icon_marker', true );
                            if( !empty($icon_marker['url']) ) {
                                $marker_icon        = $icon_marker['url'];
                            } else {
                                $marker_icon    = GOLO_PLUGIN_URL . 'assets/images/map-marker-icon.png';
                            }

                        }
                    }

                    $html_cate = '';
                
                    if( $primary_term ){
                        $primary_terms = get_term_by('id', $primary_term, 'place-categories');
                        $cate_link = get_term_link($primary_term, 'place-categories');
                        $html_cate .= '<a href="' . $cate_link . '?city=' . $current_city . '">' . $primary_terms->name . '</a>';
                    }
                    if( $place_categories ) {
                        foreach ($place_categories as $cate) {
                            $cate_link = get_term_link($cate, 'place-categories');
                            $link = $cate_link;
                            if( !empty($current_city) ) {
                                $link = $cate_link . '?city=' . $current_city;
                            }
                            $html_cate .= '<a href="' . $link . '">' . $cate->name . '</a>';
                        }
                    }

                    $attach_id = get_post_thumbnail_id();
                    $width  = 120;
                    $height = 150;
                    if ( !empty($attach_id) ) {
                        $image_src = golo_image_resize_id($attach_id, $width, $height, true);
                    } else {
                        
                        $default_image = golo_get_option('default_place_image','');
                        if( $default_image != '' )
                        {
                            $image_src = $default_image['url'];
                        }
                    }

                    if (!$image_src) {
                        $image_src = GOLO_PLUGIN_URL . 'assets/images/no-image.jpg';
                    }

                    $price = '';

                    $currency_sign = golo_get_option('currency_sign', '$');
                    $low_price     = golo_get_option('low_price', '$');
                    $medium_price  = golo_get_option('medium_price', '$$');
                    $high_price    = golo_get_option('high_price', '$$$');

                    if( $price_range && $price_range != 0 ){

                        if( $price_range == 1 ){
                            $price = esc_html__('Free', 'golo-framework');
                        }
                        if( $price_range == 2 ){
                            $price = $low_price;
                        }
                        if( $price_range == 3 ){
                            $price = $medium_price;
                        }
                        if( $price_range == 4 ){
                            $price = $high_price;
                        }
                    }

                    if( $price_short ){
                        $price = golo_get_format_money( $price_short );
                    }

                    if (!empty($place_location)) {
                        list($lat, $lng) = explode(',', $place_location['location']);
                    }

                    if( $place_location && $place_location['address'] )
                    {
                        $google_map_address_url = "http://maps.google.com/?q=" . $place_location['address'];
                    }
                    else
                    {
                        $google_map_address_url = "http://maps.google.com/?q=" . $place_address;
                    }

                    $prop = new stdClass();
                    $prop->image_url = $image_src;
                    $prop->id        = $place_id;
                    $prop->title     = get_the_title();
                    $prop->lat       = $lat_lng[0];
                    $prop->lng       = $lat_lng[1];
                    $prop->url       = get_permalink();
                    $prop->price     = $price;
                    $prop->rating    = $rating;
                    $prop->review    = $total_reviews;
                    $prop->city      = $city;
                    $prop->cate      = $html_cate;

                    if ( empty($place_url) ) {
                        $place_url      = GOLO_PLUGIN_URL . 'assets/images/map-marker-icon.png';
                        $default_marker = golo_get_option('marker_icon','');
                        if( $default_marker != '' )
                        {
                            if( is_array($default_marker) && $default_marker['url'] != '' )
                            {
                                $place_url = $default_marker['url'];
                            }
                        }
                    }

                    if( $marker_icon ){
                        $prop->marker_icon = $marker_icon;
                    }else{
                        $prop->marker_icon = $place_url;
                    }
                    
                    array_push($places, $prop);

                endwhile;

            }
            wp_reset_postdata();
            
            $archive_place_layout_style = golo_get_option('archive_place_layout_style', 'layout-default');
            if( !empty($title) && $archive_place_layout_style == 'layout-column' ) {
                $count_post = sprintf( esc_html__( '%1$s results for: "%2$s"', 'golo-framework' ), '<span>'. $final_total_post .'</span>', $title );
            }

            $filter_html = ob_start();
            
            $place_neighborhoods_meta_query = array();

            if (!empty($cities)) {
                $place_neighborhoods_meta_query[] = array(
                    'key' => 'place_neighborhood_city',
                    'value' => $cities,
                    'compare' => 'IN',
                );
            }

            $place_neighborhoods = get_categories(array(
                    'taxonomy'   => 'place-neighborhood',
                    'hide_empty' => 0,
                    'orderby'    => 'term_id',
                    'order'      => 'ASC',
                    'meta_query' => array(
                        'relation' => 'AND',
                        $place_neighborhoods_meta_query
                    )
                ));
                if ($place_neighborhoods) :
            ?>
            
            <?php
            foreach ($place_neighborhoods as $place_neighborhood) {
            ?>
                <li>
                    <input type="checkbox" id="golo_<?php echo esc_attr($place_neighborhood->slug); ?>" class="custom-checkbox input-control" name="neighborhood" value="<?php echo esc_attr($place_neighborhood->term_id); ?>" <?php if( in_array($place_neighborhood->term_id, $neighborhood) ) { echo 'checked'; } ?> />
                    <label for="golo_<?php echo esc_attr($place_neighborhood->slug); ?>"><?php echo esc_html($place_neighborhood->name); ?></label>
                </li>
            <?php } ?>

            <?php endif;

            $filter_html = ob_get_clean();
            
            if ($total_post > 0) {
                echo json_encode(array('success' => true,'places' => $places, 'count_place' => count($places), 'pagination' => $pagination, 'hidden_pagination' => $hidden_pagination,'pagination_type' => $pagination_type, 'place_html' => $place_html, 'filter_html' => $filter_html, 'total_post' => $total_post, 'count_post' => $count_post));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post, 'count_post' => $count_post));
            }
            wp_die();
        }

        public function golo_place_search_map_ajax()
        {   
            global $wpdb;

            $city              = isset($_REQUEST['city']) ? golo_clean(wp_unslash($_REQUEST['city'])) : '';
            $paged             = isset($_REQUEST['paged']) ? golo_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $item_amount       = isset($_REQUEST['item_amount']) ? golo_clean(wp_unslash($_REQUEST['item_amount'])) : '12';
            $marker_image_size = isset($_REQUEST['marker_image_size']) ? golo_clean(wp_unslash($_REQUEST['marker_image_size'])) : '100x100';
            $taxonomy_name     = isset($_REQUEST['taxonomy_name']) ? golo_clean(wp_unslash($_REQUEST['taxonomy_name'])) : '';
            $current_term      = isset($_REQUEST['current_term']) ? golo_clean(wp_unslash($_REQUEST['current_term'])) : '';
            $category          = isset($_REQUEST['category']) ? golo_clean(wp_unslash($_REQUEST['category'])) : '';

            if( $city ){
                $current_city = $city;
            }else{
                $tax   = get_term_by('id', $current_term, $type_term);
                $current_city   = $tax->slug;
            }

            $meta_query = array();
            $tax_query  = array();

            $args = array(
                'posts_per_page'      => -1,
                'post_type'           => 'place',
                'ignore_sticky_posts' => 1,
                'post_status'         => 'publish',
                'meta_key'              => GOLO_METABOX_PREFIX. 'place_featured',
                'orderby'               => 'meta_value_num',
            );

            //city
            if (!empty($current_city) && $taxonomy_name == 'place-city' ) {
                $tax_query[] = array(
                    'taxonomy' => 'place-city',
                    'field'    => 'slug',
                    'terms'    => $current_city
                );
            }

            //category
            if (!empty($category)) {
                $tax_query[] = array(
                    'taxonomy' => 'place-categories',
                    'field'    => 'slug',
                    'terms'    => $category
                );
            }

            if (!empty($taxonomy_name)) {
                $tax_query[] = array(
                    'taxonomy' => $taxonomy_name,
                    'field'    => 'slug',
                    'terms'    => $current_term
                );
            }

            $args['meta_query'] = array(
                'relation' => 'AND',
                $meta_query
            );

            $tax_count = count($tax_query);
            if ($tax_count > 0) {
                $args['tax_query'] = array(
                    'relation' => 'AND',
                    $tax_query
                );
            }

            $data       = new WP_Query($args);
            $total_post = $data->found_posts;
            $places     = array();
            $place_html = '';

            ob_start();

            if($total_post > 0){
                $custom_place_image_size = golo_get_option('custom_place_image_size', '540x480' );
                $place_item_class = array('place-item golo-item-wrap');

                while ( $data->have_posts() ) : $data->the_post();
                    $place_id = get_the_ID();
                    
                    $place_title = get_the_title();

                    $place_meta_data = get_post_custom( $place_id );

                    $map_zoom_level = golo_get_option('map_zoom_level', '15');

                    $price_short      = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_price_short']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_price_short'][0] : '';
                    $price_range      = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_price_range']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_price_range'][0] : '';
                    $place_address    = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_address']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_address'][0] : '';
                    $place_location   = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_location', true);

                    if ( !empty($place_location['location']) ) {
                        $lat_lng = explode(',', $place_location['location']);
                    } else {
                        $lat_lng = array();
                    }

                    // Rating
                    $rating = $total_reviews = $total_stars = 0;
                    $comments_query = "SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $place_id AND meta.meta_key = 'place_rating' AND meta.comment_id = comment.comment_ID AND ( comment.comment_approved = 1 )";
                    $my_review      = $wpdb->get_row("SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $place_id  AND meta.meta_key = 'place_rating' AND meta.comment_id = comment.comment_ID ORDER BY comment.comment_ID DESC");
                    $get_comments   = $wpdb->get_results($comments_query);
                    if (!is_null($get_comments)) {
                        foreach ($get_comments as $comment) {
                            if ($comment->comment_approved == 1) {
                                if( !empty($comment->meta_value) ){
                                    $total_reviews++;
                                }
                                if( $comment->meta_value > 0 ){
                                    $total_stars += $comment->meta_value;
                                }
                            }
                        }

                        if ($total_reviews != 0) {
                            $rating = number_format($total_stars / $total_reviews, 1);
                        }
                    }

                    $primary_term               = get_primary_taxonomy_id($place_id, 'place-categories');

                    $marker_icon = '';
                    $place_categories = get_the_terms( $place_id, 'place-categories');
                    if( $place_categories ) {
                        foreach ($place_categories as $cate) {
                            $cate_id     = $cate->term_id;
                            $icon_marker = get_term_meta( $cate_id, 'place_categories_icon_marker', true );
                            if( !empty($icon_marker) ) {
                                $marker_icon = $icon_marker['url'];
                                break;
                            }
                            
                        }
                        
                        
                        if($primary_term){
                            $icon_marker = get_term_meta( $primary_term, 'place_categories_icon_marker', true );
                            if( !empty($icon_marker['url']) ) {
                                $marker_icon        = $icon_marker['url'];
                            } else {
                                $marker_icon    = GOLO_PLUGIN_URL . 'assets/images/map-marker-icon.png';
                            }

                        }
                    }

                    $html_cate = '';
                
                    if( $primary_term ){
                        $primary_terms = get_term_by('id', $primary_term, 'place-categories');
                        $cate_link = get_term_link($primary_term, 'place-categories');
                        $html_cate .= '<a href="' . $cate_link . '?city=' . $current_city . '">' . $primary_terms->name . '</a>';
                    }
                    if( $place_categories ) {
                        foreach ($place_categories as $cate) {
                            if( $cate->term_id != $primary_term ) {
                                $cate_link = get_term_link($cate, 'place-categories');
                                $html_cate .= '<a href="' . $cate_link . '?city=' . $current_city . '">' . $cate->name . '</a>';
                            }
                        }
                    }

                    $attach_id = get_post_thumbnail_id();
                    $width  = 120;
                    $height = 150;
                    if ( !empty($attach_id) ) {
                        $image_src = golo_image_resize_id($attach_id, $width, $height, true);
                    } else {
                        $default_image = golo_get_option('default_place_image','');
                        if( $default_image != '' ) {
                            $image_src = $default_image['url'];
                        }
                    }

                    if (!$image_src) {
                        $image_src = GOLO_PLUGIN_URL . 'assets/images/no-image.jpg';
                    }

                    $price = '';

                    $currency_sign = golo_get_option('currency_sign', '$');
                    $low_price     = golo_get_option('low_price', '$');
                    $medium_price  = golo_get_option('medium_price', '$$');
                    $high_price    = golo_get_option('high_price', '$$$');

                    if( $price_range && $price_range != 'none' ){

                        if( $price_range == 'free' ){
                            $price = esc_html__('Free', 'golo-framework');
                        }
                        if( $price_range == 1 ){
                            $price = $low_price;
                        }
                        if( $price_range == 2 ){
                            $price = $medium_price;
                        }
                        if( $price_range == 3 ){
                            $price = $high_price;
                        }
                    }

                    if( $price_short ){
                        $price = golo_get_format_money( $price_short );
                    }

                    if (!empty($place_location)) {
                        list($lat, $lng) = explode(',', $place_location['location']);
                    }

                    if( $place_location && $place_location['address'] )
                    {
                        $google_map_address_url = "http://maps.google.com/?q=" . $place_location['address'];
                    }
                    else
                    {
                        $google_map_address_url = "http://maps.google.com/?q=" . $place_address;
                    }

                    $prop = new stdClass();
                    $prop->image_url = $image_src;
                    $prop->id        = $place_id;
                    $prop->title     = get_the_title();
                    $prop->lat       = $lat_lng[0];
                    $prop->lng       = $lat_lng[1];
                    $prop->url       = get_permalink();
                    $prop->price     = $price;
                    $prop->rating    = $rating;
                    $prop->review    = $total_reviews;
                    $prop->city      = $city;
                    $prop->cate      = $html_cate;

                    if ($place_url == '') {
                        $place_url      = GOLO_PLUGIN_URL . 'assets/images/map-marker-icon.png';
                        $default_marker = golo_get_option('marker_icon','');
                        if( $default_marker != '' )
                        {
                            if( is_array($default_marker) && $default_marker['url'] != '' )
                            {
                                $place_url = $default_marker['url'];
                            }
                        }
                    }

                    if( $marker_icon ){
                        $prop->marker_icon = $marker_icon;
                    }else{
                        $prop->marker_icon = $place_url;
                    }
                    
                    array_push($places, $prop);

                    golo_get_template('content-place.php', array(
                        'place_item_class' => $place_item_class,
                        'custom_place_image_size' => $custom_place_image_size,
                    ));

                endwhile;
            }
            wp_reset_postdata();

            $place_html = ob_get_clean();

            if (count($places) > 0) {
                echo json_encode(array('success' => true, 'places' => $places, 'place_html' => $place_html,'total_post' => $total_post));
            } else {
                echo json_encode(array('success' => false));
            }
            wp_die();
        }

        public function golo_filter_my_place()
        {
            $item_amount      = isset($_REQUEST['item_amount']) ? golo_clean(wp_unslash($_REQUEST['item_amount'])) : '12';
            $paged            = isset($_REQUEST['paged']) ? golo_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $place_search     = isset($_REQUEST['place_search']) ? golo_clean(wp_unslash($_REQUEST['place_search'])) : '';
            $place_city       = isset($_REQUEST['place_city']) ? golo_clean(wp_unslash($_REQUEST['place_city'])) : '';
            $place_categories = isset($_REQUEST['place_categories']) ? golo_clean(wp_unslash($_REQUEST['place_categories'])) : '';
            $item_id          = isset($_REQUEST['item_id']) ? golo_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $action_click     = isset($_REQUEST['action_click']) ? golo_clean(wp_unslash($_REQUEST['action_click'])) : '';

            global $current_user;
            wp_get_current_user();
            $user_id = $current_user->ID;
            $golo_profile = new Golo_Profile();

            $meta_query = array();
            $tax_query  = array();

            $package_num_featured_listings = get_the_author_meta(GOLO_METABOX_PREFIX . 'package_number_featured', $user_id);

            $user_package_id = get_the_author_meta(GOLO_METABOX_PREFIX . 'package_id', $user_id);
            $package_unlimited_listing = get_post_meta($user_package_id, GOLO_METABOX_PREFIX . 'package_unlimited_listing', true);

            if( !empty($item_id) ){
                $place = get_post($item_id);

                if( $action_click == 'delete' ) {
                    wp_trash_post($item_id);
                    $package_number_listings = get_user_meta($user_id, GOLO_METABOX_PREFIX . 'package_number_listings', true);
                    if ($package_unlimited_listing != '1') {
                        update_user_meta($user_id, GOLO_METABOX_PREFIX . 'package_number_listings', $package_number_listings + 1);
                    }
                }

                if( $action_click == 'mark-featured' ) {
                    update_user_meta($user_id, GOLO_METABOX_PREFIX . 'package_number_featured', $package_num_featured_listings - 1);
                    update_post_meta($item_id, GOLO_METABOX_PREFIX . 'place_featured', 1);
                }

                if( $action_click == 'reactivate-place' ) {
                    $package_number_listings = get_user_meta($user_id, GOLO_METABOX_PREFIX . 'package_number_listings', true);
                    $auto_publish = golo_get_option('auto_publish', 1);
                    if ($auto_publish == 1)
                    {
                        $data = array(
                            'ID' => $item_id,
                            'post_type' => 'place',
                            'post_status' => 'publish'
                        );
                    }else{
                        $data = array(
                            'ID' => $item_id,
                            'post_type' => 'place',
                            'post_status' => 'pending'
                        );
                    }
                    wp_update_post($data);
                    update_post_meta($item_id, GOLO_METABOX_PREFIX . 'place_featured', 0);
                    update_user_meta($user_id, GOLO_METABOX_PREFIX . 'package_number_listings', $package_number_listings - 1);
                }

                if( $action_click == 'show' ) {
                    if( $place->post_status == 'hidden' ) {
                        $data = array(
                            'ID' => $item_id,
                            'post_type' => 'place',
                            'post_status' => 'publish'
                        );
                    }
                    wp_update_post($data);
                }

                if( $action_click == 'hidden' ) {
                    $data = array(
                        'ID' => $item_id,
                        'post_type' => 'place',
                        'post_status' => 'hidden'
                    );
                    wp_update_post($data);
                }
            }

            $args = array(
                'posts_per_page'      => ($item_amount > 0) ? $item_amount : -1,
                'post_type'           => 'place',
                'paged'               => $paged,
                'post_status'         => array('publish', 'expired', 'pending', 'hidden'),
                'ignore_sticky_posts' => 1,
                'orderby'             => 'date',
                'order'               => 'desc',
                'author'              => $user_id,
            );

            if ( !empty($place_search) ) {
                $args['s'] = $place_search;
            }

            //tax query place city
            if (!empty($place_city)) {
                $tax_query[] = array(
                    'taxonomy' => 'place-city',
                    'field'    => 'slug',
                    'terms'    => $place_city
                );
            }

            //tax query place categories
            if (!empty($place_categories)) {
                $tax_query[] = array(
                    'taxonomy' => 'place-categories',
                    'field'    => 'id',
                    'terms'    => $place_categories
                );
            }

            $args['meta_query'] = array(
                'relation' => 'AND',
                $meta_query
            );

            $tax_count = count($tax_query);
            if ($tax_count > 0) {
                $args['tax_query'] = array(
                    'relation' => 'AND',
                    $tax_query
                );
            }

            $data       = new WP_Query($args);
            $total_post = $data->found_posts;
            $place_html = '';
            $count_post = sprintf( _n( '(%s Result)', '(%s Results)', $total_post, 'golo-framework' ), '<span class="count">' . esc_html( $total_post ) . '</span>' );

            $max_num_pages = $data->max_num_pages;
            $pagination = paginate_links( apply_filters( 'golo_pagination_args', array(
                'total'     => $max_num_pages,
                'current'   => $paged,
                'mid_size'  => 1,
                'type'      => 'array',
                'add_args'  => array_map( 'urlencode', $args ),
                'prev_text' => __('<i class="fal fa-chevron-left"></i>', 'golo-framework'),
                'next_text' => __('<i class="fal fa-chevron-right"></i>', 'golo-framework'),
            ) ));

            ob_start();
            
            if( $total_post > 0 ){

                while ( $data->have_posts() ) : $data->the_post();

                $id = get_the_ID();
                $status = get_post_status($id);
                $place_categories = get_the_terms( $id, 'place-categories');
                $prop_featured = get_post_meta($id, GOLO_METABOX_PREFIX . 'place_featured', true);

                $place_city = get_the_terms( $id, 'place-city');
                if( $place_city ) {
                    $city_id   = $place_city[0]->term_id;
                    $city_name = $place_city[0]->name;
                    $city_slug = $place_city[0]->slug;
                }

                $default_image = golo_get_option('default_place_image', '');
                if($default_image!='')
                {
                    if(is_array($default_image) && $default_image['url'] != '')
                    {
                        $no_image_src = $default_image['url'];
                    }
                } else {
                    $no_image_src = GOLO_PLUGIN_URL . 'assets/images/no-image.jpg';
                }
                ?>
                    <tr>
                        <td class="place-id">
                            <span class="mb-intro"><?php esc_html_e('ID', 'golo-framework'); ?></span>
                            <span><?php echo esc_html($id); ?></span> 
                        </td>
                        <td class="place-thumb">
                            <span class="mb-intro"><?php esc_html_e('Thumbnail:', 'golo-framework'); ?></span>
                            <a href="<?php echo get_the_permalink($id); ?>">
                                <?php if( has_post_thumbnail($id) ) { ?>
                                    <?php echo get_the_post_thumbnail($id, 'thumbnail'); ?>
                                <?php }else{ ?>
                                    <img src="<?php echo esc_url($no_image_src); ?>" alt="<?php the_title_attribute(); ?>">
                                <?php } ?>
                            </a>
                        </td>
                        <td class="place-name">
                            <span class="mb-intro"><?php esc_html_e('Name:', 'golo-framework'); ?></span>
                            <h3 class="place-title">
                                <a href="<?php echo get_the_permalink($id); ?>">
                                    <?php echo get_the_title($id); ?>
                                </a>
                            </h3>
                        </td>
                        <td class="place-city">
                            <span class="mb-intro"><?php esc_html_e('City:', 'golo-framework'); ?></span>
                            <div>
                                <?php if( $place_city ) : ?>
                                <a href="<?php echo get_term_link( $city_slug, 'place-city'); ?>"><?php echo esc_html($city_name); ?></a>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="place-categories list-item">
                            <span class="mb-intro"><?php esc_html_e('Categories:', 'golo-framework'); ?></span>
                            <div>
                                <?php 
                                if( $place_categories ) :
                                    foreach ($place_categories as $cate) {
                                        $cate_link = get_term_link($cate, 'place-categories');
                                        ?>
                                            <a href="<?php echo esc_url($cate_link); ?>?city=<?php echo esc_attr($city_slug); ?>"><?php echo esc_html($cate->name); ?></a>
                                        <?php
                                    }
                                endif;
                                ?>
                            </div>
                        </td>
                        <td class="place-featured">
                            <?php if( $prop_featured ) { ?>
                                <span class="has-featured"><i class="las la-star icon-large"></i></span>
                            <?php }else{ ?>
                                <span><i class="lar la-star icon-large"></i></span>
                            <?php } ?>
                        </td>
                        <td class="place-status status <?php echo esc_attr($status); ?>">
                            <?php 
                            $current_status = $status;
                            if( $current_status == 'publish' ) {
                                $current_status = esc_html__('Approved', 'golo-framework');
                            }

                            if( $current_status == 'pending' ) {
                                $current_status = esc_html__('Pending', 'golo-framework');
                            }
                            ?>
                            <div><?php echo esc_html($current_status); ?></div>
                        </td>
                        <td class="place-control">
                            <?php 
                            $my_place_link = golo_get_permalink('my_places');
                            $prop_featured = get_post_meta($id, GOLO_METABOX_PREFIX . 'place_featured', true);
                            $payment_status = get_post_meta($id, GOLO_METABOX_PREFIX . 'payment_status', true);
                            $paid_submission_type = golo_get_option('paid_submission_type','no');
                            switch ($status) { 

                                case 'publish' :
                                    if ($paid_submission_type == 'per_package') {
                                        $current_package_key = get_the_author_meta(GOLO_METABOX_PREFIX . 'package_key', $user_id);
                                        $place_package_key = get_post_meta($id, GOLO_METABOX_PREFIX . 'package_key', true);

                                        $check_package = $golo_profile->user_package_available($user_id);
                                        if($check_package != -1 && $check_package != 0)
                                        {
                                            ?>
                                                <a class="btn-edit hint--top" href="<?php echo esc_url($my_place_link); ?>?place_id=<?php echo esc_attr($id); ?>" aria-label="<?php esc_attr_e( 'Edit', 'golo-framework' ); ?>"><i class="la la-edit large"></i></a>
                                            <?php
                                        }
                                        $package_num_featured_listings = get_the_author_meta(GOLO_METABOX_PREFIX . 'package_number_featured', $user_id);
                                        if ($package_num_featured_listings > 0 && ($prop_featured != 1) && ($check_package != -1)  && ($check_package != 0)) {
                                            ?>
                                                <a class="btn-mark-featured hint--top" place-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>" aria-label="<?php esc_attr_e( 'Make Featured', 'golo-framework' ); ?>"><i class="la la-star-o large"></i></a>
                                            <?php
                                        }
                                        if($check_package != -1 && $check_package != 0)
                                        {
                                            ?>
                                                <a class="btn-hide hint--top" place-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>" aria-label="<?php esc_attr_e( 'Hide', 'golo-framework' ); ?>"><i class="la la-eye-slash large"></i></a>
                                            <?php
                                        }
                                    }else{
                                        if ($prop_featured != 1) {
                                            ?>
                                                <a class="btn-mark-featured hint--top" place-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>" aria-label="<?php esc_attr_e( 'Make Featured', 'golo-framework' ); ?>"><i class="la la-star large"></i></a>
                                            <?php
                                        }
                                        ?>
                                            <a class="btn-edit hint--top" href="<?php echo esc_url($my_place_link); ?>?place_id=<?php echo esc_attr($id); ?>" aria-label="<?php esc_attr_e( 'Edit', 'golo-framework' ); ?>"><i class="la la-edit large"></i></a>
                                            
                                            <a class="btn-hide hint--top" place-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>" aria-label="<?php esc_attr_e( 'Hide', 'golo-framework' ); ?>"><i class="la la-eye-slash large"></i></a>
                                        <?php
                                    }

                                    break;
                                case 'expired' :
                                    if ($paid_submission_type == 'per_package') {
                                        $check_package = $golo_profile->user_package_available($user_id);
                                        if($check_package == 1)
                                        {

                                            ?>
                                                <a class="btn-reactivate-place hint--top" place-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>" aria-label="<?php esc_attr_e( 'Reactivate Place', 'golo-framework' ); ?>"><i class="la la-sync large"></i></a>
                                            <?php
                                        }
                                    }else{
                                        ?>
                                            <a class="btn-reactivate-place hint--top" place-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>" aria-label="<?php esc_attr_e( 'Reactivate Place', 'golo-framework' ); ?>"><i class="la la-sync large"></i></a>
                                        <?php
                                    }
                                    break;
                                case 'pending' :
                                    ?>
                                        <a class="btn-edit hint--top" href="<?php echo esc_url($my_place_link); ?>?place_id=<?php echo esc_attr($id); ?>" aria-label="<?php esc_attr_e( 'Edit', 'golo-framework' ); ?>"><i class="la la-edit large"></i></a>
                                    <?php
                                    break;
                                case 'hidden' :
                                    ?>
                                        <a class="btn-show hint--top" place-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>" aria-label="<?php esc_attr_e( 'Show', 'golo-framework' ); ?>"><i class="la la-eye large"></i></a>
                                    <?php
                                    break;

                            } 
                            ?>
                            <a class="btn-delete hint--top" place-id="<?php echo esc_attr($id); ?>" href="#" aria-label="<?php esc_attr_e( 'Delete', 'golo-framework' ); ?>"><i class="la la-trash-alt large"></i></a>
                        </td>
                    </tr>
                    <?php

                endwhile;
            }
            wp_reset_postdata();

            $place_html = ob_get_clean();
            
            if ($total_post > 0) {
                echo json_encode(array('success' => true, 'pagination' => $pagination, 'place_html' => $place_html, 'pages' => $pages, 'total_post' => $total_post));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }

        /**
         * Update profile
         */
        public function golo_update_profile_ajax()
        {
            global $current_user;
            wp_get_current_user();
            $user_id = $current_user->ID;
            check_ajax_referer('golo_update_profile_ajax_nonce', 'golo_security_update_profile');

            $user_firstname = $user_lastname = $user_email = $author_mobile_number = $author_fax_number = $user_facebook_url = $user_twitter_url =  $user_linkedin_url = $user_pinterest_url = $user_instagram_url = $user_skype = $user_youtube_url = $user_vimeo_url = $user_website_url = '';
            $profile_pic_id = '';

            // Update first name
            if (!empty($_POST['user_firstname'])) {
                $user_firstname = sanitize_text_field(wp_unslash($_POST['user_firstname']));
                update_user_meta($user_id, 'first_name', $user_firstname);
            } else {
                delete_user_meta($user_id, 'first_name');
            }

            // Update last name
            if (!empty($_POST['user_lastname'])) {
                $user_lastname = sanitize_text_field(wp_unslash($_POST['user_lastname']));
                update_user_meta($user_id, 'last_name', $user_lastname);
            } else {
                delete_user_meta($user_id, 'last_name');
            }

            // Update author_fax_number
            if (!empty($_POST['author_fax_number'])) {
                $author_fax_number = sanitize_text_field(wp_unslash($_POST['author_fax_number']));
                update_user_meta($user_id, GOLO_METABOX_PREFIX . 'author_fax_number', $author_fax_number);
            } else {
                delete_user_meta($user_id, GOLO_METABOX_PREFIX . 'author_fax_number');
            }

            // Update Mobile
            if (!empty($_POST['author_mobile_number'])) {
                $author_mobile_number = sanitize_text_field(wp_unslash($_POST['author_mobile_number']));
                if ( 0 < strlen( trim( preg_replace( '/[\s\#0-9_\-\+\/\(\)\.]/', '', $author_mobile_number ) ) ) ) {
                    echo json_encode(array('success' => false, 'message' => esc_html__('The Mobile phone number you entered is not valid. Please try again.', 'golo-framework')));
                    wp_die();
                }
                update_user_meta($user_id, GOLO_METABOX_PREFIX . 'author_mobile_number', $author_mobile_number);
            } else {
                delete_user_meta($user_id, GOLO_METABOX_PREFIX . 'author_mobile_number');
            }

            // Update Skype
            if (!empty($_POST['user_skype'])) {
                $user_skype = sanitize_text_field(wp_unslash($_POST['user_skype']));
                update_user_meta($user_id, GOLO_METABOX_PREFIX . 'author_skype', $user_skype);
            } else {
                delete_user_meta($user_id, GOLO_METABOX_PREFIX . 'author_skype');
            }

            // Update Description
            if (!empty($_POST['user_description'])) {
                $user_description = sanitize_text_field(wp_unslash($_POST['user_description']));
                update_user_meta($user_id, 'description', $user_description);
            } else {
                delete_user_meta($user_id, 'description');
            }

            // Update facebook
            if (!empty($_POST['user_facebook_url'])) {
                $user_facebook_url = esc_url_raw(wp_unslash($_POST['user_facebook_url']));
                update_user_meta($user_id, GOLO_METABOX_PREFIX . 'author_facebook_url', $user_facebook_url);
            } else {
                delete_user_meta($user_id, GOLO_METABOX_PREFIX . 'author_facebook_url');
            }

            // Update twitter
            if (!empty($_POST['user_twitter_url'])) {
                $user_twitter_url = esc_url_raw(wp_unslash($_POST['user_twitter_url']));
                update_user_meta($user_id, GOLO_METABOX_PREFIX . 'author_twitter_url', $user_twitter_url);
            } else {
                delete_user_meta($user_id, GOLO_METABOX_PREFIX . 'author_twitter_url');
            }

            // Update linkedin
            if (!empty($_POST['user_linkedin_url'])) {
                $user_linkedin_url = esc_url_raw(wp_unslash($_POST['user_linkedin_url']));
                update_user_meta($user_id, GOLO_METABOX_PREFIX . 'author_linkedin_url', $user_linkedin_url);
            } else {
                delete_user_meta($user_id, GOLO_METABOX_PREFIX . 'author_linkedin_url');
            }

            // Update instagram
            if (!empty($_POST['user_instagram_url'])) {
                $user_instagram_url = esc_url_raw(wp_unslash($_POST['user_instagram_url']));
                update_user_meta($user_id, GOLO_METABOX_PREFIX . 'author_instagram_url', $user_instagram_url);
            } else {
                delete_user_meta($user_id, GOLO_METABOX_PREFIX . 'author_instagram_url');
            }

            // Update pinterest
            if (!empty($_POST['user_pinterest_url'])) {
                $user_pinterest_url = esc_url_raw(wp_unslash($_POST['user_pinterest_url']));
                update_user_meta($user_id, GOLO_METABOX_PREFIX . 'author_pinterest_url', $user_pinterest_url);
            } else {
                delete_user_meta($user_id, GOLO_METABOX_PREFIX . 'author_pinterest_url');
            }

            // Update youtube
            if (!empty($_POST['user_youtube_url'])) {
                $user_youtube_url = esc_url_raw(wp_unslash($_POST['user_youtube_url']));
                update_user_meta($user_id, GOLO_METABOX_PREFIX . 'author_youtube_url', $user_youtube_url);
            } else {
                delete_user_meta($user_id, GOLO_METABOX_PREFIX . 'author_youtube_url');
            }


            // Update Profile Picture
            if (!empty($_POST['user_image_url']) && !empty($_POST['user_image_id']) ) {
                $user_image_url = sanitize_text_field($_POST['user_image_url']);
                $user_image_id = sanitize_text_field($_POST['user_image_id']);
                update_user_meta($user_id, 'author_avatar_image_url', $user_image_url);
                update_user_meta($user_id, 'author_avatar_image_id', $user_image_id);
            } else {
                delete_user_meta($user_id, 'author_avatar_image_url');
                delete_user_meta($user_id, 'author_avatar_image_id');
            }

            // Update email
            if (!empty($_POST['user_email'])) {
                $user_email = sanitize_email(wp_unslash($_POST['user_email']));
                $user_email = is_email($user_email);
                if (!$user_email) {
                    echo json_encode(array('success' => false, 'message' => esc_html__('The Email you entered is not valid. Please try again.', 'golo-framework')));
                    wp_die();
                } else {
                    $email_exists = email_exists($user_email);
                    if ($email_exists) {
                        if ($email_exists != $user_id) {
                            echo json_encode(array('success' => false, 'message' => esc_html__('This Email is already used by another user. Please try a different one.', 'golo-framework')));
                            wp_die();
                        }
                    } else {
                        $return = wp_update_user(array('ID' => $user_id, 'user_email' => $user_email));
                        if (is_wp_error($return)) {
                            $error = $return->get_error_message();
                            echo esc_html($error);
                            wp_die();
                        }
                    }
                }
            }

            update_post_meta($user_id, GOLO_METABOX_PREFIX . 'agent_description', $user_des);
            update_post_meta($user_id, GOLO_METABOX_PREFIX . 'agent_position', $user_position);
            update_post_meta($user_id, GOLO_METABOX_PREFIX . 'agent_email', $user_email);
            update_post_meta($user_id, GOLO_METABOX_PREFIX . 'agent_mobile_number', $user_mobile_number);
            update_post_meta($user_id, GOLO_METABOX_PREFIX . 'agent_fax_number', $user_fax_number);
            update_post_meta($user_id, GOLO_METABOX_PREFIX . 'agent_company', $user_company);
            update_post_meta($user_id, GOLO_METABOX_PREFIX . 'agent_office_number', $user_office_number);
            update_post_meta($user_id, GOLO_METABOX_PREFIX . 'agent_office_address', $user_office_address);
            update_post_meta($user_id, GOLO_METABOX_PREFIX . 'agent_licenses', $user_licenses);
            update_post_meta($user_id, GOLO_METABOX_PREFIX . 'agent_facebook_url', $user_facebook_url);
            update_post_meta($user_id, GOLO_METABOX_PREFIX . 'agent_twitter_url', $user_twitter_url);
            update_post_meta($user_id, GOLO_METABOX_PREFIX . 'agent_linkedin_url', $user_linkedin_url);
            update_post_meta($user_id, GOLO_METABOX_PREFIX . 'agent_pinterest_url', $user_pinterest_url);
            update_post_meta($user_id, GOLO_METABOX_PREFIX . 'agent_instagram_url', $user_instagram_url);
            update_post_meta($user_id, GOLO_METABOX_PREFIX . 'agent_skype', $user_skype);
            update_post_meta($user_id, GOLO_METABOX_PREFIX . 'agent_youtube_url', $user_youtube_url);
            update_post_meta($user_id, GOLO_METABOX_PREFIX . 'agent_vimeo_url', $user_vimeo_url);
            update_post_meta($user_id, GOLO_METABOX_PREFIX . 'agent_website_url', $user_website_url);
            
            echo json_encode(array('success' => true, 'message' => esc_html__('Profile updated', 'golo-framework')));
            wp_die();
        }

        /**
         * Change password
         */
        public function golo_change_password_ajax()
        {
            check_ajax_referer('golo_change_password_ajax_nonce', 'golo_security_change_password');
            global $current_user;
            wp_get_current_user();
            $user_id = $current_user->ID;
            $allowed_html = array();

            $oldpass = isset($_POST['oldpass']) ? golo_clean(wp_unslash($_POST['oldpass'])) : '';
            $newpass = isset($_POST['newpass']) ? golo_clean(wp_unslash($_POST['newpass'])) : '';
            $confirmpass = isset($_POST['confirmpass']) ? golo_clean(wp_slash($_POST['confirmpass'])) : '';

            if ($newpass == '' || $confirmpass == '') {
                echo json_encode(array('success' => false, 'message' => esc_html__('New password or confirm password is blank', 'golo-framework')));
                wp_die();
            }
            if ($newpass != $confirmpass) {
                echo json_encode(array('success' => false, 'message' => esc_html__('Passwords do not match', 'golo-framework')));
                wp_die();
            }

            $user = get_user_by('id', $user_id);
            if ($user && wp_check_password($oldpass, $user->data->user_pass, $user_id)) {
                wp_set_password($newpass, $user_id);
                echo json_encode(array('success' => true, 'message' => esc_html__('Password Updated', 'golo-framework')));
            } else {
                echo json_encode(array('success' => false, 'message' => esc_html__('Old password is not correct', 'golo-framework')));
            }
            wp_die();
        }

        /**
         * Favorite place
         */
        public function golo_add_to_wishlist() {
            global $current_user;
            $place_id = $_POST['place_id'];
            $place_id = intval( $place_id );
            wp_get_current_user();
            $user_id       = $current_user->ID;
            $added         = $removed = false;
            $ajax_response = '';
            if ( $user_id > 0 ) {
                $my_favorites = get_user_meta( $user_id, GOLO_METABOX_PREFIX . 'place_whishlist', true );

                if ( ! empty( $my_favorites ) && ( ! in_array( $place_id, $my_favorites ) ) ) {
                    array_push( $my_favorites, $place_id );
                    $added = true;
                } else {
                    if ( empty( $my_favorites ) ) {
                        $my_favorites = array( $place_id );
                        $added        = true;
                    } else {
                        //Delete favorite
                        $key = array_search( $place_id, $my_favorites );
                        if ( $key !== false ) {
                            unset( $my_favorites[ $key ] );
                            $removed = true;
                        }
                    }
                }

                update_user_meta( $user_id, GOLO_METABOX_PREFIX . 'place_whishlist', $my_favorites );
                if ( $added ) {
                    $ajax_response = array( 'added' => true, 'message' => esc_html__( 'Added', 'golo-framework' ) );
                }
                if ( $removed ) {
                    $ajax_response = array( 'added' => false, 'message' => esc_html__( 'Removed', 'golo-framework' ) );
                }
            } else {
                $ajax_response = array(
                    'added'   => false,
                    'message' => esc_html__( 'You are not login', 'golo-framework' )
                );
            }
            echo json_encode( $ajax_response );
            wp_die();
        }

        /**
         * Ajax Search
         */
        public function golo_search_ajax()
        {
            $key       = isset($_REQUEST['key']) ? golo_clean(wp_unslash($_REQUEST['key'])) : '';
            $post_type = isset($_REQUEST['post_type']) ? golo_clean(wp_unslash($_REQUEST['post_type'])) : '';
            $location  = isset($_REQUEST['location']) ? golo_clean(wp_unslash($_REQUEST['location'])) : '';

            $custom_place_image_size = golo_get_option('custom_place_image_size', '540x480' );
            $place_item_class        = array();
            $place_item_class[]      = 'golo-item-wrap';

            $location_check_id = get_term_by('name', $key, 'place-city');

            $args = array(
                'post_type'      => $post_type,
                'posts_per_page' => 10,
                'post_status'    => 'publish',
                's'              => $key,
                'tax_query'      => array()
            );

            if ($location_check_id) {
                $args['tax_query'][] = array(
                    'taxonomy' => 'place-city',
                    'field'    => 'id',
                    'terms'    => $location_check_id->term_id
                );
                $args['s'] = '';
            }

            if( !empty($location) ) {
                $location_id = get_terms([
                    'name__like' => $location,
                    'fields' => 'ids'
                ]);

                $args['tax_query'][] = array(
                    'taxonomy' => 'place-city',
                    'field'    => 'id',
                    'terms'    => $location_id
                );
            }

            $data       = new WP_Query($args);
            $total_post = $data->found_posts;
            $places     = array();
            $place_html = '';
            
            ob_start();
            
            if( $total_post > 0 ){
            ?>
                <ul class="custom-scrollbar">
                <?php

                    while ( $data->have_posts() ) : $data->the_post();

                    $id = get_the_ID();
                    $place_categories = get_the_terms( $id, 'place-categories');
                    $place_city       = get_the_terms( $id, 'place-city');
                    if( $place_city ) {
                        $city_id   = $place_city[0]->term_id;
                        $city_name = $place_city[0]->name;
                        $city_slug = $place_city[0]->slug;
                        $term_link = get_term_link($place_city[0]);
                    } 
                    ?>
                        <li>
                            <a class="place" href="<?php echo get_the_permalink($id); ?>"><?php echo get_the_title($id); ?></a>
                            <a class="city" href="<?php echo esc_url($term_link); ?>"><i class="la la-city"></i><?php echo esc_html($city_name); ?></a>
                        </li>
                    <?php

                    endwhile;

                ?>
                </ul>
            <?php
            }
            wp_reset_postdata();

            $place_html = ob_get_clean();
            
            if ($total_post > 0) {
                echo json_encode(array('success' => true, 'place_html' => $place_html, 'total_post' => $total_post));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }

        /**
         * Ajax Search City
         */
        public function golo_search_location_ajax()
        {
            $location = isset($_REQUEST['location']) ? golo_clean(wp_unslash($_REQUEST['location'])) : '';

            $args = array(
                'taxonomy'   => array('place-city'),
                'orderby'    => 'id', 
                'order'      => 'ASC',
                'hide_empty' => true,
                'fields'     => 'all'
            );

            if( $location ) {
                $args['search'] = $location;
                $args['fields'] = 'all';
            }

            $terms = get_terms( $args );
            $count = count($terms);
            $location_html = '';
            
            ob_start();
            
            if( $count > 0 ){
            ?>
                <ul class="custom-scrollbar">
                    <?php foreach ($terms as $term) { ?>
                    <li>
                        <a href="<?php echo get_term_link( $term ); ?>"><?php echo esc_html($term->name); ?></a>
                    </li>
                    <?php } ?>
                </ul>
            <?php
            }

            $location_html = ob_get_clean();
            
            if ($count > 0) {
                echo json_encode(array('success' => true, 'location_html' => $location_html, 'total_post' => $count));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $count));
            }
            wp_die();
        }

        /**
         * Ajax Search
         */
        public function golo_booking_form_ajax()
        {
            $place_title     = isset($_REQUEST['place_title']) ? golo_clean(wp_unslash($_REQUEST['place_title'])) : '';
            $place_id        = isset($_REQUEST['place_id']) ? golo_clean(wp_unslash($_REQUEST['place_id'])) : '';
            $place_author_id = isset($_REQUEST['place_author_id']) ? golo_clean(wp_unslash($_REQUEST['place_author_id'])) : '';
            $adults          = isset($_REQUEST['adults']) ? golo_clean(wp_unslash($_REQUEST['adults'])) : '';
            $childrens       = isset($_REQUEST['childrens']) ? golo_clean(wp_unslash($_REQUEST['childrens'])) : '';
            $booking_date    = isset($_REQUEST['booking_date']) ? golo_clean(wp_unslash($_REQUEST['booking_date'])) : '';
            $booking_time    = isset($_REQUEST['booking_time']) ? golo_clean(wp_unslash($_REQUEST['booking_time'])) : '';
            $place_coupon    = isset($_REQUEST['place_coupon']) ? golo_clean(wp_unslash($_REQUEST['place_coupon'])) : '';

            $new_booking = array();
            $new_booking['post_type']   = 'booking';
            $new_booking['post_status'] = 'pending';
            global $current_user;
            wp_get_current_user();
            $user_id       = $current_user->ID;
            $user_nicename = $current_user->display_name;
            $user_email    = $current_user->user_email;
            $new_booking['post_author'] = $user_id;

            $booking_title = $user_nicename . ' booked at ' . '"'. $place_title .'" - ' .$user_email;

            if ( isset($booking_title) ) {
                $new_booking['post_title'] = $booking_title;
            }

            $booking_id = 0;
            if( !empty($new_booking['post_title']) && ( !empty($adults) || !empty($childrens) || !empty($booking_date) || !empty($booking_time) ) ) {
                $booking_id = wp_insert_post($new_booking, true);

                $args = array(
                    'booking_title' => get_the_title($place_id),
                    'booking_url' => get_permalink($place_id)
                );
                golo_send_email($user_email, 'mail_confirm_booking', $args);

                // Send Mail to Owner
                $owner = get_user_by('id', $place_author_id);
                $owner_email = $owner->user_email;
                
                $args = array(
                    'booking_title' => get_the_title($place_id),
                    'booking_url' => get_permalink($place_id)
                );
                golo_send_email($owner_email, 'mail_owner_new_booking', $args);
                
                echo json_encode(array('success' => true));
            }else{
                echo json_encode(array('success' => false));
            }

            if( $booking_id > 0 ) {


                if (isset($place_title)) {
                    update_post_meta($booking_id, GOLO_METABOX_PREFIX . 'booking_item_name', $place_title);
                }
                if (isset($place_id)) {
                    update_post_meta($booking_id, GOLO_METABOX_PREFIX . 'booking_item_id', $place_id);
                }
                if (isset($place_author_id)) {
                    update_post_meta($booking_id, GOLO_METABOX_PREFIX . 'booking_item_author', $place_author_id);
                }
                if (isset($adults)) {
                    update_post_meta($booking_id, GOLO_METABOX_PREFIX . 'booking_adults', $adults);
                }
                if (isset($childrens)) {
                    update_post_meta($booking_id, GOLO_METABOX_PREFIX . 'booking_childrens', $childrens);
                }
                if (isset($booking_date)) {
                    update_post_meta($booking_id, GOLO_METABOX_PREFIX . 'booking_date', $booking_date);
                }
                if (isset($booking_time)) {
                    update_post_meta($booking_id, GOLO_METABOX_PREFIX . 'booking_time', $booking_time);
                }
                if (isset($place_coupon)) {
                    update_post_meta($booking_id, GOLO_METABOX_PREFIX . 'place_coupon', $place_coupon);
                }
                $time = time();
                $date = date('Y-m-d H:i:s', $time);
                update_post_meta($booking_id, GOLO_METABOX_PREFIX . 'booking_activate_date', $date);
            }

            wp_die();
        }

        /**
         * Ajax Search
         */
        public function golo_claim_form_ajax()
        {
            $your_name              = isset($_REQUEST['your_name']) ? golo_clean(wp_unslash($_REQUEST['your_name'])) : '';
            $your_email             = isset($_REQUEST['your_email']) ? golo_clean(wp_unslash($_REQUEST['your_email'])) : '';
            $your_username          = isset($_REQUEST['your_username']) ? golo_clean(wp_unslash($_REQUEST['your_username'])) : '';
            $your_listing           = isset($_REQUEST['your_listing']) ? golo_clean(wp_unslash($_REQUEST['your_listing'])) : '';
            $place_id               = isset($_REQUEST['place_id']) ? golo_clean(wp_unslash($_REQUEST['place_id'])) : '';
            $messager               = isset($_REQUEST['messager']) ? golo_clean(wp_unslash($_REQUEST['messager'])) : '';

            if (!empty($your_name) && !empty($your_email) && !empty($your_username) && !empty($your_listing)) {
                $args = array(
                    'your_name' => $your_name,
                    'your_email' => $your_email,
                    'your_listing' => $your_listing,
                    'your_username' => $your_username,
                    'messager' => $messager,
                    'status' => 'pending',
                );

                $email_type = 'mail_claim_listing';

                $message = golo_get_option($email_type, '');
                $subject = golo_get_option('subject_' . $email_type, '');

                if (function_exists('icl_translate')) {
                    $message = icl_translate('golo-framework', 'golo_email_' . $message, $message);
                    $subject = icl_translate('golo-framework', 'golo_email_subject_' . $subject, $subject);
                }

                $message = wpautop( $message );

                foreach ($args as $key => $val) {
                    $subject = str_replace('%' . $key, $val, $subject);
                    $message = str_replace('%' . $key, $val, $message);
                }
                $admin_email = get_option( 'admin_email' );
                $headers = 'Content-type: text/html; charset=utf-8' . "\r\n";
                $headers = 'From: ' . $your_name . ' <' . $your_email . '>' . "\r\n";

                // Send to user
                wp_mail($your_email,
                    $subject,
                    $message,
                    $headers
                );

                $adminsubject = 'New Claim Listing from user with email ' . $your_email;

                // Send to admin
                wp_mail($admin_email,
                    $adminsubject,
                    $message,
                    $headers
                );

                update_post_meta($place_id, GOLO_METABOX_PREFIX . 'claim_request', 1);
                update_post_meta($place_id, GOLO_METABOX_PREFIX . 'cd_your_name', $your_name);
                update_post_meta($place_id, GOLO_METABOX_PREFIX . 'cd_your_email', $your_email);
                update_post_meta($place_id, GOLO_METABOX_PREFIX . 'cd_your_listing', $your_listing);
                update_post_meta($place_id, GOLO_METABOX_PREFIX . 'cd_your_username', $your_username);
                update_post_meta($place_id, GOLO_METABOX_PREFIX . 'cd_messager', $messager);
                update_post_meta($place_id, GOLO_METABOX_PREFIX . 'cd_status', 'pending');

                echo json_encode(array('success' => true, 'success_text' => esc_html__( 'You have successfully submitted the request!', 'golo-framework'), 'error_text' => esc_html( 'You have submitted a failed request!', 'golo-framework')));
            } else {
                echo json_encode(array('success' => false, 'success_text' => esc_html__( 'You have successfully submitted the request!', 'golo-framework'), 'error_text' => esc_html( 'You have submitted a failed request!', 'golo-framework')));
            }
            wp_die();
        }

        /**
         * Ajax Search
         */
        public function golo_load_unseen_notification_ajax()
        {   
            global $current_user;
            wp_get_current_user();
            $user_id = $current_user->ID;
            $noti_type = $noti_html = '';

            global $wpdb;

            // Check new review
            $comments_query = "SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE meta.meta_key = 'place_rating' AND meta.comment_id = comment.comment_ID AND ( comment.comment_approved = 1 OR comment.user_id = $user_id )";
            $get_comments   = $wpdb->get_results($comments_query);
            $comment_id_author = array();
            if (!is_null($get_comments)) {
                foreach ($get_comments as $comment) {
                    $comment_id     = $comment->comment_ID;
                    $post_id        = $comment->comment_post_ID;
                    $post_author_id = get_post_field( 'post_author', $post_id );

                    if( $post_author_id == $user_id ) {
                        $comment_id_author[] = $comment_id;
                    }
                }
            }

            $user_list_comment_id = get_user_meta($user_id, 'user_list_comment_id', true);

            $new_comment_id = array_diff($comment_id_author, $user_list_comment_id);

            update_user_meta( $user_id, 'user_list_comment_id', $comment_id_author );

            // Check new booking
            $meta_query = array();
            $booking_id_author = array();

            $args_booking = array(
                'post_type'      => 'booking',
                'post_status'    => array('publish', 'pending'),
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key'     => GOLO_METABOX_PREFIX. 'booking_item_author',
                        'value'   => $user_id,
                        'type'    => 'NUMERIC',
                        'compare' => '=',
                    )
                )
            );

            $bookings = get_posts($args_booking);

            if ( $bookings ) {
                foreach ( $bookings as $booking ) :

                    $booking_id_author[] = $booking->ID;

                endforeach; 
                wp_reset_postdata();
            }

            $user_list_booking_id = get_user_meta($user_id, 'user_list_booking_id', true);

            $new_booking_id = array_diff($booking_id_author, $user_list_booking_id);

            update_user_meta( $user_id, 'user_list_booking_id', $booking_id_author );

            ob_start();
            ?>

                <?php 
                if( !empty($new_comment_id) ) { 
                    foreach ($new_comment_id as $comment_id) {
                        $noti_comment = get_comment($comment_id);
                        $post_id = $noti_comment->comment_post_ID;
                        $author  = $noti_comment->comment_author;
                ?>
                    <li>
                        <div class="entry-detail">
                            <?php echo sprintf( esc_html__( '%1$s reviewed at %2$s', 'golo-framework' ), '<i>' . $author . '</i>', '<a class="entry-title" href="'. get_the_permalink($post_id) .'">"' . get_the_title($post_id) . '"</a>' ); ?>    

                            <div class="info">
                                <span><?php esc_html_e('1d ago', 'golo-framework'); ?></span>
                                <a href="#" class="btn-delete error-color"><?php esc_html_e('Delete', 'golo-framework'); ?></a>
                            </div>
                        </div>   
                    </li>   
                <?php } } ?>

                <?php 
                if( !empty($new_booking_id) ) { 
                    foreach ($new_booking_id as $booking_id) {
                        $item_id = get_post_meta($booking_id, GOLO_METABOX_PREFIX . 'booking_item_id', true);
                        $author_id = get_post_meta($booking_id, GOLO_METABOX_PREFIX . 'booking_item_author', true);
                        $author_name = get_the_author_meta( 'nicename', $author_id )
                ?>
                    <li>
                        <div class="entry-detail">
                            <?php echo sprintf( esc_html__( '%1$s booked at %2$s', 'golo-framework' ), '<i>' . $author_name . '</i>', '<a class="entry-title" href="'. get_the_permalink($item_id) .'">"' . get_the_title($item_id) . '"</a>' ); ?>   
                            <div class="info">
                                <span><?php esc_html_e('1d ago', 'golo-framework'); ?></span>
                                <a href="#" class="btn-delete error-color"><?php esc_html_e('Delete', 'golo-framework'); ?></a>
                            </div>
                        </div>   
                    </li>   
                <?php } } ?>

            <?php

            $noti_html = ob_get_clean();

            echo json_encode(array('success' => true, 'noti_html' => $noti_html ));

            wp_die();
        }
    }

}