<?php
/**
 * Show demo files need to import
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
?>
<i class="las la-circle-notch la-spin golo-loading__icon"></i>
<form action="#" method="POST" id="demo-steps-form">
	<h4 class="golo-popup__title animated fadeInRight"><?php esc_html_e( 'Choose what to import', 'golo-framework' ); ?></h4>
	<p class="golo-error-text">&nbsp;</p>
	<ul class="golo-demo-steps">
		<li class="golo-demo-steps__item animated fadeInRight">
			<input type="checkbox" name="all_demo_steps" id="golo-all-demo-steps" class="golo-demo-steps__checkbox" checked="true">
			<span class="golo-demo-steps__svg">
				<svg width="18px" height="18px" viewBox="0 0 18 18">
						<path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
						<polyline points="1 9 7 14 15 4"></polyline>
					</svg>
			</span>
			<label for="golo-all-demo-steps" class="golo-demo-steps__label"><?php esc_html_e( 'All', 'golo-framework' ); ?></label>
		</li>
		<?php foreach ( $demo_steps as $key => $val ) : ?>
		<li class="golo-demo-steps__item animated fadeInRight">
			<input type="checkbox" name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" class="golo-demo-steps__checkbox" checked="true">
			<span class="golo-demo-steps__svg">
				<svg width="18px" height="18px" viewBox="0 0 18 18">
						<path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
						<polyline points="1 9 7 14 15 4"></polyline>
					</svg>
			</span>
			<label for="<?php echo esc_attr( $key ); ?>" class="golo-demo-steps__label"><?php echo esc_html( $val ); ?></label>
		</li>
		<?php endforeach; ?>
	</ul>
	<input type="hidden" name="demo_slug" id="demo_slug" value="<?php echo esc_attr( $demo_slug ); ?>">
	<input type="hidden" name="selected_steps" id="selected-steps" value="">
	<input type="hidden" name="_wpnonce" id="_wpnonce" value="<?php echo esc_attr( wp_create_nonce( 'import_demo' ) ); ?>">
	<div class="golo-popup__footer animated fadeInRight">
		<div class="golo-popup__buttons">
			<a href="#" class="golo-popup__close-button"><?php esc_html_e( 'Cancel', 'golo-framework' ); ?></a>
			<button type="submit" class="golo-popup__next-button"><?php esc_html_e( 'Continue', 'golo-framework' ); ?><i class="far fa-long-arrow-alt-right" /></button>
		</div>
	</div>
</form>