<?php
/**
 * Copy images form
 *
 * @package Golo_Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
?>
<i class="las la-circle-notch la-spin golo-loading__icon"></i>
<form action="#" method="POST" id="copy-images-form">
	<h4 class="golo-popup__title animated fadeInRight"><?php esc_html_e( 'Copy images', 'golo-framework' ); ?></h4>
	<p class="golo-error-text">&nbsp;</p>
	<?php if ( isset( $media_package_local ) ) : ?>
		<input type="hidden" name="media_package_local" value="<?php echo esc_attr( $media_package_local ); ?>">
	<?php endif; ?>
	<?php if ( isset( $selected_steps_str ) && ! empty( $selected_steps_str ) ) : ?>
		<input type="hidden" name="selected_steps" value="<?php echo esc_attr( $selected_steps_str ); ?>">
	<?php endif; ?>
	<input type="hidden" name="demo_slug" id="demo_slug" value="<?php echo esc_attr( $demo_slug ); ?>">
	<input type="hidden" name="_wpnonce" id="_wpnonce" value="<?php echo esc_attr( wp_create_nonce( 'copy_images' ) ); ?>">
	<div class="golo-popup__footer animated fadeInRight">
		<i class="golo-popup__note"><?php esc_html_e( 'Please do not close this window until the process is completed', 'golo-framework' ); ?></i>
		<a href="#" class="golo-popup__close-button"><?php esc_html_e( 'Close', 'golo-framework' ); ?></a>
	</div>
</form>
