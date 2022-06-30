<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 */
?>
	
	</div><!-- End #content -->

	<footer class="site-footer">
		<?php if ( is_active_sidebar( 'footer' ) ) { ?>
		<div class="inner-footer">
			<div class="container-fluid">
				<?php dynamic_sidebar( 'footer' ); ?>
			</div>
		</div>
		<?php } ?>
		<?php get_template_part( 'templates/footer/copyright' ); ?>
	</footer>

</div><!-- End #wrapper -->

<?php wp_footer(); ?>

</body>
</html>
