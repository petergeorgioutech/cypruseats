<?php 

$section = 'header';

$default = golo_get_default_theme_options();

// Header
Golo_Kirki::add_section( $section, array(
	'title'    => esc_html__( 'Header', 'golo' ),
	'priority' => 50,
) );

Golo_Kirki::add_field( 'theme', [
	'type'     => 'notice',
	'settings' => 'header_customize',
	'label'    => esc_html__( 'Header Customize', 'golo' ),
	'section'  => $section,
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'toggle',
	'settings'  => 'sticky_header',
	'label'     => esc_html__( 'Enable Sticky', 'golo' ),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['sticky_header'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'toggle',
	'settings'  => 'sticky_header_homepage',
	'label'     => esc_html__( 'Sticky Only Homepage', 'golo' ),
	'section'   => $section,
	'default'   => $default['sticky_header_homepage'],
	'active_callback' => [
		[
			'setting'  => 'sticky_header',
			'operator' => '==',
			'value'    => '1',
		]
	],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'color',
	'settings'  => 'header_sticky_background',
	'label'     => esc_html__( 'Background Color Header Sticky', 'golo' ),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['header_sticky_background'],
	'active_callback' => [
		[
			'setting'  => 'sticky_header',
			'operator' => '==',
			'value'    => '1',
		]
	],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'toggle',
	'settings'  => 'float_header',
	'label'     => esc_html__( 'Enable Float', 'golo' ),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['float_header'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'toggle',
	'settings'  => 'float_header_homepage',
	'label'     => esc_html__( 'Float Only Homepage', 'golo' ),
	'section'   => $section,
	'default'   => $default['float_header_homepage'],
	'active_callback' => [
		[
			'setting'  => 'float_header',
			'operator' => '==',
			'value'    => '1',
		]
	],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'toggle',
	'settings'  => 'show_canvas_menu',
	'label'     => esc_html__( 'Show Canvas Menu', 'golo' ),
	'section'   => $section,
	'default'   => $default['show_canvas_menu'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'toggle',
	'settings'  => 'show_main_menu',
	'label'     => esc_html__( 'Show Main Menu', 'golo' ),
	'section'   => $section,
	'default'   => $default['show_main_menu'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'toggle',
	'settings'  => 'show_search_form',
	'label'     => esc_html__( 'Show Search Form', 'golo' ),
	'section'   => $section,
	'default'   => $default['show_search_form'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'toggle',
	'settings'  => 'hidden_search_form_homepage',
	'label'     => esc_html__( 'Hidden at Homepage', 'golo' ),
	'section'   => $section,
	'default'   => $default['hidden_search_form_homepage'],
	'active_callback' => [
		[
			'setting'  => 'show_search_form',
			'operator' => '==',
			'value'    => '1',
		]
	],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'select',
	'settings'  => 'layout_search',
	'label'     => esc_html__( 'Layout Search', 'golo' ),
	'section'   => $section,
	'default'   => $default['layout_search'],
	'choices'   => [
		'layout-01' => esc_html__( '01', 'golo' ),
		'layout-02' => esc_html__( '02', 'golo' ),
		'layout-03' => esc_html__( '03', 'golo' ),
	],
	'active_callback' => [
		[
			'setting'  => 'show_search_form',
			'operator' => '==',
			'value'    => '1',
		]
	],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'slider',
	'settings'  => 'search_form_width',
	'label'     => esc_html__( 'Search Form Max Width', 'golo' ),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['search_form_width'],
	'choices'   => [
		'min'  => 470,
		'max'  => 1000,
		'step' => 1,
	],
	'active_callback' => [
		[
			'setting'  => 'show_search_form',
			'operator' => '==',
			'value'    => '1',
		]
	],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'toggle',
	'settings'  => 'show_destinations',
	'label'     => esc_html__( 'Show Destinations', 'golo' ),
	'section'   => $section,
	'default'   => $default['show_destinations'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'toggle',
	'settings'  => 'show_login',
	'label'     => esc_html__( 'Show Login', 'golo' ),
	'section'   => $section,
	'default'   => $default['show_login'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'toggle',
	'settings'  => 'show_register',
	'label'     => esc_html__( 'Show Register', 'golo' ),
	'section'   => $section,
	'default'   => $default['show_register'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'toggle',
	'settings'  => 'show_icon_cart',
	'label'     => esc_html__( 'Show Icon Cart', 'golo' ),
	'section'   => $section,
	'default'   => $default['show_icon_cart'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'toggle',
	'settings'  => 'show_add_place_button',
	'label'     => esc_html__( 'Show "Add Place" Button', 'golo' ),
	'section'   => $section,
	'default'   => $default['show_add_place_button'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'slider',
	'settings'  => 'logo_width',
	'label'     => esc_html__( 'Logo Width', 'golo' ),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['logo_width'],
	'choices'   => [
		'min'  => 0,
		'max'  => 500,
		'step' => 1,
	],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'slider',
	'settings'  => 'header_padding_top',
	'label'     => esc_html__( 'Padding Top', 'golo' ),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['header_padding_top'],
	'choices'   => [
		'min'  => 0,
		'max'  => 200,
		'step' => 1,
	],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'slider',
	'settings'  => 'header_padding_bottom',
	'label'     => esc_html__( 'Padding Bottom', 'golo' ),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['header_padding_bottom'],
	'choices'   => [
		'min'  => 0,
		'max'  => 500,
		'step' => 1,
	],
] );