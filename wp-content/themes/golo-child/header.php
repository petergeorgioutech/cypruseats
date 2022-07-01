<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">

	<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php endif; ?>

	<?php wp_head(); ?>
</head>

<?php 
$dir = '';
$enable_rtl_mode  = Golo_Helper::golo_get_option('enable_rtl_mode', 0);
if ( is_rtl() || $enable_rtl_mode ) {
	$dir = 'dir=rtl';
}

?>

<body <?php body_class() ?> <?php echo esc_attr($dir); ?>>
	
<?php wp_body_open(); ?>

	<?php 
		$layout_content         = Golo_Helper::get_setting('layout_content');
		$sticky_header          = Golo_Helper::get_setting('sticky_header');
		$sticky_header_homepage = Golo_Helper::get_setting('sticky_header_homepage');
		$float_header           = Golo_Helper::get_setting('float_header');
		$float_header_homepage  = Golo_Helper::get_setting('float_header_homepage');
		$header_classes = array();
		if( $sticky_header )
		{
			if( $sticky_header_homepage && is_front_page() ) {
				$header_classes[] = 'sticky-header';
			}

			if( !$sticky_header_homepage ){
				$header_classes[] = 'sticky-header';
			}
		}
		if( $float_header )
		{
			if( $float_header_homepage && is_front_page() ) {
				$header_classes[] = 'float-header';
			}

			if( !$float_header_homepage ){
				$header_classes[] = 'float-header';
			}
		}
	?>
	
	<div id="wrapper" class="<?php echo esc_attr($layout_content); ?> hello-world">

		<header class="site-header <?php echo join(' ', $header_classes); ?>">
			<?php get_template_part( 'templates/header/header' ); ?>
		</header>

		<div id="content" class="site-content">