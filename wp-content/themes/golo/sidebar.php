<?php
/**
 * The sidebar containing the main widget area.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 */
?>

<aside id="secondary" class="widget-area">
	
	<div class="widget-area-init">
	
		<?php 
		if( is_single() && ( get_post_type() == 'place' ) ) :
			golo_get_template('single-place/booking.php');
		endif; 
		?>
		
		<?php if( get_post_type() !== 'place' ) { ?>

			<?php dynamic_sidebar( 'sidebar' ); ?>

		<?php }else{ ?>

			<?php dynamic_sidebar( 'place_sidebar' ); ?>
			
		<?php } ?>

	</div>

</aside>
