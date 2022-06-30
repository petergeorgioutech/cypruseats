<?php
/**
 * Single Product Meta
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/meta.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;
?>
<div class="product_meta">

	<?php do_action( 'woocommerce_product_meta_start' ); ?>

	<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

		<span class="sku_wrapper"><?php esc_html_e( 'SKU:', 'golo' ); ?> <span class="sku"><?php if( $product->get_sku() ) { echo esc_html($product->get_sku()); }else{ esc_html_e( 'N/A', 'golo' ); }; ?></span></span>

	<?php endif; ?>

	<?php echo wc_get_product_category_list( $product->get_id(), ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'golo' ) . ' ', '</span>' ); ?>

	<?php echo wc_get_product_tag_list( $product->get_id(), ', ', '<span class="tagged_as">' . _n( 'Tag:', 'Tags:', count( $product->get_tag_ids() ), 'golo' ) . ' ', '</span>' ); ?>

	<?php do_action( 'woocommerce_product_meta_end' ); ?>

</div>

<?php if( class_exists('Golo_Framework') ) : ?>
<div class="social-share">
    <div class="list-social-icon">
        <a class="facebook" onclick="window.open('https://www.facebook.com/sharer.php?u=<?php echo urlencode( get_permalink() ); ?>','sharer', 'toolbar=0,status=0');" href="javascript:void(0)">
            <i class="la la-facebook-square"></i>
        </a>

        <a class="twitter" onclick="popUp=window.open('https://twitter.com/share?url=<?php echo urlencode( get_permalink()); ?>','sharer','scrollbars=yes');popUp.focus();return false;" href="javascript:void(0)">
            <i class="la la-twitter-square"></i>
        </a>

        <a class="instagram" onclick="window.open('https://www.instagram.com/sharer.php?u=<?php echo urlencode( get_permalink() ); ?>','sharer', 'toolbar=0,status=0');" href="javascript:void(0)">
            <i class="la la-instagram"></i>
        </a>
    </div>
</div>
<?php endif; ?>
