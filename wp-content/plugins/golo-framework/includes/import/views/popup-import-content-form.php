<?php
/**
 * Import content form
 *
 * @package Golo_Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

?>
<div id="import-content-wrapper">
	<h4 class="golo-popup__title animated fadeInRight"><?php esc_html_e( 'Import data', 'golo-framework' ); ?></h4>
	<p class="golo-error-text animated fadeInRight">&nbsp;</p>
	<?php if ( isset( $import_content_steps ) && ! empty( $import_content_steps ) ) : ?>
	<ul class="golo-import-content-list animated fadeInRight">
		<?php
		$i             = 0;
		$content_steps = '';
		foreach ( $import_content_steps as $key => $text ) :
			$content_steps .= $key . ',';
			?>
		<li id="<?php echo esc_attr( $key ); ?>" class="golo-import-content__item" data-action="<?php echo esc_attr( "import_{$key}" ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( "import_{$key}" ) ); ?>">
			<i class="las la-circle-notch la-spin"></i><span class="golo-import-content__text"><?php echo esc_html( $text ); ?></span>
		</li>
		<?php endforeach; ?>
	</ul>
	<input type="hidden" name="import_content_steps" id="import_content_steps" value="<?php echo esc_attr( $content_steps ); ?>">
	<?php endif; ?>
	<input type="hidden" name="demo_slug" id="demo_slug" value="<?php echo esc_attr( $demo_slug ); ?>">
	<div class="golo-popup__footer animated fadeInRight">
		<i class="golo-popup__note"><?php esc_html_e( 'Please do not close this window until the process is completed', 'golo-framework' ); ?></i>
		<a href="#" class="golo-popup__close-button"><?php esc_html_e( 'Close', 'golo-framework' ); ?></a>
	</div>
</form>
