<?php
/**
 * Issues Box
 *
 * @package Golo_Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

?>
<div class="golo-box golo-box--red golo-box--import-issues">
	<div class="golo-box__header">
		<span class="golo-box__icon"><i class="fad fa-exclamation-triangle"></i></span>
		<span><?php esc_html_e( 'Issues Detected', 'golo-framework' ); ?></span>
	</div>
	<div class="golo-box__body">

		<?php
		/**
		 * Hook: golo_box_import_issues_before_content
		 */
		do_action( 'golo_box_import_issues_before_content' );
		?>

		<ol>
			<?php foreach ( $import_issues as $issue ) : ?>
				<li><?php echo wp_kses_post( $issue ); ?></li>
			<?php endforeach; ?>
		</ol>

		<?php
		/**
		 * Hook: golo_box_import_issues_after_content
		 */
		do_action( 'golo_box_import_issues_after_content' );
		?>

	</div>
	<div class="golo-box__footer">
		<span style="color: #dc433f">
			<?php esc_html_e( 'Please solve all issues listed above before importing demo data.', 'golo-framework' ); ?>
		</span>
	</div>
</div>
