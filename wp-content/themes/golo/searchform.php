<?php
/**
 * Search form
 *
 * @package Golo
 */

$post_type    = 'post';
$place_holder = esc_html__( 'Search posts...', 'golo' );

// if ( class_exists('WooCommerce') ) {
// 	$post_type    = 'product';
// 	$place_holder = esc_html__( 'Search products...', 'golo' );
// }

// if ( class_exists('Golo_Framework') ) {
// 	$post_type    = 'place';
// 	$place_holder = esc_html__( 'Search places, cities', 'golo' );
// }

?>
<form role="search" method="get" class="custom-form-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<div>
		<label class="screen-reader-text" for="s"><?php esc_html_e( 'Search for:', 'golo' ); ?></label>
		<input type="text" class="ip-search" name="s" placeholder="<?php echo esc_attr( $place_holder ); ?>"/>
		<input type="hidden" name="post_type" value="<?php echo esc_attr( $post_type ); ?>"/>
		<button type="submit" class="search-submit">
			<span><?php esc_html_e( 'Search', 'golo' ); ?></span>
			<i class="la la-search large"></i>	
		</button>
	</div>
</form>