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

/**
 * Custom Fields
 */

if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'group_62c83c2925e86',
	'title' => 'Related Content',
	'fields' => array(
		array(
			'key' => 'field_62c83c30a2cad',
			'label' => 'Content Selection',
			'name' => 'content_selection',
			'type' => 'relationship',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'post_type' => array(
				0 => 'post',
			),
			'taxonomy' => '',
			'filters' => array(
				0 => 'search',
			),
			'elements' => array(
				0 => 'featured_image',
			),
			'min' => '',
			'max' => '',
			'return_format' => 'object',
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'place',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
	'show_in_rest' => 0,
));

endif;		