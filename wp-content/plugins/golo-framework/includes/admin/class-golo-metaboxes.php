<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Golo_Metaboxes')) {
    /**
     * Class Golo_Metaboxes
     */
    class Golo_Metaboxes
    {
        /**
         * Meta boxes setup
         */
        public function meta_boxes_setup()
        {
            global $typenow;

            if( $typenow == 'place' ) {
                add_action('add_meta_boxes', array($this, 'render_verified_listing_meta_boxes'));
                add_action('save_post', array($this, 'save_place_metaboxes'), 10, 2);
                add_action('save_post', array($this, 'save_claim_listing_metaboxes'), 10, 2);
            }

            if ($typenow == 'user_package') {
                add_action('add_meta_boxes', array($this, 'render_user_package_meta_boxes'));
            }

            if ($typenow == 'invoice') {
                add_action('add_meta_boxes', array($this, 'render_invoice_meta_boxes'));
                add_action('save_post', array($this, 'save_invoices_metaboxes'), 10, 2);
            }

            if( $typenow == 'post' ) {
                add_action('add_meta_boxes', array($this, 'render_post_meta_boxes'));
                add_action('save_post', array($this, 'save_post_metaboxes'), 10, 2);
            }

            if ($typenow == 'shop_order') {
                add_action('save_post', array($this, 'save_order_item'), 10, 2);
            }
        }

        /**
         * Render agent package meta boxes
         */
        public function render_user_package_meta_boxes()
        {
            add_meta_box(
                GOLO_METABOX_PREFIX . 'user_package_metaboxes',
                esc_html__('Package Details', 'golo-framework'),
                array($this, 'user_package_meta'),
                array('user_package'),
                'normal',
                'default'
            );
        }

        /**
         * Render property paid meta boxes
         */
        public function render_post_meta_boxes()
        {
            add_meta_box(
                GOLO_METABOX_PREFIX . 'post_city',
                esc_html__('City', 'golo-framework'),
                array($this, 'post_city_meta'),
                'post',
                'side'
            );
        }

        /**
         * Agent package meta
         * @param $object
         */
        public function user_package_meta($object)
        {
            $postID = $object->ID;
            $package_user_id = get_post_meta($postID, GOLO_METABOX_PREFIX . 'package_user_id', true);
            $package_id = get_user_meta($package_user_id, GOLO_METABOX_PREFIX . 'package_id', true);
            $package_number_listings = get_user_meta($package_user_id, GOLO_METABOX_PREFIX . 'package_number_listings', true);
            $package_number_featured = get_user_meta($package_user_id, GOLO_METABOX_PREFIX . 'package_number_featured', true);
            $package_activate_date = get_user_meta($package_user_id, GOLO_METABOX_PREFIX . 'package_activate_date', true);
            $package_name = get_the_title($package_id);
            $user_info = get_userdata($package_user_id);
            $golo_package = new Golo_Package();
            $get_expired_date = $golo_package->get_expired_date($package_id, $package_user_id);
            ?>
            <table class="form-table">
                <tbody>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Buyer:', 'golo-framework'); ?></label></th>
                    <td><strong><?php if($user_info) echo esc_attr($user_info->display_name); ?></strong>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Package:', 'golo-framework'); ?></label></th>
                    <td><strong><?php echo esc_attr($package_name); ?></strong>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Number Listings:', 'golo-framework'); ?></label>
                    </th>
                    <td><strong><?php echo esc_attr($package_number_listings); ?></strong>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label><?php esc_html_e('Number Featured Listings:', 'golo-framework'); ?></label>
                    </th>
                    <td><strong><?php echo esc_attr($package_number_featured); ?></strong>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Activate Date:', 'golo-framework'); ?></label></th>
                    <td><strong><?php echo esc_attr($package_activate_date); ?></strong>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Expgolo Date:', 'golo-framework'); ?></label></th>
                    <td><strong><?php echo esc_attr($get_expired_date); ?></strong>
                    </td>
                </tr>
                </tbody>
            </table>
            <?php
        }

        /**
         * Render City
         * @param $object
         */
        public function post_city_meta($object)
        {
            $post_city = get_post_meta($object->ID, GOLO_METABOX_PREFIX . 'post_city', true);
            $place_cities = get_terms(array(
                'taxonomy'   => 'place-city',
                'hide_empty' => false,
                'orderby'    => 'term_id',
                'order'      => 'DESC',
            ));
            ?>
            <div id="post-city-<?php echo esc_attr($post_city); ?>" class="selectdiv golo-post-select-meta-box-wrap">
                <select id="golo[golo_post_city]" name="golo[golo_post_city]" class="components-select-control__input">
                    <?php
                    echo '<option value="">' . esc_html__('None', 'golo-framework') . '</option>';
                    foreach ($place_cities as $place_city) :
                        $slug = $place_city->slug;
                        $name = $place_city->name;
                        echo '<option ' . selected($post_city, $slug, false) . ' value="' . $slug . '">' . $name . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            <?php
        }

        /**
         * Render claim listing meta boxes
         */
        
        public function render_verified_listing_meta_boxes()
        {
            $enable_claim_listing       = golo_get_option('enable_claim_listing', '1' );
            if ($enable_claim_listing) :
                add_meta_box(
                    GOLO_METABOX_PREFIX . 'verified_listing',
                    esc_html__('Verified Listing', 'golo-framework'),
                    array($this, 'verified_listing'),
                    array('place'),
                    'side',
                    'high'
                );
            endif;
        }

        /**
         * Verified Listing
         * @param $object
         */
        public function verified_listing($object)
        {
            wp_nonce_field(plugin_basename(__FILE__), 'golo_verified_listing_nonce_field');
            $verified_listing = get_post_meta($object->ID, GOLO_METABOX_PREFIX . 'verified_listing', true);
            ?>
            <div class="golo_meta_control custom_sidebar_js">
                <?php
                if ($verified_listing == 0 || $verified_listing == '') {
                    echo '<span class="golo-label-red notice inline notice-warning notice-alt">' . esc_html__('Not verify', 'golo-framework') . '</span>';
                } else {
                    echo '<span class=" notice inline notice-success notice-alt">' . esc_html__('Verified', 'golo-framework') . '</span>';
                }
                
                ?>

                <?php if ($verified_listing == 0 || $verified_listing == '') { ?>
                <div class="golo-set-item-paid">
                    <input type="checkbox" id="golo[golo_verified_listing]" name="golo[golo_verified_listing]" value="0"/>
                    <label class="" for="golo[golo_verified_listing]"><?php esc_html_e('Tick the checkbox to mark it as Verified', 'golo-framework'); ?></label>

                </div>
                <?php } ?>
            </div>
            <?php
        }

        /**
         * Render invoice meta boxes
         */
        public function render_invoice_meta_boxes()
        {
            add_meta_box(
                GOLO_METABOX_PREFIX . 'invoice_metaboxes',
                esc_html__('Invoice Details', 'golo-framework'),
                array($this, 'invoice_meta'),
                array('invoice'),
                'normal',
                'default'
            );

            add_meta_box(
                GOLO_METABOX_PREFIX . 'invoice_payment_status',
                esc_html__('Payment Status', 'golo-framework'),
                array($this, 'invoice_payment_status'),
                array('invoice'),
                'side',
                'high'
            );
        }

        /**
         * Invoice meta
         * @param $object
         */
        public function invoice_meta($object)
        {
            $golo_invoice = new Golo_Invoice();
            $golo_meta = $golo_invoice->get_invoice_meta($object->ID);
            ?>
            <table class="form-table">
                <tbody>
                <tr>
                    <th scope="row"><?php esc_html_e('Invoice ID:', 'golo-framework'); ?></th>
                    <td><strong><?php echo intval($object->ID); ?></strong></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Payment Method:', 'golo-framework'); ?></th>
                    <td>
                        <strong>
                            <?php echo Golo_Invoice::get_invoice_payment_method($golo_meta['invoice_payment_method']); ?>
                        </strong>
                    </td>
                </tr>
                <?php if (($golo_meta['invoice_payment_method'] == 'Stripe') || ($golo_meta['invoice_payment_method'] == 'Paypal')): ?>
                    <tr>
                        <th scope="row"><?php esc_html_e('PaymentID (PayPal,Stripe):', 'golo-framework'); ?></th>
                        <td>
                            <strong>
                                <?php echo esc_attr($golo_meta['trans_payment_id']); ?>
                            </strong>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('PayerID (PayPal,Stripe):', 'golo-framework'); ?></th>
                        <td>
                            <strong>
                                <?php echo esc_attr($golo_meta['trans_payer_id']); ?>
                            </strong>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <th scope="row"><?php esc_html_e('Payment Type:', 'golo-framework'); ?></th>
                    <td>
                        <strong><?php echo Golo_Invoice::get_invoice_payment_type($golo_meta['invoice_payment_type']); ?></strong>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php
                        if ($golo_meta['invoice_payment_type'] == 'Package') {
                            esc_html_e('Package ID:', 'golo-framework');
                        } else {
                            esc_html_e('Place ID:', 'golo-framework');
                        }
                        ?>
                    </th>
                    <td>
                        <strong><?php echo esc_attr($golo_meta['invoice_item_id']); ?></strong>
                        <?php
                        if ($golo_meta['invoice_payment_type'] == 'Package') {
                            ?>
                            <a href="<?php echo get_edit_post_link($golo_meta['invoice_item_id']) ?>"><?php esc_html_e('(Edit)', 'golo-framework'); ?></a>
                            <?php
                        } else {
                            if (current_user_can('read_place', $golo_meta['invoice_item_id'])) {
                                ?>
                                <a href="<?php echo get_permalink($golo_meta['invoice_item_id']) ?>"><?php esc_html_e('(View)', 'golo-framework'); ?></a>
                                <?php
                            }
                            if (current_user_can('edit_place', $golo_meta['invoice_item_id'])) {
                                ?>
                                <a href="<?php echo get_edit_post_link($golo_meta['invoice_item_id']) ?>"><?php esc_html_e('(Edit)', 'golo-framework'); ?></a>
                                <?php
                            }
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Item Price:', 'golo-framework'); ?></th>
                    <td>
                        <strong>
                            <?php
                            $item_price = golo_get_format_money($golo_meta['invoice_item_price']);
                            echo esc_attr($item_price);
                            ?>   
                        </strong>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Purchase Date:', 'golo-framework'); ?>
                    </th>
                    <td>
                        <strong><?php echo esc_attr($golo_meta['invoice_purchase_date']); ?></strong>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Buyer Name:', 'golo-framework'); ?></th>
                    <td>
                        <strong>
                            <?php
                            $user_info = get_userdata($golo_meta['invoice_user_id']);
                            if (current_user_can('edit_users') && $user_info) {
                                echo '<a href="' . get_edit_user_link($golo_meta['invoice_user_id']) . '">' . esc_attr($user_info->display_name) . '</a>';
                            } else {
                                if($user_info) echo esc_attr($user_info->display_name);
                            }
                            ?>
                        </strong>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Buyer Mobile:', 'golo-framework'); ?></th>
                    <td>
                        <strong>
                            <?php
                            $agent_mobile_number = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_mobile_number', $golo_meta['invoice_user_id']);
                            echo esc_attr($agent_mobile_number);
                            ?>
                        </strong>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Buyer Email:', 'golo-framework'); ?></th>
                    <td>
                        <strong>
                            <?php if($user_info) echo esc_attr($user_info->user_email); ?>
                        </strong>
                    </td>
                </tr>
                </tbody>
            </table>
            <?php
        }

        /**
         * Invoice payment status
         * @param $object
         */
        public function invoice_payment_status($object)
        {
            wp_nonce_field(plugin_basename(__FILE__), 'golo_invoice_nonce_field');
            $payment_status = get_post_meta($object->ID, GOLO_METABOX_PREFIX . 'invoice_payment_status', true);
            ?>
            <div class="golo_meta_control custom_sidebar_js">
                <?php
                if ($payment_status == 0) {
                    echo '<span class="golo-label-red notice inline notice-warning notice-alt">' . esc_html__('Not Paid', 'golo-framework') . '</span>';
                } else {
                    echo '<span class="golo-label-blue notice inline notice-success notice-alt">' . esc_html__('Paid', 'golo-framework') . '</span>';
                }
                if ($payment_status == 0) {
                ?>
                    <div class="golo-set-item-paid">
                        <input type="checkbox" id="golo[golo_payment_status]" name="golo[golo_payment_status]" value="0"/>
                        <label class="golo-label-blue" for="golo[golo_payment_status]"><?php esc_html_e('Set item paid', 'golo-framework'); ?></label>
                    </div>
                <?php } ?>
            </div>
            <?php
        }

        /**
         * Save property metaboxes
         * @param $post_id
         * @return bool
         */
        public function save_post_metaboxes($post_id)
        {
            if (!is_admin()) return false;
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
                return false;
            if (isset($_POST['golo']['golo_post_city'])) {
                $post_city = golo_clean(wp_unslash($_POST['golo']['golo_post_city']))  ;
                update_post_meta($post_id, GOLO_METABOX_PREFIX . 'post_city', $post_city);
            }
            return true;
        }

        /**
         * Save property metaboxes
         * @param $post_id
         * @param $post
         * @return bool
         */
        public function save_claim_listing_metaboxes($post_id, $post)
        {
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
                return false;
            if (!isset($_POST['golo_verified_listing_nonce_field']) || !wp_verify_nonce($_POST['golo_verified_listing_nonce_field'], plugin_basename(__FILE__))) {
                return false;
            }
            if ($post->post_type == 'place' && isset($_POST['golo'])) {
                if (isset($_POST['golo']['golo_verified_listing'])) {
                    update_post_meta($post_id, GOLO_METABOX_PREFIX . 'verified_listing', 1);
                }
            }

            return true;
        }

        /**
         * Save invoices metaboxes
         * @param $post_id
         * @param $post
         * @return bool
         */
        public function save_invoices_metaboxes($post_id, $post)
        {
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
                return false;
            if (!isset($_POST['golo_invoice_nonce_field']) || !wp_verify_nonce($_POST['golo_invoice_nonce_field'], plugin_basename(__FILE__))) {
                return false;
            }
            if ($post->post_type == 'invoice' && isset($_POST['golo'])) {
                $post_type = get_post_type_object($post->post_type);
                if (!current_user_can($post_type->cap->edit_post, $post_id))
                    return false;
                if (isset($_POST['golo']['golo_payment_status'])) {
                    $golo_invoice = new Golo_Invoice();
                    $golo_meta = $golo_invoice->get_invoice_meta($post_id);
                    $user_id = $golo_meta['invoice_user_id'];
                    $user = get_user_by('id', $user_id);
                    $user_email = $user->user_email;
                    if ($golo_meta['invoice_payment_type'] == 'Package') {
                        $package_id = $golo_meta['invoice_item_id'];
                        $golo_package = new Golo_Package();
                        $golo_package->insert_user_package($user_id, $package_id);
                        update_post_meta($post_id, GOLO_METABOX_PREFIX . 'invoice_payment_status', 1);
                        $args = array();
                        golo_send_email($user_email, 'mail_activated_package', $args);
                    } else {
                        $place_id = $golo_meta['invoice_item_id'];
                        if ($golo_meta['invoice_payment_type'] == 'Listing') {
                            update_post_meta($place_id, GOLO_METABOX_PREFIX . 'payment_status', 'paid');
                            wp_update_post(array(
                                'ID' => $place_id,
                                'post_status' => 'publish',
                                'post_date' => current_time('mysql'),
                                'post_date_gmt' => current_time('mysql'),
                            ));
                            golo_send_email($user_email, 'mail_activated_listing');
                        } else if ($golo_meta['invoice_payment_type'] == 'Upgrade_To_Featured') {
                            update_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_featured', 1);
                        } else if ($golo_meta['invoice_payment_type'] == 'Listing_With_Featured') {
                            update_post_meta($place_id, GOLO_METABOX_PREFIX . 'payment_status', 'paid');
                            update_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_featured', 1);
                            wp_update_post(array(
                                'ID' => $place_id,
                                'post_status' => 'publish',
                                'post_date' => current_time('mysql'),
                                'post_date_gmt' => current_time('mysql'),
                            ));
                            golo_send_email($user_email, 'mail_activated_listing');
                        }
                        update_post_meta($post_id, GOLO_METABOX_PREFIX . 'invoice_payment_status', 1);

                    }

                    $golo_admin_invoice = new Golo_Admin_Invoice();
                    $product_package = $golo_admin_invoice->get_product_by_name(get_the_title($package_id));
                    if ($product_package) {
                       wp_delete_post($product_package->ID);
                    }
                }
            }
            return true;
        }

        /**
         * Save place metaboxes
         * @param $post_id
         * @return bool
         */
        public function save_place_metaboxes($post_id)
        {
            if ( !is_admin() ) return false;
            if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
                return false;

            $agent_display_option = get_post_meta($post_id, GOLO_METABOX_PREFIX . 'agent_display_option', true);

            if (isset($agent_display_option) && ('author_info' == $agent_display_option)) {
                $post_author = get_post_field('post_author', $post_id);
                update_post_meta($post_id, GOLO_METABOX_PREFIX . 'place_author', $post_author);
            } else {
                update_post_meta($post_id, GOLO_METABOX_PREFIX . 'place_author', '');
            }

            if ($agent_display_option != 'agent_info') {
                update_post_meta($post_id, GOLO_METABOX_PREFIX . 'place_agent', '');
            }

            $place_identity = get_post_meta($post_id, GOLO_METABOX_PREFIX . 'place_identity', true);
            if (empty($place_identity)) {
                update_post_meta($post_id, GOLO_METABOX_PREFIX . 'place_identity', $post_id);
            }

            $place_price_on_call = get_post_meta($post_id, GOLO_METABOX_PREFIX . 'place_price_on_call', true);
            if($place_price_on_call == '1')
            {
                update_post_meta($post_id, GOLO_METABOX_PREFIX . 'place_price_short', '');
                update_post_meta($post_id, GOLO_METABOX_PREFIX . 'place_price', '');
                update_post_meta($post_id, GOLO_METABOX_PREFIX . 'place_price_unit', 1);
            }else{
                $enable_price_unit = golo_get_option('enable_price_unit', '1');
                if( $enable_price_unit == '0' )
                {
                    update_post_meta($post_id, GOLO_METABOX_PREFIX . 'place_price_unit', 1);
                }
                $place_price_short = get_post_meta($post_id, GOLO_METABOX_PREFIX . 'place_price_short', true);
                $place_price_unit  = get_post_meta($post_id, GOLO_METABOX_PREFIX . 'place_price_unit', true);
                if ( !empty($place_price_short) && is_numeric($place_price_short) ) {
                    if (!empty($place_price_unit) && is_numeric($place_price_unit) && intval($place_price_unit)>1) {
                        $place_price = doubleval($place_price_short)*intval($place_price_unit);
                    }
                    else
                    {
                        $place_price = doubleval($place_price_short);
                    }
                }
                else{
                    $place_price='';
                }
                update_post_meta($post_id, GOLO_METABOX_PREFIX . 'place_price', $place_price);
            }

            return true;
        }

        /**
         * Save Order Item
         * @param $post_id
         * @return bool
         */
        public function save_order_item( $post_id, $post ) {

            if ( 'shop_order' == $post->post_type ) {
                $order_id           = $post_id;
                $order              = wc_get_order( $post_id );
                if ($order->status == 'completed') {
                    $package_id         = get_post_meta( $order_id, 'billing_package_id', true );
                    $golo_admin_invoice = new Golo_Admin_Invoice();
                    $product_package    = $golo_admin_invoice->get_product_by_name(get_the_title($package_id));
                     
                    $invoice_id         = get_post_meta( $order_id, 'invoice_id', true );
                    $golo_invoice       = new Golo_Invoice();
                    $golo_meta          = $golo_invoice->get_invoice_meta($invoice_id);
                    $user_id            = $golo_meta['invoice_user_id'];
                    $user               = get_user_by('id', $user_id);
                    $user_email         = $user->user_email;
                    $golo_package       = new Golo_Package();
                    $golo_package->insert_user_package($user_id, $package_id);
                    if ($product_package) {
                        wp_delete_post($product_package->ID);
                        update_post_meta(intval($invoice_id), GOLO_METABOX_PREFIX . 'invoice_payment_status', 1);
                        $args = array();
                        golo_send_email($user_email, 'mail_activated_package', $args);
                    }
                }
            }
        }
    }
}