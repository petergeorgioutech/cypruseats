<?php
/**
 * Success content for popup after importing
 *
 * @package Golo_Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$regenerate_thumbnails = apply_filters( 'golo_regenerate_thumbnails', false );
?>
<div class="animated fadeInRight" id="refresh-data">
	<h4 class="golo-popup__title"><?php esc_html_e( 'Refresh done!', 'golo-framework' ); ?></h4>
	<p class="golo-popup__subtitle"><?php esc_html_e( 'Refresh is successful! Now customization is as easy as pie. Enjoy it!', 'golo-framework' ); ?></p>
	<div class="golo-popup__footer">
		<div class="golo-popup__buttons">
			<a href="#" class="golo-popup__close-button"><?php esc_html_e( 'Close', 'golo-framework' ); ?></a>
			<a href="<?php echo esc_url( site_url( '/' ) ); ?>" target="_blank" class="golo-popup__next-button"><?php esc_html_e( 'View your website', 'golo-framework' ); ?></a>
		</div>
	</div>
</div>