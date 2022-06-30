<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if (!class_exists('Golo_Admin_Invoice')) {
    /**
     * Class Golo_Admin_Invoice
     */
    class Golo_Admin_Invoice
    {

        /**
         * Get product by name
         */
        public function get_product_by_name($post_name, $output = OBJECT) {
            global $wpdb;
                $post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type='product'", $post_name ));
                if ( $post )
                    return get_post($post, $output);

            return null;
        }
        
        /**
         * Register custom columns
         * @param $columns
         * @return array
         */
        public function register_custom_column_titles($columns)
        {
            $columns['cb'] = "<input type=\"checkbox\" />";
            $columns['title'] = esc_html__('Invoice', 'golo-framework');
            $columns['invoice_status'] = esc_html__('Status', 'golo-framework');
            $columns['invoice_payment_method'] = esc_html__('Payment Method', 'golo-framework');
            $columns['invoice_payment_type'] = esc_html__('Payment Type', 'golo-framework');
            $columns['invoice_price'] = esc_html__('Money', 'golo-framework');
            $columns['invoice_user_id'] = esc_html__('Buyer', 'golo-framework');
            $columns['date'] = esc_html__('Date', 'golo-framework');
            $new_columns = array();
            $custom_order = array('cb', 'title','invoice_status', 'invoice_payment_method','invoice_payment_type','invoice_price','invoice_user_id','date');
            foreach ($custom_order as $colname){
                $new_columns[$colname] = $columns[$colname];
            }
            return $new_columns;
        }

        /**
         * sortable_columns
         * @param $columns
         * @return mixed
         */
        public function sortable_columns($columns)
        {
            $columns['title'] = 'title';
            $columns['invoice_status'] = 'invoice_status';
            $columns['invoice_payment_method'] = 'invoice_payment_method';
            $columns['invoice_payment_type'] = 'invoice_payment_type';
            $columns['invoice_price'] = 'invoice_price';
            $columns['date'] = 'date';
            return $columns;
        }

        /**
         * @param $vars
         * @return array
         */
        public function column_orderby($vars) {
            if ( !is_admin() )
                return $vars;

            if ( isset($vars['orderby']) && 'invoice_payment_method' == $vars['orderby'] ) {
                $vars = array_merge($vars, array(
                    'meta_key' => GOLO_METABOX_PREFIX. 'invoice_payment_method',
                    'orderby' => 'meta_value',
                ));
            }
            if ( isset($vars['orderby']) && 'invoice_payment_type' == $vars['orderby'] ) {
                $vars = array_merge($vars, array(
                    'meta_key' => GOLO_METABOX_PREFIX. 'invoice_payment_type',
                    'orderby' => 'meta_value',
                ));
            }
            if ( isset($vars['orderby']) && 'invoice_price' == $vars['orderby'] ) {
                $vars = array_merge($vars, array(
                    'meta_key' => GOLO_METABOX_PREFIX. 'invoice_price',
                    'orderby' => 'meta_value_num',
                ));
            }
            if ( isset($vars['orderby']) && 'invoice_status' == $vars['orderby'] ) {
                $vars = array_merge($vars, array(
                    'meta_key' => GOLO_METABOX_PREFIX. 'invoice_payment_status',
                    'orderby' => 'meta_value_num',
                ));
            }
            return $vars;
        }
        /**
         * Display custom column for invoice
         * @param $column
         */
        public function display_custom_column($column)
        {
            global $post;
            $invoice_meta = get_post_meta($post->ID, GOLO_METABOX_PREFIX . 'invoice_meta', true);
            switch ($column) {
                case 'invoice_payment_method':
                    echo Golo_Invoice::get_invoice_payment_method($invoice_meta['invoice_payment_method']);
                    break;
                case 'invoice_payment_type':
                    echo Golo_Invoice::get_invoice_payment_type($invoice_meta['invoice_payment_type']);
                    break;
                case 'invoice_price':
                    echo esc_html($invoice_meta['invoice_item_price']);
                    break;
                case 'invoice_user_id':
                    $user_info = get_userdata($invoice_meta['invoice_user_id']);
                    if($user_info)
                    {
                        echo esc_html($user_info->display_name);
                    }
                    break;
                case 'invoice_status':
                    $invoice_status = get_post_meta($post->ID, GOLO_METABOX_PREFIX . 'invoice_payment_status', true);
                    if ($invoice_status == 0) {
                        echo '<span class="golo-label-red">' . esc_html__('Not Paid', 'golo-framework') . '</span>';
                    } else {
                        echo '<span class="golo-label-blue">' . esc_html__('Paid', 'golo-framework') . '</span>';
                    }
                    break;
            }
        }

        /**
         * Get invoices by place
         * @param $place_id
         */
        public function get_invoices_by_place($place_id)
        {
            $args = array(
                'post_type' => 'invoice',
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => GOLO_METABOX_PREFIX . 'invoice_item_id',
                        'value' => $place_id,
                        'compare' => '=',
                        'type' => 'NUMERIC'
                    ),
                    array(
                        'key' => GOLO_METABOX_PREFIX . 'invoice_payment_type',
                        'value' => 'Package',
                        'compare' => '!=',
                        'type' => 'CHAR'
                    )
                )
            );
            $invoices = get_posts($args);
            if (!$invoices) {
                esc_html_e('No invoice', 'golo-framework');
            } else {
                foreach ($invoices as $invoice):
                    if ($invoice->ID > 0):
                        ?>
                        <a title="<?php esc_attr_e('Click to view invoice', 'golo-framework') ?>"
                           href="<?php echo get_edit_post_link($invoice->ID) ?>"><?php echo esc_html($invoice->ID); ?></a>
                        <?php
                    endif;
                endforeach;
            }
        }

        /**
         * Modify invoice slug
         * @param $existing_slug
         * @return string
         */
        public function modify_invoice_slug($existing_slug)
        {
            $invoice_url_slug = golo_get_option('invoice_url_slug');
            if ($invoice_url_slug) {
                return $invoice_url_slug;
            }
            return $existing_slug;
        }
        /**
         * filter_restrict_manage_invoice
         */
        public function filter_restrict_manage_invoice() {
            global $typenow;
            $post_type = 'invoice';

            if ($typenow == $post_type){
                //Invoice Status
                $values=array(
                    'not_paid'=>esc_html__('Not Paid','golo-framework'),
                    'paid'=>esc_html__('Paid','golo-framework'),
                );
                ?>
                <select name="invoice_status">
                    <option value=""><?php esc_html_e('All Status', 'golo-framework'); ?></option>
                    <?php $current_v = isset($_GET['invoice_status'])? golo_clean(wp_unslash($_GET['invoice_status'])) :'';
                    foreach ($values as $value => $label) {
                        printf
                        (
                            '<option value="%s"%s>%s</option>',
                            $value,
                            $value == $current_v? ' selected="selected"':'',
                            $label
                        );
                    }
                    ?>
                </select>
                <?php
                //Payment method
                $values=array(
                    'Paypal' => esc_html__('Paypal', 'golo-framework'),
                    'Stripe' => esc_html__('Stripe', 'golo-framework'),
                    'Wgolo_Transfer' => esc_html__('Wire Transfer', 'golo-framework'),
                    'Free_Package' => esc_html__('Free Package', 'golo-framework'),
                );
                ?>
                <select name="invoice_payment_method">
                    <option value=""><?php esc_html_e('All Payment Methods', 'golo-framework'); ?></option>
                    <?php $current_v = isset($_GET['invoice_payment_method'])? wp_unslash(golo_clean($_GET['invoice_payment_method'])) :'';
                    foreach ($values as $value => $label) {
                        printf
                        (
                            '<option value="%s"%s>%s</option>',
                            $value,
                            $value == $current_v? ' selected="selected"':'',
                            $label
                        );
                    }
                    ?>
                </select>
                <?php
                //Payment type
                $values=array(
                    'Package'=>esc_html__('Package','golo-framework'),
                    'Listing'=>esc_html__('Listing','golo-framework'),
                    'Upgrade_To_Featured'=>esc_html__('Upgrade to Featured','golo-framework'),
                    'Listing_With_Featured'=>esc_html__('Listing with Featured','golo-framework'),
                );
                ?>
                <select name="invoice_payment_type">
                    <option value=""><?php esc_html_e('All Payment Types', 'golo-framework'); ?></option>
                    <?php $current_v = isset($_GET['invoice_payment_type'])? golo_clean(wp_unslash($_GET['invoice_payment_type'])) :'';
                    foreach ($values as $value => $label) {
                        printf
                        (
                            '<option value="%s"%s>%s</option>',
                            $value,
                            $value == $current_v? ' selected="selected"':'',
                            $label
                        );
                    }
                    $invoice_user = isset($_GET['invoice_user'])? golo_clean(wp_unslash($_GET['invoice_user'])) :'';
                    ?>
                </select>
                <input type="text" placeholder="<?php esc_attr_e('Buyer','golo-framework');?>" name="invoice_user" value="<?php echo esc_attr($invoice_user);?>">
            <?php }
        }

        /**
         * invoice_filter
         * @param $query
         */
        public function invoice_filter($query) {
            global $pagenow;
            $post_type = 'invoice';
            $q_vars    = &$query->query_vars;$filter_arr=array();
            if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type)
            {
                $invoice_user = isset($_GET['invoice_user']) ? golo_clean(wp_unslash($_GET['invoice_user'])) : '';
                if($invoice_user !== '')
                {
                    $user = get_user_by('login',$invoice_user);
                    $user_id=-1;
                    if($user)
                    {
                        $user_id=$user->ID;
                    }
                    $filter_arr[] = array(
                        'key' => GOLO_METABOX_PREFIX. 'invoice_user_id',
                        'value' => $user_id,
                        'compare' => 'IN',
                    );
                }

                $_invoice_status = isset($_GET['invoice_status']) ? golo_clean(wp_unslash($_GET['invoice_status'])) : '';

                if($_invoice_status !== '')
                {
                    $invoice_status=0;
                    if($_invoice_status=='paid')
                    {
                        $invoice_status=1;
                    }
                    $filter_arr[] = array(
                        'key' =>GOLO_METABOX_PREFIX. 'invoice_payment_status',
                        'value' => $invoice_status,
                        'compare' => '=',
                    );
                }

                $invoice_payment_method = isset($_GET['invoice_payment_method']) ? golo_clean(wp_unslash($_GET['invoice_payment_method'])) : '';

                if($invoice_payment_method !== '') {
                    $filter_arr[] = array(
                        'key' => GOLO_METABOX_PREFIX . 'invoice_payment_method',
                        'value' => $invoice_payment_method,
                        'compare' => '=',
                    );
                }

                $invoice_payment_type = isset($_GET['invoice_payment_type']) ? golo_clean(wp_unslash($_GET['invoice_payment_type'])) : '';

                if($invoice_payment_type !== '') {
                    $filter_arr[] = array(
                        'key' => GOLO_METABOX_PREFIX . 'invoice_payment_type',
                        'value' => $invoice_payment_type,
                        'compare' => '=',
                    );
                }
                if (! empty($filter_arr) ) {
                    $q_vars['meta_query'] = $filter_arr;
                }
            }
        }

        /**
         * @param $actions
         * @param $post
         * @return mixed
         */
        public function modify_list_row_actions( $actions, $post ) {
            // Check for your post type.
            if ( $post->post_type == 'invoice' ) {
                unset( $actions[ 'view' ] );
            }
            return $actions;
        }
    }
}