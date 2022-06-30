<?php
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

if ( !class_exists('Golo_Admin') ) {
    /**
     * Class Golo_Admin
     */
    class Golo_Admin 
    {

        /**
         * Remove admin bar
         * @return bool
         */
        function remove_admin_bar() {
            if (!current_user_can('administrator') && !is_admin()) {
              show_admin_bar(false);
            }
        }

        /**
         * Check if it is a place edit page.
         * @return bool
         */
        public function is_golo_admin()
        {
            if (is_admin()) {
                global $pagenow;
                if (in_array($pagenow, array('edit.php', 'post.php', 'post-new.php','edit-tags.php'))) {
                    global $post_type;
                    if ('place' == $post_type) {
                        return true;
                    }
                }
            }
            return false;
        }

        /**
         * Register admin_menu
         */
        public function admin_menu()
        {
            $enable_claim_listing       = golo_get_option('enable_claim_listing', '1' );
            if ($enable_claim_listing) :
                add_menu_page(
                    esc_html__('Claim Listing', 'golo-framework' ),
                    esc_html__('Claim Listing', 'golo-framework'),
                    'manage_options',
                    'claim_listing',
                    array($this, 'menu_claim_listing_callback'),
                    'dashicons-list-view',
                    12
                );
            endif;
        }

        public function menu_claim_listing_callback()
        {
            $claim_email = $claim_name = $claim_username = $claim_status = '';

            $meta_query = array(
                'relative'  => 'AND',
                array(
                    'key'     => 'golo-claim_request',
                    'value'   => 1,
                    'compare' => '=',
                ),
            );
            if (isset($_GET['claim_name']) && $_GET['claim_name'] != '') {
                $claim_name = $_GET['claim_name'];
                $meta_query[] = array(
                    'key'     => 'golo-cd_your_name',
                    'value'   => $_GET['claim_name'],
                    'compare' => 'LIKE',
                );
            }
            if (isset($_GET['claim_email']) && $_GET['claim_email'] != '') {
                $claim_email = $_GET['claim_email'];
                $meta_query[] = array(
                    'key'     => 'golo-cd_your_email',
                    'value'   => $_GET['claim_email'],
                    'compare' => 'LIKE',
                );
            }
            if (isset($_GET['claim_username']) && $_GET['claim_username'] != '') {
                $claim_username = $_GET['claim_username'];
                $meta_query[] = array(
                    'key'     => 'golo-cd_your_username',
                    'value'   => $_GET['claim_username'],
                    'compare' => 'LIKE',
                );
            }
            if (isset($_GET['claim_status']) && $_GET['claim_status'] != '') {
                $claim_status = $_GET['claim_status'];
                $meta_query[] = array(
                    'key'     => 'golo-cd_status',
                    'value'   => $_GET['claim_status'],
                    'compare' => '=',
                );
            }
            $paged = isset( $_REQUEST['paged']) ? max( 1, ( int ) $_REQUEST['paged'] ) : 1;
            $args = array(
                'post_type'         => 'place',
                'posts_per_page'    => 20,
                'paged'             => $paged,
                'post_status'       => 'publish',
                'meta_query'        => $meta_query,
            );
            // The Query
            $the_query = new WP_Query( $args );
            $count = $the_query->found_posts;
            ?>
            <div class="golo-wrap wrap about-wrap claim-wrap">
                <div class="entry-search">
                    <div class="claim-action">
                        <a href="#" class="button button-delete"><?php esc_html_e('Delete', 'golo-framework'); ?></a>
                    </div>
                    <form action="" method="GET" class="claimFilter">
                        <div class="field-group">
                            <input type="text" name="claim_name" value="<?php echo $claim_name; ?>" placeholder="<?php esc_html_e('Name', 'golo-framework'); ?>">
                            <input type="email" name="claim_email" value="<?php echo $claim_email; ?>" placeholder="<?php esc_html_e('Email', 'golo-framework'); ?>">
                            <input type="text" name="claim_username" value="<?php echo $claim_username; ?>" placeholder="<?php esc_html_e('Username', 'golo-framework'); ?>">
                            <select name="claim_status" id="claim_status">
                                <option value=""><?php esc_html_e('All Status', 'golo-framework'); ?></option>
                                <option value="pending" <?php if ($claim_status == 'pending') { echo 'selected'; } ?>><?php esc_html_e('Pending', 'golo-framework'); ?></option>
                                <option value="accept" <?php if ($claim_status == 'accept') { echo 'selected'; } ?>><?php esc_html_e('Accept', 'golo-framework'); ?></option>
                                <option value="refuse" <?php if ($claim_status == 'refuse') { echo 'selected'; } ?>><?php esc_html_e('Refuse', 'golo-framework'); ?></option>
                            </select>
                            <input type="hidden" name="page" value="claim_listing">
                            <input type="submit" name="submit" value="<?php esc_html_e('Filter', 'golo-framework'); ?>">
                        </div>
                    </form>
                    <div class="total"><?php printf( _n( '%s item', '%s items', $count, 'golo-framework' ), '<span class="count">' . esc_html( $count ) . '</span>' ); ?></div>
                </div>

                <div class="wrap-content">
                    <form action="" method="POST">
                        <table class="table-changelogs">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="checkall" name="claim_item"></th>
                                    <th><?php esc_html_e('Name', 'golo-framework'); ?></th>
                                    <th><?php esc_html_e('Email', 'golo-framework'); ?></th>
                                    <th><?php esc_html_e('Username', 'golo-framework'); ?></th>
                                    <th><?php esc_html_e('Listing Url', 'golo-framework'); ?></th>
                                    <th><?php esc_html_e('Messager', 'golo-framework'); ?></th>
                                    <th><?php esc_html_e('Status', 'golo-framework'); ?></th>
                                    <th><?php esc_html_e('Action', 'golo-framework'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    // The Loop
                                    if ( $the_query->have_posts() ) {
                                        
                                        $i = 0;
                                        while ( $the_query->have_posts() ) {
                                            $the_query->the_post();
                                            $i++;
                                            $id = get_the_ID();
                                            $cd_your_name = get_post_meta($id, GOLO_METABOX_PREFIX . 'cd_your_name', true);
                                            $cd_your_email = get_post_meta($id, GOLO_METABOX_PREFIX . 'cd_your_email', true);
                                            $cd_your_listing = get_post_meta($id, GOLO_METABOX_PREFIX . 'cd_your_listing', true);
                                            $cd_your_username = get_post_meta($id, GOLO_METABOX_PREFIX . 'cd_your_username', true);
                                            $cd_messager = get_post_meta($id, GOLO_METABOX_PREFIX . 'cd_messager', true);
                                            $cd_status = get_post_meta($id, GOLO_METABOX_PREFIX . 'cd_status', true);
                                            $verified_listing = get_post_meta($id, GOLO_METABOX_PREFIX . 'verified_listing', true);
                                            echo '<tr>';
                                            echo '<td><input type="checkbox" name="claim_item"></td>';
                                            echo '<td>' . $cd_your_name . '</td>';
                                            echo '<td>' . $cd_your_email . '</td>';
                                            echo '<td>' . $cd_your_username . '</td>';
                                            echo '<td><a href="' . $cd_your_listing . '" target="_Blank">' . $cd_your_listing . '</a></td>';
                                            echo '<td>' . $cd_messager . '</td>';
                                            if( $cd_status == 'pending' ){
                                                $value = 'pending';
                                            }else if ($verified_listing == 1) {
                                                $value = 'accept';
                                            } else {
                                                $value = 'refuse';
                                            }
                                            $data = 'data-status="' . $value . '"';
                                            echo '<td class="status"' . $data . '>' . $value . '</td>';
                                            echo '<td>
                                                <input type="submit" data-status="accept" data-place_id="' . $id . '" class="button button-primary" value="' . esc_attr( 'Accept', 'golo-framework' ) . '">
                                                <input type="submit" data-status="refuse" data-place_id="' . $id . '" class="button button-secondary" value="' . esc_attr( 'Refuse', 'golo-framework' ) . '">
                                                <input type="submit" data-status="delete" data-place_id="' . $id . '" class="button button-delete" value="' . esc_attr( 'Delete', 'golo-framework' ) . '"></td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr class="align-center">';
                                            echo '<td colspan="7">' . esc_attr( 'No result', 'golo-framework' ) . '</td>';
                                        echo '</tr>';
                                    }
                                    /* Restore original Post Data */
                                    wp_reset_postdata();
                                ?>
                            </tbody>
                        </table>
                        <div class="pagination">
                        <?php
                            $big = 999999999; // need an unlikely integer
                             
                            echo paginate_links( array(
                                'base' => admin_url( 'admin.php?page=claim_listing&paged=%#%' ),
                                'format' => '?paged=%#%',
                                'current' => max(1, $paged),
                                'total' => $the_query->max_num_pages,
                                'prev_text' => '<i class="las la-angle-left"></i>',
                                'next_text' => '<i class="las la-angle-right"></i>',
                            ) );
                        ?>
                        </div>
                        <div class="golo-loading-effect"><span class="golo-dual-ring small"></span></div>
                    </form>
                </div><!-- end .wrap-content -->
            </div>
            <?php
        }

        /**
         * Register post_type
         * @param $post_types
         * @return mixed
         */
        public function register_post_type($post_types)
        {
            $post_types['place'] = array(
                'label'           => esc_html__('Place', 'golo-framework'),
                'singular_name'   => esc_html__('Place', 'golo-framework'),
                'supports'        => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions', 'page-attributes', 'comments'),
                'menu_icon'       => 'dashicons-location',
                'can_export'      => true,
                'show_in_rest'    => true,
                'capability_type' => 'place',
                'map_meta_cap'    => true,
                'rewrite'         => array(
                    'slug' => apply_filters('golo_place_slug', 'place'),
                ),
            );

            $post_types['booking'] = array(
                'label'           => esc_html__('Booking', 'golo-framework'),
                'singular_name'   => esc_html__('Booking', 'golo-framework'),
                'supports'        => array('title', 'author'),
                'menu_icon'       => 'dashicons-calendar-alt',
                'can_export'      => true,
                'show_in_rest'    => true,
                'capability_type' => 'booking',
                'map_meta_cap'    => true,
                'rewrite'         => array(
                    'slug' => apply_filters('golo_booking_slug', 'booking'),
                ),
            );

            $post_types['package'] = array(
                'label'           => esc_html__('Packages', 'golo-framework'),
                'singular_name'   => esc_html__('Package', 'golo-framework'),
                'supports'        => array('title', 'thumbnail'),
                'menu_icon'       => 'dashicons-archive',
                'can_export'      => true,
                'show_in_rest'    => true,
                'capability_type' => 'package',
                'map_meta_cap'    => true,
                'rewrite'         => array(
                    'slug' => apply_filters('golo_package_slug', 'package'),
                ),
            );

            $post_types['user_package'] = array(
                'label' => esc_html__('User Packages', 'golo-framework'),
                'singular_name' => esc_html__('User Packages', 'golo-framework'),
                'supports' => array('title', 'excerpt'),
                'menu_icon' => 'dashicons-money',
                'can_export' => true,
                'capabilities' => $this->get_user_package_capabilities(),
                'map_meta_cap'  => true,
                'rewrite' => array(
                    'slug' => apply_filters('golo_user_package_slug', 'user_package'),
                ),
            );

            $post_types['invoice'] = array(
                'label'         => esc_html__('Invoices', 'golo-framework'),
                'singular_name' => esc_html__('Invoice', 'golo-framework'),
                'supports'      => array('title', 'excerpt'),
                'menu_icon'     => 'dashicons-list-view',
                'capabilities'  => $this->get_invoice_capabilities(),
                'map_meta_cap'  => true,
                'rewrite'       => array(
                    'slug' => apply_filters('golo_invoice_slug', 'invoice'),
                ),
            );
            
            return $post_types;
        }

        /**
         * Register place post status
         */
        public function register_post_status()
        {
            register_post_status('expired', array(
                'label' => _x('Expired', 'post status', 'golo-framework'),
                'public' => true,
                'protected' => true,
                'exclude_from_search' => true,
                'show_in_admin_all_list' => true,
                'show_in_admin_status_list' => true,
                'label_count' => _n_noop('Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>', 'golo-framework'),
            ));

            register_post_status('hidden', array(
                'label' => _x('Hidden', 'post status', 'golo-framework'),
                'public' => true,
                'protected' => true,
                'exclude_from_search' => true,
                'show_in_admin_all_list' => true,
                'show_in_admin_status_list' => true,
                'label_count' => _n_noop('Hidden <span class="count">(%s)</span>', 'Hidden <span class="count">(%s)</span>', 'golo-framework'),
            ));

            register_post_status('canceled', array(
                'label' => _x('Canceled', 'post status', 'golo-framework'),
                'public' => true,
                'protected' => true,
                'exclude_from_search' => true,
                'show_in_admin_all_list' => true,
                'show_in_admin_status_list' => true,
                'label_count' => _n_noop('Canceled <span class="count">(%s)</span>', 'Canceled <span class="count">(%s)</span>', 'golo-framework'),
            ));
        }

        /**
         * Get invoice capabilities
         * @return mixed
         */
        private function get_invoice_capabilities()
        {
            $caps = array(
                'create_posts' => 'do_not_allow',
                'edit_post'    => 'edit_invoices',
                'delete_posts' => 'delete_invoices'
            );
            return apply_filters('get_invoice_capabilities', $caps);
        }

        /**
         * Get user_package capabilities
         * @return mixed
         */
        private function get_user_package_capabilities()
        {
            $caps = array(
                'create_posts' => 'do_not_allow',
                'edit_post' => 'edit_user_packages',
                'delete_posts' => 'do_not_allow'
            );
            return apply_filters('get_user_package_capabilities', $caps);
        }

        /**
         * Register taxonomy
         * @param $taxonomies
         * @return mixed
         */
        public function register_taxonomy($taxonomies)
        {
            $taxonomies['place-type'] = array(
                'post_type'     => 'place',
                'hierarchical'  => true,
                'show_in_rest'  => true,
                'label'         => esc_html__('Type', 'golo-framework'),
                'singular_name' => esc_html__('Place Type', 'golo-framework'),
                'rewrite'       => array(
                    'slug' => apply_filters('golo_place_type_slug', 'place-type'),
                ),
            );
            $taxonomies['place-categories'] = array(
                'post_type'     => 'place',
                'hierarchical'  => true,
                'show_in_rest'  => true,
                'label'         => esc_html__('Categories', 'golo-framework'),
                'singular_name' => esc_html__('Place Categories', 'golo-framework'),
                'rewrite'       => array(
                    'slug' => apply_filters('golo_place_categories_slug', 'place-categories'),
                ),
            );
            $taxonomies['place-amenities'] = array(
                'post_type'     => 'place',
                'hierarchical'  => true,
                'show_in_rest'  => true,
                'label'         => esc_html__('Amenities', 'golo-framework'),
                'singular_name' => esc_html__('Place Amenities', 'golo-framework'),
                'rewrite'       => array(
                    'slug' => apply_filters('golo_place_amenities_slug', 'place-amenities'),
                ),
            );
            $taxonomies['place-city'] = array(
                'post_type'     => 'place',
                'hierarchical'  => false,
                'show_in_rest'  => true,
                'meta_box_cb'   => array($this, 'taxonomy_select_meta_box'),
                'label'         => esc_html__('City / Town', 'golo-framework'),
                'singular_name' => esc_html__('City / Town', 'golo-framework'),
                'rewrite'       => array(
                    'slug' => apply_filters('golo_place_city_slug', 'place-city'),
                ),
            );
            $taxonomies['place-neighborhood'] = apply_filters('golo_register_taxonomy_place_neighborhood', array(
                'post_type' => 'place',
                'hierarchical' => false,
                'meta_box_cb' => array($this, 'taxonomy_select_meta_box'),
                'label' => esc_html__('Neighborhood', 'golo-framework'),
                'singular_name' => esc_html__('Neighborhood', 'golo-framework'),
                'rewrite' => array(
                    'slug' => apply_filters('golo_place_neighborhood_slug', 'place-neighborhood'),
                ),
            ));
            return $taxonomies;
        }

        /**
         * taxonomy_select_meta_box
         */
        public function taxonomy_select_meta_box($post, $box)
        {
            $defaults = array('taxonomy' => 'category');

            if (!isset($box['args']) || !is_array($box['args']))
                $args = array();
            else
                $args = $box['args'];

            extract(wp_parse_args($args, $defaults), EXTR_SKIP);
            $tax = get_taxonomy($taxonomy);
            $selected = wp_get_object_terms($post->ID, $taxonomy, array('fields' => 'ids'));
            $hierarchical = $tax->hierarchical;
            ?>
            <div id="taxonomy-<?php echo esc_attr($taxonomy); ?>" class="selectdiv golo-place-select-meta-box-wrap">
                <?php if (current_user_can($tax->cap->edit_terms)): ?>
                    <?php
                    $class = 'widefat';
                    if ($taxonomy == 'place-state') {
                        $class .= ' golo-place-state-ajax';
                    } elseif ($taxonomy == 'place-city') {
                        $class .= ' golo-place-city-ajax';
                    } elseif (($taxonomy == 'place-neighborhood')) {
                        $class .= ' golo-place-neighborhood-ajax';
                    }
                    if ($hierarchical) {
                        wp_dropdown_categories( array(
                            'taxonomy'        => $taxonomy,
                            'class'           => $class,
                            'hide_empty'      => false,
                            'name'            => "tax_input[$taxonomy][]",
                            'selected'        => count($selected) >= 1 ? $selected[0] : '',
                            'orderby'         => 'name',
                            'hierarchical'    => false,
                            'show_option_all' => esc_html__('None', 'golo-framework')
                        ));
                    } else {
                        ?>
                        <select name="<?php echo "tax_input[$taxonomy][]"; ?>" class="<?php echo esc_attr($class); ?>"
                                data-selected="<?php echo golo_get_taxonomy_slug_by_post_id($post->ID, $taxonomy); ?>">
                            <option value=""><?php esc_html_e('None', 'golo-framework'); ?></option>
                            <?php
                            $terms = get_categories(
                                array(
                                    'taxonomy'   => $taxonomy,
                                    'orderby'    => 'name',
                                    'order'      => 'ASC',
                                    'hide_empty' => false,
                                    'parent'     => 0
                                )
                            );
                            foreach ($terms as $term): ?>
                                <option value="<?php echo esc_attr($term->slug); ?>" <?php echo selected($term->term_id, count($selected) >= 1 ? $selected[0] : ''); ?>><?php echo esc_html($term->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php
                    }
                    ?>
                <?php endif; ?>
            </div>
            <?php
        }

        /**
         * Register term_meta
         * @param $configs
         * @return mixed
         */
        public function register_term_meta($configs)
        {
            $countries = golo_get_selected_countries();
            $default_country = golo_get_option('default_country', 'US');

            $configs['place-amenities-settings'] = apply_filters('golo_register_term_meta_place_type', array(
                'name'     => esc_html__('Taxonomy Setting', 'golo-framework'),
                'layout'   => 'horizontal',
                'taxonomy' => array('place-amenities'),
                'fields'   => array(
                    array(
                        'id'      => 'place_amenities_icon',
                        'title'   => esc_html__('Icon image', 'golo-framework'),
                        'desc'    => esc_html__('Icon amenities', 'golo-framework'),
                        'type'    => 'image',
                        'default' => '',
                    ),
                )
            ));

            $configs['place-categories-settings'] = apply_filters('golo_register_term_meta_place_categories', array(
                'name'     => esc_html__('Taxonomy Setting', 'golo-framework'),
                'layout'   => 'horizontal',
                'taxonomy' => array('place-categories'),
                'fields'   => array(
                    array(
                        'id'    => 'place_category_banner_image',
                        'title' => esc_html__('Banner Image', 'golo-framework'),
                        'type'  => 'image',
                    ),
                    array(
                        'id'      => 'place_categories_icon_marker',
                        'title'   => esc_html__('Icon Map Marker', 'golo-framework'),
                        'desc'    => esc_html__('Icon map marker', 'golo-framework'),
                        'type'    => 'image',
                        'default' => '',
                    ),
                )
            ));

            $configs['place-city-settings'] = apply_filters('golo_register_term_meta_place_city', array(
                'name'     => '',
                'layout'   => 'horizontal',
                'taxonomy' => array('place-city'),
                'fields'   => array(
                    array(
                        'id'      => 'place_city_country',
                        'title'   => esc_html__('Country', 'golo-framework'),
                        'default' => $default_country,
                        'type'    => 'select',
                        'options' => $countries,
                    ),
                    array(
                        'id'    => 'place_city_featured_image',
                        'title' => esc_html__('Featured Image', 'golo-framework'),
                        'type'  => 'image',
                    ),
                    array(
                        'id'    => 'place_city_banner_image',
                        'title' => esc_html__('Banner Image', 'golo-framework'),
                        'type'  => 'image',
                    ),
                    array(
                        'id'    => 'place_city_banner_intro',
                        'title' => esc_html__('Banner Intro', 'golo-framework'),
                        'type'  => 'text',
                    ),
                    array(
                        'type'   => 'row',
                        'col'    => '6',
                        'fields' => array(
                            array(
                                'title'      => __('Currency', 'golo-framework'),
                                'id'         => 'place_city_currency',
                                'type'       => 'text',
                            ),
                            array(
                                'title'      => __('Language', 'golo-framework'),
                                'id'         => 'place_city_language',
                                'type'       => 'text',
                            ),
                        )
                    ),
                    array(
                        'type'   => 'row',
                        'col'    => '6',
                        'fields' => array(
                            array(
                                'title'      => __('Time to visit', 'golo-framework'),
                                'id'         => 'place_city_visit_time',
                                'type'       => 'text',
                            ),
                            array(
                                'title'      => __('Youtube URL', 'golo-framework'),
                                'id'         => 'place_city_youtube_url',
                                'type'       => 'text',
                                'input_type' => 'url',
                            ),
                        )
                    ),
                    array(
                        'id'    => 'place_city_address',
                        'title' => esc_html__('Google Map Address', 'golo-framework'),
                        'type'  => 'map',
                    ),
                )
            ));
            return apply_filters('golo_register_term_meta', $configs);
        }

        /**
         * Register meta boxes
         * @param $configs
         * @return mixed
         */
        public function register_meta_boxes($configs)
        {
            $meta_prefix   = GOLO_METABOX_PREFIX;
            $dec_point     = golo_get_option('decimal_separator', '.');
            $currency_sign = golo_get_option('currency_sign', '$');
            $low_price     = golo_get_option('low_price', '$');
            $medium_price  = golo_get_option('medium_price', '$$');
            $high_price    = golo_get_option('high_price', '$$$');
            $format_number = '^[0-9]+([' . $dec_point . '][0-9]+)?$';

            $cf7_chosen_form = golo_get_option('cf7_contact_form', '');

            if (!empty($cf7_chosen_form)) {
                $cf7_chosen_form = explode(',', $cf7_chosen_form);
            }

            $cf7_field = get_option('field-name');
            $cf7_list  = get_posts(array(
                'post_type'     => 'wpcf7_contact_form',
                'include'		=> $cf7_chosen_form,
                'numberposts'   => -1
            ));
            
            $cf7_forms = array('' => 'None');
            $cf7_default = '';
            if( !empty($cf7_list[0]->ID) ) {
                $cf7_default = $cf7_list[0]->ID;
            }
            foreach ($cf7_list as $cf7) {
                $cf7_forms[$cf7->ID] = $cf7->post_title. " (". $cf7->ID .")";
            }

            $enable_time_format_24 = golo_get_option('enable_time_format_24', 0);
            if ($enable_time_format_24 == 1) {
                $ex = esc_html__( 'Opening Time (Ex: 9:00 - 17:00 OR 9:00 - 11:00 & 14:00 - 17:00)', 'golo-framework' );
            } else {
                $ex = esc_html__( 'Opening Time (Ex: 9:00 AM - 5:00 PM OR 9:00 AM - 11:00 AM & 2:00 PM - 5:00 PM)', 'golo-framework' );
            }

            $menu_types = array('' => 'None');
            if (isset($_GET['post']) && $_GET['post'] != '') {
                $types = get_post_meta(intval($_GET['post']), GOLO_METABOX_PREFIX . 'menu_types_name', true);

                if ($types) {
                    foreach ($types as $key => $value) {
                        $menu_types[$value] = $value;
                    }
                }
            }

            $render_additional_fields = golo_render_additional_fields();
            $additional_fields = array();
            if (count($render_additional_fields) > 0) {
                $additional_fields = array(
                    array(
                        'id' => "{$meta_prefix}additional_fields_tab",
                        'title' => esc_html__('Additional Fields', 'golo-framework'),
                        'icon' => 'dashicons dashicons-welcome-add-page',
                        'fields' => $render_additional_fields
                    ),
                );
            }
            
            $default_booking_type = golo_get_option('default_booking_type', '');
            $configs['place_meta_boxes'] = apply_filters('golo_register_meta_boxes_place', array(
                'name'      => esc_html__('Place Information', 'golo-framework'),
                'post_type' => array('place'),
                'section'   => array_merge(
                    apply_filters('golo_register_meta_boxes_place_top', array()),
                    apply_filters('golo_register_meta_boxes_place_main',
                        array_merge(
                            array(
                                array(
                                    'id'     => "{$meta_prefix}details_tab",
                                    'title'  => esc_html__('Basic Infomation', 'golo-framework'),
                                    'icon'   => 'dashicons-admin-home',
                                    'fields' => array(
                                        array(
                                            'type'   => 'row',
                                            'col'    => '6',
                                            'fields' => array(
                                                array(
                                                    'id'      => "{$meta_prefix}place_price_short",
                                                    'title'   => esc_html__('Price', 'golo-framework'),
                                                    'desc'    => esc_html__('Example Value: 50', 'golo-framework'),
                                                    'type'    => 'text',
                                                    'pattern' => "{$format_number}",
                                                    'default' => '',
                                                    'col'     => '3',
                                                ),
                                                array(
                                                    'id'      => "{$meta_prefix}place_price_unit",
                                                    'title'   => esc_html__('Price Unit', 'golo-framework'),
                                                    'type'    => 'button_set',
                                                    'options' => array(
                                                        'h' => esc_html__('Hour', 'golo-framework'),
                                                        'd' => esc_html__('Day', 'golo-framework'),
                                                        'm' => esc_html__('Month ', 'golo-framework'),
                                                    ),
                                                    'default' => 'm',
                                                    'col'     => '4',
                                                ),
                                                array(
                                                    'id'      => "{$meta_prefix}place_price_range",
                                                    'title'   => esc_html__('Price Range', 'golo-framework'),
                                                    'type'    => 'button_set',
                                                    'options' => array(
                                                        '0' => esc_html__('None', 'golo-framework'),
                                                        '1' => esc_html__('Free', 'golo-framework'),
                                                        '2' => $low_price,
                                                        '3' => $medium_price,
                                                        '4' => $high_price,
                                                    ),
                                                    'default' => 'none',
                                                    'col'     => '5',
                                                ),
                                            )
                                        ),

                                        array(
                                            'type' => 'divide'
                                        ),

                                        array(
                                            'type' => 'row',
                                            'col' => '12',
                                            'fields' => array(
                                                array(
                                                    'id'      => "{$meta_prefix}place_booking_type",
                                                    'title'   => esc_html__('Booking Type ?', 'golo-framework'),
                                                    'type'    => 'button_set',
                                                    'options' => array(
                                                        ''        => esc_html__('None', 'golo-framework'),
                                                        'info'    => esc_html__('Booking Contact', 'golo-framework'),
                                                        'link'    => esc_html__('Booking Affiliate', 'golo-framework'),
                                                        'banner'  => esc_html__('Banner Link', 'golo-framework'),
                                                        'form'    => esc_html__('Booking Form', 'golo-framework'),
                                                        'contact' => esc_html__('Contact Information', 'golo-framework'),
                                                    ),
                                                    'default' => $default_booking_type,
                                                ),
                                                array(
                                                    'id'       => "{$meta_prefix}place_booking",
                                                    'title'    => esc_html__('URL 1', 'golo-framework'),
                                                    'type'     => 'text',
                                                    'default'  => '',
                                                    'col'      => '6',
                                                    'required' => array("{$meta_prefix}place_booking_type", '=', 'link'),
                                                ),
                                                array(
                                                    'id'       => "{$meta_prefix}place_booking_site",
                                                    'title'    => esc_html__('Booking Site 1', 'golo-framework'),
                                                    'desc'     => esc_html__('Example: Booking.com', 'golo-framework'),
                                                    'type'     => 'text',
                                                    'default'  => '',
                                                    'col'      => '6',
                                                    'required' => array("{$meta_prefix}place_booking_type", '=', 'link'),
                                                ),
                                                array(
                                                    'id'       => "{$meta_prefix}place_booking_2",
                                                    'title'    => esc_html__('URL 2', 'golo-framework'),
                                                    'type'     => 'text',
                                                    'default'  => '',
                                                    'col'      => '6',
                                                    'required' => array("{$meta_prefix}place_booking_type", '=', 'link'),
                                                ),
                                                array(
                                                    'id'       => "{$meta_prefix}place_booking_site_2",
                                                    'title'    => esc_html__('Booking Site 2', 'golo-framework'),
                                                    'desc'     => esc_html__('Example: Booking.com', 'golo-framework'),
                                                    'type'     => 'text',
                                                    'default'  => '',
                                                    'col'      => '6',
                                                    'required' => array("{$meta_prefix}place_booking_type", '=', 'link'),
                                                ),
                                                array(
                                                    'id'       => "{$meta_prefix}place_booking_banner",
                                                    'title'    => esc_html__('Image', 'golo-framework'),
                                                    'type'     => 'image',
                                                    'col'      => '6',
                                                    'required' => array("{$meta_prefix}place_booking_type", '=', 'banner'),
                                                ),
                                                array(
                                                    'id'       => "{$meta_prefix}place_booking_banner_url",
                                                    'title'    => esc_html__('URL', 'golo-framework'),
                                                    'type'     => 'text',
                                                    'default'  => '',
                                                    'col'      => '6',
                                                    'required' => array("{$meta_prefix}place_booking_type", '=', 'banner'),
                                                ),
                                                array(
                                                    'id'       => "{$meta_prefix}place_booking_form",
                                                    'title'    => esc_html__('Contact Information', 'golo-framework'),
                                                    'desc'     => esc_html__('Custom form by Enquiry', 'golo-framework'),
                                                    'type'     => 'select',
                                                    'options'  => $cf7_forms,
                                                    'default'  => $cf7_default,
                                                    'required' => array("{$meta_prefix}place_booking_type", '=', 'contact'),
                                                ),
                                            )
                                        ),

                                        array(
                                            'type' => 'divide'
                                        ),

                                        array(
                                            'type'   => 'row',
                                            'col'    => '6',
                                            'fields' => array(
                                                array(
                                                    'id'      => "{$meta_prefix}place_phone",
                                                    'title'   => esc_html__('Phone 1', 'golo-framework'),
                                                    'type'    => 'text',
                                                    'default' => '',
                                                ),
                                                array(
                                                    'id'      => "{$meta_prefix}place_phone2",
                                                    'title'   => esc_html__('Phone 2', 'golo-framework'),
                                                    'type'    => 'text',
                                                    'default' => '',
                                                ),
                                                array(
                                                    'id'      => "{$meta_prefix}place_email",
                                                    'title'   => esc_html__('Email', 'golo-framework'),
                                                    'type'    => 'text',
                                                    'default' => '',
                                                ),
                                                array(
                                                    'id'      => "{$meta_prefix}place_website",
                                                    'title'   => esc_html__('Website', 'golo-framework'),
                                                    'type'    => 'text',
                                                    'default' => '',
                                                ),
                                                array(
                                                    'id'      => "{$meta_prefix}place_identity",
                                                    'title'   => esc_html__('Place ID', 'golo-framework'),
                                                    'desc'    => esc_html__('Place ID will help to search place directly (default=postId)', 'golo-framework'),
                                                    'type'    => 'text',
                                                    'default' => '',
                                                ),
                                                array(
                                                    'id'      => "{$meta_prefix}google_review_placeid",
                                                    'title'   => esc_html__('Place ID for Google Review', 'golo-framework'),
                                                    'type'    => 'text',
                                                    'default' => '',
                                                    'desc' => esc_html__('Place ID will help display Google review', 'golo-framework'),
                                                ),
                                                array(
                                                    'id'      => "{$meta_prefix}place_facebook",
                                                    'title'   => esc_html__('Facebook', 'golo-framework'),
                                                    'type'    => 'text',
                                                    'default' => '',
                                                ),
                                                array(
                                                    'id'      => "{$meta_prefix}place_instagram",
                                                    'title'   => esc_html__('Instagram', 'golo-framework'),
                                                    'type'    => 'text',
                                                    'default' => '',
                                                ),
                                                array(
                                                    'id'      => "{$meta_prefix}place_twitter",
                                                    'title'   => esc_html__('Twitter', 'golo-framework'),
                                                    'type'    => 'text',
                                                    'default' => '',
                                                ),
                                            )
                                        ),

                                        array(
                                            'id' => "{$meta_prefix}additional_detail",
                                            'type' => 'repeater',
                                            'title' => esc_html__('Additional details:', 'golo-framework'),
                                            'col' => '6',
                                            'sort' => true,
                                            'fields' => array(
                                                array(
                                                    'id' => "{$meta_prefix}additional_detail_icon",
                                                    'title' => esc_html__('Icon ( https://icons8.com/line-awesome )', 'golo-framework'),
                                                    'desc' => esc_html__('Enter additional icon ( Example: "lab la-twitter" )', 'golo-framework'),
                                                    'type' => 'text',
                                                    'default' => '',
                                                    'col' => '4',
                                                ),
                                                array(
                                                    'id'      => "{$meta_prefix}additional_detail_name",
                                                    'title'   => esc_html__('Name', 'golo-framework'),
                                                    'desc' => esc_html__('Enter additional name', 'golo-framework'),
                                                    'type'    => 'text',
                                                    'default' => '',
                                                    'col' => '4',
                                                ),
                                                array(
                                                    'id' => "{$meta_prefix}additional_detail_url",
                                                    'title' => esc_html__('Url', 'golo-framework'),
                                                    'desc' => esc_html__('Enter additional url', 'golo-framework'),
                                                    'type' => 'text',
                                                    'default' => '',
                                                    'col' => '4',
                                                ),
                                            )
                                        ),

                                        array(
                                            'type' => 'divide'
                                        ),

                                        array(
                                            'type'   => 'row',
                                            'col'    => '6',
                                            'fields' => array(
                                                array(
                                                    'id'      => "{$meta_prefix}opening_monday",
                                                    'title'   => esc_html__('Title', 'golo-framework'),
                                                    'type'    => 'text',
                                                    'default' => esc_html__('Monday', 'golo-framework'),
                                                ),
                                                array(
                                                    'id'      => "{$meta_prefix}opening_monday_time",
                                                    'title'   => $ex,
                                                    'type'    => 'text',
                                                    'default' => '',
                                                ),
                                            ),
                                        ),

                                        array(
                                            'type'   => 'row',
                                            'col'    => '6',
                                            'fields' => array(
                                                array(
                                                    'id'      => "{$meta_prefix}opening_tuesday",
                                                    'type'    => 'text',
                                                    'default' => esc_html__('Tuesday', 'golo-framework'),
                                                ),
                                                array(
                                                    'id'      => "{$meta_prefix}opening_tuesday_time",
                                                    'type'    => 'text',
                                                    'default' => '',
                                                ),
                                            ),
                                        ),

                                        array(
                                            'type'   => 'row',
                                            'col'    => '6',
                                            'fields' => array(
                                                array(
                                                    'id'      => "{$meta_prefix}opening_wednesday",
                                                    'type'    => 'text',
                                                    'default' => esc_html__('Wednesday', 'golo-framework'),
                                                ),
                                                array(
                                                    'id'      => "{$meta_prefix}opening_wednesday_time",
                                                    'type'    => 'text',
                                                    'default' => '',
                                                ),
                                            ),
                                        ),

                                        array(
                                            'type'   => 'row',
                                            'col'    => '6',
                                            'fields' => array(
                                                array(
                                                    'id'      => "{$meta_prefix}opening_thursday",
                                                    'type'    => 'text',
                                                    'default' => esc_html__('Thursday', 'golo-framework'),
                                                ),
                                                array(
                                                    'id'      => "{$meta_prefix}opening_thursday_time",
                                                    'type'    => 'text',
                                                    'default' => '',
                                                ),
                                            ),
                                        ),

                                        array(
                                            'type'   => 'row',
                                            'col'    => '6',
                                            'fields' => array(
                                                array(
                                                    'id'      => "{$meta_prefix}opening_friday",
                                                    'type'    => 'text',
                                                    'default' => esc_html__('Friday', 'golo-framework'),
                                                ),
                                                array(
                                                    'id'      => "{$meta_prefix}opening_friday_time",
                                                    'type'    => 'text',
                                                    'default' => '',
                                                ),
                                            ),
                                        ),

                                        array(
                                            'type'   => 'row',
                                            'col'    => '6',
                                            'fields' => array(
                                                array(
                                                    'id'      => "{$meta_prefix}opening_saturday",
                                                    'type'    => 'text',
                                                    'default' => esc_html__('Saturday', 'golo-framework'),
                                                ),
                                                array(
                                                    'id'      => "{$meta_prefix}opening_saturday_time",
                                                    'type'    => 'text',
                                                    'default' => '',
                                                ),
                                            ),
                                        ),

                                        array(
                                            'type'   => 'row',
                                            'col'    => '6',
                                            'fields' => array(
                                                array(
                                                    'id'      => "{$meta_prefix}opening_sunday",
                                                    'type'    => 'text',
                                                    'default' => esc_html__('Sunday', 'golo-framework'),
                                                ),
                                                array(
                                                    'id'      => "{$meta_prefix}opening_sunday_time",
                                                    'type'    => 'text',
                                                    'default' => '',
                                                ),
                                            ),
                                        ),

                                        array(
                                            'type' => 'divide'
                                        ),

                                        array(
                                            'id' => "{$meta_prefix}yelp_review",
                                            'type' => 'repeater',
                                            'title' => esc_html__('Yelp review:', 'golo-framework'),
                                            'col' => '6',
                                            'sort' => true,
                                            'fields' => array(
                                                array(
                                                    'id' => "{$meta_prefix}yelp_review_title",
                                                    'title' => esc_html__('Title', 'golo-framework'),
                                                    'desc' => esc_html__('Enter title', 'golo-framework'),
                                                    'type' => 'text',
                                                    'default' => '',
                                                    'col' => '6',
                                                ),
                                                array(
                                                    'id' => "{$meta_prefix}yelp_review_type",
                                                    'title' => esc_html__('Type', 'golo-framework'),
                                                    'desc' => esc_html__('Enter type filter ( Example: restaurant, hotel, education... )', 'golo-framework'),
                                                    'type' => 'text',
                                                    'default' => '',
                                                    'col' => '6',
                                                ),
                                            )
                                        ),

                                        array(
                                            'type'   => 'row',
                                            'col'    => '6',
                                            'fields' => array(
                                                array(
                                                    'id'      => "{$meta_prefix}place_views_count",
                                                    'title'   => esc_html__('Views', 'golo-framework'),
                                                    'type'    => 'text',
                                                    'default' => 0,
                                                ),
                                            )
                                        ),

                                    )
                                )
                            ),
                            $additional_fields,
                            array(
                                array(
                                    'id' => "{$meta_prefix}menu_tabs",
                                    'title' => esc_html__('Menu', 'golo-framework'),
                                    'icon' => 'dashicons-editor-ul',
                                    'fields' => array(
                                        array(
                                            'id' => "{$meta_prefix}menu_enable",
                                            'title' => esc_html__('Enable Menu', 'golo-framework'),
                                            'type' => 'button_set',
                                            'options' => array(
                                                '1' => esc_html__('Yes', 'golo-framework'),
                                                '0' => esc_html__('No', 'golo-framework'),
                                            ),
                                            'default' => '0',
                                        ),
                                        array(
                                            'id' => "{$meta_prefix}menu_types",
                                            'type' => 'repeater',
                                            'title' => esc_html__('Menu Type', 'golo-framework'),
                                            'fields' => array(
                                                array(
                                                    'id' => "{$meta_prefix}menu_types_name",
                                                    'title' => esc_html__('Type Name', 'golo-framework'),
                                                    'desc' => esc_html__('Example Value: Starters, Main Course...', 'golo-framework'),
                                                    'type' => 'text',
                                                    'default' => '',
                                                ),
                                            )
                                        ),
                                        array(
                                            'id' => "{$meta_prefix}menu_tab",
                                            'type' => 'panel',
                                            'title' => esc_html__('Menu', 'golo-framework'),
                                            'sort' => true,
                                            'required' => array("{$meta_prefix}menu_enable", '=', '1'),
                                            'fields' => array(
                                                array(
                                                    'type' => 'row',
                                                    'col' => '12',
                                                    'fields' => array(
                                                        array(
                                                            'id'          => "{$meta_prefix}menu_title",
                                                            'title'       => esc_html__('Name', 'golo-framework'),
                                                            'desc'        => esc_html__('Example Value: Caesar Salad, Superior Room... ', 'golo-framework'),
                                                            'type'        => 'text',
                                                            'default'     => '',
                                                            'panel_title' => true,
                                                            'col'         => '9',
                                                        ),
                                                        array(
                                                            'id'      => "{$meta_prefix}menu_price",
                                                            'title'   => esc_html__('Price', 'golo-framework'),
                                                            'desc'    => esc_html__('Example Value: $11', 'golo-framework'),
                                                            'type'    => 'text',
                                                            'default' => '',
                                                            'col'     => '3',
                                                        ),
                                                    )
                                                ),
                                                array(
                                                    'id' => "{$meta_prefix}menu_description",
                                                    'title' => esc_html__('Description', 'golo-framework'),
                                                    'type' => 'editor',
                                                    'default' => '',
                                                ),
                                                array(
                                                    'id' => "{$meta_prefix}menu_image",
                                                    'title' => esc_html__('Image', 'golo-framework'),
                                                    'type' => 'image',
                                                ),
                                                array(
                                                    'id'       => "{$meta_prefix}menu_item_type",
                                                    'title'    => esc_html__('Menu Type', 'golo-framework'),
                                                    'desc'     => esc_html__('Choose Menu Type', 'golo-framework'),
                                                    'type'     => 'select',
                                                    'options'  => $menu_types,
                                                    'default'  => '',
                                                ),
                                            )
                                        ),
                                    )
                                ),
                                array(
                                    'id'     => "{$meta_prefix}location_tab",
                                    'title'  => esc_html__('Location', 'golo-framework'),
                                    'icon'   => 'dashicons-location-alt',
                                    'fields' => array(
                                        array(
                                            'type'   => 'row',
                                            'fields' => array(
                                                array(
                                                    'id'    => "{$meta_prefix}place_address",
                                                    'title' => esc_html__('Place Address', 'golo-framework'),
                                                    'desc'  => esc_html__('Full Address', 'golo-framework'),
                                                    'type'  => 'text',
                                                    'col'   => 6
                                                ),
                                                array(
                                                    'id'    => "{$meta_prefix}place_zip",
                                                    'title' => esc_html__('Zip', 'golo-framework'),
                                                    'type'  => 'text',
                                                    'col'   => 3
                                                ),
                                                array(
                                                    'id'      => "{$meta_prefix}place_timezone",
                                                    'title'   => esc_html__('Time Zone', 'golo-framework'),
                                                    'type'    => 'select',
                                                    'options' => get_timezone_array(),
                                                    'default' => get_wp_timezone(),
                                                    'col'     => 3
                                                ),
                                            )
                                        ),
                                        array(
                                            'id'            => "{$meta_prefix}place_location",
                                            'title'         => esc_html__('Place Location at Google Map', 'golo-framework'),
                                            'desc'          => esc_html__('Drag the google map marker to point your place location. You can also use the address field above to search for your place', 'golo-framework'),
                                            'type'          => 'map',
                                            'address_field' => "{$meta_prefix}place_address",
                                        ),
                                    )
                                ),
                                array(
                                    'id'     => "{$meta_prefix}setting_tab",
                                    'title'  => esc_html__('Place Setting', 'golo-framework'),
                                    'icon'   => 'dashicons-admin-generic',
                                    'fields' => array(
                                        array(
                                            'id'      => "{$meta_prefix}place_featured",
                                            'title'   => esc_html__('Mark this place as featured ?', 'golo-framework'),
                                            'type'    => 'button_set',
                                            'options' => array(
                                                '1' => esc_html__('Yes', 'golo-framework'),
                                                '0' => esc_html__('No', 'golo-framework'),
                                            ),
                                            'default' => '0',
                                        ),
                                        array(
                                            'id'      => "{$meta_prefix}place_logged",
                                            'title'   => esc_html__('Logged in to view ?', 'golo-framework'),
                                            'desc'    => esc_html__('If "Yes" then only logged in user can view place details.'),
                                            'type'    => 'button_set',
                                            'options' => array(
                                                '1' => esc_html__('Yes', 'golo-framework'),
                                                '0' => esc_html__('No', 'golo-framework'),
                                            ),
                                            'default' => '0',
                                        ),
                                    )
                                ),
                                array(
                                    'id'     => "{$meta_prefix}gallery_tab",
                                    'title'  => esc_html__('Gallery Images', 'golo-framework'),
                                    'icon'   => 'dashicons-format-gallery',
                                    'fields' => array(
                                        array(
                                            'id'    => "{$meta_prefix}place_images",
                                            'title' => esc_html__('Place Gallery Images', 'golo-framework'),
                                            'type'  => 'gallery',
                                        ),
                                    )
                                ),
                                array(
                                    'id'     => "{$meta_prefix}video_tab",
                                    'title'  => esc_html__('Place Video', 'golo-framework'),
                                    'icon'   => 'dashicons-video-alt3',
                                    'fields' => array(
                                        array(
                                            'id'    => "{$meta_prefix}place_video_url",
                                            'title' => esc_html__('Video URL', 'golo-framework'),
                                            'desc'  => esc_html__('Input only URL. YouTube, Vimeo, SWF File and MOV File', 'golo-framework'),
                                            'type'  => 'text',
                                            'col'   => 12,
                                        ),
                                        array(
                                            'id'    => "{$meta_prefix}place_video_image",
                                            'title' => esc_html__('Video Image', 'golo-framework'),
                                            'type'  => 'gallery',
                                            'col'   => 12,
                                        ),
                                    )
                                ),
                                array(
                                    'id' => "{$meta_prefix}faq_tabs",
                                    'title' => esc_html__('FAQs', 'golo-framework'),
                                    'icon' => 'dashicons-editor-help',
                                    'fields' => array(
                                        array(
                                            'id' => "{$meta_prefix}faqs_enable",
                                            'title' => esc_html__('Enable FAQs', 'golo-framework'),
                                            'type' => 'button_set',
                                            'options' => array(
                                                '1' => esc_html__('Yes', 'golo-framework'),
                                                '0' => esc_html__('No', 'golo-framework'),
                                            ),
                                            'default' => '0',
                                        ),
                                        array(
                                            'id' => "{$meta_prefix}faqs_tab",
                                            'type' => 'panel',
                                            'title' => esc_html__('Faqs', 'golo-framework'),
                                            'sort' => true,
                                            'required' => array("{$meta_prefix}faqs_enable", '=', '1'),
                                            'fields' => array(
                                                array(
                                                    'id'          => "{$meta_prefix}faqs_title",
                                                    'title'       => esc_html__('Question', 'golo-framework'),
                                                    'type'        => 'text',
                                                    'default'     => '',
                                                    'panel_title' => true,
                                                ),
                                                array(
                                                    'id' => "{$meta_prefix}faqs_description",
                                                    'title' => esc_html__('Answer', 'golo-framework'),
                                                    'type' => 'textarea',
                                                    'default' => '',
                                                ),
                                            )
                                        ),
                                    )
                                ),
                            )
                        )
                    ),
                    apply_filters('golo_register_meta_boxes_place_bottom', array())
                ),
            ));

            $configs['booking_meta_boxes'] = apply_filters('golo_register_meta_boxes_booking', array(
                'name'      => esc_html__('Booking Information', 'golo-framework'),
                'post_type' => array('booking'),
                'fields' => array_merge(
                    apply_filters('golo_register_meta_boxes_booking_top', array()),
                    apply_filters('golo_register_meta_boxes_booking_main', array(
                        array(
                            'type' => 'row',
                            'col' => '4',
                            'fields' => array(
                                array(
                                    'id' => "{$meta_prefix}booking_item_name",
                                    'title' => esc_html__('Name', 'golo-framework'),
                                    'type' => 'text',
                                ),
                                array(
                                    'id' => "{$meta_prefix}booking_item_id",
                                    'title' => esc_html__('ID', 'golo-framework'),
                                    'type' => 'text',
                                ),
                                array(
                                    'id' => "{$meta_prefix}booking_item_author",
                                    'title' => esc_html__('Author ID', 'golo-framework'),
                                    'type' => 'text',
                                ),
                            )
                        ),
                        array(
                            'type' => 'divide'
                        ),
                        array(
                            'type' => 'row',
                            'col' => '4',
                            'fields' => array(
                                array(
                                    'id' => "{$meta_prefix}booking_adults",
                                    'title' => esc_html__('Adults', 'golo-framework'),
                                    'type' => 'text',
                                ),
                                array(
                                    'id' => "{$meta_prefix}booking_childrens",
                                    'title' => esc_html__('Childrens', 'golo-framework'),
                                    'type' => 'text',
                                ),
                            )
                        ),
                        array(
                            'type' => 'divide'
                        ),
                        array(
                            'type' => 'row',
                            'col' => '4',
                            'fields' => array(
                                array(
                                    'id' => "{$meta_prefix}booking_date",
                                    'title' => esc_html__('Date Booking', 'golo-framework'),
                                    'type' => 'text',
                                ),
                                array(
                                    'id' => "{$meta_prefix}booking_time",
                                    'title' => esc_html__('Time Booking', 'golo-framework'),
                                    'type' => 'text',
                                ),
                                array(
                                    'id' => "{$meta_prefix}place_coupon",
                                    'title' => esc_html__('Coupon', 'golo-framework'),
                                    'type' => 'text',
                                ),
                            )
                        ),
                    )),
                    apply_filters('golo_register_meta_boxes_booking_bottom', array())
                ),
            ));

            $configs['package_meta_boxes'] = array(
                'name' => esc_html__('Package Settings', 'golo-framework'),
                'post_type' => array('package'),
                'fields' => array(
                    array(
                        'type' => 'row',
                        'col' => '4',
                        'fields' => array(
                            array(
                                'id' => "{$meta_prefix}package_free",
                                'title' => esc_html__('Free package', 'golo-framework'),
                                'type' => 'button_set',
                                'options' => array(
                                    '1' => esc_html__('Yes', 'golo-framework'),
                                    '0' => esc_html__('No', 'golo-framework'),
                                ),
                                'default' => '0',
                            ),
                            array(
                                'id' => "{$meta_prefix}package_price",
                                'title' => esc_html__('Package Price', 'golo-framework'),
                                'type' => 'text',
                                'required' => array("{$meta_prefix}package_free", '=', '0'),
                            ),
                        )
                    ),
                    array(
                        'type' => 'divide'
                    ),
                    array(
                        'type' => 'row',
                        'col' => '4',
                        'fields' => array(
                            array(
                                'id' => "{$meta_prefix}package_unlimited_time",
                                'title' => esc_html__('Unlimited time', 'golo-framework'),
                                'type' => 'button_set',
                                'options' => array(
                                    '1' => esc_html__('Yes', 'golo-framework'),
                                    '0' => esc_html__('No', 'golo-framework'),
                                ),
                                'default' => '0',
                            ),
                            array(
                                'id' => "{$meta_prefix}package_time_unit",
                                'title' => esc_html__('Time Unit', 'golo-framework'),
                                'type' => 'button_set',
                                'options' => array(
                                    'Day' => esc_html__('Day', 'golo-framework'),
                                    'Week' => esc_html__('Week', 'golo-framework'),
                                    'Month' => esc_html__('Month', 'golo-framework'),
                                    'Year' => esc_html__('Year', 'golo-framework'),
                                ),
                                'default' => 'Day',
                                'required' => array("{$meta_prefix}package_unlimited_time", '=', '0'),
                            ),
                            array(
                                'id' => "{$meta_prefix}package_period",
                                'title' => esc_html__('Number Of "Time Unit"', 'golo-framework'),
                                'type' => 'text',
                                'default' => '1',
                                'pattern' => '[0-9]*',
                                'required' => array("{$meta_prefix}package_unlimited_time", '=', '0'),
                            ),
                        )
                    ),
                    array(
                        'type' => 'divide'
                    ),
                    array(
                        'type' => 'row',
                        'col' => '4',
                        'fields' => array(
                            array(
                                'id' => "{$meta_prefix}package_unlimited_listing",
                                'title' => esc_html__('Unlimited listings', 'golo-framework'),
                                'type' => 'button_set',
                                'options' => array(
                                    '1' => esc_html__('Yes', 'golo-framework'),
                                    '0' => esc_html__('No', 'golo-framework'),
                                ),
                                'default' => '0',
                            ),
                            array(
                                'id' => "{$meta_prefix}package_number_listings",
                                'title' => esc_html__('Number Listings', 'golo-framework'),
                                'type' => 'text',
                                'default' => '',
                                'pattern' => '[0-9]*',
                                'required' => array("{$meta_prefix}package_unlimited_listing", '=', '0'),
                            ),
                        )
                    ),
                    array(
                        'type' => 'divide'
                    ),
                    array(
                        'type' => 'row',
                        'col' => '4',
                        'fields' => array(
                            array(
                                'id' => "{$meta_prefix}package_number_featured",
                                'title' => esc_html__('Number Featured Listings', 'golo-framework'),
                                'type' => 'text',
                                'default' => '',
                                'pattern' => '[0-9]*',
                            ),
                            array(
                                'id' => "{$meta_prefix}package_order_display",
                                'title' => esc_html__('Order Number Display Via Frontend', 'golo-framework'),
                                'type' => 'text',
                                'default' => '1',
                                'pattern' => '[0-9]*',
                            ),
                        )
                    ),
                    array(
                        'type' => 'row',
                        'col' => '4',
                        'fields' => array(
                            array(
                                'id' => "{$meta_prefix}package_featured",
                                'title' => esc_html__('Is Featured?', 'golo-framework'),
                                'type' => 'button_set',
                                'options' => array(
                                    '1' => esc_html__('Yes', 'golo-framework'),
                                    '0' => esc_html__('No', 'golo-framework'),
                                ),
                                'default' => '0',
                            ),
                            array(
                                'id' => "{$meta_prefix}package_visible",
                                'title' => esc_html__('Is Visible?', 'golo-framework'),
                                'type' => 'button_set',
                                'options' => array(
                                    '1' => esc_html__('Yes', 'golo-framework'),
                                    '0' => esc_html__('No', 'golo-framework'),
                                ),
                                'default' => '1',
                            ),
                        )
                    ),
                ),
            );

            return apply_filters('golo_register_meta_boxes', $configs);
        }

        /**
         * Register options config
         * @param $configs
         * @return mixed
         */
        public function register_options_config($configs)
        {
            $configs[GOLO_OPTIONS_NAME] = array(
                'layout'      => 'horizontal',
                'page_title'  => esc_html__('Theme Options', 'golo-framework'),
                'menu_title'  => esc_html__('Theme Options', 'golo-framework'),
                'option_name' => GOLO_OPTIONS_NAME,
                'permission'  => 'edit_theme_options',
                'section'     => array_merge(
                    apply_filters('golo_register_options_config_top', array()),
                    array(
                        $this->general_option(),
                        $this->url_slugs_option(),
                        $this->additional_fields_option(),
                        $this->listing_option(),
                        $this->listing_card_option(),
                        $this->listing_page_option(),
                        $this->search_page_option(),
                        $this->single_place_page_option(),
                        $this->place_option(),
                        $this->search_option(),
                        $this->price_format_option(),
                        $this->google_map_option(),
                        $this->payment_option(),
                        $this->payment_complete_option(),
                        $this->login_option(),
                        $this->user_option(),
                        $this->setup_page(),
                        $this->email_management_option(),
                        $this->yelp_option(),
                        $this->coupon_option(),
                    ),
                    apply_filters('golo_register_options_config_bottom', array())
                )
            );
            return apply_filters('golo_register_options_config', $configs);
        }

        /**
         * @return mixed|void
         */
        private function general_option()
        {
            return apply_filters('golo_register_option_general', array(
                'id'     => 'golo_general_option',
                'title'  => esc_html__('General Option', 'golo-framework'),
                'icon'   => 'dashicons-admin-multisite',
                'fields' => array_merge(
                    apply_filters('golo_register_option_general_top', array()),
                    array(
                        array(
                            'id'       => 'enable_rtl_mode',
                            'type'     => 'button_set',
                            'title'    => esc_html__('Enable RTL Mode', 'golo-framework'),
                            'subtitle' => esc_html__('Enable/Disable RTL mode', 'golo-framework'),
                            'desc'     => '',
                            'options'  => array(
                                '1' => esc_html__('On', 'golo-framework'),
                                '0' => esc_html__('Off', 'golo-framework'),
                            ),
                            'default'  => '0'
                        ),
                        array(
                            'id' => 'header_script',
                            'type' => 'ace_editor',
                            'title' => esc_html__('Header Script', 'golo-framework'),
                            'subtitle' => esc_html__('Add custom scripts inside HEAD tag. You need to have a SCRIPT tag around scripts.', 'golo-framework'),
                            'default' => ''
                        ),
                        array(
                            'id' => 'footer_script',
                            'type' => 'ace_editor',
                            'title' => esc_html__('Footer Script', 'golo-framework'),
                            'subtitle' => esc_html__('Add custom scripts you might want to be loaded in the footer of your website. You need to have a SCRIPT tag around scripts.', 'golo-framework'),
                            'default' => ''
                        ),
                    ),
                    apply_filters('golo_register_option_general_bottom', array())
                )
            ));
        }

        /**
         * @return mixed|void
         */
        private function setup_page()
        {
            return apply_filters('golo_register_setup_page', array(
                'id'     => 'golo_setup_page',
                'title'  => esc_html__('Setup Page', 'golo-framework'),
                'icon'   => 'dashicons-admin-page',
                'fields' => array_merge(
                    apply_filters('golo_register_setup_page_top', array()),
                    array(
                        array(
                            'id' => 'golo_submit_place_page_id',
                            'title' => esc_html__('New Place', 'golo-framework'),
                            'type' => 'select',
                            'data' => 'page',
                            'data_args' => array(
                                'numberposts' => -1,
                            )
                        ),
                        array(
                            'id' => 'golo_my_places_page_id',
                            'title' => esc_html__('My Places Page', 'golo-framework'),
                            'type' => 'select',
                            'data' => 'page',
                            'data_args' => array(
                                'numberposts' => -1,
                            )
                        ),
                        array(
                            'id' => 'golo_my_profile_page_id',
                            'title' => esc_html__('My Profile Page', 'golo-framework'),
                            'type' => 'select',
                            'data' => 'page',
                            'data_args' => array(
                                'numberposts' => -1,
                            )
                        ),
                        array(
                            'id' => 'golo_my_wishlist_page_id',
                            'title' => esc_html__('My Whishlist Page', 'golo-framework'),
                            'type' => 'select',
                            'data' => 'page',
                            'data_args' => array(
                                'numberposts' => -1,
                            )
                        ),
                        array(
                            'id' => 'golo_my_booking_page_id',
                            'title' => esc_html__('My Booking Page', 'golo-framework'),
                            'type' => 'select',
                            'data' => 'page',
                            'data_args' => array(
                                'numberposts' => -1,
                            )
                        ),
                        array(
                            'id' => 'golo_bookings_page_id',
                            'title' => esc_html__('Booking Page', 'golo-framework'),
                            'type' => 'select',
                            'data' => 'page',
                            'data_args' => array(
                                'numberposts' => -1,
                            )
                        ),
                        array(
                            'id' => 'golo_dashboard_page_id',
                            'title' => esc_html__('Dashboard Page', 'golo-framework'),
                            'type' => 'select',
                            'data' => 'page',
                            'data_args' => array(
                                'numberposts' => -1,
                            )
                        ),
                        array(
                            'id' => 'golo_packages_page_id',
                            'title' => esc_html__('Package Page', 'golo-framework'),
                            'type' => 'select',
                            'data' => 'page',
                            'data_args' => array(
                                'numberposts' => -1,
                            )
                        ),
                        array(
                            'id' => 'golo_payment_page_id',
                            'title' => esc_html__('Payment Page', 'golo-framework'),
                            'type' => 'select',
                            'data' => 'page',
                            'data_args' => array(
                                'numberposts' => -1,
                            )
                        ),
                        array(
                            'id' => 'golo_payment_completed_page_id',
                            'title' => esc_html__('Payment Completed Page', 'golo-framework'),
                            'type' => 'select',
                            'data' => 'page',
                            'data_args' => array(
                                'numberposts' => -1,
                            )
                        ),
                    ),
                    apply_filters('golo_register_setup_page_bottom', array())
                )
            ));
        }

        /**
         * @return mixed|void
         */
        private function url_slugs_option()
        {
            return apply_filters('golo_register_option_url_slugs', array(
                'id' => 'golo_url_slugs_option',
                'title' => esc_html__('URL Slug', 'golo-framework'),
                'icon' => 'dashicons-admin-links',
                'fields' => array_merge(
                    apply_filters('golo_register_option_url_slugs_top', array()),
                    array(
                        array(
                            'id' => 'url_slug_info',
                            'type' => 'info',
                            'style' => 'info',
                            'title' => esc_html__('URL Slug Setting( Please go to Settings -> Permarlink -> Save after changing)', 'golo-framework'),
                        ),
                        array(
                            'id' => 'place_url_slug',
                            'title' => esc_html__('Place Slug', 'golo-framework'),
                            'type' => 'text',
                            'default' => 'place',
                        ),
                        array(
                            'id' => 'place_type_url_slug',
                            'title' => esc_html__('Place Type Slug', 'golo-framework'),
                            'type' => 'text',
                            'default' => 'place-type',
                        ),
                        array(
                            'id' => 'place_categories_url_slug',
                            'title' => esc_html__('Place Categories Slug', 'golo-framework'),
                            'type' => 'text',
                            'default' => 'place-categories',
                        ),
                        array(
                            'id' => 'place_amenities_url_slug',
                            'title' => esc_html__('Place Amenities Slug', 'golo-framework'),
                            'type' => 'text',
                            'default' => 'place-amenities',
                        ),
                        array(
                            'id' => 'place_city_url_slug',
                            'title' => esc_html__('City Slug', 'golo-framework'),
                            'type' => 'text',
                            'default' => 'place-city',
                        ),
                        array(
                            'id' => 'place_neighborhood_url_slug',
                            'title' => esc_html__('Neighborhood Slug', 'golo-framework'),
                            'type' => 'text',
                            'default' => 'place-neighborhood',
                        ),
                        array(
                            'id' => 'package_url_slug',
                            'title' => esc_html__('Package Slug', 'golo-framework'),
                            'type' => 'text',
                            'default' => 'package',
                        ),
                        array(
                            'id' => 'invoice_url_slug',
                            'title' => esc_html__('Invoice Slug', 'golo-framework'),
                            'type' => 'text',
                            'default' => 'invoice',
                        )
                    ),
                    apply_filters('golo_register_option_url_slugs_bottom', array())
                )
            ));
        }

        /**
         * @return mixed|void
         */
        private function additional_fields_option()
        {
            return apply_filters('golo_register_option_additional_fields', array(
                'id' => 'golo_additional_fields_option',
                'title' => esc_html__('Additional Fields', 'golo-framework'),
                'icon' => 'dashicons dashicons-welcome-add-page',
                'fields' => array_merge(
                    apply_filters('golo_register_option_additional_fields_top', array()),
                    apply_filters('golo_register_option_additional_fields_main', array(
                        array(
                            'id' => "additional_fields",
                            'type' => 'panel',
                            'title' => esc_html__('Property Field', 'golo-framework'),
                            'sort' => true,
                            'panel_title' => 'label',
                            'fields' => array(
                                array(
                                    'title' => esc_html__('Label', 'golo-framework'),
                                    'id' => "label",
                                    'type' => 'text',
                                    'default' => '',
                                ),
                                array(
                                    'title' => esc_html__('ID', 'golo-framework'),
                                    'id' => "id",
                                    'type' => 'text',
                                    'placeholder' => esc_html__('Enter field ID','golo-framework'),
                                    'desc' => esc_html__('ID values cannot be changed after being set!','golo-framework'),
                                    'default' => '',
                                ),
                                array(
                                    'title' => esc_html__('Field Type', 'golo-framework'),
                                    'id' => "field_type",
                                    'type' => 'select',
                                    'default' => 'text',
                                    'options' => array(
                                        'text' => esc_html__('Text', 'golo-framework'),
                                        'url' => esc_html__('Url', 'golo-framework'),
                                        'textarea' => esc_html__('Text Multiple Line', 'golo-framework'),
                                        'select' => esc_html__('Select', 'golo-framework'),
                                        'checkbox_list' => esc_html__('Checkbox List', 'golo-framework'),
                                        'radio' => esc_html__('Radio', 'golo-framework'),
                                    )
                                ),
                                array(
                                    'title' => esc_html__('Options Value', 'golo-framework'),
                                    'subtitle' => esc_html__('Input each per line', 'golo-framework'),
                                    'id' => "select_choices",
                                    'type' => 'textarea',
                                    'default' => '',
                                    'required' => array(
                                        "additional_fields_field_type",
                                        'in',
                                        array('checkbox_list', 'radio', 'select')
                                    ),
                                ),
                            )
                        )
                    )),
                    apply_filters('golo_register_option_additional_fields_bottom', array())
                )
            ));
        }

        function additional_details_field($meta_prefix) {
            if (!class_exists('Golo_Framework')) {
                return array(
                    'id' => "{$meta_prefix}additional_features",
                    'title' => esc_html__('Additional details:', 'golo-framework'),
                    'type' => 'custom',
                    'default' => array(),
                    'template' => GOLO_PLUGIN_DIR . '/includes/admin/templates/additional-details-field.php',
                );
            }
            return array(
                'id' => "{$meta_prefix}additional_features",
                'type' => 'repeater',
                'title' => esc_html__('Additional details:', 'golo-framework'),
                'col' => '6',
                'sort' => true,
                'fields' => array(
                    array(
                        'id' => "{$meta_prefix}additional_feature_title",
                        'title' => esc_html__('Title:', 'golo-framework'),
                        'desc' => esc_html__('Enter additional title', 'golo-framework'),
                        'type' => 'text',
                        'default' => '',
                        'col' => '5',
                    ),
                    array(
                        'id' => "{$meta_prefix}additional_feature_value",
                        'title' => esc_html__('Value', 'golo-framework'),
                        'desc' => esc_html__('Enter additional value', 'golo-framework'),
                        'type' => 'text',
                        'default' => '',
                        'col' => '7',
                    ),
                )
            );

        }

        /**
         * @return mixed|void
         */
        private function price_format_option()
        {
            return apply_filters('golo_register_option_price_format', array(
                'id' => 'golo_price_format_option',
                'title' => esc_html__('Price Format', 'golo-framework'),
                'icon' => 'dashicons-money',
                'fields' => array_merge(
                    apply_filters('golo_register_option_price_format_top', array()),
                    apply_filters('golo_register_option_price_format_main', array(
                        array(
                            'id'      => 'currency_sign',
                            'title'   => esc_html__('Currency Sign', 'golo-framework'),
                            'type'    => 'text',
                            'default' => '$',
                        ),
                        array(
                            'id'      => 'currency_position',
                            'title'   => esc_html__('Currency Sign Position', 'golo-framework'),
                            'type'    => 'select',
                            'options' => array(
                                'before' => esc_html__('Before ($59)', 'golo-framework'),
                                'after'  => esc_html__('After (59$)', 'golo-framework'),
                            ),
                            'default' => 'before',
                        ),
                        array(
                            'id' => 'thousand_separator',
                            'title' => esc_html__('Thousand Separator', 'golo-framework'),
                            'type' => 'text',
                            'default' => ',',
                        ),
                        array(
                            'id' => 'decimal_separator',
                            'title' => esc_html__('Decimal Separator', 'golo-framework'),
                            'type' => 'text',
                            'default' => '.',
                        ),
                        array(
                            'id' => 'low_price',
                            'title' => esc_html__('Low Price', 'golo-framework'),
                            'type' => 'text',
                            'default' => '$',
                        ),
                        array(
                            'id' => 'medium_price',
                            'title' => esc_html__('Medium Price', 'golo-framework'),
                            'type' => 'text',
                            'default' => '$$',
                        ),
                        array(
                            'id' => 'high_price',
                            'title' => esc_html__('High Price', 'golo-framework'),
                            'type' => 'text',
                            'default' => '$$$',
                        ),
                    )),
                    apply_filters('golo_register_option_price_format_bottom', array())
                )
            ));
        }

        /**
         * @return mixed|void
         */
        private function google_map_option()
        {
            $allowed_html = array(
                'i' => array(
                    'class' => array()
                ),
                'span' => array(
                    'class' => array()
                ),
                'a' => array(
                    'href'   => array(),
                    'title'  => array(),
                    'target' => array()
                )
            );
            return apply_filters('golo_register_option_google_map', array(
                'id' => 'golo_google_map_option',
                'title' => esc_html__('Maps Config', 'golo-framework'),
                'icon' => 'dashicons-admin-site',
                'fields' => array_merge(
                    apply_filters('golo_register_option_google_map_top', array()),
                    apply_filters('golo_register_option_google_map_main', array(
                        array(
                            'id' => 'map_type',
                            'title' => esc_html__('Maps Type', 'golo-framework'),
                            'type' => 'select',
                            'options' => array(
                                'google_map'            => esc_html__('Google Map', 'golo-framework'),
                                'mapbox'                => esc_html__('Mapbox', 'golo-framework'),
                                'openstreetmap'         => esc_html__('OpenStreetMap', 'golo-framework'),
                            ),
                            'default'  => 'google_map',
                        ),
                        array(
                            'id'       => 'map_ssl',
                            'title'    => esc_html__('Maps SSL', 'golo-framework'),
                            'subtitle' => esc_html__('Use maps with ssl', 'golo-framework'),
                            'type'     => 'button_set',
                            'options'  => array(
                                '1' => esc_html__('Yes', 'golo-framework'),
                                '0' => esc_html__('No', 'golo-framework'),
                            ),
                            'default'  => '0',
                        ),
                        array(
                            'id'        => 'googlemap_type',
                            'title'     => esc_html__('Google Maps Type', 'golo-framework'),
                            'type'      => 'select',
                            'options'   => array(
                                'roadmap'         => esc_html__('Roadmap', 'golo-framework'),
                                'satellite'       => esc_html__('Satellite', 'golo-framework'),
                                'hybrid'          => esc_html__('Hybrid', 'golo-framework'),
                                'terrain'         => esc_html__('Terrain', 'golo-framework'),
                            ),
                            'default'   => 'roadmap',
                            'required'  => array("map_type", '=', 'google_map'),
                        ),
                        array(
                            'id'       => 'googlemap_api_key',
                            'type'     => 'text',
                            'title'    => esc_html__('Google Maps API KEY', 'golo-framework'),
                            'subtitle' => esc_html__('Enter your google maps api key', 'golo-framework'),
                            'default'  => 'AIzaSyBvPDNG6pePr9iFpeRKaOlaZF_l0oT3lWk',
                            'required' => array("map_type", '=', 'google_map'),
                        ),
                        array(
                            'id'       => 'mapbox_api_key',
                            'type'     => 'text',
                            'title'    => esc_html__('Mapbox API KEY', 'golo-framework'),
                            'subtitle' => esc_html__('Enter your mapbox api key', 'golo-framework'),
                            'default'  => 'pk.eyJ1Ijoic2F5aTc3NDciLCJhIjoiY2tpcXRmYW1tMWpjMjJzbGllbThieTFlaCJ9.eDj6zNLBZpG-veFqXiyVPw',
                            'required' => array("map_type", '=', 'mapbox'),
                        ),
                        array(
                            'id'       => 'openstreetmap_api_key',
                            'type'     => 'text',
                            'title'    => esc_html__('OpenStreetMap API KEY', 'golo-framework'),
                            'subtitle' => esc_html__('Enter your OpenStreetMap api key', 'golo-framework'),
                            'default'  => 'pk.eyJ1Ijoic2F5aTc3NDciLCJhIjoiY2tpcXRmYW1tMWpjMjJzbGllbThieTFlaCJ9.eDj6zNLBZpG-veFqXiyVPw',
                            'required' => array("map_type", '=', 'openstreetmap'),
                        ),
                        array(
                            'id'      => 'map_zoom_level',
                            'type'    => 'text',
                            'title'   => esc_html__('Default Map Zoom', 'golo-framework'),
                            'default' => '12'
                        ),
                        array(
                            'id'       => 'map_pin_cluster',
                            'title'    => esc_html__('Pin Cluster', 'golo-framework'),
                            'subtitle' => esc_html__('Use pin cluster on map', 'golo-framework'),
                            'type'     => 'button_set',
                            'options'  => array(
                                '1' => esc_html__('Yes', 'golo-framework'),
                                '0' => esc_html__('No', 'golo-framework'),
                            ),
                            'default'  => '0',                            
                            'required' => array("map_type", '=', 'google_map'),
                        ),
                        array(
                            'id' => 'googlemap_style',
                            'type' => 'ace_editor',
                            'title' => esc_html__('Style for Google Map', 'golo-framework'),
                            'subtitle' => sprintf(__('Use %s https://snazzymaps.com/ %s to create styles', 'golo-framework'),
                                '<a href="https://snazzymaps.com/" target="_blank">',
                                '</a>'
                            ),
                            'default' => '',
                            'required' => array("map_type", '=', 'google_map'),
                        ),
                        array(
                            'id' => 'mapbox_style',
                            'title' => esc_html__('Style for Mapbox', 'golo-framework'),
                            'type' => 'select',
                            'options' => array(
                                'streets-v11' => esc_html__('Streets', 'golo-framework'),
                                'light-v10' => esc_html__('Light', 'golo-framework'),
                                'dark-v10' => esc_html__('Dark', 'golo-framework'),
                                'outdoors-v11' => esc_html__('Outdoors', 'golo-framework'),
                                'satellite-v9' => esc_html__('Satellite', 'golo-framework'),
                            ),
                            'required' => array("map_type", '=', 'mapbox'),
                        ),
                        array(
                            'id' => 'openstreetmap_style',
                            'title' => esc_html__('Style for OpenStreetMap', 'golo-framework'),
                            'type' => 'select',
                            'options' => array(
                                'streets-v11' => esc_html__('Streets', 'golo-framework'),
                                'light-v10' => esc_html__('Light', 'golo-framework'),
                                'dark-v10' => esc_html__('Dark', 'golo-framework'),
                                'outdoors-v11' => esc_html__('Outdoors', 'golo-framework'),
                                'satellite-v9' => esc_html__('Satellite', 'golo-framework'),
                            ),
                            'required' => array("map_type", '=', 'openstreetmap'),
                        ),
                        array(
                            'id' => 'map_default_position',
                            'title' => esc_html__('Default Position', 'smart-framework'),
                            'type' => 'map',
                        ),
                    )),
                    apply_filters('golo_register_option_google_map_bottom', array())
                )
            ));
        }

        /**
         * @return mixed|void
         */
        private function payment_option()
        {
            return apply_filters('golo_register_option_payment', array(
                'id' => 'golo_payment_option',
                'title' => esc_html__('Payment', 'golo-framework'),
                'icon' => 'dashicons-cart',
                'fields' => array_merge(
                    apply_filters('golo_register_option_payment_top', array()),
                    apply_filters('golo_register_option_payment_main', array(
                        array(
                            'id' => 'paid_submission_type',
                            'type' => 'select',
                            'title' => esc_html__('Paid Submission Type', 'golo-framework'),
                            'subtitle' => '',
                            'options' => array(
                                'no' => esc_html__('Free Submit', 'golo-framework'),
                                'per_package' => esc_html__('Pay Per Package', 'golo-framework')
                            ),
                            'default' => 'no',
                        ),
                        array(
                            'id' => 'payment_terms_condition',
                            'title' => esc_html__('Terms & Conditions', 'golo-framework'),
                            'subtitle' => esc_html__('Select terms & conditions page', 'golo-framework'),
                            'type' => 'select',
                            'data' => 'page',
                            'data_args' => array(
                                'numberposts' => -1,
                            )
                        ),
                        array(
                            'id' => 'currency_code',
                            'type' => 'text',
                            'required' => array('paid_submission_type', '!=', 'no'),
                            'title' => esc_html__('Currency Code', 'golo-framework'),
                            'subtitle' => esc_html__('Provide the currency code that you want to use. Ex. USD', 'golo-framework'),
                            'default' => 'USD',
                        ),
                        array(
                            'id' => 'golo_paypal',
                            'type' => 'info',
                            'style' => 'info',
                            'title' => esc_html__('Paypal Setting', 'golo-framework'),
                            'required' => array('paid_submission_type', '!=', 'no'),
                        ),
                        array(
                            'id' => 'enable_paypal',
                            'title' => esc_html__('Enable Paypal', 'golo-framework'),
                            'type' => 'button_set',
                            'options' => array(
                                '1' => esc_html__('Enabled', 'golo-framework'),
                                '0' => esc_html__('Disabled', 'golo-framework'),
                            ),
                            'default' => '0',
                            'required' => array('paid_submission_type', '!=', 'no'),
                        ),
                        array(
                            'id' => 'paypal_api',
                            'type' => 'select',
                            'required' => array(
                                array('enable_paypal', '=', '1'),
                                array('paid_submission_type', '!=', 'no')
                            ),
                            'title' => esc_html__('Paypal Api', 'golo-framework'),
                            'subtitle' => esc_html__('Sandbox = test API. LIVE = real payments API', 'golo-framework'),
                            'desc' => esc_html__('Update PayPal settings according to API type selection', 'golo-framework'),
                            'options' => array(
                                'sandbox' => esc_html__('Sandbox', 'golo-framework'),
                                'live' => esc_html__('Live', 'golo-framework')
                            ),
                            'default' => 'sandbox',
                        ),
                        array(
                            'id' => 'paypal_client_id',
                            'type' => 'text',
                            'required' => array(
                                array('enable_paypal', '=', '1'),
                                array('paid_submission_type', '!=', 'no')
                            ),
                            'title' => esc_html__('Paypal Client ID', 'golo-framework'),
                            'subtitle' => '',
                            'default' => '',
                        ),
                        array(
                            'id' => 'paypal_client_secret_key',
                            'type' => 'text',
                            'required' => array(
                                array('enable_paypal', '=', '1'),
                                array('paid_submission_type', '!=', 'no')
                            ),
                            'title' => esc_html__('Paypal Client Secret Key', 'golo-framework'),
                            'subtitle' => '',
                            'default' => '',
                        ),
                        array(
                            'id' => 'golo_stripe',
                            'type' => 'info',
                            'style' => 'info',
                            'title' => esc_html__('Stripe Setting', 'golo-framework'),
                            'required' => array('paid_submission_type', '!=', 'no'),
                        ),
                        array(
                            'id' => 'enable_stripe',
                            'title' => esc_html__('Enable Stripe', 'golo-framework'),
                            'type' => 'button_set',
                            'options' => array(
                                '1' => esc_html__('Enabled', 'golo-framework'),
                                '0' => esc_html__('Disabled', 'golo-framework'),
                            ),
                            'default' => '0',
                            'required' => array('paid_submission_type', '!=', 'no'),
                        ),
                        array(
                            'id' => 'stripe_secret_key',
                            'type' => 'text',
                            'required' => array(
                                array('enable_stripe', '=', '1'),
                                array('paid_submission_type', '!=', 'no')
                            ),
                            'title' => esc_html__('Stripe Secret Key', 'golo-framework'),
                            'subtitle' => esc_html__('Info is taken from your account at https://dashboard.stripe.com/login', 'golo-framework'),
                            'default' => '',
                        ),
                        array(
                            'id' => 'stripe_publishable_key',
                            'type' => 'text',
                            'required' => array(
                                array('enable_stripe', '=', '1'),
                                array('paid_submission_type', '!=', 'no')
                            ),
                            'title' => esc_html__('Stripe Publishable Key', 'golo-framework'),
                            'subtitle' => esc_html__('Info is taken from your account at https://dashboard.stripe.com/login', 'golo-framework'),
                            'default' => '',
                        ),
                        array(
                            'id' => 'golo_woocheckout',
                            'type' => 'info',
                            'style' => 'info',
                            'title' => esc_html__('Woocommerce Setting', 'golo-framework'),
                            'required' => array('paid_submission_type', '!=', 'no'),
                        ),
                        array(
                            'id' => 'enable_woocheckout',
                            'title' => esc_html__('Enable Woocommerce Checkout', 'golo-framework'),
                            'type' => 'button_set',
                            'options' => array(
                                '1' => esc_html__('Enabled', 'golo-framework'),
                                '0' => esc_html__('Disabled', 'golo-framework'),
                            ),
                            'default' => '0',
                            'required' => array('paid_submission_type', '!=', 'no'),
                        ),
                        array(
                            'id' => 'golo_wire_transfer',
                            'type' => 'info',
                            'style' => 'info',
                            'title' => esc_html__('Wire Transfer Setting', 'golo-framework'),
                            'required' => array('paid_submission_type', '!=', 'no'),
                        ),
                        array(
                            'id' => 'enable_wire_transfer',
                            'title' => esc_html__('Enable Wire Transfer', 'golo-framework'),
                            'type' => 'button_set',
                            'options' => array(
                                '1' => esc_html__('Enabled', 'golo-framework'),
                                '0' => esc_html__('Disabled', 'golo-framework'),
                            ),
                            'default' => '0',
                            'required' => array('paid_submission_type', '!=', 'no'),
                        ),
                        array(
                            'id' => 'wire_transfer_card_number',
                            'type' => 'text',
                            'required' => array(
                                array('enable_wire_transfer', '=', '1'),
                                array('paid_submission_type', '!=', 'no')
                            ),
                            'title' => esc_html__('Card Number', 'golo-framework'),
                            'subtitle' => '',
                            'default' => '',
                        ),
                        array(
                            'id' => 'wire_transfer_card_name',
                            'type' => 'text',
                            'required' => array(
                                array('enable_wire_transfer', '=', '1'),
                                array('paid_submission_type', '!=', 'no')
                            ),
                            'title' => esc_html__('Card Name', 'golo-framework'),
                            'subtitle' => '',
                            'default' => '',
                        ),
                        array(
                            'id' => 'wire_transfer_bank_name',
                            'type' => 'text',
                            'required' => array(
                                array('enable_wire_transfer', '=', '1'),
                                array('paid_submission_type', '!=', 'no')
                            ),
                            'title' => esc_html__('Bank Name', 'golo-framework'),
                            'subtitle' => '',
                            'default' => '',
                        ),
                    )),
                    apply_filters('golo_register_option_payment_bottom', array())
                )
            ));
        }

        /**
         * @return mixed|void
         */
        private function payment_complete_option()
        {
            return apply_filters('golo_register_option_payment_complete', array(
                'id' => 'golo_payment_complete_option',
                'title' => esc_html__('Payment Complete', 'golo-framework'),
                'icon' => 'dashicons-feedback',
                'fields' => array_merge(
                    apply_filters('golo_register_option_payment_complete_top', array()),
                    array(
                        array(
                            'id' => 'golo_thankyou',
                            'type' => 'info',
                            'style' => 'info',
                            'title' => esc_html__('Thank you note after payment via Paypal or Stripe', 'golo-framework'),
                        ),
                        array(
                            'id' => 'thankyou_title',
                            'type' => 'text',
                            'title' => esc_html__('Title', 'golo-framework'),
                            'default' => esc_html__('Thank you for your purchase', 'golo-framework'),
                        ),
                        array(
                            'id' => 'thankyou_content',
                            'title' => esc_html__('Thank-you Content', 'golo-framework'),
                            'type' => 'editor',
                            'default' => '',
                        ),
                    ),
                    apply_filters('golo_register_option_payment_complete_bottom', array())
                )
            ));
        }

        /**
         * @return mixed|void
         */
        private function login_option()
        {
            return apply_filters('golo_register_option_login', array(
                'id'     => 'golo_login_option',
                'title'  => esc_html__('Login Option', 'golo-framework'),
                'icon'   => 'dashicons-admin-users',
                'fields' => array_merge(
                    apply_filters('golo_register_option_login_top', array()),
                    array(
                        array(
                            'id' => 'terms_login',
                            'title' => esc_html__('Terms & Conditions', 'golo-framework'),
                            'subtitle' => esc_html__('Select terms & conditions page', 'golo-framework'),
                            'type' => 'select',
                            'data' => 'page',
                            'data_args' => array(
                                'numberposts' => -1,
                            )
                        ),
                        array(
                            'id' => 'privacy_policy_login',
                            'title' => esc_html__('Privacy Policy', 'golo-framework'),
                            'subtitle' => esc_html__('Select privacy policy page', 'golo-framework'),
                            'type' => 'select',
                            'data' => 'page',
                            'data_args' => array(
                                'numberposts' => -1,
                            )
                        ),
                        array(
                            'id'       => 'enable_social_login',
                            'type'     => 'button_set',
                            'title'    => esc_html__('Enable Social Login', 'golo-framework'),
                            'subtitle' => esc_html__('Enable/Disable Social Login', 'golo-framework'),
                            'desc'     => '',
                            'options'  => array(
                                '1' => esc_html__('On', 'golo-framework'),
                                '0' => esc_html__('Off', 'golo-framework'),
                            ),
                            'default'  => '1'
                        ),
                        array(
                            'id'       => 'enable_user_role',
                            'type'     => 'button_set',
                            'title'    => esc_html__('Enable User Role', 'golo-framework'),
                            'subtitle' => esc_html__('Enable/Disable User Role In Form Register', 'golo-framework'),
                            'desc'     => '',
                            'options'  => array(
                                '1' => esc_html__('On', 'golo-framework'),
                                '0' => esc_html__('Off', 'golo-framework'),
                            ),
                            'default'  => '1'
                        ),
                        array(
                            'id'       => 'enable_captcha',
                            'type'     => 'button_set',
                            'title'    => esc_html__('Enable Captcha', 'golo-framework'),
                            'subtitle' => esc_html__('Enable/Disable Captcha', 'golo-framework'),
                            'desc'     => '',
                            'options'  => array(
                                '1' => esc_html__('On', 'golo-framework'),
                                '0' => esc_html__('Off', 'golo-framework'),
                            ),
                            'default'  => ''
                        ),
                        array(
                            'id'       => 'google_login_api',
                            'type'     => 'text',
                            'title'    => esc_html__('Google Login API', 'golo-framework'),
                            'subtitle' => esc_html__('Enter your google login api key'),
                            'default'  => '927330078961-nqdjhogd6fmjd3dsg300jhi3430hqu94.apps.googleusercontent.com'
                        ),
                        array(
                            'id'       => 'facebook_app_id',
                            'type'     => 'text',
                            'title'    => esc_html__('Facebook Login API', 'golo-framework'),
                            'subtitle' => esc_html__('Enter your facebook login api key'),
                            'default'  => '697200430787915'
                        ),
                    ),
                    apply_filters('golo_register_option_login_bottom', array())
                )
            ));
        }

        /**
         * @return mixed|void
         */
        private function user_option()
        {
            return apply_filters('golo_register_user_option', array(
                'id'     => 'golo_user_option',
                'title'  => esc_html__('User Navigation', 'golo-framework'),
                'icon'   => 'dashicons-groups',
                'fields' => array_merge(
                    apply_filters('golo_register_user_option_top', array()),
                    array(
                        array(
                            'id'       => 'show_dashboard',
                            'type'     => 'button_set',
                            'title'    => esc_html__('Show "Dashboard"', 'golo-framework'),
                            'subtitle' => esc_html__('Show/Hide "Dashboard" on navigation', 'golo-framework'),
                            'desc'     => '',
                            'options'  => array(
                                '1' => esc_html__('On', 'golo-framework'),
                                '0' => esc_html__('Off', 'golo-framework'),
                            ),
                            'default'  => '1'
                        ),
                        array(
                            'id'       => 'show_profile',
                            'type'     => 'button_set',
                            'title'    => esc_html__('Show "Profile"', 'golo-framework'),
                            'subtitle' => esc_html__('Show/Hide "Profile" on navigation', 'golo-framework'),
                            'desc'     => '',
                            'options'  => array(
                                '1' => esc_html__('On', 'golo-framework'),
                                '0' => esc_html__('Off', 'golo-framework'),
                            ),
                            'default'  => '1'
                        ),
                        array(
                            'id'       => 'show_my_places',
                            'type'     => 'button_set',
                            'title'    => esc_html__('Show "My Places"', 'golo-framework'),
                            'subtitle' => esc_html__('Show/Hide "My Places" on navigation', 'golo-framework'),
                            'desc'     => '',
                            'options'  => array(
                                '1' => esc_html__('On', 'golo-framework'),
                                '0' => esc_html__('Off', 'golo-framework'),
                            ),
                            'default'  => '1'
                        ),
                        array(
                            'id'       => 'show_my_booking',
                            'type'     => 'button_set',
                            'title'    => esc_html__('Show "My Booking"', 'golo-framework'),
                            'subtitle' => esc_html__('Show/Hide "My Booking" on navigation', 'golo-framework'),
                            'desc'     => '',
                            'options'  => array(
                                '1' => esc_html__('On', 'golo-framework'),
                                '0' => esc_html__('Off', 'golo-framework'),
                            ),
                            'default'  => '1'
                        ),
                        array(
                            'id'       => 'show_booking',
                            'type'     => 'button_set',
                            'title'    => esc_html__('Show "Booking"', 'golo-framework'),
                            'subtitle' => esc_html__('Show/Hide "My Booking" on navigation', 'golo-framework'),
                            'desc'     => '',
                            'options'  => array(
                                '1' => esc_html__('On', 'golo-framework'),
                                '0' => esc_html__('Off', 'golo-framework'),
                            ),
                            'default'  => '1'
                        ),
                        array(
                            'id'       => 'show_my_wishlist',
                            'type'     => 'button_set',
                            'title'    => esc_html__('Show "My Wishlist"', 'golo-framework'),
                            'subtitle' => esc_html__('Show/Hide "My Wishlist" on navigation', 'golo-framework'),
                            'desc'     => '',
                            'options'  => array(
                                '1' => esc_html__('On', 'golo-framework'),
                                '0' => esc_html__('Off', 'golo-framework'),
                            ),
                            'default'  => '1'
                        ),
                        array(
                            'id'       => 'show_add_new_place',
                            'type'     => 'button_set',
                            'title'    => esc_html__('Show "New Place"', 'golo-framework'),
                            'subtitle' => esc_html__('Show/Hide "New Place" on navigation', 'golo-framework'),
                            'desc'     => '',
                            'options'  => array(
                                '1' => esc_html__('On', 'golo-framework'),
                                '0' => esc_html__('Off', 'golo-framework'),
                            ),
                            'default'  => '0'
                        ),
                    ),
                    apply_filters('golo_register_user_option_bottom', array())
                )
            ));
        }

        /**
         * Place page option
         * @return mixed
         */
        private function listing_option()
        {
            return apply_filters('golo_register_option_listing_setting_page', array(
                'id' => 'golo_listing_setting_page_option',
                'title' => esc_html__('Listing Setting', 'golo-framework'),
                'icon' => 'dashicons-list-view',
                'fields' => array_merge(
                    apply_filters('golo_register_option_listing_setting_page_top', array()),
                    apply_filters('golo_register_option_listing_setting_page_main', array(
                        array(
                            'id'      => 'listing_view_place',
                            'type'    => 'select',
                            'title'   => esc_html__('Listing View', 'golo-framework'),
                            'default' => 'layout-02',
                            'options' => array(
                                'layout-02' => esc_html__('Grid View', 'golo-framework'),
                                'layout-list' => esc_html__('List View', 'golo-framework')
                            )
                        ),

                        array(
                            'id'      => 'pagination_type',
                            'type'    => 'select',
                            'title'   => esc_html__('Listing Pagination', 'golo-framework'),
                            'default' => 'number',
                            'options' => array(
                                'number' => esc_html__('Number', 'golo-framework'),
                                'loadmore' => esc_html__('Load More', 'golo-framework')
                            )
                        ),

                        array(
                            'id'       => 'enable_map_event',
                            'type'     => 'button_set',
                            'title'    => esc_html__('Enable Map Event', 'golo-framework'),
                            'subtitle' => esc_html__('Enable/Disable Scroll to Item When Hover or Click Map Marker', 'golo-framework'),
                            'options'  => array(
                                '1' => esc_html__('On', 'golo-framework'),
                                '0' => esc_html__('Off', 'golo-framework'),
                            ),
                            'default'  => '1',
                        ),
                    )),
                    apply_filters('golo_register_option_listing_setting_page_bottom', array())
                )
            ));
        }

        /**
         * Place page option
         * @return mixed
         */
        private function listing_card_option()
        {
            return apply_filters('golo_register_option_listing_card_page', array(
                'id' => 'golo_listing_card_page_option',
                'title' => esc_html__('Listing Card', 'golo-framework'),
                'icon' => 'dashicons-welcome-widgets-menus',
                'fields' => array_merge(
                    apply_filters('golo_register_option_listing_card_page_top', array()),
                    apply_filters('golo_register_option_listing_card_page_main', array(
                        array(
                            'id'      => 'layout_content_place',
                            'type'    => 'select',
                            'title'   => esc_html__('Layout', 'golo-framework'),
                            'default' => 'layout-02',
                            'options' => array(
                                'layout-01' => esc_html__('Layout 01', 'golo-framework'),
                                'layout-02' => esc_html__('Layout 02', 'golo-framework')
                            )
                        ),

                        array(
                            'id'       => 'archive_place_image_size',
                            'type'     => 'text',
                            'title'    => esc_html__('Image Size', 'golo-framework'),
                            'subtitle' => esc_html__('Enter image size. Alternatively enter size in pixels (Example : 330x180 (Not Include Unit, Space))', 'golo-framework'),
                            'default'  => '540x480',
                        ),

                        array(
                            'id'       => 'enable_excerpt',
                            'type'     => 'button_set',
                            'title'    => esc_html__('Display Excerpt', 'golo-framework'),
                            'subtitle' => esc_html__('Show/Hide Excerpt', 'golo-framework'),
                            'desc'     => '',
                            'options'  => array(
                                '1' => esc_html__('On', 'golo-framework'),
                                '0' => esc_html__('Off', 'golo-framework'),
                            ),
                            'default'  => '0',
                            'required' => array('layout_content_place', '=', 'layout-02'),
                        ),

                        array(
                            'id'       => 'enable_address',
                            'type'     => 'button_set',
                            'title'    => esc_html__('Display Address', 'golo-framework'),
                            'subtitle' => esc_html__('Show/Hide Address', 'golo-framework'),
                            'desc'     => '',
                            'options'  => array(
                                '1' => esc_html__('On', 'golo-framework'),
                                '0' => esc_html__('Off', 'golo-framework'),
                            ),
                            'default'  => '1',
                            'required' => array('layout_content_place', '=', 'layout-02'),
                        ),

                        array(
                            'id'       => 'enable_status',
                            'type'     => 'button_set',
                            'title'    => esc_html__('Display Status Time', 'golo-framework'),
                            'subtitle' => esc_html__('Show/Hide status time', 'golo-framework'),
                            'desc'     => '',
                            'options'  => array(
                                '1' => esc_html__('On', 'golo-framework'),
                                '0' => esc_html__('Off', 'golo-framework'),
                            ),
                            'default'  => '0',
                            'required' => array('layout_content_place', '=', 'layout-02'),
                        ),

                        array(
                            'id'       => 'display_author',
                            'type'     => 'button_set',
                            'title'    => esc_html__('Display Author', 'golo-framework'),
                            'subtitle' => esc_html__('Show/Hide Avatar Author', 'golo-framework'),
                            'desc'     => '',
                            'options'  => array(
                                '1' => esc_html__('On', 'golo-framework'),
                                '0' => esc_html__('Off', 'golo-framework'),
                            ),
                            'default'  => '1',
                        ),
                    )),
                    apply_filters('golo_register_option_listing_card_page_bottom', array())
                )
            ));
        }

        /**
         * Place page option
         * @return mixed
         */
        private function listing_page_option()
        {
            return apply_filters('golo_register_option_listing_page', array(
                'id' => 'golo_listing_page_option',
                'title' => esc_html__('City Layout', 'golo-framework'),
                'icon' => 'dashicons-building',
                'fields' => array_merge(
                    apply_filters('golo_register_option_listing_page_top', array()),
                    apply_filters('golo_register_option_listing_page_main', array(
                        apply_filters('golo_register_option_archive_city', array(
                            'id'     => 'archive_city',
                            'type'   => 'group',
                            'title'  => esc_html__('City Layout', 'golo-framework'),
                            'fields' => array(
                                array(
                                    'id'      => 'archive_city_layout_style',
                                    'type'    => 'select',
                                    'title'   => esc_html__('Layout', 'golo-framework'),
                                    'default' => 'layout-column',
                                    'options' => array(
                                        'layout-default'   => esc_html__('Layout Default', 'golo-framework'),
                                        'layout-column'    => esc_html__('Layout Left Filter', 'golo-framework'),
                                        'layout-top-filter' => esc_html__('Layout Top Filter', 'golo-framework'),
                                        //'layout-map-fixed' => esc_html__('Layout Map Fixed', 'golo-framework'),
                                    )
                                ),
                                array(
                                    'id'      => 'archive_city_banner_layout',
                                    'type'    => 'select',
                                    'title'   => esc_html__('City Banner', 'golo-framework'),
                                    'default' => 'layout-02',
                                    'options' => array(
                                        'layout-01' => esc_html__('Layout 01', 'golo-framework'),
                                        'layout-02'  => esc_html__('Layout 02', 'golo-framework')
                                    ),
                                ),
                                array(
                                    'id'       => 'enable_city_post',
                                    'type'     => 'button_set',
                                    'title'    => esc_html__('Enable Related Post', 'golo-framework'),
                                    'subtitle' => esc_html__('Enable/Disable Related Post', 'golo-framework'),
                                    'desc'     => '',
                                    'options'  => array(
                                        '1' => esc_html__('On', 'golo-framework'),
                                        '0' => esc_html__('Off', 'golo-framework'),
                                    ),
                                    'default'  => '0',
                                ),
                                array(
                                    'id'       => 'enable_city_filter',
                                    'type'     => 'button_set',
                                    'title'    => esc_html__('Enable Filter', 'golo-framework'),
                                    'subtitle' => esc_html__('Enable/Disable Filter', 'golo-framework'),
                                    'desc'     => '',
                                    'options'  => array(
                                        '1' => esc_html__('On', 'golo-framework'),
                                        '0' => esc_html__('Off', 'golo-framework'),
                                    ),
                                    'default'  => '1',
                                    'required' => array('archive_city_layout_style', '!=', 'layout-default'),
                                ),
                                array(
                                    'id'       => 'enable_city_map',
                                    'type'     => 'button_set',
                                    'title'    => esc_html__('Enable Map', 'golo-framework'),
                                    'subtitle' => esc_html__('Enable/Disable Map', 'golo-framework'),
                                    'desc'     => '',
                                    'options'  => array(
                                        '1' => esc_html__('On', 'golo-framework'),
                                        '0' => esc_html__('Off', 'golo-framework'),
                                    ),
                                    'default'  => '1',
                                    'required' => array('archive_city_layout_style', '!=', 'layout-default'),
                                ),
                                array(
                                    'id'      => 'archive_city_items_amount',
                                    'type'    => 'text',
                                    'title'   => esc_html__('Items Amount', 'golo-framework'),
                                    'default' => 16,
                                    'pattern' => '[0-9]*',
                                    'required' => array('archive_city_layout_style', '!=', 'layout-default'),
                                ),
                                array(
                                    'id'       => 'archive_city_columns',
                                    'type'     => 'select',
                                    'title'    => esc_html__('Columns', 'golo-framework'),
                                    'options'  => array(
                                        '1' => esc_html__('1', 'golo-framework'),
                                        '2' => esc_html__('2', 'golo-framework'),
                                        '3' => esc_html__('3', 'golo-framework'),
                                        '4' => esc_html__('4', 'golo-framework'),
                                        '5' => esc_html__('5', 'golo-framework'),
                                        '6' => esc_html__('6', 'golo-framework'),
                                    ),
                                    'default'  => '2',
                                    'required' => array('archive_city_layout_style', '!=', 'layout-default'),
                                ),
                                array(
                                    'id'       => 'archive_city_columns_lg',
                                    'type'     => 'select',
                                    'title'    => esc_html__('Columns Desktop Small', 'golo-framework'),
                                    'subtitle' => esc_html__('Browser Width < 1200px', 'golo-framework'),
                                    'options'  => array(
                                        '1' => esc_html__('1', 'golo-framework'),
                                        '2' => esc_html__('2', 'golo-framework'),
                                        '3' => esc_html__('3', 'golo-framework'),
                                        '4' => esc_html__('4', 'golo-framework'),
                                        '5' => esc_html__('5', 'golo-framework'),
                                        '6' => esc_html__('6', 'golo-framework'),
                                    ),
                                    'default'  => '2',
                                    'required' => array('archive_city_layout_style', '!=', 'layout-default'),
                                ),
                                array(
                                    'id'      => 'archive_city_columns_md',
                                    'type'    => 'select',
                                    'title'   => esc_html__('Columns Tablet', 'golo-framework'),
                                    'subtitle' => esc_html__('Browser Width < 992px', 'golo-framework'),
                                    'options' => array(
                                        '1' => esc_html__('1', 'golo-framework'),
                                        '2' => esc_html__('2', 'golo-framework'),
                                        '3' => esc_html__('3', 'golo-framework'),
                                        '4' => esc_html__('4', 'golo-framework'),
                                        '5' => esc_html__('5', 'golo-framework'),
                                        '6' => esc_html__('6', 'golo-framework'),
                                    ),
                                    'default' => '1',
                                    'required' => array('archive_city_layout_style', '!=', 'layout-default'),
                                ),
                                array(
                                    'id'       => 'archive_city_columns_sm',
                                    'type'     => 'select',
                                    'title'    => esc_html__('Columns Tablet Small', 'golo-framework'),
                                    'subtitle' => esc_html__('Browser Width < 768px', 'golo-framework'),
                                    'options'  => array(
                                        '1' => esc_html__('1', 'golo-framework'),
                                        '2' => esc_html__('2', 'golo-framework'),
                                        '3' => esc_html__('3', 'golo-framework'),
                                        '4' => esc_html__('4', 'golo-framework'),
                                        '5' => esc_html__('5', 'golo-framework'),
                                        '6' => esc_html__('6', 'golo-framework'),
                                    ),
                                    'default'  => '2',
                                    'required' => array('archive_city_layout_style', '!=', 'layout-default'),
                                ),
                                array(
                                    'id'       => 'archive_city_columns_xs',
                                    'type'     => 'select',
                                    'title'    => esc_html__('Columns Mobile', 'golo-framework'),
                                    'subtitle' => esc_html__('Browser Width < 480px', 'golo-framework'),
                                    'options'  => array(
                                        '1' => esc_html__('1', 'golo-framework'),
                                        '2' => esc_html__('2', 'golo-framework'),
                                        '3' => esc_html__('3', 'golo-framework'),
                                        '4' => esc_html__('4', 'golo-framework'),
                                        '5' => esc_html__('5', 'golo-framework'),
                                        '6' => esc_html__('6', 'golo-framework'),
                                    ),
                                    'default' => '1',
                                    'required' => array('archive_city_layout_style', '!=', 'layout-default'),
                                ),
                            )
                        )),
                    )),
                    apply_filters('golo_register_option_listing_page_bottom', array())
                )
            ));
        }

        /**
         * Place page option
         * @return mixed
         */
        private function search_page_option()
        {
            return apply_filters('golo_register_option_search_page', array(
                'id' => 'golo_search_page_option',
                'title' => esc_html__('Search Layout', 'golo-framework'),
                'icon' => 'dashicons-search',
                'fields' => array_merge(
                    apply_filters('golo_register_option_search_page_top', array()),
                    apply_filters('golo_register_option_search_page_main', array(
                        apply_filters('golo_register_option_place_search', array(
                            'id'     => 'place_search',
                            'type'   => 'group',
                            'title'  => esc_html__('Search layout', 'golo-framework'),
                            'fields' => array(
                                array(
                                    'id'      => 'archive_place_layout_style',
                                    'type'    => 'select',
                                    'title'   => esc_html__('Layout', 'golo-framework'),
                                    'default' => 'layout-column',
                                    'options' => array(
                                        'layout-column'     => esc_html__('Layout Column', 'golo-framework'),
                                        'layout-top-filter' => esc_html__('Layout Top Filter', 'golo-framework'),
                                    )
                                ),
                                array(
                                    'id'       => 'enable_archive_filter',
                                    'type'     => 'button_set',
                                    'title'    => esc_html__('Enable Filter', 'golo-framework'),
                                    'subtitle' => esc_html__('Enable/Disable Filter', 'golo-framework'),
                                    'desc'     => '',
                                    'options'  => array(
                                        '1' => esc_html__('On', 'golo-framework'),
                                        '0' => esc_html__('Off', 'golo-framework'),
                                    ),
                                    'default'  => '1',
                                ),
                                array(
                                    'id'      => 'default_search_order',
                                    'type'    => 'select',
                                    'title'   => esc_html__('Default Search Order', 'golo-framework'),
                                    'default' => 'place_featured',
                                    'options' => array(
                                        'date'    => esc_html__('Newest', 'golo-framework'),
                                        'rand'    => esc_html__('Random', 'golo-framework'),
                                        'place_featured'  => esc_html__('Featured', 'golo-framework'),
                                    ),
                                ),
                                array(
                                    'id'       => 'enable_archive_map',
                                    'type'     => 'button_set',
                                    'title'    => esc_html__('Enable Map', 'golo-framework'),
                                    'subtitle' => esc_html__('Enable/Disable Map', 'golo-framework'),
                                    'desc'     => '',
                                    'options'  => array(
                                        '1' => esc_html__('On', 'golo-framework'),
                                        '0' => esc_html__('Off', 'golo-framework'),
                                    ),
                                    'default'  => '1',
                                ),
                                array(
                                    'id'       => 'default_map',
                                    'type'     => 'button_set',
                                    'title'    => esc_html__('Map Hidden', 'golo-framework'),
                                    'desc'     => '',
                                    'options'  => array(
                                        '1' => esc_html__('On', 'golo-framework'),
                                        '0' => esc_html__('Off', 'golo-framework'),
                                    ),
                                    'default'  => '1',
                                ),
                                array(
                                    'id'      => 'archive_place_items_amount',
                                    'type'    => 'text',
                                    'title'   => esc_html__('Items Amount', 'golo-framework'),
                                    'default' => 16,
                                    'pattern' => '[0-9]*',
                                ),
                                array(
                                    'id'      => 'archive_place_columns',
                                    'type'    => 'select',
                                    'title'   => esc_html__('Columns', 'golo-framework'),
                                    'options' => array(
                                        '1' => esc_html__('1', 'golo-framework'),
                                        '2' => esc_html__('2', 'golo-framework'),
                                        '3' => esc_html__('3', 'golo-framework'),
                                        '4' => esc_html__('4', 'golo-framework'),
                                        '5' => esc_html__('5', 'golo-framework'),
                                        '6' => esc_html__('6', 'golo-framework'),
                                    ),
                                    'default' => '2',
                                ),
                                array(
                                    'id'       => 'archive_place_columns_lg',
                                    'type'     => 'select',
                                    'title'    => esc_html__('Columns Desktop Small', 'golo-framework'),
                                    'subtitle' => esc_html__('Browser Width < 1200px', 'golo-framework'),
                                    'options'  => array(
                                        '1' => esc_html__('1', 'golo-framework'),
                                        '2' => esc_html__('2', 'golo-framework'),
                                        '3' => esc_html__('3', 'golo-framework'),
                                        '4' => esc_html__('4', 'golo-framework'),
                                        '5' => esc_html__('5', 'golo-framework'),
                                        '6' => esc_html__('6', 'golo-framework'),
                                    ),
                                    'default'  => '2',
                                ),
                                array(
                                    'id'       => 'archive_place_columns_md',
                                    'type'     => 'select',
                                    'title'    => esc_html__('Columns Tablet', 'golo-framework'),
                                    'subtitle' => esc_html__('Browser Width < 992px', 'golo-framework'),
                                    'options'  => array(
                                        '1' => esc_html__('1', 'golo-framework'),
                                        '2' => esc_html__('2', 'golo-framework'),
                                        '3' => esc_html__('3', 'golo-framework'),
                                        '4' => esc_html__('4', 'golo-framework'),
                                        '5' => esc_html__('5', 'golo-framework'),
                                        '6' => esc_html__('6', 'golo-framework'),
                                    ),
                                    'default'  => '1',
                                ),
                                array(
                                    'id'       => 'archive_place_columns_sm',
                                    'type'     => 'select',
                                    'title'    => esc_html__('Columns Tablet Small', 'golo-framework'),
                                    'subtitle' => esc_html__('Browser Width < 768px', 'golo-framework'),
                                    'options'  => array(
                                        '1' => esc_html__('1', 'golo-framework'),
                                        '2' => esc_html__('2', 'golo-framework'),
                                        '3' => esc_html__('3', 'golo-framework'),
                                        '4' => esc_html__('4', 'golo-framework'),
                                        '5' => esc_html__('5', 'golo-framework'),
                                        '6' => esc_html__('6', 'golo-framework'),
                                    ),
                                    'default'  => '2',
                                ),
                                array(
                                    'id'       => 'archive_place_columns_xs',
                                    'type'     => 'select',
                                    'title'    => esc_html__('Columns Mobile', 'golo-framework'),
                                    'subtitle' => esc_html__('Browser Width < 480px', 'golo-framework'),
                                    'options'  => array(
                                        '1' => esc_html__('1', 'golo-framework'),
                                        '2' => esc_html__('2', 'golo-framework'),
                                        '3' => esc_html__('3', 'golo-framework'),
                                        '4' => esc_html__('4', 'golo-framework'),
                                        '5' => esc_html__('5', 'golo-framework'),
                                        '6' => esc_html__('6', 'golo-framework'),
                                    ),
                                    'default'  => '2',
                                ),
                            )
                        )),
                    )),
                    apply_filters('golo_register_option_search_page_bottom', array())
                )
            ));
        }

        /**
         * Place page option
         * @return mixed
         */
        private function single_place_page_option()
        {
            return apply_filters('golo_register_option_single_place_page', array(
                'id' => 'golo_single_place_page_option',
                'title' => esc_html__('Listing Detail', 'golo-framework'),
                'icon' => 'dashicons-media-text',
                'fields' => array_merge(
                    apply_filters('golo_register_option_single_place_page_top', array()),
                    apply_filters('golo_register_option_single_place_page_main', array(
                        apply_filters('golo_register_option_single_place', array(
                            'id'     => 'place_search',
                            'type'   => 'group',
                            'title'  => esc_html__('Listing Detail', 'golo-framework'),
                            'fields' => array(
                                array(
                                    'id'      => 'type_single_place',
                                    'type'    => 'select',
                                    'title'   => esc_html__('Layout', 'golo-framework'),
                                    'default' => 'type-1',
                                    'options' => array(
                                        'type-1' => esc_html__('Image', 'golo-framework'),
                                        'type-2'  => esc_html__('Carousel', 'golo-framework')
                                    )
                                ),

                                array(
                                    'id'      => 'datetimepicker_language',
                                    'type'    => 'select',
                                    'title'   => esc_html__('Datetimepicker Language', 'golo-framework'),
                                    'default' => 'none',
                                    'options' => array(
                                        ''   => esc_html__('Default', 'golo-framework'),
                                        'ar' => esc_html__('Arabic', 'golo-framework'),
                                        'az' => esc_html__('Azerbaijanian (Azeri)', 'golo-framework'),
                                        'bg' => esc_html__('Bulgarian', 'golo-framework'),
                                        'bs' => esc_html__('Bosanski', 'golo-framework'),
                                        'ca' => esc_html__('Català', 'golo-framework'),
                                        'ch' => esc_html__('Simplified Chinese', 'golo-framework'),
                                        'cs' => esc_html__('Czech', 'golo-framework'),
                                        'da' => esc_html__('Dansk', 'golo-framework'),
                                        'de' => esc_html__('German', 'golo-framework'),
                                        'el' => esc_html__('Ελληνικά', 'golo-framework'),
                                        'en' => esc_html__('English', 'golo-framework'),
                                        'en-GB' => esc_html__('English (British)', 'golo-framework'),
                                        'es' => esc_html__('Spanish', 'golo-framework'),
                                        'et' => esc_html__('Eesti', 'golo-framework'),
                                        'eu' => esc_html__('Euskara', 'golo-framework'),
                                        'fa' => esc_html__('Persian', 'golo-framework'),
                                        'fi' => esc_html__('Finnish (Suomi)', 'golo-framework'),
                                        'fr' => esc_html__('French', 'golo-framework'),
                                        'gl' => esc_html__('Galego', 'golo-framework'),
                                        'he' => esc_html__('Hebrew (עברית)', 'golo-framework'),
                                        'hr' => esc_html__('Hrvatski', 'golo-framework'),
                                        'hu' => esc_html__('Hungarian', 'golo-framework'),
                                        'hy' => esc_html__('Armenian', 'golo-framework'),
                                        'id' => esc_html__('Indonesian', 'golo-framework'),
                                        'it' => esc_html__('Italian', 'golo-framework'),
                                        'ja' => esc_html__('Japanese', 'golo-framework'),
                                        'ko' => esc_html__('Korean (한국어)', 'golo-framework'),
                                        'kr' => esc_html__('Korean', 'golo-framework'),
                                        'lt' => esc_html__('Lithuanian (lietuvių)', 'golo-framework'),
                                        'lv' => esc_html__('Latvian (Latviešu)', 'golo-framework'),
                                        'mk' => esc_html__('Macedonian (Македонски)', 'golo-framework'),
                                        'mn' => esc_html__('Mongolian (Монгол)', 'golo-framework'),
                                        'nl' => esc_html__('Dutch', 'golo-framework'),
                                        'no' => esc_html__('Norwegian', 'golo-framework'),
                                        'pl' => esc_html__('Polish', 'golo-framework'),
                                        'pt' => esc_html__('Portuguese', 'golo-framework'),
                                        'pt-BR' => esc_html__('Português(Brasil)', 'golo-framework'),
                                        'ro' => esc_html__('Romanian', 'golo-framework'),
                                        'ru' => esc_html__('Russian', 'golo-framework'),
                                        'se' => esc_html__('Swedish', 'golo-framework'),
                                        'sk' => esc_html__('Slovenčina', 'golo-framework'),
                                        'sl' => esc_html__('Slovenščina', 'golo-framework'),
                                        'sq' => esc_html__('Albanian (Shqip)', 'golo-framework'),
                                        'sr' => esc_html__('Serbian Cyrillic (Српски)', 'golo-framework'),
                                        'sr-YU' => esc_html__('Serbian (Srpski)', 'golo-framework'),
                                        'sv' => esc_html__('Svenska', 'golo-framework'),
                                        'th' => esc_html__('Thai', 'golo-framework'),
                                        'tr' => esc_html__('Turkish', 'golo-framework'),
                                        'uk' => esc_html__('Ukrainian', 'golo-framework'),
                                        'vi' => esc_html__('Vietnamese', 'golo-framework'),
                                        'zh' => esc_html__('Simplified Chinese (简体中文)', 'golo-framework'),
                                        'zh-TW' => esc_html__('Traditional Chinese (繁體中文)', 'golo-framework'),
                                    )
                                ),

                                array(
                                    'id'       => 'enable_sticky_booking_type',
                                    'type'     => 'button_set',
                                    'title'    => esc_html__('Enable Sticky Booking', 'golo-framework'),
                                    'subtitle' => esc_html__('Enable/Disable sticky booking when scroll', 'golo-framework'),
                                    'desc'     => '',
                                    'options'  => array(
                                        '1' => esc_html__('On', 'golo-framework'),
                                        '0' => esc_html__('Off', 'golo-framework'),
                                    ),
                                    'default'  => '1',
                                ),

                                array(
                                    'id'       => 'enable_claim_listing',
                                    'type'     => 'button_set',
                                    'title'    => esc_html__('Enable Claim Listing', 'golo-framework'),
                                    'subtitle' => esc_html__('Enable/Disable Claim Listing', 'golo-framework'),
                                    'desc'     => '',
                                    'options'  => array(
                                        '1' => esc_html__('On', 'golo-framework'),
                                        '0' => esc_html__('Off', 'golo-framework'),
                                    ),
                                    'default'  => '1',
                                ),

                                array(
                                    'id'       => 'enable_single_place_toggle_desc',
                                    'type'     => 'button_set',
                                    'title'    => esc_html__('Enable Toggle Description', 'golo-framework'),
                                    'subtitle' => esc_html__('Enable/Disable Toggle Description', 'golo-framework'),
                                    'desc'     => '',
                                    'options'  => array(
                                        '1' => esc_html__('On', 'golo-framework'),
                                        '0' => esc_html__('Off', 'golo-framework'),
                                    ),
                                    'default'  => '1',
                                    'required' => array('enable_single_place_desc', 'in', array('1')),
                                ),

                                array(
                                    'id'       => 'enable_single_place_related',
                                    'type'     => 'button_set',
                                    'title'    => esc_html__('Enable Place Related', 'golo-framework'),
                                    'subtitle' => esc_html__('Enable/Disable Place Related', 'golo-framework'),
                                    'desc'     => '',
                                    'options'  => array(
                                        '1' => esc_html__('On', 'golo-framework'),
                                        '0' => esc_html__('Off', 'golo-framework'),
                                    ),
                                    'default'  => '1',
                                ),

                                array(
                                    'id'       => 'enable_readmore_mobile',
                                    'type'     => 'button_set',
                                    'title'    => esc_html__('Enable Readmore Sidebar on Mobile', 'golo-framework'),
                                    'subtitle' => esc_html__('Enable/Disable Readmore Sidebar on Mobile', 'golo-framework'),
                                    'desc'     => '',
                                    'options'  => array(
                                        '1' => esc_html__('On', 'golo-framework'),
                                        '0' => esc_html__('Off', 'golo-framework'),
                                    ),
                                    'default'  => '1',
                                ),
                                array(
                                    'id'        => 'google_review_api_key',
                                    'type'      => 'text',
                                    'title'     => esc_html__('Google Review API Key', 'golo-framework'),
                                    'subtitle'  => esc_html__('You need to have UNRESTRICTED API key set for radius search', 'golo-framework'),
                                    'default'   => '',
                                ),
                                array(
                                    'id' => 'place_details_order',
                                    'type' => 'sortable',
                                    'title' => esc_html__('Place Details Order', 'golo-framework'),
                                    'desc' => esc_html__('Drag and drop layout manager, to quickly organize your place details.', 'golo-framework'),
                                    'options' => array(
                                        'enable_sp_amenities' => esc_html__('Amenities', 'golo-framework'),
                                        'enable_sp_description' => esc_html__('Description', 'golo-framework'),
                                        'enable_sp_menu' => esc_html__('Menu', 'golo-framework'),
                                        'enable_sp_location' => esc_html__('Location', 'golo-framework'),
                                        'enable_sp_contact' => esc_html__('Contact', 'golo-framework'),
                                        'enable_sp_additional_fields' => esc_html__('Additional Fields', 'golo-framework'),
                                        'enable_sp_time_opening' => esc_html__('Time Opening', 'golo-framework'),
                                        'enable_sp_video' => esc_html__('Video', 'golo-framework'),
                                        'enable_sp_faqs' => esc_html__('FAQs', 'golo-framework'),
                                        'enable_sp_nearby_yelp_review' => esc_html__('Nearby YELP Review', 'golo-framework'),
                                        'enable_sp_google_review' => esc_html__('Google Review', 'golo-framework'),
                                        'enable_sp_author_info' => esc_html__('Author Info', 'golo-framework'),
                                        'enable_sp_review' => esc_html__('Review', 'golo-framework'),
                                        'enable_sp_product' => esc_html__('Product Related', 'golo-framework'),
                                    ),
                                    'default' =>  array('enable_sp_amenities',  'enable_sp_description', 'enable_sp_menu','enable_sp_location', 'enable_sp_contact', 'enable_sp_additional_fields', 'enable_sp_time_opening', 'enable_sp_video', 'enable_sp_faqs', 'enable_sp_nearby_yelp_review', 'enable_sp_google_review', 'enable_sp_author_info', 'enable_sp_review', 'enable_sp_product')
                                ),
                            )
                        )),
                    )),
                    apply_filters('golo_register_option_single_place_page_bottom', array())
                )
            ));
        }

        /**
         * @return mixed|void
         */
        private function place_option()
        {
            return apply_filters('golo_place_option', array(
                'id'     => 'golo_place_option',
                'title'  => esc_html__('Add Place', 'golo-framework'),
                'icon'   => 'dashicons-welcome-widgets-menus',
                'fields' => array_merge(
                    apply_filters('golo_place_option_top', array()),
                    array(
                        array(
                            'id' => 'section_place_main_option',
                            'title' => esc_html__('Main Option', 'golo-framework'),
                            'type' => 'group',
                            'fields' => array(
                                array(
                                    'id'       => 'enable_login_to_submit',
                                    'type'     => 'button_set',
                                    'title'    => esc_html__('Enable Login to Submit', 'golo-framework'),
                                    'subtitle' => esc_html__('Enable/Disable Login to Submit', 'golo-framework'),
                                    'desc'     => '',
                                    'options'  => array(
                                        '1' => esc_html__('On', 'golo-framework'),
                                        '0' => esc_html__('Off', 'golo-framework'),
                                    ),
                                    'default'  => '1'
                                ),

                                array(
                                    'id' => 'auto_publish',
                                    'title' => esc_html__('Automatically publish the submitted place?', 'golo-framework'),
                                    'type' => 'button_set',
                                    'options' => array(
                                        '1' => esc_html__('Yes', 'golo-framework'),
                                        '0' => esc_html__('No', 'golo-framework'),
                                    ),
                                    'default' => '1',
                                ),

                                array(
                                    'id' => 'auto_publish_edited',
                                    'title' => esc_html__('Automatically publish the edited place?', 'golo-framework'),
                                    'type' => 'button_set',
                                    'options' => array(
                                        '1' => esc_html__('Yes', 'golo-framework'),
                                        '0' => esc_html__('No', 'golo-framework'),
                                    ),
                                    'default' => '1',
                                ),

                                array(
                                    'id'       => 'enable_gutenberg_edit_place',
                                    'title'    => esc_html__('Enable Gutenberg Edit Place', 'golo-framework'),
                                    'subtitle' => esc_html__('Enable/Disable Gutenberg Edit Place', 'golo-framework'),
                                    'type'     => 'button_set',
                                    'options'  => array(
                                        '1' => esc_html__('On', 'golo-framework'),
                                        '0' => esc_html__('Off', 'golo-framework'),
                                    ),
                                    'default'  => '0',
                                ),

                                array(
                                    'id'       => 'enable_time_format_24',
                                    'type'     => 'button_set',
                                    'title'    => esc_html__('Enable Time Format 24h', 'golo-framework'),
                                    'subtitle' => esc_html__('Enable/Disable Time Format 24h', 'golo-framework'),
                                    'desc'     => '',
                                    'options'  => array(
                                        '1' => esc_html__('On', 'golo-framework'),
                                        '0' => esc_html__('Off', 'golo-framework'),
                                    ),
                                    'default'  => '1'
                                ),

                                array(
                                    'id' => 'max_place_gallery_images',
                                    'type' => 'text',
                                    'title' => esc_html__('Maximum Images', 'golo-framework'),
                                    'subtitle' => esc_html__('Maximum images allowed for single place.', 'golo-framework'),
                                    'default' => '5',
                                ),

                                array(
                                    'id' => 'image_max_file_size',
                                    'type' => 'text',
                                    'title' => esc_html__('Maximum File Size', 'golo-framework'),
                                    'subtitle' => esc_html__('Maximum upload image size. For example 10kb, 500kb, 1mb, 10mb, 100mb', 'golo-framework'),
                                    'default' => '1000kb',
                                ),

                                array(
                                    'id' => 'default_place_image',
                                    'type' => 'image',
                                    'url' => true,
                                    'title' => esc_html__('Default Place Image', 'golo-framework'),
                                    'subtitle' => esc_html__('Display this if no place image', 'golo-framework'),
                                    'default' => GOLO_PLUGIN_URL . 'assets/images/no-image.jpg'
                                ),

                                array(
                                    'id'      => 'default_booking_type',
                                    'type'    => 'select',
                                    'title'   => esc_html__('Default Booking Type', 'golo-framework'),
                                    'default' => '',
                                    'options' => array(
                                        ''        => esc_html__('None', 'golo-framework'),
                                        'info'    => esc_html__('Booking Contact', 'golo-framework'),
                                        'link'    => esc_html__('Affiliate Link', 'golo-framework'),
                                        'banner'  => esc_html__('Affiliate Banner', 'golo-framework'),
                                        'form'    => esc_html__('Booking Form', 'golo-framework'),
                                        'contact' => esc_html__('Enquiry Form', 'golo-framework'),
                                    ),
                                ),

                                array(
                                    'id' => 'cf7_contact_form',
                                    'type' => 'text',
                                    'title' => esc_html__('CF7 For Place Enquiry Form', 'golo-framework'),
                                    'subtitle' => esc_html__('Id of CF7 to be used for Enquiry Form. Example: 188, 660, 5505', 'golo-framework'),
                                    'default' => '',
                                ),
                            )
                        ),
                        array(
                            'id' => 'section_place_hide_group_fields',
                            'title' => esc_html__('Hide Submit Group Form Fields', 'golo-framework'),
                            'type' => 'group',
                            'fields' => array(
                                array(
                                    'id' => 'hide_place_group_fields',
                                    'type' => 'checkbox_list',
                                    'title' => esc_html__('Hide Submit Form Groups', 'golo-framework'),
                                    'subtitle' => esc_html__('Choose which fields you want to hide on New Property page?', 'golo-framework'),
                                    'options' => array(
                                        //General
                                        'general' => esc_html__('General', 'golo-framework'),
                                        'hightlights' => esc_html__('Hightlights', 'golo-framework'),
                                        'menu' => esc_html__('Menu', 'golo-framework'),
                                        'location' => esc_html__('Location', 'golo-framework'),
                                        'contact' => esc_html__('Contact', 'golo-framework'),
                                        'additional' => esc_html__('Additional Fields', 'golo-framework'),
                                        'socials' => esc_html__('Socials', 'golo-framework'),
                                        'time-opening' => esc_html__('Time opening', 'golo-framework'),
                                        'media' => esc_html__('Media', 'golo-framework'),
                                        'booking' => esc_html__('Booking', 'golo-framework'),
                                        'faqs' => esc_html__('FAQs', 'golo-framework'),
                                    ),
                                    'value_inline' => false,
                                    'default' => array()
                                ),
                            )
                        ),
                        array(
                            'id' => 'section_place_hide_fields',
                            'title' => esc_html__('Hide Submit Form Fields', 'golo-framework'),
                            'type' => 'group',
                            'fields' => array(
                                array(
                                    'id' => 'hide_place_fields',
                                    'type' => 'checkbox_list',
                                    'title' => esc_html__('Hide Submit Form Fields', 'golo-framework'),
                                    'subtitle' => esc_html__('Choose which fields you want to hide on New Property page?', 'golo-framework'),
                                    'options' => array(
                                        //General
                                        'place_name' => esc_html__('Name', 'golo-framework'),
                                        'place_des' => esc_html__('Description', 'golo-framework'),
                                        'place_price' => esc_html__('Price', 'golo-framework'),
                                        'place_price_unit' => esc_html__('Price Unit', 'golo-framework'),
                                        'place_price_ranger' => esc_html__('Price Ranger', 'golo-framework'),
                                        'place_category' => esc_html__('Category', 'golo-framework'),
                                        'place_type' => esc_html__('Type', 'golo-framework'),

                                        //Highlights
                                        'place_highlights' => esc_html__('Highlights', 'golo-framework'),

                                        //Location
                                        'city_town' => esc_html__('City / Town', 'golo-framework'),
                                        'new_place_city' => esc_html__('Create New City', 'golo-framework'),
                                        'postal_code' => esc_html__('Postal Code / Zip', 'golo-framework'),
                                        'address' => esc_html__('Address', 'golo-framework'),

                                        //Contact
                                        'email' => esc_html__('Email', 'golo-framework'),
                                        'phone' => esc_html__('Phone', 'golo-framework'),
                                        'website' => esc_html__('Website', 'golo-framework'),

                                        //Contact
                                        'additional' => esc_html__('Additional Fields', 'golo-framework'),

                                        //Social
                                        'facebook' => esc_html__('Facebook', 'golo-framework'),
                                        'instagram' => esc_html__('Instagram', 'golo-framework'),
                                        'twitter' => esc_html__('Twitter', 'golo-framework'),

                                        //Opening Time
                                        'opening_time' => esc_html__('Opening Time', 'golo-framework'),

                                        //Media
                                        'featured_image' => esc_html__('Featured image', 'golo-framework'),
                                        'gallery_image' => esc_html__('Gallery Images', 'golo-framework'),
                                        'video' => esc_html__('Video', 'golo-framework'),

                                        //Booking
                                        'booking' => esc_html__('Booking', 'golo-framework'),

                                        //FAQs
                                        'faqs' => esc_html__('FAQs', 'golo-framework'),
                                    ),
                                    'value_inline' => false,
                                    'default' => array()
                                ),
                            )
                        ),
                    ),
                    apply_filters('golo_place_option_bottom', array())
                )
            ));
        }

        /**
         * @return mixed|void
         */
        private function search_option($cities = array())
        {
            return apply_filters('golo_register_option_search', array(
                'id' => 'golo_search_option',
                'title' => esc_html__('Search Filter', 'golo-framework'),
                'icon' => 'dashicons-filter',
                'fields' => array_merge(
                    apply_filters('golo_register_option_search_top', array()),
                    array(
                        array(
                            'id' => 'section_search_field_option',
                            'title' => esc_html__('Show / Hide / Arrange Search Fields', 'golo-framework'),
                            'type' => 'group',
                            'fields' => array(
                                array(
                                    'id' => 'search_fields',
                                    'type' => 'sortable',
                                    'title' => esc_html__('Search Fields', 'golo-framework'),
                                    'desc' => esc_html__('Drag and drop layout manager, to quickly organize your form search layout.', 'golo-framework'),
                                    'options' => array(
                                        'sort_by' => esc_html__('Sort By', 'golo-framework'),
                                        'filter_price' => esc_html__('Price', 'golo-framework'),
                                        'filter_city' => esc_html__('City', 'golo-framework'),
                                        'filter_categories' => esc_html__('Categories', 'golo-framework'),
                                        'filter_type' => esc_html__('Type', 'golo-framework'),
                                        'filter_amenities' => esc_html__('Amenities', 'golo-framework'),
                                        'filter_neighborhood' => esc_html__('Neighborhood', 'golo-framework'),
                                        'open_time' => esc_html__('Open Time', 'golo-framework'),
                                    ),
                                    'default' =>  array('sort_by',  'filter_price', 'filter_city', 'filter_categories','filter_type', 'filter_amenities')
                                ),
                            )
                        ),
                    ),
                    apply_filters('golo_register_option_search_bottom', array())
                )
            ));
        }

        /**
         * @return mixed|void
         */
        private function email_management_option()
        {
            return apply_filters('golo_register_option_email_management', array(
                'id' => 'golo_email_management_option',
                'title' => esc_html__('Email Template', 'golo-framework'),
                'icon' => 'dashicons-email-alt',
                'fields' => array_merge(
                    apply_filters('golo_register_option_email_management_top', array()),
                    array(
                        array(
                            'id' => 'email-new-user',
                            'title' => esc_html__('New Registed User', 'golo-framework'),
                            'type' => 'group',
                            'toggle_default' => false,
                            'fields' => array(
                                array(
                                    'id' => 'golo_user_mail_register_user',
                                    'type' => 'info',
                                    'style' => 'info',
                                    'title' => esc_html__('User Email', 'golo-framework'),
                                ),
                                array(
                                    'id' => 'subject_mail_register_user',
                                    'type' => 'text',
                                    'title' => esc_html__('Subject', 'golo-framework'),
                                    'default' => esc_html__('Your username and password on %website_url', 'golo-framework'),
                                ),
                                array(
                                    'id' => 'mail_register_user',
                                    'type' => 'editor',
                                    'args' => array(
                                        'media_buttons' => true,
                                        'quicktags'     => true,
                                    ),
                                    'title' => esc_html__('Content', 'golo-framework'),
                                    'default' => esc_html__('Hi %your_name, 
                                        Welcome to %website_url! You can login now using the below credentials:
                                        Username: %user_login_register
                                        Password: %user_pass_register
                                        If you have any problems, please contact us.
                                        Thank you!', 'golo-framework'
                                    ),
                                ),
                                array(
                                    'id' => 'golo_admin_mail_register_user',
                                    'type' => 'info',
                                    'style' => 'info',
                                    'title' => esc_html__('Admin Email', 'golo-framework'),
                                ),
                                array(
                                    'id' => 'subject_admin_mail_register_user',
                                    'type' => 'text',
                                    'title' => esc_html__('Subject', 'golo-framework'),
                                    'default' => esc_html__('New User Registration', 'golo-framework'),
                                ),
                                array(
                                    'id' => 'admin_mail_register_user',
                                    'type' => 'editor',
                                    'args' => array(
                                        'media_buttons' => true,
                                        'quicktags'     => true,
                                    ),
                                    'title' => esc_html__('Content', 'golo-framework'),
                                    'default' => esc_html__('New user registration on %website_url.
                                        Username: %user_login_register,
                                        E-mail: %user_email_register', 'golo-framework'
                                    ),
                                )
                            )
                        ),
                        array(
                            'id' => 'email-activated-package',
                            'title' => esc_html__('Activated Package', 'golo-framework'),
                            'type' => 'group',
                            'toggle_default' => false,
                            'fields' => array(
                                array(
                                    'id' => 'golo_user_mail_activated_package',
                                    'type' => 'info',
                                    'style' => 'info',
                                    'title' => esc_html__('User Email', 'golo-framework'),
                                ),
                                array(
                                    'id' => 'subject_mail_activated_package',
                                    'type' => 'text',
                                    'title' => esc_html__('Subject', 'golo-framework'),
                                    'default' => esc_html__('Your purchase was activated', 'golo-framework'),
                                ),
                                array(
                                    'id' => 'mail_activated_package',
                                    'type' => 'editor',
                                    'args' => array(
                                        'media_buttons' => true,
                                        'quicktags'     => true,
                                    ),
                                    'title' => esc_html__('Content', 'golo-framework'),
                                    'default' => esc_html__("Hi thgolo,
                                        Welcome to %website_url and thank you for purchasing a plan with us. We are excited you have chosen %website_name . %website_name is a great place to advertise and search properties.
                                        You plan on  %website_url activated! You can now list your properties according to you plan.", 'golo-framework'
                                    ),
                                )
                            )
                        ),

                        array(
                            'id' => 'email-activated-listing',
                            'title' => esc_html__('Activated Listing', 'golo-framework'),
                            'type' => 'group',
                            'toggle_default' => false,
                            'fields' => array(
                                array(
                                    'id' => 'golo_user_mail_activated_listing',
                                    'type' => 'info',
                                    'style' => 'info',
                                    'title' => esc_html__('User Email', 'golo-framework'),
                                ),
                                array(
                                    'id' => 'subject_mail_activated_listing',
                                    'type' => 'text',
                                    'title' => esc_html__('Subject', 'golo-framework'),
                                    'default' => esc_html__('Your purchase was activated', 'golo-framework'),
                                ),
                                array(
                                    'id' => 'mail_activated_listing',
                                    'type'     => 'editor',
                                    'args'     => array(
                                        'media_buttons' => true,
                                        'quicktags'     => true,
                                    ),
                                    'title' => esc_html__('Content', 'golo-framework'),
                                    'default' => esc_html__('Hi thgolo,Your purchase on %website_url is activated! You should go and check it out.', 'golo-framework'),
                                )
                            )
                        ),

                        array(
                            'id' => 'email-approved-listing',
                            'title' => esc_html__('Approved Listing', 'golo-framework'),
                            'type' => 'group',
                            'toggle_default' => false,
                            'fields' => array(
                                array(
                                    'id' => 'golo_user_mail_approved_listing',
                                    'type' => 'info',
                                    'style' => 'info',
                                    'title' => esc_html__('User Email', 'golo-framework'),
                                ),
                                array(
                                    'id' => 'subject_mail_approved_listing',
                                    'type' => 'text',
                                    'title' => esc_html__('Subject', 'golo-framework'),
                                    'default' => esc_html__('Your listing approved', 'golo-framework'),
                                ),
                                array(
                                    'id' => 'mail_approved_listing',
                                    'type'     => 'editor',
                                    'args'     => array(
                                        'media_buttons' => true,
                                        'quicktags'     => true,
                                    ),
                                    'title' => esc_html__('Content', 'golo-framework'),
                                    'default' => esc_html__("Hi thgolo,
                                        Your place on %website_url has been approved.

                                        Place Title:%listing_title
                                        Place Url: %listing_url", 'golo-framework'
                                    ),
                                )
                            )
                        ),
                        array(
                            'id' => 'email-expired-listing',
                            'title' => esc_html__('Expired Listing', 'golo-framework'),
                            'type' => 'group',
                            'toggle_default' => false,
                            'fields' => array(
                                array(
                                    'id' => 'golo_user_mail_expired_listing',
                                    'type' => 'info',
                                    'style' => 'info',
                                    'title' => esc_html__('User Email', 'golo-framework'),
                                ),
                                array(
                                    'id' => 'subject_mail_expired_listing',
                                    'type' => 'text',
                                    'title' => esc_html__('Subject', 'golo-framework'),
                                    'default' => esc_html__('Your listing expired', 'golo-framework'),
                                ),
                                array(
                                    'id' => 'mail_expired_listing',
                                    'type'     => 'editor',
                                    'args'     => array(
                                        'media_buttons' => true,
                                        'quicktags'     => true,
                                    ),
                                    'title' => esc_html__('Content', 'golo-framework'),
                                    'default' => esc_html__("Hi,
                                        Your place on %website_url has been expired.

                                        Place Title:%listing_title
                                        Place Url: %listing_url", 'golo-framework'
                                    ),
                                )
                            )
                        ),
                        array(
                            'id' => 'email-claim-listing',
                            'title' => esc_html__('Claim Listing', 'golo-framework'),
                            'type' => 'group',
                            'toggle_default' => false,
                            'fields' => array(
                                array(
                                    'id' => 'golo_user_mail_claim_listing',
                                    'type' => 'info',
                                    'style' => 'info',
                                    'title' => esc_html__('User Email', 'golo-framework'),
                                ),
                                array(
                                    'id' => 'subject_mail_claim_listing',
                                    'type' => 'text',
                                    'title' => esc_html__('Subject', 'golo-framework'),
                                    'default' => esc_html__('Your claim listing', 'golo-framework'),
                                ),
                                array(
                                    'id' => 'mail_claim_listing',
                                    'type' => 'editor',
                                    'args' => array(
                                        'media_buttons' => true,
                                        'quicktags'     => true,
                                    ),
                                    'title' => esc_html__('Content', 'golo-framework'),
                                    'default' => esc_html__("Hi,
                                        Name: %your_name.
                                        Username: %your_username.
                                        Listing Url: %your_listing.
                                        Messager:%messager.
                                        Please wait for review.", 'golo-framework'

                                        // Your claim on %website_url.
                                        // Place Title:%claim_title
                                        // Listing Url: %claim_url. Please wait for review.", 'golo-framework'
                                    ),
                                )
                            )
                        ),
                        array(
                            'id' => 'email-confirm-booking',
                            'title' => esc_html__('Confirm Booking', 'golo-framework'),
                            'type' => 'group',
                            'toggle_default' => false,
                            'fields' => array(
                                array(
                                    'id' => 'golo_user_mail_confirm_booking',
                                    'type' => 'info',
                                    'style' => 'info',
                                    'title' => esc_html__('User Email', 'golo-framework'),
                                ),
                                array(
                                    'id' => 'subject_mail_confirm_booking',
                                    'type' => 'text',
                                    'title' => esc_html__('Subject', 'golo-framework'),
                                    'default' => esc_html__('Your booking', 'golo-framework'),
                                ),
                                array(
                                    'id' => 'mail_confirm_booking',
                                    'type' => 'editor',
                                    'args' => array(
                                        'media_buttons' => true,
                                        'quicktags'     => true,
                                    ),
                                    'title' => esc_html__('Content', 'golo-framework'),
                                    'default' => esc_html__("Hi,
                                        Your booking on %website_url.
                                        Place Title:%booking_title
                                        Place Url: %booking_url. Please wait for review.", 'golo-framework'
                                    ),
                                )
                            )
                        ),
                        array(
                            'id' => 'email-owner-new-booking',
                            'title' => esc_html__('Owner New Booking', 'golo-framework'),
                            'type' => 'group',
                            'toggle_default' => false,
                            'fields' => array(
                                array(
                                    'id' => 'golo_owner_mail_new_booking',
                                    'type' => 'info',
                                    'style' => 'info',
                                    'title' => esc_html__('Owner Email', 'golo-framework'),
                                ),
                                array(
                                    'id' => 'subject_mail_owner_new_booking',
                                    'type' => 'text',
                                    'title' => esc_html__('Subject', 'golo-framework'),
                                    'default' => esc_html__('New booking', 'golo-framework'),
                                ),
                                array(
                                    'id' => 'mail_owner_new_booking',
                                    'type' => 'editor',
                                    'args' => array(
                                        'media_buttons' => true,
                                        'quicktags'     => true,
                                    ),
                                    'title' => esc_html__('Content', 'golo-framework'),
                                    'default' => esc_html__("NOTIFICATION:
                                        New booking on %website_url.
                                        Place Title: %booking_title.
                                        Place Url: %booking_url.", 'golo-framework'
                                    ),
                                )
                            )
                        ),
                        array(
                            'id' => 'email-approved-booking',
                            'title' => esc_html__('Approved Booking', 'golo-framework'),
                            'type' => 'group',
                            'toggle_default' => false,
                            'fields' => array(
                                array(
                                    'id' => 'golo_user_mail_approved_booking',
                                    'type' => 'info',
                                    'style' => 'info',
                                    'title' => esc_html__('User Email', 'golo-framework'),
                                ),
                                array(
                                    'id' => 'subject_mail_approved_booking',
                                    'type' => 'text',
                                    'title' => esc_html__('Subject', 'golo-framework'),
                                    'default' => esc_html__('Your booking approved', 'golo-framework'),
                                ),
                                array(
                                    'id' => 'mail_approved_booking',
                                    'type' => 'editor',
                                    'args' => array(
                                        'media_buttons' => true,
                                        'quicktags'     => true,
                                    ),
                                    'title' => esc_html__('Content', 'golo-framework'),
                                    'default' => esc_html__("Hi,
                                        Your booking on %website_url has been approved.

                                        Booking Title:%booking_title.
                                        Place Url: %booking_url.", 'golo-framework'
                                    ),
                                )
                            )
                        ),
                        array(
                            'id' => 'email-canceled-booking',
                            'title' => esc_html__('Canceled Booking', 'golo-framework'),
                            'type' => 'group',
                            'toggle_default' => false,
                            'fields' => array(
                                array(
                                    'id' => 'golo_user_mail_canceled_booking',
                                    'type' => 'info',
                                    'style' => 'info',
                                    'title' => esc_html__('User Email', 'golo-framework'),
                                ),
                                array(
                                    'id' => 'subject_mail_canceled_booking',
                                    'type' => 'text',
                                    'title' => esc_html__('Subject', 'golo-framework'),
                                    'default' => esc_html__('Your booking cancel', 'golo-framework'),
                                ),
                                array(
                                    'id' => 'mail_canceled_booking',
                                    'type' => 'editor',
                                    'args' => array(
                                        'media_buttons' => true,
                                        'quicktags'     => true,
                                    ),
                                    'title' => esc_html__('Content', 'golo-framework'),
                                    'default' => esc_html__("Hi,
                                        Your booking on %website_url has been canceled.

                                        Booking Title:%booking_title.
                                        Place Url: %booking_url.", 'golo-framework'
                                    ),
                                )
                            )
                        ),
                        array(
                            'id' => 'email-new-wire-transfer',
                            'title' => esc_html__('New Wire Transfer', 'golo-framework'),
                            'type' => 'group',
                            'toggle_default' => false,
                            'fields' => array(
                                array(
                                    'id' => 'golo_user_mail_new_wire_transfer',
                                    'type' => 'info',
                                    'style' => 'info',
                                    'title' => esc_html__('User Email', 'golo-framework'),
                                ),
                                array(
                                    'id' => 'subject_mail_new_wire_transfer',
                                    'type' => 'text',
                                    'title' => esc_html__('Subject', 'golo-framework'),
                                    'default' => esc_html__('You ordgolod a new Wire Transfer', 'golo-framework'),
                                ),
                                array(
                                    'id' => 'mail_new_wire_transfer',
                                    'type'     => 'editor',
                                    'args'     => array(
                                        'media_buttons' => true,
                                        'quicktags'     => true,
                                    ),
                                    'title' => esc_html__('Content', 'golo-framework'),
                                    'default' => esc_html__('We received your Wire Transfer payment request on  %website_url !
                                        Please follow the instructions below in order to start submitting properties as soon as possible.
                                        The invoice number is: %invoice_no, Amount: %total_price.', 'golo-framework'
                                    ),
                                ),
                                array(
                                    'id' => 'golo_admin_mail_new_wire_transfer',
                                    'type' => 'info',
                                    'style' => 'info',
                                    'title' => esc_html__('Admin Email', 'golo-framework'),
                                ),
                                array(
                                    'id' => 'subject_admin_mail_new_wire_transfer',
                                    'type' => 'text',
                                    'title' => esc_html__('Subject', 'golo-framework'),
                                    'default' => esc_html__('Somebody ordgolod a new Wire Transfer', 'golo-framework'),
                                ),
                                array(
                                    'id' => 'admin_mail_new_wire_transfer',
                                    'type'     => 'editor',
                                    'args'     => array(
                                        'media_buttons' => true,
                                        'quicktags'     => true,
                                    ),
                                    'title' => esc_html__('Content', 'golo-framework'),
                                    'default' => esc_html__('We received your Wire Transfer payment request on  %website_url !
                                        Please follow the instructions below in order to start submitting properties as soon as possible.
                                        The invoice number is: %invoice_no, Amount: %total_price.', 'golo-framework'
                                    ),
                                )
                            )
                        ),
                    ),
                    apply_filters('golo_register_option_email_management_bottom', array())
                )
            ));
        }

        /**
         * @return mixed|void
         */
        private function yelp_option($cities = array())
        {
            return apply_filters('golo_register_option_yelp', array(
                'id' => 'golo_yelp_option',
                'title' => esc_html__('Yelp Option', 'golo-framework'),
                'icon' => 'dashicons-star-filled',
                'fields' => array_merge(
                    apply_filters('golo_register_option_yelp_top', array()),
                    array(
                        array(
                            'id'      => 'yelp_limit_review',
                            'title'   => esc_html__('Limit', 'golo-framework'),
                            'type'    => 'text',
                            'default' => '3',
                        ),

                        // array(
                        //     'id'      => 'yelp_sort_review',
                        //     'type'    => 'select',
                        //     'title'   => esc_html__('Sort by', 'golo-framework'),
                        //     'options' => array(
                        //         '0' => esc_html__('Best Match', 'golo-framework'),
                        //         '1' => esc_html__('Distance', 'golo-framework'),
                        //         '2' => esc_html__('Rating', 'golo-framework'),
                        //     ),
                        //     'default' => '2',
                        // ),

                        array(
                            'id'       => 'yelp_display_address',
                            'title'    => esc_html__('Display Address', 'golo-framework'),
                            'type'     => 'button_set',
                            'options'  => array(
                                '1' => esc_html__('On', 'golo-framework'),
                                '' => esc_html__('Off', 'golo-framework'),
                            ),
                            'default'  => '1',
                        ),

                        array(
                            'id'       => 'yelp_display_phone',
                            'title'    => esc_html__('Display Phone', 'golo-framework'),
                            'type'     => 'button_set',
                            'options'  => array(
                                '1' => esc_html__('On', 'golo-framework'),
                                '' => esc_html__('Off', 'golo-framework'),
                            ),
                            'default'  => '',
                        ),
                    ),
                    apply_filters('golo_register_option_yelp_bottom', array())
                )
            ));
        }

        /**
         * @return mixed|void
         */
        private function coupon_option()
        {
            return apply_filters('golo_register_option_coupon', array(
                'id' => 'golo_coupon_option',
                'title' => esc_html__('Coupon', 'golo-framework'),
                'icon' => 'dashicons-tickets-alt',
                'fields' => array_merge(
                    apply_filters('golo_register_option_coupon_top', array()),
                    array(
                        array(
                            'id'          => "place_coupon_title",
                            'title'       => esc_html__('Title', 'golo-framework'),
                            'type'        => 'text',
                            'default'     => '',
                            'panel_title' => true,
                        ),
                        array(
                            'id' => "place_coupon_description",
                            'title' => esc_html__('Description', 'golo-framework'),
                            'type' => 'textarea',
                            'default' => '',
                        ),
                        array(
                            'id' => "place_coupon_image",
                            'title' => esc_html__('Background Image', 'golo-framework'),
                            'type' => 'image',
                        ),
                        array(
                            'id'          => "place_coupon_code",
                            'title'       => esc_html__('Code', 'golo-framework'),
                            'type'        => 'text',
                            'default'     => '',
                        ),
                    ),
                    apply_filters('golo_register_option_coupon_bottom', array())
                )
            ));
        }
    }
}