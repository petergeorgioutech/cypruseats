<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 */

?>

<?php
$copyright_enable = Golo_Helper::get_setting( 'footer_copyright_enable' );
$copyright_text   = Golo_Helper::get_setting( 'footer_copyright_text' );
if ( $copyright_enable ) {
?>
<div class="copyright">
	<div class="container">
		<div class="area-copyright">
			<div class="row">
				<div class="col-md-4">
					<?php
					if ( is_active_sidebar( 'copyright-01' ) ) {
						dynamic_sidebar( 'copyright-01' );
					}
					?>
				</div>
				<div class="copyright-text align-center col-md-4"><?php echo esc_html( $copyright_text ); ?></div>
				<div class="col-md-4 text-md-right">
					<?php
					if ( is_active_sidebar( 'copyright-02' ) ) {
						dynamic_sidebar( 'copyright-02' );
					}
					?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php } ?>
