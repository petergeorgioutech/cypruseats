<?php

if ( ! defined( 'GOLO_MEGA_MENU_POST_TYPE' ) ) {
	define( 'GOLO_MEGA_MENU_POST_TYPE', 'golo_mega_menu' );
}

require_once( trailingslashit( dirname( __FILE__ ) ) . 'class-walker-nav-menu.php' );

require_once( trailingslashit( dirname( __FILE__ ) ) . 'class-walker-nav-menu-edit.php' );

if ( ! class_exists( 'Golo_Mega_Menu' ) ) {

	class Golo_Mega_Menu {

		function __construct() {
			$this->golo_mega_menu_hooks();
			$this->add_cpt_mega_menus();
		}

		function golo_mega_menu_hooks() {

			add_action( 'init', array(
				$this,
				'golo_core_register_megamenu',
			) );
		}

		function add_cpt_mega_menus() {

			add_post_type_support( 'golo_mega_menu', 'elementor' );
		}

		/**
		 * Register Mega_Menu Post Type
		 */
		function golo_core_register_megamenu() {

			$labels = array(
				'name'               => _x( 'Mega Menus', 'Post Type General Name', 'golo-framework' ),
				'singular_name'      => _x( 'Mega Menus', 'Post Type Singular Name', 'golo-framework' ),
				'menu_name'          => esc_html__( 'Mega Menu', 'golo-framework' ),
				'name_admin_bar'     => esc_html__( 'Mega Menu', 'golo-framework' ),
				'parent_item_colon'  => esc_html__( 'Parent Menu:', 'golo-framework' ),
				'all_items'          => esc_html__( 'All Menus', 'golo-framework' ),
				'add_new_item'       => esc_html__( 'Add New Menu', 'golo-framework' ),
				'add_new'            => esc_html__( 'Add New', 'golo-framework' ),
				'new_item'           => esc_html__( 'New Menu', 'golo-framework' ),
				'edit_item'          => esc_html__( 'Edit Menu', 'golo-framework' ),
				'update_item'        => esc_html__( 'Update Menu', 'golo-framework' ),
				'view_item'          => esc_html__( 'View Menu', 'golo-framework' ),
				'search_items'       => esc_html__( 'Search Menu', 'golo-framework' ),
				'not_found'          => esc_html__( 'Not found', 'golo-framework' ),
				'not_found_in_trash' => esc_html__( 'Not found in Trash', 'golo-framework' ),
			);

			$args = array(
				'label'               => esc_html__( 'Mega Menus', 'golo-framework' ),
				'description'         => esc_html__( 'Golo Mega Menu', 'golo-framework' ),
				'labels'              => $labels,
				'supports'            => array(
					'title',
					'editor',
					'revisions',
				),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'menu_position'       => 20,
				'menu_icon'           => 'dashicons-list-view',
				'show_in_admin_bar'   => true,
				'show_in_nav_menus'   => true,
				'can_export'          => true,
				'has_archive'         => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'rewrite'             => false,
				'capability_type'     => 'page',
				'publicly_queryable'  => true, // Enable TRUE for Elementor Editing
			);

			register_post_type( GOLO_MEGA_MENU_POST_TYPE, $args );

		}
	}

	new Golo_Mega_Menu();
}