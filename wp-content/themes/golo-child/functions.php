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

if( function_exists('acf_add_local_field_group') ):

	acf_add_local_field_group(array(
		'key' => 'group_62d7d22bd0187',
		'title' => 'Delivery',
		'fields' => array(
			array(
				'key' => 'field_62d811f69a102',
				'label' => 'Delivery Options',
				'name' => 'delivery_options',
				'type' => 'repeater',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'collapsed' => 'field_62d812469a105',
				'min' => 0,
				'max' => 0,
				'layout' => 'row',
				'button_label' => 'Add Delivery Option',
				'sub_fields' => array(
					array(
						'key' => 'field_62d81528d08d9',
						'label' => 'Default Options',
						'name' => 'default_options',
						'type' => 'select',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(
							'foody' => 'Foody',
							'wolt' => 'Wolt',
							'bolt' => 'Bolt',
						),
						'default_value' => false,
						'allow_null' => 1,
						'multiple' => 0,
						'ui' => 0,
						'return_format' => 'value',
						'ajax' => 0,
						'placeholder' => '',
					),
					array(
						'key' => 'field_62d812469a105',
						'label' => 'Name',
						'name' => 'name',
						'type' => 'text',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_62d81528d08d9',
									'operator' => '==empty',
								),
							),
						),
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					),
					array(
						'key' => 'field_62d812219a104',
						'label' => 'Link',
						'name' => 'link',
						'type' => 'url',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
					),
					array(
						'key' => 'field_62d812059a103',
						'label' => 'Image',
						'name' => 'image',
						'type' => 'image',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_62d81528d08d9',
									'operator' => '==empty',
								),
							),
						),
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'return_format' => 'url',
						'preview_size' => 'medium',
						'library' => 'all',
						'min_width' => '',
						'min_height' => '',
						'min_size' => '',
						'max_width' => '',
						'max_height' => '',
						'max_size' => '',
						'mime_types' => '',
					),
				),
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