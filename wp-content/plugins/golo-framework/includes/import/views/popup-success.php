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
<div class="animated fadeInRight" id="import-success">
	<h4 class="golo-popup__title"><?php esc_html_e( 'All done!', 'golo-framework' ); ?></h4>
	<p class="golo-popup__subtitle"><?php esc_html_e( 'Import is successful! Now customization is as easy as pie. Enjoy it!', 'golo-framework' ); ?></p>
	<?php if ( ! $regenerate_thumbnails ) : ?>
		<p>
			<?php
				echo sprintf(
					/* translators: %s: Regenerate Thumbnails plugin URL */
					wp_kses_post( __( 'You should use <a href="%s" class="thickbox" title="Install Regenerate Thumbnails">Regenerate Thumbnails</a> plugin to regenerate all thumbnail sizes to make sure that everything works fine.', 'golo-framework' ) ),
					esc_url( admin_url( '/plugin-install.php?tab=plugin-information&plugin=regenerate-thumbnails&TB_iframe=true&width=800&height=550' ) )
				);
			?>
		</p>
	<?php endif; ?>
	<div class="golo-popup__footer">
		<div class="golo-popup__buttons">
			<a href="#" class="golo-popup__close-button"><?php esc_html_e( 'Close', 'golo-framework' ); ?></a>
			<a href="<?php echo esc_url( site_url( '/' ) ); ?>" target="_blank" class="golo-popup__next-button"><?php esc_html_e( 'View your website', 'golo-framework' ); ?></a>
		</div>
	</div>
</div>
