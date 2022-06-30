<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Setup for customizer of this theme
 */
class Golo_Customizer {
	/**
	 * The constructor.
	 */
	public function __construct() {
		// Remove unused native sections and controls.
		add_action( 'customize_register', array( $this, 'remove_customizer_sections' ) );

		// Load customizer sections when all widgets init
		add_action( 'widgets_init', array( $this, 'load_customizer' ), 99 );

		add_action( 'wp_default_scripts', array( $this, 'wp_default_custom_scripts' ) );
	}

	/**
	 * Load Customizer.
	 */
	public function load_customizer() {
		require_once( GOLO_THEME_DIR . 'customizer/customizer.php' );
	}	

	/**
	 * Remove unused native sections and controls
	 *
	 * @param $wp_customize
	 */
	public function remove_customizer_sections( $wp_customize ) 
	{
		$wp_customize->remove_section( 'nav' );
		$wp_customize->remove_section( 'colors' );
		$wp_customize->remove_section( 'background_image' );
		$wp_customize->remove_section( 'header_image' );
		$wp_customize->remove_control( 'display_header_text' );
	}
	
	public function wp_default_custom_scripts( $scripts ){
		if( is_admin() ){
			$scripts->add( 'wp-color-picker', "/wp-admin/js/color-picker.js", array( 'iris' ), false, 1 );
			did_action( 'init' ) && $scripts->localize(
				'wp-color-picker',
				'wpColorPickerL10n',
				array(
					'clear'            => __( 'Clear', 'golo' ),
					'clearAriaLabel'   => __( 'Clear color', 'golo' ),
					'defaultString'    => __( 'Default', 'golo' ),
					'defaultAriaLabel' => __( 'Select default color', 'golo' ),
					'pick'             => __( 'Select Color', 'golo' ),
					'defaultLabel'     => __( 'Color value', 'golo' ),
				)
			);
		}
	}
}
