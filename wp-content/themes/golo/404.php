<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 */

get_header(); 
?>

<?php echo Golo_Templates::page_title(); ?>

<div class="main-content content-page">
	
	<div class="container">
	
		<div class="site-layout">

			<div class="area-404 align-center">
						
				<h2><?php esc_html_e('OOPS!', 'golo'); ?></h2>		
				
				<h3><?php esc_html_e("Sorry, we couldn't find that page.", 'golo'); ?></h3>

				<p><?php esc_html_e("We can't find the page or studio you're looking for.", 'golo'); ?></p>
				<p><?php echo sprintf( esc_html__( "Make sure you've typed in the URL correctly or try go %s", 'golo' ), '<a href="'. get_site_url() .'">'. esc_html__('Homepage', 'golo') .'</a>' ); ?></p>
			</div>
			
		</div>

	</div>

</div>

<?php
get_footer();