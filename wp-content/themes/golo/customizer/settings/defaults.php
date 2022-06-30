<?php 
/**
 * Calzones: Default Options
 * Created by letruong272@gmail.com
 *
 * @package WordPress
 * @subpackage Calzones Theme
 * @since 1.0
 */

/**
*  Get default options
*/
if ( !function_exists( 'golo_get_default_theme_options' ) ) {
	function golo_get_default_theme_options() {
		$defaults = array();

		/**
		*  General
		*/
		$defaults['logo_dark']         = GOLO_IMAGES . 'logo.png';
		$defaults['logo_dark_retina']  = GOLO_IMAGES . 'logo-retina.png';
		$defaults['logo_light']        = GOLO_IMAGES . 'logo-light.png';
		$defaults['logo_light_retina'] = GOLO_IMAGES . 'logo-light-retina.png';

		$defaults['type_loading_effect'] 	  = 'none';
		$defaults['animation_loading_effect'] = 'css-1';
		$defaults['image_loading_effect'] 	  = '';

		$defaults['url_facebook'] 	 = '';
		$defaults['url_twitter'] 	 = '';
		$defaults['url_instagram'] 	 = '';
		$defaults['url_youtube'] 	 = '';
		$defaults['url_google_plus'] = '';
		$defaults['url_skype'] 	  	 = '';
		$defaults['url_linkedin'] 	 = '';
		$defaults['url_pinterest'] 	 = '';
		$defaults['url_slack'] 	  	 = '';
		$defaults['url_rss'] 	  	 = '';

		$defaults['page_title_text_color']     = '#ffffff';
		$defaults['page_title_bg_color']       = '#23d3d3';
		$defaults['page_title_bg_image']       = GOLO_IMAGES . 'banner-other.png';
		$defaults['page_title_bg_size']        = 'auto';
		$defaults['page_title_bg_repeat']      = 'no-repeat';
		$defaults['page_title_bg_position']    = 'right top';
		$defaults['page_title_bg_attachment']  = 'scroll';
		$defaults['page_title_font_size']      = 40;
		$defaults['page_title_letter_spacing'] = 0;

		/**
		*  Color
		*/
		$defaults['primary_color'] 	  	   = '#2d2d2d';
		$defaults['text_color'] 	  	   = '#5d5d5d';
		$defaults['accent_color'] 	  	   = '#23d3d3';
		$defaults['body_background_color'] = '#ffffff';
		$defaults['bg_body_image'] 	  	   = '';
		$defaults['bg_body_size'] 	  	   = 'auto';
		$defaults['bg_body_repeat'] 	   = 'no-repeat';
		$defaults['bg_body_position'] 	   = 'left top';
		$defaults['bg_body_attachment']    = 'scroll';

		/**
		*  Typography
		*/
		$defaults['font-style'] 	= array( 'bold', 'italic' );
		$defaults['font-family'] 	= 'Jost';
		$defaults['font-size'] 		= '16px';
		$defaults['font-weight'] 	= 'normal';
		$defaults['letter-spacing'] = 'inherit';

		$defaults['heading-font-style'] 	= array( 'bold', 'italic' );
		$defaults['heading-font-family'] 	= 'Jost';
		$defaults['heading-font-size'] 		= '24px';
		$defaults['heading-line-height'] 	= 'inherit';
		$defaults['heading-variant'] 		= '500';
		$defaults['heading-letter-spacing'] = 'inherit';

		/**
		*  Layout
		*/
		$defaults['layout_content'] = 'fullwidth';
		$defaults['content_width']  = 1920;
		$defaults['layout_sidebar'] = 'right-sidebar';
		$defaults['sidebar_width']  = 370;

		/**
		*  Header
		*/
		$defaults['sticky_header']               = '0';
		$defaults['sticky_header_homepage']      = '0';
		$defaults['header_sticky_background']    = '#000000';
		$defaults['float_header']                = '0';
		$defaults['float_header_homepage']       = '1';
		$defaults['show_canvas_menu']            = '1';
		$defaults['show_main_menu']              = '1';
		$defaults['show_search_form']            = '1';
		$defaults['hidden_search_form_homepage'] = '0';
		$defaults['layout_search']               = 'layout-01';
		$defaults['search_form_width']           = '500';
		$defaults['show_destinations']           = '1';
		$defaults['show_login']                  = '1';
		$defaults['show_register']               = '1';
		$defaults['show_icon_cart']              = '1';
		$defaults['show_add_place_button']       = '1';
		$defaults['logo_width']                  = '98';
		$defaults['header_padding_top']          = '20';
		$defaults['header_padding_bottom']       = '20';

		/**
		*  Footer
		*/
		$defaults['footer_type'] = '3506';
		$defaults['footer_copyright_enable'] = true;
		$defaults['footer_copyright_text']   = esc_html__('2020 © Uxper Studio. All rights reserved.', 'golo');

		/**
		*  Blog
		*/
		$defaults['blog_sidebar']                   = 'right-sidebar';
		$defaults['blog_sidebar_width']             = 370;
		$defaults['blog_image_size']             	= '1024x684';
		$defaults['blog_content_layout']            = 'layout-list';
		$defaults['blog_enable_categories']         = '0';
		$defaults['blog_number_column']             = 'columns-3';
		$defaults['enable_page_title_blog']         = '1';
		$defaults['page_title_blog_name']         	= esc_html__('Blog', 'golo');
		$defaults['page_title_blog_des']         	= esc_html__('Let our experts inspire you', 'golo');
		$defaults['style_page_title_blog']          = 'normal';
		$defaults['bg_page_title_blog']             = '#23d3d3';
		$defaults['color_page_title_blog']          = '#ffffff';
		$defaults['bg_image_page_title_blog']       = GOLO_IMAGES . 'banner-blog.png';
		$defaults['bg_size_page_title_blog']        = 'auto';
		$defaults['bg_repeat_page_title_blog']      = 'no-repeat';
		$defaults['bg_position_page_title_blog']    = 'right top';
		$defaults['bg_attachment_page_title_blog']  = 'scroll';
		$defaults['font_size_page_title_blog']      = 40;
		$defaults['letter_spacing_page_title_blog'] = 0;

		/**
		*  Single Post
		*/
		$defaults['post_single_sidebar'] = 'right-sidebar';
		$defaults['post_comment']  	     = '1';

		if ( class_exists( 'WooCommerce' ) ) {
			/**
			*  Product Archive
			*/
			$defaults['shop_layout_content'] = 'container';
			$defaults['shop_sidebar']        = 'right-sidebar';
			$defaults['shop_sidebar_width']  = 320;
			$defaults['shop_number_column']  = 'columns-4';

			$defaults['enable_page_title_shop']         = '1';
			$defaults['page_title_shop_name']         	= esc_html__('Shop', 'golo');
			$defaults['page_title_shop_des']         	= esc_html__('Travel Products We Love', 'golo');
			$defaults['style_page_title_shop']          = 'normal';
			$defaults['bg_page_title_shop']             = '#23d3d3';
			$defaults['color_page_title_shop']          = '#ffffff';
			$defaults['bg_image_page_title_shop']       = GOLO_IMAGES . 'banner-shop.png';
			$defaults['bg_size_page_title_shop']        = 'auto';
			$defaults['bg_repeat_page_title_shop']      = 'no-repeat';
			$defaults['bg_position_page_title_shop']    = 'right top';
			$defaults['bg_attachment_page_title_shop']  = 'scroll';
			$defaults['font_size_page_title_shop']      = 40;
			$defaults['letter_spacing_page_title_shop'] = 0;

			/**
			*  Product Single
			*/
			$defaults['single_sidebar'] = 'right-sidebar';
		}

		return $defaults;
	}
}

/**
*  Get theme options
*/
if ( !function_exists( 'get_option_customize' ) ) {
	function get_option_customize( $key ) {

		$value = null;

		$default_options = golo_get_default_theme_options();

		if ( empty($key) ) {
			return;
		}

		if( class_exists('Golo_Framework') ) {
			$theme_option = Kirki::get_option( $key );
		}

		if ( isset($theme_option) ) {
			$value = $theme_option;
		}elseif( isset($default_options[$key]) ){
			$value = $default_options[$key];
		}

		return $value;
	}
}

/**
*  Get theme mod
*/
if ( !function_exists( 'golo_get_theme_mod' ) ) {
	function golo_get_theme_mod( $key ) {

		$value = null;

		if ( empty( $key ) ) {
			return;
		}

		$theme_option = get_theme_mod( $key );

		if ( !empty( $theme_option ) ) {
			$value = $theme_option;
		}else{
			$value = false;
		}

		return $value;
	}
}