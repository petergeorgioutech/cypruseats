<?php 

/**
 * Enqueue script for live preview customizer.
 */
function golo_customizer_live_preview() {
    wp_enqueue_script( 'golo-customize-preview', get_template_directory_uri().'/customizer/assets/js/customize-preview.js', array( 'jquery','customize-preview' ),'',true );
}
add_action( 'customize_preview_init', 'golo_customizer_live_preview' );

/**
 * Enqueue script for custom customize control.
 */
function golo_customize_enqueue() {
	wp_enqueue_style('font-awesome', GOLO_THEME_URI . '/assets/libs/font-awesome/css/fontawesome.min.css', array(), '5.1.0', 'all');

    wp_enqueue_style('golo_customizer', get_template_directory_uri() . '/customizer/assets/css/custom.css', array() );
}
add_action( 'customize_controls_enqueue_scripts', 'golo_customize_enqueue', 10 );

/**
 * Register customizer
 */
function golo_customizer_register( $wp_customize ) {
	/**
	 * Register controls
	 */
	$wp_customize->get_control( 'blogname'        )->section = 'site_identity';
	$wp_customize->get_control( 'blogdescription' )->section = 'site_identity';
	$wp_customize->get_control( 'site_icon'       )->section = 'site_identity';

	if ( get_pages() ) {
		$wp_customize->get_control( 'show_on_front'  )->section = 'system';
		$wp_customize->get_control( 'page_on_front'  )->section = 'system';
		$wp_customize->get_control( 'page_for_posts' )->section = 'system';
	}

	/**
	 * Remove default sections
	 */
	$wp_customize->remove_section( 'title_tagline' );
	$wp_customize->remove_section( 'colors'        );

	/**
	 * The custom control class
	 */
	if( class_exists( 'Golo_Framework' ) ){
		class Kirki_Controls_Notice_Control extends Kirki_Control_Base {
			public $type = 'notice';
			public function render_content() { 
				?>
				<h3 class="entry-notice"><?php echo esc_html( $this->label ); ?></h3>
				<?php
			}
		}
	}
	// Register our custom control with Kirki
	add_filter( 'kirki_control_types', function( $controls ) {
		$controls['notice'] = 'Kirki_Controls_Notice_Control';
		return $controls;
	} );
}
add_action( 'customize_register', 'golo_customizer_register' );

/**
 * Get list sidebars
 * *******************************************************
 */
if (!function_exists('golo_get_sidebars')) {
	function golo_get_sidebars() {
		$sidebars = array('default'=>'-- Select Sidebar --');
		if (is_array($GLOBALS['wp_registered_sidebars'])) {
			foreach ($GLOBALS['wp_registered_sidebars'] as $sidebar) {
				$sidebars[$sidebar['id']] = ucwords($sidebar['name']);
			}
		}
		return $sidebars;
	}
}

/**
 * Get list footer elementor
 * *******************************************************
 */
if (!function_exists('golo_get_footer_elementor')) {
	function golo_get_footer_elementor() {
		$footers = get_posts(array(
		    'posts_per_page' => -1,
		    'post_type' => 'elementor_library',
		    'tax_query' => array(
		        array(
			        'taxonomy' => 'elementor_library_type',
			        'field' => 'slug',
			        'terms' => 'footer',
		    	)
		    ),
		));

		$arr_footer = array();
		foreach ( $footers as $footer ) {
			$arr_footer[$footer->ID] = ucwords($footer->post_name);
		}
		return $arr_footer;
	}
}