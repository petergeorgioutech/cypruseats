<?php 

$section = 'color';

$default = golo_get_default_theme_options();

// Color
Golo_Kirki::add_section( $section, array(
	'title'    => esc_html__( 'Color', 'golo' ),
	'priority' => 30,
) );

// Content
Golo_Kirki::add_field( 'theme', [
	'type'     => 'notice',
	'settings' => 'color_content',
	'label'    => esc_html__( 'Content', 'golo' ),
	'section'  => $section,
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'color',
	'settings'  => 'primary_color',
	'label'     => esc_html__( 'Primary', 'golo' ),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['primary_color'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'color',
	'settings'  => 'text_color',
	'label'     => esc_html__( 'Text', 'golo' ),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['text_color'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'color',
	'settings'  => 'accent_color',
	'label'     => esc_html__( 'Accent', 'golo' ),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['accent_color'],
] );

// Background
Golo_Kirki::add_field( 'theme', [
	'type'     => 'notice',
	'settings' => 'color_bg_body',
	'label'    => esc_html__( 'Background', 'golo' ),
	'section'  => $section,
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'color',
	'settings'  => 'body_background_color',
	'label'     => esc_html__( 'Body Background', 'golo' ),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['body_background_color'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'image',
	'settings'  => 'bg_body_image',
	'label'     => esc_html__( 'Body BG Image', 'golo' ),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['bg_body_image'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'select',
	'settings'  => 'bg_body_size',
	'label'     => esc_html__( 'Background Size', 'golo' ),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['bg_body_size'],
	'choices'   => [
		'auto'    => esc_html__( 'Auto', 'golo' ),
		'cover'   => esc_html__( 'Cover', 'golo' ),
		'contain' => esc_html__( 'Contain', 'golo' ),
		'initial' => esc_html__( 'Initial', 'golo' ),
	],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'select',
	'settings'  => 'bg_body_repeat',
	'label'     => esc_html__( 'Background Repeat', 'golo' ),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['bg_body_repeat'],
	'choices'   => [
		'no-repeat' => esc_html__( 'No Repeat', 'golo' ),
		'repeat'    => esc_html__( 'Repeat', 'golo' ),
		'repeat-x'  => esc_html__( 'Repeat X', 'golo' ),
		'repeat-y'  => esc_html__( 'Repeat Y', 'golo' ),
	],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'select',
	'settings'  => 'bg_body_position',
	'label'     => esc_html__( 'Background Position', 'golo' ),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['bg_body_position'],
	'choices'   => [
		'left top'      => esc_html__( 'Left Top', 'golo' ),
		'left center'   => esc_html__( 'Left Center', 'golo' ),
		'left bottom'   => esc_html__( 'Left Bottom', 'golo' ),
		'right top'     => esc_html__( 'Right Top', 'golo' ),
		'right center'  => esc_html__( 'Right Center', 'golo' ),
		'right bottom'  => esc_html__( 'Right Bottom', 'golo' ),
		'center top'    => esc_html__( 'Center Top', 'golo' ),
		'center center' => esc_html__( 'Center Center', 'golo' ),
		'center bottom' => esc_html__( 'Center Bottom', 'golo' ),
	],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'select',
	'settings'  => 'bg_body_attachment',
	'label'     => esc_html__( 'Background Attachment', 'golo' ),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['bg_body_attachment'],
	'choices'   => [
		'scroll' => esc_html__( 'Scroll', 'golo' ),
		'fixed'  => esc_html__( 'Fixed', 'golo' ),
	],
] );