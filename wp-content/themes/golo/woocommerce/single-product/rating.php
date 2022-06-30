<?php
/**
 * Single Product Rating
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/rating.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $product;

if ( ! wc_review_ratings_enabled() ) {
	return;
}

$rating_count = $product->get_rating_count();
$review_count = $product->get_review_count();
$average      = $product->get_average_rating();

if ( $rating_count > 0 ) : ?>

	<div class="woocommerce-product-rating">
		<div class="author-rating">
            <span class="star <?php if( intval($average) >= 1 ) : echo 'checked';endif; ?>">
                <i class="la la-star"></i>
            </span>
            <span class="star <?php if( intval($average) >= 2 ) : echo 'checked';endif; ?>">
                <i class="la la-star"></i>
            </span>
            <span class="star <?php if( intval($average) >= 3 ) : echo 'checked';endif; ?>">
                <i class="la la-star"></i>
            </span>
            <span class="star <?php if( intval($average) >= 4 ) : echo 'checked';endif; ?>">
                <i class="la la-star"></i>
            </span>
            <span class="star <?php if( intval($average) == 5 ) : echo 'checked';endif; ?>">
                <i class="la la-star"></i>
            </span>
        </div>
        
		<?php if ( comments_open() ) : ?>
			<?php //phpcs:disable ?>
			<a href="#reviews" class="woocommerce-review-link" rel="nofollow">(<?php printf( _n( '%s review', '%s reviews', $review_count, 'golo' ), '<span class="count">' . esc_html( $review_count ) . '</span>' ); ?>)</a>
			<?php // phpcs:enable ?>
		<?php endif ?>
	</div>

<?php endif; ?>
