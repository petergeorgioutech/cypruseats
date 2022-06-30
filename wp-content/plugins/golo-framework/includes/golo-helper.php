<?php 
/**
 * Get Option
 */
if (!function_exists('golo_get_option')) {
    function golo_get_option($key, $default = '')
    {
        $option = get_option(GOLO_OPTIONS_NAME);
        return (isset($option[$key])) ? $option[$key] : $default;
    }
}

/**
 * Check nonce
 *
 * @param string $action Action name.
 * @param string $nonce Nonce.
 */
if (!function_exists('verify_nonce')) {
    function verify_nonce( $action = '', $nonce = '' ) {

        if ( ! $nonce && isset( $_REQUEST['_wpnonce'] ) ) {
            $nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) );
        }

        return wp_verify_nonce( $nonce, $action );
    }
}

/**
 * Check theme support
 */
if (!function_exists('is_theme_support')) {
    function is_theme_support() {
        return current_theme_supports( 'golo' );
    }
}

/**
 * Check has shortcode
 */
if (!function_exists('golo_page_shortcode')) {
    function golo_page_shortcode( $shortcode = NULL ) {

        $post = get_post(get_the_ID());

        $found = false;

        if( empty($post->post_content) ) {
            return $found;
        }

        if ( $post->post_content === $shortcode ) {
            $found = true;
        }

        // return our final results
        return $found;
    }
}

/**
 * Insert custom header script.
 *
 * @return void
 */
function golo_custom_header_js() {
    if ( golo_get_option( 'header_script', '' ) && ! is_admin() ) {
        echo golo_get_option( 'header_script', '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}
add_action( 'wp_head', 'golo_custom_header_js', 99 );

/**
 * Insert custom footer script.
 *
 * @return void
 */
function golo_footer_scripts(){
  echo do_shortcode( golo_get_option( 'footer_script', '' ) );
}
add_action('wp_footer', 'golo_footer_scripts');

/**
 * Convert text to 1 line
 *
 * @param $str
 *
 * @return string
 */
if (!function_exists('text2line')) {
    function text2line( $str ) {
        return trim( preg_replace( "/[\r\v\n\t]*/", '', $str ) );
    }
}

/**
 * Get template part (for templates like the shop-loop).
 *
 * @param mixed $slug
 * @param string $name (default: '')
 */
if (!function_exists('golo_get_template_part')) {
    function golo_get_template_part($slug, $name = '')
    {
        $template = '';
        if ($name) {
            $template = locate_template(array("{$slug}-{$name}.php", GOLO()->template_path() . "{$slug}-{$name}.php"));
        }

        // Get default slug-name.php
        if ( !$template && $name && file_exists( GOLO_PLUGIN_DIR . "templates/{$slug}-{$name}.php") ) {
            $template = GOLO_PLUGIN_DIR . "templates/{$slug}-{$name}.php";
        }

        if (!$template) {
            $template = locate_template(array("{$slug}.php", GOLO()->template_path() . "{$slug}.php"));
        }

        // Allow 3rd party plugins to filter template file from their plugin.
        $template = apply_filters('golo_get_template_part', $template, $slug, $name);

        if ($template) {
            load_template($template, false);
        }
    }
}

/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 */
if (!function_exists('golo_get_template')) {
    function golo_get_template($template_name, $args = array(), $template_path = '', $default_path = '')
    {
        if (!empty($args) && is_array($args)) {
            extract($args);
        }
        
        $located = golo_locate_template($template_name, $template_path, $default_path);

        if (!file_exists($located)) {
            _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $located), '2.1');
            return;
        }

        // Allow 3rd party plugin filter template file from their plugin.
        $located = apply_filters('golo_get_template', $located, $template_name, $args, $template_path, $default_path);

        do_action('golo_before_template_part', $template_name, $template_path, $located, $args);

        include($located);

        do_action('golo_after_template_part', $template_name, $template_path, $located, $args);
    }
}

/**
 * Like golo_get_template, but returns the HTML instead of outputting.
 */
if (!function_exists('golo_get_template_html')) {
    function golo_get_template_html($template_name, $args = array(), $template_path = '', $default_path = '')
    {
        ob_start();
        golo_get_template($template_name, $args, $template_path, $default_path);
        return ob_get_clean();
    }
}

/**
 * Status Open/Closed Place
 */
if (!function_exists('golo_status_time_place')) {
    function golo_status_time_place($storeSchedule)
    {
        $class = 'close';

        // default status
        $status = sprintf( esc_html__( '%s Closed', 'golo-framework' ), '<i class="las la-door-closed icon-large"></i>' );

        $place_timezone = get_post_meta(get_the_ID(), GOLO_METABOX_PREFIX . 'place_timezone', true);
        
        $tzstring = convert_place_timezone($place_timezone);
        
        $enable_time_format_24 = golo_get_option('enable_time_format_24', 0);        

        // get current time object of the place
        $currentTime = new DateTime("now", new DateTimeZone($tzstring) );
        $timestamp = strtotime($currentTime->format("Y-m-d H:i:s"));

        $current_day = date('D', $timestamp);

        $arr = $storeSchedule[$current_day];

        if( !$arr ) {
            return;
        }

        if (strpos($arr, '&') != false) {
            $arr = str_replace( '&', '-', $arr );
        }

        $arr = explode('-', $arr);
        
        if( !empty($arr[0]) && !empty($arr[1]) ) {
            $arr[0] = trim($arr[0]);
            $arr[1] = trim($arr[1]);

            if ($enable_time_format_24 == 1) {
                $startTime = DateTime::createFromFormat('H:i', $arr[0], new DateTimeZone($tzstring));
                $endTime   = DateTime::createFromFormat('H:i', $arr[1], new DateTimeZone($tzstring));
            } else {
                $startTime = DateTime::createFromFormat('h:i A', $arr[0], new DateTimeZone($tzstring));
                $endTime   = DateTime::createFromFormat('h:i A', $arr[1], new DateTimeZone($tzstring));
            }

            if ($endTime < $currentTime) {
                if( !empty($arr[2]) && !empty($arr[3]) ) {
                    $arr[2] = trim($arr[2]);
                    $arr[3] = trim($arr[3]);

                    if ($enable_time_format_24 == 1) {
                        $startTime = DateTime::createFromFormat('H:i', $arr[2], new DateTimeZone($tzstring));
                        $endTime   = DateTime::createFromFormat('H:i', $arr[3], new DateTimeZone($tzstring));
                    } else {
                        $startTime = DateTime::createFromFormat('h:i A', $arr[2], new DateTimeZone($tzstring));
                        $endTime   = DateTime::createFromFormat('h:i A', $arr[3], new DateTimeZone($tzstring));
                    }

                    // check if current time is within a range
                    if ( ( $startTime < $endTime ) && ($startTime < $currentTime) && ($currentTime < $endTime) ) {
                        $class  = 'open';
                        $status = sprintf( esc_html__( '%s Open now', 'golo-framework' ), '<i class="las la-door-open icon-large"></i>' );
                    } else if ( ( $startTime > $endTime ) && (($startTime < $currentTime) || ($currentTime < $endTime)) ) {
                        $class  = 'open';
                        $status = sprintf( esc_html__( '%s Open now', 'golo-framework' ), '<i class="las la-door-open icon-large"></i>' );
                    } else {
                        $class  = 'close';
                        $status = sprintf( esc_html__( '%s Closed', 'golo-framework' ), '<i class="las la-door-closed icon-large"></i>' );
                    }
                } else if ( ( $startTime > $endTime ) && (($startTime < $currentTime) || ($currentTime < $endTime)) ) {
                    $class  = 'open';
                    $status = sprintf( esc_html__( '%s Open now', 'golo-framework' ), '<i class="las la-door-open icon-large"></i>' );
                } else {
                    $class  = 'close';
                    $status = sprintf( esc_html__( '%s Closed', 'golo-framework' ), '<i class="las la-door-closed icon-large"></i>' );
                }
            } else {
                if ( ( $startTime < $endTime ) && ($startTime < $currentTime) && ($currentTime < $endTime) ) {
                    $class  = 'open';
                    $status = sprintf( esc_html__( '%s Open now', 'golo-framework' ), '<i class="las la-door-open icon-large"></i>' );
                } else if ( ( $startTime > $endTime ) && (($startTime < $currentTime) || ($currentTime < $endTime)) ) {
                    $class  = 'open';
                    $status = sprintf( esc_html__( '%s Open now', 'golo-framework' ), '<i class="las la-door-open icon-large"></i>' );
                } else {
                    $class  = 'close';
                    $status = sprintf( esc_html__( '%s Closed', 'golo-framework' ), '<i class="las la-door-closed icon-large"></i>' );
                }
            }

        } else {
            $class  = 'close';
            $status = sprintf( esc_html__( '%s Closed', 'golo-framework' ), '<i class="las la-door-closed icon-large"></i>' );
        }

        return '<div class="place-status '. $class .'">' . $status . '</div>';
    }
}

/**
 * Send email
 */
if (!function_exists('golo_send_email')) {
    function golo_send_email($email, $email_type, $args = array())
    {
        $message = golo_get_option($email_type, '');
        $subject = golo_get_option('subject_' . $email_type, '');

        if (function_exists('icl_translate')) {
            $message = icl_translate('golo-framework', 'golo_email_' . $message, $message);
            $subject = icl_translate('golo-framework', 'golo_email_subject_' . $subject, $subject);
        }
        $message = wpautop( $message );
        $args['website_url'] = get_option('siteurl');
        $args['website_name'] = get_option('blogname');
        $args['user_email'] = $email;
        $user = get_user_by('email', $email);
        $args['username'] = $user->user_login;

        foreach ($args as $key => $val) {
            $subject = str_replace('%' . $key, $val, $subject);
            $message = str_replace('%' . $key, $val, $message);
        }
        $headers = apply_filters( 'golo_contact_mail_header', array('Content-Type: text/html; charset=UTF-8'));

        @wp_mail(
            $email,
            $subject,
            $message,
            $headers
        );
    }
}

/**
 * Get posts by user id
 */
if (!function_exists('get_posts_by_user')) {
    function get_posts_by_user($user_id, $post_type = 'post', $number = 6, $item = 4, $item_lg = 4, $item_md = 3, $item_sm = 2, $item_xs = 2)
    {
        $custom_place_image_size = golo_get_option('custom_place_image_size', '540x480' );
        $archive_place_items_amount = golo_get_option('archive_place_items_amount', '12');
        $archive_class    = array();
        $archive_class[]  = 'grid';
        $archive_class[] = 'columns-'. $item;
        $archive_class[] = 'columns-lg-'. $item_lg;
        $archive_class[] = 'columns-md-'. $item_md;
        $archive_class[] = 'columns-sm-'. $item_sm;
        $archive_class[] = 'columns-xs-'. $item_xs;

        $args = array(
            'post_type' => $post_type,
            'posts_per_page' => $number,
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'author' => $user_id,
            'orderby' => array(
                'menu_order' => 'ASC',
                'date'       => 'DESC',
            ),
        );
        $posts = new WP_Query($args);
        $total_post = $posts->found_posts;
        $post_html = '';

        ob_start();
            
        if( $total_post > 0 ){
        ?>
            <div class="area-places <?php echo join(' ', $archive_class); ?>" data-item-amount='<?php echo esc_attr($number); ?>'>

            <?php while ( $posts->have_posts() ) : $posts->the_post(); ?>

                <?php golo_get_template('content-place.php', array(
                    'custom_place_image_size' => $custom_place_image_size
                )); ?>

            <?php endwhile; ?>

            </div>

        <?php } else { ?>

            <div class="item-not-found"><?php esc_html_e('No item found', 'golo-framework'); ?></div>

        <?php }

        $max_num_pages = $posts->max_num_pages;
        golo_get_template('global/pagination.php', array('max_num_pages' => $max_num_pages, 'type' => 'ajax-call'));

        wp_reset_postdata();

        $post_html = ob_get_clean();

        return $post_html;
    }
}

/**
 * Get total posts by user id
 */
if (!function_exists('get_total_posts_by_user')) {
    function get_total_posts_by_user($user_id, $post_type = 'post')
    {
        $args = array(
            'post_type' => $post_type,
            'post_status' => 'publish',
            'author' => $user_id,
        );
        $posts = new WP_Query($args);
        wp_reset_postdata();
        return $posts->found_posts;
    }
}

/**
 * Get total reviews by user id
 */
if (!function_exists('get_total_reviews_by_user')) {
    function get_total_reviews_by_user($user_id)
    {
        global $wpdb;
        $comments_query = "SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE meta.meta_key = 'place_rating' AND meta.comment_id = comment.comment_ID AND ( comment.comment_approved = 1 OR comment.user_id = $user_id )";
        $get_comments   = $wpdb->get_results($comments_query);

        $author_obj = get_user_by('id', $user_id);

        $comment_author = array();
        if (!is_null($get_comments)) {
            foreach ($get_comments as $comment) {
                $comment_id     = $comment->comment_ID;
                $post_id        = $comment->comment_post_ID;
                $comment_name   = $comment->comment_author;
                $status         = get_post_status($post_id);
                $post_author_id = get_post_field( 'post_author', $post_id );

                if( $author_obj->user_login == $comment_name && $status == 'publish' ) {
                    $comment_author[] = $comment_id;
                }
            }
        }
        $total_post = count($comment_author);

        return $total_post;
    }
}

/**
 * Get page id
 */
if (!function_exists('golo_get_page_id')) {
    function golo_get_page_id($page)
    {
        $page_id = golo_get_option('golo_' . $page . '_page_id');
        if ($page_id) {
            return absint(function_exists('pll_get_post') ? pll_get_post($page_id) : $page_id);
        } else {
            return 0;
        }
    }
}

/**
 * Get permalink
 */
if (!function_exists('golo_get_permalink')) {
    function golo_get_permalink($page)
    {
        if ($page_id = golo_get_page_id($page)) {
            return get_permalink($page_id);
        } else {
            return false;
        }
    }
}

/**
 * allow submit
 */
if (!function_exists('golo_allow_submit')){
    function golo_allow_submit(){
        $enable_submit_place_via_frontend = golo_get_option('enable_submit_place_via_frontend', 1);
        $user_can_submit = golo_get_option('user_can_submit', 1);

        $allow_submit = true;
        if($enable_submit_place_via_frontend != 1)
        {
            $allow_submit = false;
        }
        else{
            if( $user_can_submit != 1)
            {
                $allow_submit = false;
            }
        }
        return $allow_submit;
    }
}

if (!function_exists('golo_total_actived_places')){
    function golo_total_actived_places(){

        global $current_user;
        wp_get_current_user();
        $user_id = $current_user->ID;

        $args = array(
            'post_type'           => 'place',
            'post_status'         => array('publish'),
            'ignore_sticky_posts' => 1,
            'posts_per_page'      => -1,
            'author'              => $user_id,
        );

        $data = new WP_Query($args);
        $total_post = $data->found_posts;

        return $total_post;
    }
}

if (!function_exists('golo_total_view_places')){
    function golo_total_view_places(){

        global $current_user;
        wp_get_current_user();
        $user_id = $current_user->ID;

        $args = array(
            'post_type'           => 'place',
            'post_status'         => array('publish'),
            'ignore_sticky_posts' => 1,
            'posts_per_page'      => -1,
            'author'              => $user_id,
        );

        $data = new WP_Query($args);
        $total_post = $data->found_posts;
        $total_view = 0;

        if($total_post > 0){
            while ( $data->have_posts() ) : $data->the_post();

                $id = get_the_ID();
                $place_views_count = get_post_meta($id, GOLO_METABOX_PREFIX . 'place_views_count', true);
                $total_view += intval($place_views_count);

            endwhile;
        }
        wp_reset_postdata();

        return $total_view;
    }
}

if (!function_exists('golo_total_user_booking')){
    function golo_total_user_booking(){

        global $current_user;
        wp_get_current_user();
        $user_id = $current_user->ID;
        $booking_author = array();

        $meta_query = array();

        $args = array(
            'post_type'      => 'booking',
            'post_status'    => array('publish', 'pending'),
            'posts_per_page' => -1,
        );

        $meta_query[] = array(
            'key'     => GOLO_METABOX_PREFIX. 'booking_item_author',
            'value'   => $user_id,
            'type'    => 'NUMERIC',
            'compare' => '=',
        );

        $args['meta_query'] = array(
            'relation' => 'AND',
            $meta_query
        );

        $data = new WP_Query($args);
        $total_post = $data->found_posts;

        $booking_id_author = array();

        while ( $data->have_posts() ) : $data->the_post();

            $id = get_the_ID();

            $booking_author[] = $id;

        endwhile;

        wp_reset_postdata();

        add_user_meta( $user_id, 'user_list_booking_id', $booking_author );

        return $total_post;
    }
}

if (!function_exists('golo_total_user_review')){
    function golo_total_user_review(){

        global $current_user;
        wp_get_current_user();
        $user_id = $current_user->ID;

        global $wpdb;
        $comments_query = "SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE meta.meta_key = 'place_rating' AND meta.comment_id = comment.comment_ID AND ( comment.comment_approved = 1 OR comment.user_id = $user_id )";

        $get_comments   = $wpdb->get_results($comments_query);

        $comment_author = array();
        if (!is_null($get_comments)) {
            foreach ($get_comments as $comment) {
                $comment_id             = $comment->comment_ID;
                $post_id                = $comment->comment_post_ID;
                $comment_user_id        = $comment->user_id;
                $post_author_id         = get_post_field( 'post_author', $post_id );
                if( $post_author_id == $user_id ) {
                    $comment_author[] = $comment_id;
                }
            }
        }
        $total_post = count($comment_author);

        add_user_meta( $user_id, 'user_list_comment_id', $comment_author );

        return $total_post;
    }
}

/**
 * golo_admin_taxonomy_terms
 */
if (!function_exists('golo_admin_taxonomy_terms')) {
    function golo_admin_taxonomy_terms($post_id, $taxonomy, $post_type)
    {

        $terms = get_the_terms($post_id, $taxonomy);

        if (!empty ($terms)) {
            $results = array();
            foreach ($terms as $term) {
                $results[] = sprintf('<a href="%s">%s</a>',
                    esc_url(add_query_arg(array('post_type' => $post_type, $taxonomy => $term->slug), 'edit.php')),
                    esc_html(sanitize_term_field('name', $term->name, $term->term_id, $taxonomy, 'display'))
                );
            }
            return join(', ', $results);
        }

        return false;
    }
}

/**
 * Get format number
 */
if (!function_exists('golo_get_format_number')) {
    function golo_get_format_number($number,$decimals=0)
    {
        $number = doubleval($number);
        if ($number) {
            $dec_point = golo_get_option('decimal_separator', '.');
            $thousands_sep = golo_get_option('thousand_separator', ',');
            return number_format($number, $decimals, $dec_point, $thousands_sep);
        } else {
            return 0;
        }
    }
}

/**
 * Format money
 */
if (!function_exists('golo_get_format_money')) {
    function golo_get_format_money($money, $price_unit = '', $decimals = 0, $small_sign = false)
    {
        $formatted_price = $money;
        $money           = doubleval($money);
        if ($money) {
            $dec_point     = golo_get_option('decimal_separator', '.');
            $thousands_sep = golo_get_option('thousand_separator', ',');

            $price_unit = intval($price_unit);
            $formatted_price = number_format($money, $decimals, $dec_point, $thousands_sep);

            $currency = golo_get_option('currency_sign', esc_html__('$', 'golo-framework'));
            if($small_sign == true)
            {
                $currency = '<sup>'.$currency.'</sup>';
            }
            $currency_position = golo_get_option('currency_position', 'before');
            if ($currency_position == 'before') {
                return $currency . $formatted_price;
            } else {
                return $formatted_price . $currency;
            }

        } else {
            $currency = 0;
        }
        return $currency;
    }
}

/**
 * Image size
 */
if (!function_exists('golo_image_resize')) {
    function golo_image_resize( $data, $image_size ) {
        if( preg_match( '/\d+x\d+/', $image_size) ){
            $image_sizes = explode( 'x', $image_size );
            $image_src  = golo_image_resize_id($data, $image_sizes[0], $image_sizes[1], true);
        }else{
            if(!in_array( $image_size, array('full','thumbnail'))){
                $image_size = 'full';
            }
            $image_src = wp_get_attachment_image_src($data, $image_size);
            if ( $image_src && ! empty( $image_src[0] ) ) {
                $image_src = $image_src[0];
            }
        }
        return $image_src;
    }
}

/**
 * Image resize by url
 */
if (!function_exists('golo_image_resize_url')) {
    function golo_image_resize_url($url, $width = NULL, $height = NULL, $crop = true, $retina = false)
    {

        global $wpdb;

        if (empty($url))
            return new WP_Error('no_image_url', esc_html__('No image URL has been entered.', 'golo-framework'), $url);

        if (class_exists('Jetpack') && method_exists('Jetpack', 'get_active_modules') && in_array('photon', Jetpack::get_active_modules())) {
            $args_crop = array(
                'resize' => $width . ',' . $height,
                'crop' => '0,0,' . $width . 'px,' . $height . 'px'
            );
            $url = jetpack_photon_url($url, $args_crop);
        }

        // Get default size from database
        $width = ($width) ? $width : get_option('thumbnail_size_w');
        $height = ($height) ? $height : get_option('thumbnail_size_h');

        // Allow for different retina sizes
        $retina = $retina ? ($retina === true ? 2 : $retina) : 1;

        // Get the image file path
        $file_path = parse_url($url);
        $file_path = $_SERVER['DOCUMENT_ROOT'] . $file_path['path'];

        // Check for Multisite
        if ( is_multisite() ) { 
            global $blog_id; 
            $blog_details = get_blog_details( $blog_id );
            $file_path = str_replace( $blog_details->path, '/', $file_path );
            //$file_path = str_replace( $blog_details->path . 'files/', '/wp-content/blogs.dir/' . $blog_id . '/files/', $file_path );
        }

        // Destination width and height variables
        $dest_width = $width * $retina; 
        
        $dest_height = $height * $retina;

        // File name suffix (appended to original file name)
        $suffix = "{$dest_width}x{$dest_height}";

        // Some additional info about the image
        $info = pathinfo($file_path);
        $dir = $info['dirname'];
        $ext = $name = '';
        if( !empty($info['extension']) ) {
            $ext = $info['extension'];
            $name = wp_basename($file_path, ".$ext");
        }

        if ('bmp' == $ext) {
            return new WP_Error('bmp_mime_type', esc_html__('Image is BMP. Please use either JPG or PNG.', 'golo-framework'), $url);
        }

        // Suffix applied to filename
        $suffix = "{$dest_width}x{$dest_height}";

        // Get the destination file name
        $dest_file_name = "{$dir}/{$name}-{$suffix}.{$ext}";

        if (!file_exists($dest_file_name)) {

            /*
             *  Bail if this image isn't in the Media Library.
             *  We only want to resize Media Library images, so we can be sure they get deleted correctly when appropriate.
             */
            $query = $wpdb->prepare("SELECT * FROM $wpdb->posts WHERE guid='%s'", $url);
            $get_attachment = $wpdb->get_results($query);

            //if (!$get_attachment)
                //return array('url' => $url, 'width' => $width, 'height' => $height);

            // Load Wordpress Image Editor
            $editor = wp_get_image_editor($file_path);

            if (is_wp_error($editor))
                return array('url' => $url, 'width' => $width, 'height' => $height);

            // Get the original image size
            $size = $editor->get_size();
            $orig_width = $size['width'];
            $orig_height = $size['height'];

            $src_x = $src_y = 0;
            $src_w = $orig_width;
            $src_h = $orig_height;

            if ($crop) {

                $cmp_x = $orig_width / $dest_width;
                $cmp_y = $orig_height / $dest_height;

                // Calculate x or y coordinate, and width or height of source
                if ($cmp_x > $cmp_y) {
                    $src_w = round($orig_width / $cmp_x * $cmp_y);
                    $src_x = round(($orig_width - ($orig_width / $cmp_x * $cmp_y)) / 2);
                } else if ($cmp_y > $cmp_x) {
                    $src_h = round($orig_height / $cmp_y * $cmp_x);
                    $src_y = round(($orig_height - ($orig_height / $cmp_y * $cmp_x)) / 2);
                }

            }

            // Time to crop the image!
            $editor->crop($src_x, $src_y, $src_w, $src_h, $dest_width, $dest_height);

            // Now let's save the image
            $saved = $editor->save($dest_file_name);

            // Get resized image information
            $resized_url = str_replace(wp_basename($url), wp_basename($saved['path']), $url);
            $resized_width = $saved['width'];
            $resized_height = $saved['height'];
            $resized_type = $saved['mime-type'];

            // Add the resized dimensions to original image metadata (so we can delete our resized images when the original image is delete from the Media Library)
            if( $get_attachment ) {
                if( $get_attachment[0]->ID ){
                    $metadata = wp_get_attachment_metadata($get_attachment[0]->ID);
                    if (isset($metadata['image_meta'])) {
                        $metadata['image_meta']['resized_images'][] = $resized_width . 'x' . $resized_height;
                        wp_update_attachment_metadata($get_attachment[0]->ID, $metadata);
                    }
                }
            }

            // Create the image array
            $image_array = array(
                'url' => $resized_url,
                'width' => $resized_width,
                'height' => $resized_height,
                'type' => $resized_type
            );

        } else {
            $image_array = array(
                'url' => str_replace(wp_basename($url), wp_basename($dest_file_name), $url),
                'width' => $dest_width,
                'height' => $dest_height,
                'type' => $ext
            );
        }

        // Return image array
        return $image_array;
    }
}

/*
 * Image resize by id
 */
if (!function_exists('golo_image_resize_id')) {
    function golo_image_resize_id($images_id, $width = NULL, $height = NULL, $crop = true, $retina = false)
    {
        $output = '';
        $image_src = wp_get_attachment_image_src( $images_id, 'full');
        if (is_array($image_src)) {
            $resize = golo_image_resize_url($image_src[0], $width, $height, $crop, $retina);
            if ($resize != null && is_array($resize)) {
                $output = $resize['url'];
            }
        }
        return $output;
    }
}

if (!function_exists('golo_get_selected_countries')) {
    function golo_get_selected_countries()
    {
        $countries = golo_get_countries();
        $countries_selected = get_option( 'country_list' );
        if( !empty($countries_selected) && is_array($countries_selected) )
        {
            $results = array();
            foreach( $countries_selected as $country ){
                foreach( $countries as $key => $value )
                {
                    if( $country === $key )
                    {
                        $results[$key] = $value;
                    }
                }
            }
            return $results;
        }
        else
        {
            return $countries;
        }
    }
}

/**
 * Get countries by code
 */
if (!function_exists('golo_get_country_by_code')) {
    function golo_get_country_by_code($code)
    {
        $countries = golo_get_countries();
        foreach ($countries as $key => $val) {
            if ($key == $code) return $val;
        }
        return null;
    }
}

/**
 * Get countries by name
 */
if (!function_exists('golo_get_code_country_by_name')) {
    function golo_get_code_country_by_name($name)
    {
        $countries = golo_get_countries();
        $country_code = array_search($name, $countries);
        return $country_code;
    }
}

/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 *
 * @access public
 * @param string $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 */
if (!function_exists('golo_get_template')) {
    function golo_get_template($template_name, $args = array(), $template_path = '', $default_path = '')
    {
        if (!empty($args) && is_array($args)) {
            extract($args);
        }

        $located = golo_locate_template($template_name, $template_path, $default_path);

        if (!file_exists($located)) {
            _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $located), '2.1');
            return;
        }

        // Allow 3rd party plugin filter template file from their plugin.
        $located = apply_filters('golo_get_template', $located, $template_name, $args, $template_path, $default_path);

        do_action('golo_before_template_part', $template_name, $template_path, $located, $args);

        include($located);

        do_action('golo_after_template_part', $template_name, $template_path, $located, $args);
    }
}

/**
 * Locate a template and return the path for inclusion.
 */
if (!function_exists('golo_locate_template')) {
    function golo_locate_template($template_name, $template_path = '', $default_path = '')
    {
        if (!$template_path) {
            $template_path = GOLO()->template_path();
        }

        if (!$default_path) {
            $default_path = GOLO_PLUGIN_DIR . 'templates/';
        }

        // Look within passed path within the theme - this is priority.
        $template = locate_template(
            array(
                trailingslashit($template_path) . $template_name,
                $template_name
            )
        );

        // Get default template/
        if (!$template) {
            $template = $default_path . $template_name;
        }

        // Return what we found.
        return apply_filters('golo_locate_template', $template, $template_name, $template_path);
    }
}

/**
 * golo_get_place_by_category
 */
if (!function_exists('golo_get_place_by_category')) {
    function golo_get_place_by_category($total = 6, $show = 4, $city = 0, $category = 0, $custom_image_size = '')
    {   
        $exclude = '';

        if( empty($custom_image_size) ){
            $custom_image_size = golo_get_option('archive_place_image_size', '540x480' );
        }

        if( is_single() ){
            $exclude = get_the_ID();
        }

        $args = array(
            'posts_per_page'      => $total,
            'post_type'           => 'place',
            'post_status'         => 'publish',
            'ignore_sticky_posts' => 1,
            'exclude'             => $exclude,
            'orderby' => array(
                'menu_order' => 'ASC',
                'date'       => 'DESC',
            ),
            'tax_query' => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'place-city',
                    'field'    => 'id',
                    'terms'    => $city
                ),
                array(
                    'taxonomy' => 'place-categories',
                    'field'    => 'id',
                    'terms'    => $category
                )
             )
        );
        $places = get_posts( $args );

        $slick_attributes = array(
            '"slidesToShow": ' . $show,
            '"slidesToScroll": 1',
            '"autoplay": false',
            '"autoplaySpeed": 5000',
            '"responsive": [{ "breakpoint": 374, "settings": {"slidesToShow": 1,"infinite": true, "swipeToSlide": true, "dots": true} }, { "breakpoint": 375, "settings": {"slidesToShow": 1, "infinite": true, "swipeToSlide": true, "dots": true} },{ "breakpoint": 479, "settings": {"slidesToShow": 1,"infinite": true, "swipeToSlide": true, "dots": true} },{ "breakpoint": 768, "settings": {"slidesToShow": 2,"infinite": true, "dots": true} },{ "breakpoint": 992, "settings": {"slidesToShow": 3} },{ "breakpoint": 1200, "settings": {"slidesToShow": 3} } ]'
        );
        $wrapper_attributes[] = "data-slick='{". implode(', ', $slick_attributes) ."}'";

        ob_start();

        ?>
        
        <div class="golo-slick-carousel" <?php echo implode(' ', $wrapper_attributes); ?>>
        
            <?php foreach ($places as $place) { ?>
                
                <?php golo_get_template('content-place.php', array(
                    'place_id' => $place->ID,
                    'custom_place_image_size' => $custom_image_size
                )); ?>

            <?php } ?>

        </div>

        <?php

        return ob_get_clean();
    }
}

/**
 * golo_get_product_related
 */
if (!function_exists('golo_get_product_related')) {
    function golo_get_product_related($total = 6, $show = 4, $custom_image_size = '')
    {   

        if( empty($custom_image_size) ){
            $custom_image_size = golo_get_option('archive_place_image_size', '540x480' );
        }

        $args = array(
            'posts_per_page'      => $total,
            'post_type'           => 'product',
            'post_status'         => 'publish',
            'ignore_sticky_posts' => 1,
            'orderby' => array(
                'menu_order' => 'ASC',
                'date'       => 'DESC',
            ),
        );
        $loop = new WP_Query($args);

        if ($loop->have_posts()){

        $slick_attributes = array(
            '"slidesToShow": ' . $show,
            '"slidesToScroll": 1',
            '"autoplay": false',
            '"autoplaySpeed": 5000',
            '"responsive": [{ "breakpoint": 374, "settings": {"slidesToShow": 1,"infinite": true, "swipeToSlide": true, "dots": true} }, { "breakpoint": 375, "settings": {"slidesToShow": 1,"infinite": true, "swipeToSlide": true, "dots": true} },{ "breakpoint": 479, "settings": {"slidesToShow": 1,"infinite": true, "swipeToSlide": true, "dots": true} },{ "breakpoint": 768, "settings": {"slidesToShow": 2,"infinite": true, "dots": true} },{ "breakpoint": 992, "settings": {"slidesToShow": 3} },{ "breakpoint": 1200, "settings": {"slidesToShow": 3} } ]'
        );
        $wrapper_attributes[] = "data-slick='{". implode(', ', $slick_attributes) ."}'";

        ob_start();

        ?>
        
        <div class="golo-slick-carousel" <?php echo implode(' ', $wrapper_attributes); ?>>
        
            <?php 
                while ($loop->have_posts()) : $loop->the_post(); global $product;
            ?>
                <?php wc_get_template_part( 'content', 'product' ); ?>
            <?php
                endwhile;
                wp_reset_postdata();
            ?>

        </div>

        <?php

        return ob_get_clean();
        }
    }
}

/**
 * golo_get_category_count
 */
if (!function_exists('golo_get_category_count')) {
    function golo_get_category_count( $city, $category)
    {
        $cate_count = '';
        $args = array(
            'posts_per_page'      => -1,
            'post_type'           => 'place',
            'post_status'         => 'publish',
            'ignore_sticky_posts' => 1,
            'tax_query' => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'place-city',
                    'field'    => 'slug',
                    'terms'    => $city
                ),
                array(
                    'taxonomy' => 'place-categories',
                    'field'    => 'slug',
                    'terms'    => $category
                )
             )
        );
        $places     = get_posts( $args );
        $cate_count = count($places);
        
        if( $cate_count ) {
            return $cate_count;
        }
    }
}

/**
 * get_taxonomy
 */
if (!function_exists('golo_get_taxonomy')) {
    function golo_get_taxonomy($taxonomy_name, $value_as_slug = false, $show_default_none = true)
    {
        $taxonomy_terms = get_categories(
            array(
                'taxonomy'   => $taxonomy_name,
                'orderby'    => 'name',
                'order'      => 'ASC',
                'hide_empty' => false,
                'parent'     => 0
            )
        );
        if ($show_default_none) {
            echo '<option value="" selected>' . esc_html__('None', 'golo-framework') . '</option>';
        }
        if (!empty($taxonomy_terms)) {
            if ($value_as_slug) {
                foreach ($taxonomy_terms as $term) {
                    echo '<option value="' . $term->slug . '">' . $term->name . '</option>';
                }
            } else {
                foreach ($taxonomy_terms as $term) {
                    echo '<option value="' . $term->term_id . '">' . $term->name . '</option>';
                }
            }
        }
    }
}

/**
 * Get taxonomy slug by post id
 */
if (!function_exists('golo_get_taxonomy_slug_by_post_id')) {
    function golo_get_taxonomy_slug_by_post_id($post_id, $taxonomy_name)
    {
        $tax_terms = get_the_terms($post_id, $taxonomy_name);
        if (!empty($tax_terms)) {
            foreach ($tax_terms as $tax_term) {
                return $tax_term->slug;
            }
        }
        return null;
    }
}

/**
 * golo_get_taxonomy_slug
 */
if (!function_exists('golo_get_taxonomy_slug')) {
    function golo_get_taxonomy_slug($taxonomy_name, $target_term_slug = '',$prefix = '')
    {
        $taxonomy_terms = get_categories(
            array(
                'taxonomy'   => $taxonomy_name,
                'orderby'    => 'name',
                'order'      => 'ASC',
                'hide_empty' => false,
                'parent'     => 0
            )
        );

        if (!empty($taxonomy_terms)) {
            foreach ($taxonomy_terms as $term) {
                if ($target_term_slug == $term->slug) {
                    echo '<option value="' . $term->slug . '" selected>' . $prefix . $term->name . '</option>';
                } else {
                    echo '<option value="' . $term->slug . '">' . $prefix . $term->name . '</option>';
                }
            }
        }
    }
}

/**
 * get_taxonomy_by_post_id
 */
if (!function_exists('golo_get_taxonomy_by_post_id')) {
    function golo_get_taxonomy_by_post_id($post_id, $taxonomy_name, $is_target_by_name = false, $show_default_none = true)
    {
        $taxonomy_terms = get_categories(
            array(
                'taxonomy'   => $taxonomy_name,
                'orderby'    => 'name',
                'order'      => 'ASC',
                'hide_empty' => false,
                'parent'     => 0
            )
        );
        $target_by_name = array();
        $target_by_id = array();
        $tax_terms = get_the_terms($post_id, $taxonomy_name);
        if ($is_target_by_name) {
            if (!empty($tax_terms)) {
                foreach ($tax_terms as $tax_term) {
                    $target_by_name[] = $tax_term->name;
                }
            }
            if($show_default_none) {
                if (empty($target_by_name)) {
                    echo '<option value="" selected>' . esc_html__('None', 'golo-framework') . '</option>';
                } else {
                    echo '<option value="">' . esc_html__('None', 'golo-framework') . '</option>';
                }
            }
            golo_get_taxonomy_target_by_name($taxonomy_terms, $target_by_name);
        } else {
            if (!empty($tax_terms)) {
                foreach ($tax_terms as $tax_term) {
                    $target_by_id[] = $tax_term->term_id;
                }
            }
            if($show_default_none)
            {
                if ($target_by_id == 0 || empty($target_by_id)) {
                    echo '<option value="-1" selected>' . esc_html__('None', 'golo-framework') . '</option>';
                } else {
                    echo '<option value="-1">' . esc_html__('None', 'golo-framework') . '</option>';
                }
            }
            golo_get_taxonomy_target_by_id( $taxonomy_terms, $target_by_id);
        }
    }
}

/**
 * get_taxonomy_target_by_name
 */
if (!function_exists('golo_get_taxonomy_target_by_name')) {
    function golo_get_taxonomy_target_by_name($taxonomy_terms, $target_term_name, $prefix = "")
    {
        if (!empty($taxonomy_terms)) {
            foreach ($taxonomy_terms as $term) {
                if (in_array($term->name, $target_term_name)) {
                    echo '<option value="' . $term->slug . '" selected>' . $prefix . $term->name . '</option>';
                } else {
                    echo '<option value="' . $term->slug . '">' . $prefix . $term->name . '</option>';
                }
            }
        }
    }
}

/**
 * get_taxonomy_target_by_id
 */
if (!function_exists('golo_get_taxonomy_target_by_id')) {
    function golo_get_taxonomy_target_by_id( $taxonomy_terms, $target_term_id, $prefix = "")
    {
        if (!empty($taxonomy_terms)) {
            foreach ($taxonomy_terms as $term) {
                if (in_array($term->term_id, $target_term_id)) {
                    echo '<option value="' . $term->term_id . '" selected>' . $prefix . $term->name . '</option>';
                } else {
                    echo '<option value="' . $term->term_id . '">' . $prefix . $term->name . '</option>';
                }
            }
        }
    }
}

/**
 * Get countries by code
 */
if (!function_exists('golo_get_country_by_code')) {
    function golo_get_country_by_code($code)
    {
        $countries = golo_get_countries();
        foreach ($countries as $key => $val) {
            if ($key == $code) return $val;
        }
        return null;
    }
}
/**
 * Get countries by name
 */
if ( !function_exists('golo_get_code_country_by_name') ) {
    function golo_get_code_country_by_name($name)
    {
        $countries = golo_get_countries();
        $country_code = array_search($name, $countries);
        return $country_code;
    }
}

if ( !function_exists('golo_server_protocol') ) {
    function golo_server_protocol() 
    {
        if ( is_ssl() ) {
            return 'https://';
        }
        return 'http://';
    }
}

if ( !function_exists('golo_get_comment_time') ) {
    function golo_get_comment_time($comment_id = 0)
    {
        return sprintf(
            _x('%s ago', 'Human-readable time', 'golo-framework'),
            human_time_diff(
                get_comment_date('U', $comment_id),
                current_time('timestamp')
            )
        );
    }
}

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 *
 * @param string|array $var Data to sanitize.
 * @return string|array
 */
if ( !function_exists('golo_clean') ) {
    function golo_clean( $var ) {
        if ( is_array( $var ) ) {
            return array_map( 'golo_clean', $var );
        } else {
            return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
        }
    }
}

if ( !function_exists('golo_clean_double_val') ) {
    function golo_clean_double_val($string)
    {
        $string = preg_replace('/&#36;/', '', $string);
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
        $string = preg_replace('/\D/', '', $string);
        return $string;
    }
}

/**
 * Get measurement units
 */
if (!function_exists('golo_get_measurement_units')) {
    function golo_get_measurement_units()
    {
        $measurement_units = golo_get_option('measurement_units', 'SqFt');
        if ($measurement_units == 'custom') {
            return golo_get_option('custom_measurement_units', 'SqFt');
        }
        else
        {
            return $measurement_units;
        }
    }
}
if (!function_exists('golo_get_measurement_units_land_area')) {
    function golo_get_measurement_units_land_area()
    {
        $measurement_units = golo_get_option('measurement_units_land_area', '');
        if(empty($measurement_units))
        {
            $measurement_units = golo_get_measurement_units();
        }
        if ($measurement_units == 'custom') {
            return golo_get_option('custom_measurement_units_land_area', 'SqFt');
        }
        else
        {
            return $measurement_units;
        }
    }
}

if (!function_exists('golo_render_additional_fields')) {
    function golo_render_additional_fields()
    {
        $meta_prefix = GOLO_METABOX_PREFIX;
        $form_fields = golo_get_option('additional_fields');
        $configs = array();
        if ($form_fields && is_array($form_fields)) {
            foreach ($form_fields as $key => $field) {
                if(!empty($field['label']))
                {
                    $type = $field['field_type'];
                    $config = array(
                        'title' => $field['label'],
                        'id' => $meta_prefix . sanitize_title($field['label']),
                        'type' => $type,
                    );
                    $first_opt = '';
                    switch ($type) {
                        case 'checkbox_list':
                        case 'select':
                        case 'radio':
                            $options = array();
                            $options_arr = isset($field['select_choices']) ? $field['select_choices'] : '';
                            $options_arr = str_replace("\r\n", "\n", $options_arr);
                            $options_arr = str_replace("\r", "\n", $options_arr);
                            $options_arr = explode("\n", $options_arr);
                            $first_opt = !empty($options_arr) ? $options_arr[0] : '';
                            foreach ($options_arr as $opt_value) {
                                $options[$opt_value] = $opt_value;
                            }

                            $config['options'] = $options;
                            break;
                    }
                    if (in_array($type, array('select', 'radio'))) {
                        $config['default'] = $first_opt;
                    }

                    $configs[] = $config;
                }
            }
        }
        return $configs;
    }
}

/**
 * Get countries
 */
if (!function_exists('golo_get_countries')) {
    function golo_get_countries()
    {
        $countries = array(
            ''   => esc_html__('None', 'golo-framework'),
            'AF' => esc_html__('Afghanistan', 'golo-framework'),
            'AX' => esc_html__('Aland Islands', 'golo-framework'),
            'AL' => esc_html__('Albania', 'golo-framework'),
            'DZ' => esc_html__('Algeria', 'golo-framework'),
            'AS' => esc_html__('American Samoa', 'golo-framework'),
            'AD' => esc_html__('Andorra', 'golo-framework'),
            'AO' => esc_html__('Angola', 'golo-framework'),
            'AI' => esc_html__('Anguilla', 'golo-framework'),
            'AQ' => esc_html__('Antarctica', 'golo-framework'),
            'AG' => esc_html__('Antigua and Barbuda', 'golo-framework'),
            'AR' => esc_html__('Argentina', 'golo-framework'),
            'AM' => esc_html__('Armenia', 'golo-framework'),
            'AW' => esc_html__('Aruba', 'golo-framework'),
            'AU' => esc_html__('Australia', 'golo-framework'),
            'AT' => esc_html__('Austria', 'golo-framework'),
            'AZ' => esc_html__('Azerbaijan', 'golo-framework'),
            'BS' => esc_html__('Bahamas the', 'golo-framework'),
            'BH' => esc_html__('Bahrain', 'golo-framework'),
            'BD' => esc_html__('Bangladesh', 'golo-framework'),
            'BB' => esc_html__('Barbados', 'golo-framework'),
            'BY' => esc_html__('Belarus', 'golo-framework'),
            'BE' => esc_html__('Belgium', 'golo-framework'),
            'BZ' => esc_html__('Belize', 'golo-framework'),
            'BJ' => esc_html__('Benin', 'golo-framework'),
            'BM' => esc_html__('Bermuda', 'golo-framework'),
            'BT' => esc_html__('Bhutan', 'golo-framework'),
            'BO' => esc_html__('Bolivia', 'golo-framework'),
            'BA' => esc_html__('Bosnia and Herzegovina', 'golo-framework'),
            'BW' => esc_html__('Botswana', 'golo-framework'),
            'BV' => esc_html__('Bouvet Island (Bouvetoya)', 'golo-framework'),
            'BR' => esc_html__('Brazil', 'golo-framework'),
            'IO' => esc_html__('British Indian Ocean Territory (Chagos Archipelago)', 'golo-framework'),
            'VG' => esc_html__('British Virgin Islands', 'golo-framework'),
            'BN' => esc_html__('Brunei Darussalam', 'golo-framework'),
            'BG' => esc_html__('Bulgaria', 'golo-framework'),
            'BF' => esc_html__('Burkina Faso', 'golo-framework'),
            'BI' => esc_html__('Burundi', 'golo-framework'),
            'KH' => esc_html__('Cambodia', 'golo-framework'),
            'CM' => esc_html__('Cameroon', 'golo-framework'),
            'CA' => esc_html__('Canada', 'golo-framework'),
            'CV' => esc_html__('Cape Verde', 'golo-framework'),
            'KY' => esc_html__('Cayman Islands', 'golo-framework'),
            'CF' => esc_html__('Central African Republic', 'golo-framework'),
            'TD' => esc_html__('Chad', 'golo-framework'),
            'CL' => esc_html__('Chile', 'golo-framework'),
            'CN' => esc_html__('China', 'golo-framework'),
            'CX' => esc_html__('Christmas Island', 'golo-framework'),
            'CC' => esc_html__('Cocos (Keeling) Islands', 'golo-framework'),
            'CO' => esc_html__('Colombia', 'golo-framework'),
            'KM' => esc_html__('Comoros the', 'golo-framework'),
            'CD' => esc_html__('Congo', 'golo-framework'),
            'CG' => esc_html__('Congo the', 'golo-framework'),
            'CK' => esc_html__('Cook Islands', 'golo-framework'),
            'CR' => esc_html__('Costa Rica', 'golo-framework'),
            'CI' => esc_html__("Cote d'Ivoire", 'golo-framework'),
            'HR' => esc_html__('Croatia', 'golo-framework'),
            'CU' => esc_html__('Cuba', 'golo-framework'),
            'CY' => esc_html__('Cyprus', 'golo-framework'),
            'CZ' => esc_html__('Czech Republic', 'golo-framework'),
            'DK' => esc_html__('Denmark', 'golo-framework'),
            'DJ' => esc_html__('Djibouti', 'golo-framework'),
            'DM' => esc_html__('Dominica', 'golo-framework'),
            'DO' => esc_html__('Dominican Republic', 'golo-framework'),
            'EC' => esc_html__('Ecuador', 'golo-framework'),
            'EG' => esc_html__('Egypt', 'golo-framework'),
            'SV' => esc_html__('El Salvador', 'golo-framework'),
            'GQ' => esc_html__('Equatorial Guinea', 'golo-framework'),
            'ER' => esc_html__('Eritrea', 'golo-framework'),
            'EE' => esc_html__('Estonia', 'golo-framework'),
            'ET' => esc_html__('Ethiopia', 'golo-framework'),
            'FO' => esc_html__('Faroe Islands', 'golo-framework'),
            'FK' => esc_html__('Falkland Islands (Malvinas)', 'golo-framework'),
            'FJ' => esc_html__('Fiji the Fiji Islands', 'golo-framework'),
            'FI' => esc_html__('Finland', 'golo-framework'),
            'FR' => esc_html__('France', 'golo-framework'),
            'GF' => esc_html__('French Guiana', 'golo-framework'),
            'PF' => esc_html__('French Polynesia', 'golo-framework'),
            'TF' => esc_html__('French Southern Territories', 'golo-framework'),
            'GA' => esc_html__('Gabon', 'golo-framework'),
            'GM' => esc_html__('Gambia the', 'golo-framework'),
            'GE' => esc_html__('Georgia', 'golo-framework'),
            'DE' => esc_html__('Germany', 'golo-framework'),
            'GH' => esc_html__('Ghana', 'golo-framework'),
            'GI' => esc_html__('Gibraltar', 'golo-framework'),
            'GR' => esc_html__('Greece', 'golo-framework'),
            'GL' => esc_html__('Greenland', 'golo-framework'),
            'GD' => esc_html__('Grenada', 'golo-framework'),
            'GP' => esc_html__('Guadeloupe', 'golo-framework'),
            'GU' => esc_html__('Guam', 'golo-framework'),
            'GT' => esc_html__('Guatemala', 'golo-framework'),
            'GG' => esc_html__('Guernsey', 'golo-framework'),
            'GN' => esc_html__('Guinea', 'golo-framework'),
            'GW' => esc_html__('Guinea-Bissau', 'golo-framework'),
            'GY' => esc_html__('Guyana', 'golo-framework'),
            'HT' => esc_html__('Haiti', 'golo-framework'),
            'HM' => esc_html__('Heard Island and McDonald Islands', 'golo-framework'),
            'VA' => esc_html__('Holy See (Vatican City State)', 'golo-framework'),
            'HN' => esc_html__('Honduras', 'golo-framework'),
            'HK' => esc_html__('Hong Kong', 'golo-framework'),
            'HU' => esc_html__('Hungary', 'golo-framework'),
            'IS' => esc_html__('Iceland', 'golo-framework'),
            'IN' => esc_html__('India', 'golo-framework'),
            'ID' => esc_html__('Indonesia', 'golo-framework'),
            'IR' => esc_html__('Iran', 'golo-framework'),
            'IQ' => esc_html__('Iraq', 'golo-framework'),
            'IE' => esc_html__('Ireland', 'golo-framework'),
            'IM' => esc_html__('Isle of Man', 'golo-framework'),
            'IL' => esc_html__('Israel', 'golo-framework'),
            'IT' => esc_html__('Italy', 'golo-framework'),
            'JM' => esc_html__('Jamaica', 'golo-framework'),
            'JP' => esc_html__('Japan', 'golo-framework'),
            'JE' => esc_html__('Jersey', 'golo-framework'),
            'JO' => esc_html__('Jordan', 'golo-framework'),
            'KZ' => esc_html__('Kazakhstan', 'golo-framework'),
            'KE' => esc_html__('Kenya', 'golo-framework'),
            'KI' => esc_html__('Kiribati', 'golo-framework'),
            'KP' => esc_html__('Korea', 'golo-framework'),
            'KR' => esc_html__('Korea', 'golo-framework'),
            'KW' => esc_html__('Kuwait', 'golo-framework'),
            'KG' => esc_html__('Kyrgyz Republic', 'golo-framework'),
            'LA' => esc_html__('Lao', 'golo-framework'),
            'LV' => esc_html__('Latvia', 'golo-framework'),
            'LB' => esc_html__('Lebanon', 'golo-framework'),
            'LS' => esc_html__('Lesotho', 'golo-framework'),
            'LR' => esc_html__('Liberia', 'golo-framework'),
            'LY' => esc_html__('Libyan Arab Jamahiriya', 'golo-framework'),
            'LI' => esc_html__('Liechtenstein', 'golo-framework'),
            'LT' => esc_html__('Lithuania', 'golo-framework'),
            'LU' => esc_html__('Luxembourg', 'golo-framework'),
            'MO' => esc_html__('Macao', 'golo-framework'),
            'MK' => esc_html__('Macedonia', 'golo-framework'),
            'MG' => esc_html__('Madagascar', 'golo-framework'),
            'MW' => esc_html__('Malawi', 'golo-framework'),
            'MY' => esc_html__('Malaysia', 'golo-framework'),
            'MV' => esc_html__('Maldives', 'golo-framework'),
            'ML' => esc_html__('Mali', 'golo-framework'),
            'MT' => esc_html__('Malta', 'golo-framework'),
            'MH' => esc_html__('Marshall Islands', 'golo-framework'),
            'MQ' => esc_html__('Martinique', 'golo-framework'),
            'MR' => esc_html__('Mauritania', 'golo-framework'),
            'MU' => esc_html__('Mauritius', 'golo-framework'),
            'YT' => esc_html__('Mayotte', 'golo-framework'),
            'MX' => esc_html__('Mexico', 'golo-framework'),
            'FM' => esc_html__('Micronesia', 'golo-framework'),
            'MD' => esc_html__('Moldova', 'golo-framework'),
            'MC' => esc_html__('Monaco', 'golo-framework'),
            'MN' => esc_html__('Mongolia', 'golo-framework'),
            'ME' => esc_html__('Montenegro', 'golo-framework'),
            'MS' => esc_html__('Montserrat', 'golo-framework'),
            'MA' => esc_html__('Morocco', 'golo-framework'),
            'MZ' => esc_html__('Mozambique', 'golo-framework'),
            'MM' => esc_html__('Myanmar', 'golo-framework'),
            'NA' => esc_html__('Namibia', 'golo-framework'),
            'NR' => esc_html__('Nauru', 'golo-framework'),
            'NP' => esc_html__('Nepal', 'golo-framework'),
            'AN' => esc_html__('Netherlands Antilles', 'golo-framework'),
            'NL' => esc_html__('Netherlands the', 'golo-framework'),
            'NC' => esc_html__('New Caledonia', 'golo-framework'),
            'NZ' => esc_html__('New Zealand', 'golo-framework'),
            'NI' => esc_html__('Nicaragua', 'golo-framework'),
            'NE' => esc_html__('Niger', 'golo-framework'),
            'NG' => esc_html__('Nigeria', 'golo-framework'),
            'NU' => esc_html__('Niue', 'golo-framework'),
            'NF' => esc_html__('Norfolk Island', 'golo-framework'),
            'MP' => esc_html__('Northern Mariana Islands', 'golo-framework'),
            'NO' => esc_html__('Norway', 'golo-framework'),
            'OM' => esc_html__('Oman', 'golo-framework'),
            'PK' => esc_html__('Pakistan', 'golo-framework'),
            'PW' => esc_html__('Palau', 'golo-framework'),
            'PS' => esc_html__('Palestinian Territory', 'golo-framework'),
            'PA' => esc_html__('Panama', 'golo-framework'),
            'PG' => esc_html__('Papua New Guinea', 'golo-framework'),
            'PY' => esc_html__('Paraguay', 'golo-framework'),
            'PE' => esc_html__('Peru', 'golo-framework'),
            'PH' => esc_html__('Philippines', 'golo-framework'),
            'PN' => esc_html__('Pitcairn Islands', 'golo-framework'),
            'PL' => esc_html__('Poland', 'golo-framework'),
            'PT' => esc_html__('Portugal, Portuguese Republic', 'golo-framework'),
            'PR' => esc_html__('Puerto Rico', 'golo-framework'),
            'QA' => esc_html__('Qatar', 'golo-framework'),
            'RE' => esc_html__('Reunion', 'golo-framework'),
            'RO' => esc_html__('Romania', 'golo-framework'),
            'RU' => esc_html__('Russian Federation', 'golo-framework'),
            'RW' => esc_html__('Rwanda', 'golo-framework'),
            'BL' => esc_html__('Saint Barthelemy', 'golo-framework'),
            'SH' => esc_html__('Saint Helena', 'golo-framework'),
            'KN' => esc_html__('Saint Kitts and Nevis', 'golo-framework'),
            'LC' => esc_html__('Saint Lucia', 'golo-framework'),
            'MF' => esc_html__('Saint Martin', 'golo-framework'),
            'PM' => esc_html__('Saint Pierre and Miquelon', 'golo-framework'),
            'VC' => esc_html__('Saint Vincent and the Grenadines', 'golo-framework'),
            'WS' => esc_html__('Samoa', 'golo-framework'),
            'SM' => esc_html__('San Marino', 'golo-framework'),
            'ST' => esc_html__('Sao Tome and Principe', 'golo-framework'),
            'SA' => esc_html__('Saudi Arabia', 'golo-framework'),
            'SN' => esc_html__('Senegal', 'golo-framework'),
            'RS' => esc_html__('Serbia', 'golo-framework'),
            'SC' => esc_html__('Seychelles', 'golo-framework'),
            'SL' => esc_html__('Sierra Leone', 'golo-framework'),
            'SG' => esc_html__('Singapore', 'golo-framework'),
            'SK' => esc_html__('Slovakia (Slovak Republic)', 'golo-framework'),
            'SI' => esc_html__('Slovenia', 'golo-framework'),
            'SB' => esc_html__('Solomon Islands', 'golo-framework'),
            'SO' => esc_html__('Somalia, Somali Republic', 'golo-framework'),
            'ZA' => esc_html__('South Africa', 'golo-framework'),
            'GS' => esc_html__('South Georgia and the South Sandwich Islands', 'golo-framework'),
            'ES' => esc_html__('Spain', 'golo-framework'),
            'LK' => esc_html__('Sri Lanka', 'golo-framework'),
            'SD' => esc_html__('Sudan', 'golo-framework'),
            'SR' => esc_html__('Suriname', 'golo-framework'),
            'SJ' => esc_html__('Svalbard & Jan Mayen Islands', 'golo-framework'),
            'SZ' => esc_html__('Swaziland', 'golo-framework'),
            'SE' => esc_html__('Sweden', 'golo-framework'),
            'CH' => esc_html__('Switzerland, Swiss Confederation', 'golo-framework'),
            'SY' => esc_html__('Syrian Arab Republic', 'golo-framework'),
            'TW' => esc_html__('Taiwan', 'golo-framework'),
            'TJ' => esc_html__('Tajikistan', 'golo-framework'),
            'TZ' => esc_html__('Tanzania', 'golo-framework'),
            'TH' => esc_html__('Thailand', 'golo-framework'),
            'TL' => esc_html__('Timor-Leste', 'golo-framework'),
            'TG' => esc_html__('Togo', 'golo-framework'),
            'TK' => esc_html__('Tokelau', 'golo-framework'),
            'TO' => esc_html__('Tonga', 'golo-framework'),
            'TT' => esc_html__('Trinidad and Tobago', 'golo-framework'),
            'TN' => esc_html__('Tunisia', 'golo-framework'),
            'TR' => esc_html__('Turkey', 'golo-framework'),
            'TM' => esc_html__('Turkmenistan', 'golo-framework'),
            'TC' => esc_html__('Turks and Caicos Islands', 'golo-framework'),
            'TV' => esc_html__('Tuvalu', 'golo-framework'),
            'UG' => esc_html__('Uganda', 'golo-framework'),
            'UA' => esc_html__('Ukraine', 'golo-framework'),
            'AE' => esc_html__('United Arab Emirates', 'golo-framework'),
            'GB' => esc_html__('United Kingdom', 'golo-framework'),
            'SCL' => esc_html__('Scotland', 'golo-framework'),
            'WL' => esc_html__('Wales', 'golo-framework'),
            'NIR' => esc_html__('Northern Ireland', 'golo-framework'),
            'US' => esc_html__('United States', 'golo-framework'),
            'UM' => esc_html__('United States Minor Outlying Islands', 'golo-framework'),
            'VI' => esc_html__('United States Virgin Islands', 'golo-framework'),
            'UY' => esc_html__('Uruguay, Eastern Republic of', 'golo-framework'),
            'UZ' => esc_html__('Uzbekistan', 'golo-framework'),
            'VU' => esc_html__('Vanuatu', 'golo-framework'),
            'VE' => esc_html__('Venezuela', 'golo-framework'),
            'VN' => esc_html__('Vietnam', 'golo-framework'),
            'WF' => esc_html__('Wallis and Futuna', 'golo-framework'),
            'EH' => esc_html__('Western Sahara', 'golo-framework'),
            'YE' => esc_html__('Yemen', 'golo-framework'),
            'ZM' => esc_html__('Zambia', 'golo-framework'),
            'ZW' => esc_html__('Zimbabwe', 'golo-framework'),
        );
        return $countries;
    }
}

//GET PRIMARY TAXONOMY
if ( ! function_exists( 'get_primary_taxonomy_id' ) ) {
    function get_primary_taxonomy_id( $post_id, $taxonomy ) {
        if (class_exists('WPSEO_Primary_Term')) {
            
            $prm_term = '';
        
            $wpseo_primary_term = new WPSEO_Primary_Term( $taxonomy, $post_id );
            $prm_term = $wpseo_primary_term->get_primary_term();
        
            if ( !is_object($wpseo_primary_term) || empty( $prm_term ) ) {
                $term = wp_get_post_terms( $post_id, $taxonomy );
                if (isset( $term ) && !empty( $term ) ) {
                    return $term[0]->term_id;
                } else {
                    return '';
                }
            }
            return $wpseo_primary_term->get_primary_term();
        }
    }
}

//GET TIME ZONE ARRAY
if ( ! function_exists( 'get_timezone_array' ) ) {
    function get_timezone_array() {
        static $mo_loaded = false, $locale_loaded = null;

        $continents = array( 'Africa', 'America', 'Antarctica', 'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific' );

        // Load translations for continents and cities.
        if ( ! $mo_loaded || $locale !== $locale_loaded ) {
            $locale_loaded = !empty($locale) ? $locale : get_locale();
            $mofile        = WP_LANG_DIR . '/continents-cities-' . $locale_loaded . '.mo';
            unload_textdomain( 'continents-cities' );
            load_textdomain( 'continents-cities', $mofile );
            $mo_loaded = true;
        }

        $zonen = array();
        foreach ( timezone_identifiers_list() as $zone ) {
            $zone = explode( '/', $zone );
            if ( ! in_array( $zone[0], $continents, true ) ) {
                continue;
            }

            // This determines what gets set and translated - we don't translate Etc/* strings here, they are done later.
            $exists    = array(
                0 => ( isset( $zone[0] ) && $zone[0] ),
                1 => ( isset( $zone[1] ) && $zone[1] ),
                2 => ( isset( $zone[2] ) && $zone[2] ),
            );
            $exists[3] = ( $exists[0] && 'Etc' !== $zone[0] );
            $exists[4] = ( $exists[1] && $exists[3] );
            $exists[5] = ( $exists[2] && $exists[3] );

            // phpcs:disable WordPress.WP.I18n.LowLevelTranslationFunction,WordPress.WP.I18n.NonSingularStringLiteralText
            $zonen[] = array(
                'continent'   => ( $exists[0] ? $zone[0] : '' ),
                'city'        => ( $exists[1] ? $zone[1] : '' ),
                'subcity'     => ( $exists[2] ? $zone[2] : '' ),
                't_continent' => ( $exists[3] ? translate( str_replace( '_', ' ', $zone[0] ), 'continents-cities' ) : '' ),
                't_city'      => ( $exists[4] ? translate( str_replace( '_', ' ', $zone[1] ), 'continents-cities' ) : '' ),
                't_subcity'   => ( $exists[5] ? translate( str_replace( '_', ' ', $zone[2] ), 'continents-cities' ) : '' ),
            );
            // phpcs:enable
        }
        usort( $zonen, '_wp_timezone_choice_usort_callback' );

        $structure = array();

        foreach ( $zonen as $key => $zone ) {
            // Build value in an array to join later.
            $value = array( $zone['continent'] );

            if ( empty( $zone['city'] ) ) {
                // It's at the continent level (generally won't happen).
                $display = $zone['t_continent'];
            } else {
                // It's inside a continent group.

                // Continent optgroup.
                if ( ! isset( $zonen[ $key - 1 ] ) || $zonen[ $key - 1 ]['continent'] !== $zone['continent'] ) {
                    $label       = $zone['t_continent'];
                }

                // Add the city to the value.
                $value[] = $zone['city'];

                $display = $zone['t_city'];
                if ( ! empty( $zone['subcity'] ) ) {
                    // Add the subcity to the value.
                    $value[]  = $zone['subcity'];
                    $display .= ' - ' . $zone['t_subcity'];
                }
            }

            // Build the value.
            $value    = implode( '/', $value );            

            $structure[$label][$value] = $display;
        }

        // Do UTC.
        $offset_range = array(
            -12,
            -11.5,
            -11,
            -10.5,
            -10,
            -9.5,
            -9,
            -8.5,
            -8,
            -7.5,
            -7,
            -6.5,
            -6,
            -5.5,
            -5,
            -4.5,
            -4,
            -3.5,
            -3,
            -2.5,
            -2,
            -1.5,
            -1,
            -0.5,
            0,
            0.5,
            1,
            1.5,
            2,
            2.5,
            3,
            3.5,
            4,
            4.5,
            5,
            5.5,
            5.75,
            6,
            6.5,
            7,
            7.5,
            8,
            8.5,
            8.75,
            9,
            9.5,
            10,
            10.5,
            11,
            11.5,
            12,
            12.75,
            13,
            13.75,
            14,
        );
        foreach ( $offset_range as $offset ) {
            if ( 0 <= $offset ) {
                $offset_name = '+' . $offset;
            } else {
                $offset_name = (string) $offset;
            }

            $offset_value = $offset_name;
            $offset_name  = str_replace( array( '.25', '.5', '.75' ), array( ':15', ':30', ':45' ), $offset_name );
            $offset_name  = 'UTC' . $offset_name;
            $offset_value = 'UTC' . $offset_value;

            $structure[esc_attr__( 'Manual Offsets' )][$offset_value] = $offset_name;

        }

        return $structure;
    }
}

//CONVERT PLACE TIMEZONE
//RETURN PLACE TIMEZONE OR WORDPRESS DEFAULT TIMEZONE
if ( ! function_exists( 'convert_place_timezone' ) ) {
    function convert_place_timezone( $place_timezone ) {
        if ( ! empty( $place_timezone )) {
            if ( preg_match( '/^UTC[+-]/', $place_timezone ) ) {
                $tzstring = '';
                $offset      = preg_replace( '/UTC\+?/', '', $place_timezone );
            } else {
                $tzstring = $place_timezone;
            }
        } else {
            $tzstring = get_option( 'timezone_string' );            
            $offset   = get_option( 'gmt_offset' );
        }
        
        // Uuse ETC/GMT for integer offset
        if( empty( $tzstring ) && 0 != $offset && floor( $offset ) == $offset ){
            $offset_st = $offset > 0 ? "-$offset" : '+'.absint( $offset );
            $tzstring  = 'Etc/GMT'.$offset_st;
        }

        // Use GMT for decimal offset or PHP not recognizing
        if (empty( $tzstring ) && 0 != $offset && floor( $offset ) != $offset) {
            $offset  = str_replace( array( '.25', '.5', '.75' ), array( ':15', ':30', ':45' ), $offset );
            $offset_st = $offset > 0 ? "+$offset" : "-$offset";
            $tzstring  = 'GMT'.$offset_st;
        }
        
        //Issue with the timezone selected, set to 'UTC'
        if( empty( $tzstring ) ){
            $tzstring = 'UTC';
        }

        return $tzstring;
    }
}

//CONVERT PLACE TIMEZONE
//GET WORDPRESS TIMEZONE
if ( ! function_exists( 'get_wp_timezone' ) ) {
    function get_wp_timezone() {
        $current_offset = get_option( 'gmt_offset' );
        $tzstring       = get_option( 'timezone_string' );

        // Remove old Etc mappings. Fallback to gmt_offset.
        if ( false !== strpos( $tzstring, 'Etc/GMT' ) ) {
            $tzstring = '';
        }

        if ( empty( $tzstring ) ) { // Create a UTC+- zone if no timezone string exists.
            if ( 0 == $current_offset ) {
                $tzstring = 'UTC+0';
            } elseif ( $current_offset < 0 ) {
                $tzstring = 'UTC' . $current_offset;
            } else {
                $tzstring = 'UTC+' . $current_offset;
            }
        }

        return $tzstring;
    }
}