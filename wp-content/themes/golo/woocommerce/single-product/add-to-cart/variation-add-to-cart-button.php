<?php
/**
 * Single variation cart button
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;
?>
<div class="woocommerce-variation-add-to-cart variations_button">

	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

	<?php do_action( 'woocommerce_before_add_to_cart_quantity' ); ?>

	<div class="product-quantity">
		<div class="entry-quantity">
	        <div class="minus btn-quantity">
	        	<i class="la la-minus"></i>
	        </div>
	        <input class="input-text qty text" type="number" inputmode="numeric" pattern="[0-9]*" value="1" name="quantity" max="" min="1" step="1">
	        <div class="plus btn-quantity">
		    	<i class="la la-plus"></i>
		    </div>
	    </div>
	</div>

	<?php do_action( 'woocommerce_after_add_to_cart_quantity' ); ?>

	<button type="submit" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

	<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

	<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>" />
	<input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>" />
	<input type="hidden" name="variation_id" class="variation_id" value="0" />
</div>
