<?php
/**
 * Import Demos Box
 *
 * @package Golo_Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$demos       = Golo_Importer::get_import_demos();
$demos_count = count( $demos );
?>
<div class="golo-box golo-box--green golo-box--import-demos">
	<div class="golo-box__header">
		<span class="golo-box__icon"><i class="fad fa-download"></i></span>
		<h3>
			<?php
			if ( ! empty( $demos ) && 1 < $demos_count ) {
				esc_html_e( 'Select a demo to import', 'golo-framework' );
			} elseif ( 1 === $demos_count ) {
				$demo     = reset( $demos );
				$name     = isset( $demo['name'] ) ? $demo['name'] : esc_html__( 'Import Demo', 'golo-framework' );
				$imported = get_option( GLF_THEME_SLUG . '_' . key( $demos ) . '_imported', false );

				if ( ! $imported ) :
					echo esc_html( $name );
				else :
					echo esc_html( $name );
					?>
					<small><?php esc_html_e( '(has been imported before)', 'golo-framework' ); ?></small>
					<?php
				endif;
			}
			?>
		</h3>
		<?php if ( 1 === $demos_count ) : ?>
			<a href="#" class="button golo-import-demo__button" data-demo-slug="<?php echo esc_attr( key( $demos ) ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'fetch_demo_steps' ) ); ?>"><?php esc_html_e( 'Import Demo Data', 'golo-framework' ); ?></a>
		<?php endif; ?>

		<a href="#" class="button golo-import-refresh__button"><?php esc_html_e( 'Refresh Data', 'golo-framework' ); ?></a>
	</div>
	<div class="golo-box__body<?php echo esc_attr( 1 < $demos_count ) ? ' golo-box__body--flex' : ''; ?>">

		<?php
		/**
		 * Hook: golo_box_import_demos_before_content
		 */
		do_action( 'golo_box_import_demos_before_content' );
		?>

		<p class="golo-error-text"></p>

		<?php if ( ! empty( $demos ) ) : ?>
			
			<?php 
			$grid_class = '';
			if ( 0 < $demos_count ) {
				$grid_class .= ' grid columns-3';
			}
			?>
			<div class="list-demo <?php echo esc_attr( $grid_class ); ?>">

			<?php foreach ( $demos as $demo_slug => $demo ) : ?>
				<?php $imported = get_option( GLF_THEME_SLUG . '_' . $demo_slug . '_imported', false ); ?>
				<?php if ( isset( $demo['name'], $demo['preview_image_url'] ) ) : ?>
					<?php
					$css_class = "golo-import-demo golo-import-demo--{$demo_slug}";
					?>
				<div class="<?php echo esc_attr( $css_class ); ?>">
					<div class="golo-import-demo__inner">
						<div class="golo-import-demo__preview">
							<img src="<?php echo esc_attr( $demo['preview_image_url'] ); ?>" alt="<?php echo esc_attr( $demo['name'] ); ?>" />
						</div>

						<?php if ( 1 < $demos_count ) : ?>
							<div class="golo-import-demo__footer">
								<p class="golo-import-demo__name">
									<?php if ( ! $imported ) : ?>
										<span><?php echo esc_html( $demo['name'] ); ?></span>
									<?php else : ?>
										<span>
											<?php echo esc_html( $demo['name'] ); ?>
											<small><?php esc_html_e( '(has been imported before)', 'golo-framework' ); ?></small>
										</span>
									<?php endif; ?>
									<?php if ( isset( $demo['description'] ) ) : ?>
										<span class="golo-import-demo__help hint--right" aria-label="<?php echo esc_attr( $demo['description'] ); ?>"><i class="fad fa-question-circle"></i></span>
									<?php endif; ?>
								</p>
								<a href="#" class="button golo-import-demo__button" data-demo-slug="<?php echo esc_attr( $demo_slug ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'fetch_demo_steps' ) ); ?>">
									<?php esc_html_e( 'Import', 'golo-framework' ); ?>
								</a>
							</div>
						<?php endif; ?>
					</div>
				</div>
				<!-- /Import <?php echo esc_html( $demo['name'] ); ?> -->
				<?php endif; ?>
			<?php endforeach; ?>

			</div>
		<?php endif; ?>

		<?php
		/**
		 * Hook: golo_box_import_demos_after_content
		 */
		do_action( 'golo_box_import_demos_after_content' );
		?>

	</div>

	<div id="golo-import-demo-popup" class="golo-popup mfp-hide">
	</div>
</div>
