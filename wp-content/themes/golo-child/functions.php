<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue child scripts
 */
add_action( 'wp_enqueue_scripts', 'golo_child_enqueue_scripts' );
if ( ! function_exists( 'golo_child_enqueue_scripts' ) ) {

	function golo_child_enqueue_scripts() {
		wp_enqueue_style( 'golo_child-style', trailingslashit( get_stylesheet_directory_uri() ) . 'style.css' );
		wp_enqueue_script( 'golo_child-script',
			trailingslashit( get_stylesheet_directory_uri() ) . 'script.js',
			array( 'jquery' ),
			null,
			true );
	}

}