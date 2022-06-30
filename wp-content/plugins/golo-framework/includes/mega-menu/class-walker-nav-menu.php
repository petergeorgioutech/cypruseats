<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Golo_Framework
 */

if ( class_exists( 'Walker_Nav_Menu' ) && ! class_exists( 'Golo_Walker_Nav_Menu' ) ) {

	class Golo_Walker_Nav_Menu extends Walker_Nav_Menu {

		public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

			/**
			 * Filter the arguments for a single nav menu item.
			 */
			$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes[] = 'menu-item-' . $item->ID;

			$children = get_posts( array(
				'post_type'   => 'nav_menu_item',
				'nopaging'    => true,
				'numberposts' => 1,
				'meta_key'    => '_menu_item_menu_item_parent',
				'meta_value'  => $item->ID,
			) );

			foreach ( $children as $child ) {
				$obj = get_post_meta( $child->ID, '_menu_item_object' );

				if ( $obj[0] == 'golo_mega_menu' ) {

					$classes[] = 'mega-menu-' . ( ! empty( $item->layout ) ? $item->layout : 'default' );
					$classes[] = apply_filters( 'golo_mega_menu_css_class', 'mega-menu', $item, $args, $depth );
				}
			}

			/**
			 * Filter the CSS class(es) applied to a menu item's list item element.
			 *
			 */
			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
			$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

			/**
			 * Filter the ID applied to a menu item's list item element.
			 */
			$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
			$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';
			$output .= $indent . '<li' . $id . $class_names . '>';

			/**
			 * Filter the HTML attributes applied to a menu item's anchor element.
			 */
			$atts           = array();
			$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
			$atts['target'] = ! empty( $item->target ) ? $item->target : '';
			$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
			$atts['href']   = ! empty( $item->url ) ? $item->url : '';
			$atts           = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
			$attributes     = '';
			foreach ( $atts as $attr => $value ) {
				if ( ! empty( $value ) ) {
					$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}

			/**  Filter a menu item's title  **/
			$item_output = $args->before;
			$item_output .= '<a' . $attributes . '>';
			$item_output .= $args->link_before;

			$item_output .= apply_filters( 'the_title', $item->title, $item->ID );

			$item_output .= $args->link_after;
			$item_output .= '</a>';
			$item_output .= $args->after;

			$css = $this->get_css( $item );

			if ( $css ) {
				$js_output = '';
				$js_output .= 'if ( Golo_Inline_Style !== null ) {';
				$js_output .= 'Golo_Inline_Style.textContent+=\'' . text2line( $css ) . '\';';
				$js_output .= '}';
				wp_add_inline_script( GOLO_PLUGIN_PREFIX . 'template', text2line( $js_output ) );
			}

			if ( $item->object == 'golo_mega_menu' ) {
				$menu_post               = get_post( $item->object_id );
				$mega_menu_content_class = apply_filters( 'golo_mega_menu_content_css_class',
					'mega-menu-content container',
					$item,
					$args,
					$depth );
				$output .= '<div class="' . esc_attr( $mega_menu_content_class ) . '">' . do_shortcode("[elementor-template id='". $item->object_id ."']") . '</div>';
			} else {
				$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
			}
		}

		public function get_css( $item ) {

			$css = '';

			if ( ! empty( $item->layout ) && $item->layout == 'custom' && ! empty( $item->width ) ) {
				$css .= '.menu-item-' . $item->ID . ' > .sub-menu {';
				$css .= 'width: ' . $item->width . 'px !important; ';
				$css .= '}';
			}

			return $css;
		}
	}

	add_filter( 'golo_bmw_nav_args', 'golo_add_extra_params_to_bmw' );
	function golo_add_extra_params_to_bmw( $args ) {
		$args['walker'] = new Golo_Walker_Nav_Menu;

		return $args;
	}

	new Golo_Walker_Nav_Menu();
}