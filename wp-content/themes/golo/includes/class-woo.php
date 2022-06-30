<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Custom functions for WooCommerce
 *
 */
if ( ! class_exists( 'Golo_Woo' ) ) {

	class Golo_Woo 
	{

		/**
		 * The constructor.
		 */
		public function __construct() 
		{

			/******************************************************************************************
			 * Shop Page (Product Archive Page)
			 *****************************************************************************************/

			// Remove breadcrumb
			remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );

			add_filter( 'woocommerce_pagination_args', 	'golo_woo_pagination' );
			function golo_woo_pagination( $args ) {
				$args['prev_text'] = '<i class="las la-angle-left"></i>';
				$args['next_text'] = '<i class="las la-angle-right"></i>';
				return $args;
			}

			// Hide default wishlist button
			add_filter( 'yith_wcwl_positions',
				function () {
					return array(
						'add-to-cart' => array(
							'hook'     => '',
							'priority' => 0,
						),
						'thumbnails'  => array(
							'hook'     => '',
							'priority' => 0,
						),
						'summary'     => array(
							'hook'     => '',
							'priority' => 0,
						),
					);
				} 
			);

			// Change number of Related & Up sells product
			add_filter( 'woocommerce_output_related_products_args',
				function ( $args ) {

					$args['posts_per_page'] = 4;
					$args['columns']        = 4;

					return $args;
				} 
			);

			// Hide default compare button
			add_filter( 'yith_woocompare_remove_compare_link_by_cat', '__return_true' );

			/******************************************************************************************
			 * Checkout
			 *****************************************************************************************/
			function custom_override_checkout_fields($fields) {
			    $fields['billing']['billing_first_name']['priority'] = 2;
			    $fields['billing']['billing_last_name']['priority'] = 2;
			    $fields['billing']['billing_company']['priority'] = 3;
			    $fields['billing']['billing_country']['priority'] = 1;
			    $fields['billing']['billing_state']['priority'] = 5;
			    $fields['billing']['billing_address_1']['priority'] = 6;
			    $fields['billing']['billing_address_2']['priority'] = 7;
			    $fields['billing']['billing_city']['priority'] = 8;
			    $fields['billing']['billing_postcode']['priority'] = 9;
			    $fields['billing']['billing_email']['priority'] = 10;
			    $fields['billing']['billing_phone']['priority'] = 11;

			    $fields['billing']['billing_first_name']['placeholder'] = 'First Name';
			    $fields['billing']['billing_last_name']['placeholder'] = 'Last Name';
			    
			    $fields['billing']['billing_email']['label'] = 'Info';
			    $fields['billing']['billing_email']['placeholder'] = 'Email';
			    $fields['billing']['billing_phone']['placeholder'] = 'Phone';

			    return $fields;
			}

			//AJAX immediately update number of items in cart
			add_filter( 'add_to_cart_fragments', 'iconic_cart_count_fragments', 10, 1 );
			function iconic_cart_count_fragments( $fragments ) {

				$fragments['span.cart-count'] = '<span class="cart-count">(' . WC()->cart->get_cart_contents_count() . ')</span>';
			    	return $fragments;	
			}

			/*
			 * Add package ID field
			 */
			if ( ! function_exists( 'golo_add_package_id_field' ) ) {
				add_action( 'woocommerce_after_order_notes', 'golo_add_package_id_field', 10, 1 );
				function golo_add_package_id_field( $checkout ) {

				    // Get an instance of the current user object
				    $package_id = $_GET['package_id'];

				    // Output the hidden link
				    echo '<div id="package-id-field">
				            <input type="hidden" class="input-hidden" name="package_id" id="package-id" value="' . $package_id . '">
				    </div>';
				}
			}


			/*
			 * Save package ID
			 */
			if ( ! function_exists( 'save_package_id_field' ) ) {
				add_action( 'woocommerce_checkout_update_order_meta', 'save_package_id_field', 10, 1 );
				function save_package_id_field( $order_id ) {

				    if ( ! empty( $_POST['package_id'] ) )
				        update_post_meta( $order_id, 'billing_package_id', sanitize_text_field( $_POST['package_id'] ) );

				}
			}

			/*
			 * Add Invoice after WooCommerce sets an order on completed
			 */
			if ( ! function_exists( 'add_invoice' ) ) {
				add_action( 'woocommerce_thankyou', 'add_invoice', 10, 1 );
				function add_invoice($order_id) {

				    $order = wc_get_order( $order_id );

				    $payment_method = 'Woocommerce';
				    $user_id 		= $order->get_user_id();
				    $package_id 	= get_post_meta( $order_id, 'billing_package_id', true );
				    $golo_invoice 	= new Golo_Invoice();

				    $invoice_id 	= $golo_invoice->insert_invoice('Package', $package_id, $user_id, 0, $payment_method, 0);
				    update_post_meta( $order_id, 'invoice_id', $invoice_id );
				}
			}

		}

	}

	new Golo_Woo();
}
