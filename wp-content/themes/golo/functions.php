<?php
/**
 * Define constants
 */
$golo_theme = wp_get_theme();

if ( ! defined( 'DS' ) ) {
	define( 'DS', DIRECTORY_SEPARATOR );
}

if ( !empty( $golo_theme['Template'] ) ) 
{
	$golo_theme = wp_get_theme( $golo_theme['Template'] );
}

if (!defined('GOLO_THEME_NAME')) 
{
	define('GOLO_THEME_NAME', $golo_theme['Name'] );
}

if (!defined('GOLO_THEME_SLUG')) 
{
	define('GOLO_THEME_SLUG', $golo_theme['Template'] );
}

if (!defined('GOLO_THEME_VER')) 
{
	define( 'GOLO_THEME_VER', $golo_theme['Version'] );
}

if (!defined('GOLO_THEME_DIR')) 
{
	define('GOLO_THEME_DIR', trailingslashit( get_template_directory() ) );
}

if (!defined('GOLO_THEME_URI')) 
{
	define('GOLO_THEME_URI', get_template_directory_uri() );
}

if (!defined('GOLO_THEME_PREFIX')) 
{
    define('GOLO_THEME_PREFIX', 'golo_');
}

if (!defined('GOLO_METABOX_PREFIX')) {
    define('GOLO_METABOX_PREFIX', 'golo-');
}

if (!defined('GOLO_CUSTOMIZER_DIR')) 
{
	define('GOLO_CUSTOMIZER_DIR', GOLO_THEME_DIR . '/customizer' );
}

if (!defined('GOLO_IMAGES')) 
{
	define('GOLO_IMAGES', GOLO_THEME_URI . '/assets/images/' );
}

define( 'GOLO_ELEMENTOR_DIR', get_template_directory() . DS . 'elementor' );
define( 'GOLO_ELEMENTOR_URI', get_template_directory_uri() . '/elementor' );
define( 'GOLO_ELEMENTOR_ASSETS', get_template_directory_uri() . '/elementor/assets' );

/**
 * Load Theme Class.
 *
 */
foreach ( glob( get_template_directory() . '/includes/*.php' ) as $theme_class ) {
	require_once( $theme_class );
}

require_once GOLO_ELEMENTOR_DIR . '/class-entry.php';

function golo_load_elementor_options() {  
    update_option( 'elementor_disable_typography_schemes', 'yes' );
}

add_action( 'after_switch_theme', 'golo_load_elementor_options' );


/**
 * Init the theme
 *
 */
new Golo_Init();
