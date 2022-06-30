<?php 
/**
 * General Option
 *
 * @package Golo Theme
 * @version 1.0.0
 */

$panel = 'general';

$default = golo_get_default_theme_options();

// General
Golo_Kirki::add_panel( $panel, array(
	'title'    => esc_html__( 'General', 'golo' ),
	'priority' => 10,
) );

// Site Identity
Golo_Kirki::add_section( 'site_identity', array(
	'title'    => esc_html__( 'Site Identity', 'golo' ),
	'priority' => 10,
	'panel'    => $panel,
) );

Golo_Kirki::add_field( 'theme', [
	'type'            => 'image',
	'priority'        => 80,
	'settings'        => 'logo_dark',
	'label'           => esc_html__( 'Logo Dark', 'golo' ),
	'section'         => 'site_identity',
	'default'         => $default['logo_dark'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'            => 'image',
	'priority'        => 80,
	'settings'        => 'logo_dark_retina',
	'label'           => esc_html__( 'Logo Dark Retina', 'golo' ),
	'section'         => 'site_identity',
	'default'         => $default['logo_dark_retina'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'            => 'image',
	'priority'        => 80,
	'settings'        => 'logo_light',
	'label'           => esc_html__( 'Logo Light', 'golo' ),
	'section'         => 'site_identity',
	'default'         => $default['logo_light'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'            => 'image',
	'priority'        => 80,
	'settings'        => 'logo_light_retina',
	'label'           => esc_html__( 'Logo Light Retina', 'golo' ),
	'section'         => 'site_identity',
	'default'         => $default['logo_light_retina'],
] );

// Page Loading Effect
Golo_Kirki::add_section( 'page_loading_effect', array(
	'title'    => esc_html__( 'Page Loading Effect', 'golo' ),
	'priority' => 10,
	'panel'    => $panel,
) );

Golo_Kirki::add_field( 'theme', [
	'type'     => 'radio',
	'settings' => 'type_loading_effect',
	'label'    => esc_html__( 'Type Loading Effect', 'golo' ),
	'section'  => 'page_loading_effect',
	'default'  => $default['type_loading_effect'],
	'choices'  => [
		'none'   		=> esc_html__( 'None', 'golo' ),
		'css_animation' => esc_html__( 'CSS Animation', 'golo' ),
		'image'  		=> esc_html__( 'Image', 'golo' ),
	],
] );

Golo_Kirki::add_field( 'theme', [
	'type'     => 'radio-buttonset',
	'settings' => 'animation_loading_effect',
	'label'    => esc_html__( 'Animation Type', 'golo' ),
	'section'  => 'page_loading_effect',
	'default'  => $default['animation_loading_effect'],
	'choices'  => [
		'css-1'  => '<span class="golo-ldef-circle golo-ldef-loading"><span></span></span>',
		'css-2'  => '<span class="golo-ldef-dual-ring golo-ldef-loading"></span>',
		'css-3'  => '<span class="golo-ldef-facebook golo-ldef-loading"><span></span><span></span><span></span></span>',
		'css-4'  => '<span class="golo-ldef-heart golo-ldef-loading"><span></span></span>',
		'css-5'  => '<span class="golo-ldef-ring golo-ldef-loading"><span></span><span></span><span></span><span></span></span>',
		'css-6'  => '<span class="golo-ldef-roller golo-ldef-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>',
		'css-7'  => '<span class="golo-ldef-default golo-ldef-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>',
		'css-8'  => '<span class="golo-ldef-ellipsis golo-ldef-loading"><span></span><span></span><span></span><span></span></span>',
		'css-9'  => '<span class="golo-ldef-grid golo-ldef-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>',
		'css-10' => '<span class="golo-ldef-hourglass golo-ldef-loading"></span>',
		'css-11' => '<span class="golo-ldef-ripple golo-ldef-loading"><span></span><span></span></span>',
		'css-12' => '<span class="golo-ldef-spinner golo-ldef-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>',
	],
] );

Golo_Kirki::add_field( 'theme', [
	'type'     => 'image',
	'settings' => 'image_loading_effect',
	'label'    => esc_html__( 'Image', 'golo' ),
	'section'  => 'page_loading_effect',
	'default'  => $default['image_loading_effect'],
] );

// Socials Profile
Golo_Kirki::add_section( 'socials', array(
	'title'    => esc_html__( 'Socials', 'golo' ),
	'priority' => 10,
	'panel'    => $panel,
) );

Golo_Kirki::add_field( 'theme', [
	'type'     => 'text',
	'settings' => 'url_facebook',
	'label'    => esc_html__( 'Facebook', 'golo' ),
	'section'  => 'socials',
	'default'  => $default['url_facebook'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'     => 'text',
	'settings' => 'url_twitter',
	'label'    => esc_html__( 'Twitter', 'golo' ),
	'section'  => 'socials',
	'default'  => $default['url_twitter'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'     => 'text',
	'settings' => 'url_instagram',
	'label'    => esc_html__( 'Instagram', 'golo' ),
	'section'  => 'socials',
	'default'  => $default['url_instagram'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'     => 'text',
	'settings' => 'url_youtube',
	'label'    => esc_html__( 'Youtube', 'golo' ),
	'section'  => 'socials',
	'default'  => $default['url_youtube'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'     => 'text',
	'settings' => 'url_google_plus',
	'label'    => esc_html__( 'Google Plus', 'golo' ),
	'section'  => 'socials',
	'default'  => $default['url_google_plus'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'     => 'text',
	'settings' => 'url_skype',
	'label'    => esc_html__( 'Skype', 'golo' ),
	'section'  => 'socials',
	'default'  => $default['url_skype'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'     => 'text',
	'settings' => 'url_linkedin',
	'label'    => esc_html__( 'Linkedin', 'golo' ),
	'section'  => 'socials',
	'default'  => $default['url_linkedin'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'     => 'text',
	'settings' => 'url_pinterest',
	'label'    => esc_html__( 'Pinterest', 'golo' ),
	'section'  => 'socials',
	'default'  => $default['url_pinterest'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'     => 'text',
	'settings' => 'url_slack',
	'label'    => esc_html__( 'Slack', 'golo' ),
	'section'  => 'socials',
	'default'  => $default['url_slack'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'     => 'text',
	'settings' => 'url_rss',
	'label'    => esc_html__( 'RSS', 'golo' ),
	'section'  => 'socials',
	'default'  => $default['url_rss'],
] );

// Page Title
Golo_Kirki::add_section( 'page_title', array(
	'title'    => esc_html__( 'Page Title', 'golo' ),
	'priority' => 10,
	'panel'    => $panel,
) );


Golo_Kirki::add_field( 'theme', [
	'type'      => 'color',
	'settings'  => 'page_title_text_color',
	'label'     => esc_html__( 'Text Color', 'golo' ),
	'section'   => 'page_title',
	'transport' => 'postMessage',
	'default'   => $default['page_title_text_color'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'color',
	'settings'  => 'page_title_bg_color',
	'label'     => esc_html__( 'Background Color', 'golo' ),
	'section'   => 'page_title',
	'transport' => 'postMessage',
	'default'   => $default['page_title_bg_color'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'image',
	'settings'  => 'page_title_bg_image',
	'label'     => esc_html__( 'Background Image', 'golo' ),
	'section'   => 'page_title',
	'transport' => 'postMessage',
	'default'   => $default['page_title_bg_image'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'select',
	'settings'  => 'page_title_bg_size',
	'label'     => esc_html__( 'Background Size', 'golo' ),
	'section'   => 'page_title',
	'default'   => $default['page_title_bg_size'],
	'transport' => 'postMessage',
	'choices'   => [
		'auto'    => esc_html__( 'Auto', 'golo' ),
		'cover'   => esc_html__( 'Cover', 'golo' ),
		'contain' => esc_html__( 'Contain', 'golo' ),
		'initial' => esc_html__( 'Initial', 'golo' ),
	],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'select',
	'settings'  => 'page_title_bg_repeat',
	'label'     => esc_html__( 'Background Repeat', 'golo' ),
	'section'   => 'page_title',
	'default'   => $default['page_title_bg_repeat'],
	'transport' => 'postMessage',
	'choices'   => [
		'no-repeat' => esc_html__( 'No Repeat', 'golo' ),
		'repeat'    => esc_html__( 'Repeat', 'golo' ),
		'repeat-x'  => esc_html__( 'Repeat X', 'golo' ),
		'repeat-y'  => esc_html__( 'Repeat Y', 'golo' ),
	],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'select',
	'settings'  => 'page_title_bg_position',
	'label'     => esc_html__( 'Background Position', 'golo' ),
	'section'   => 'page_title',
	'default'   => $default['page_title_bg_position'],
	'transport' => 'postMessage',
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
	'settings'  => 'page_title_bg_attachment',
	'label'     => esc_html__( 'Background Attachment', 'golo' ),
	'section'   => 'page_title',
	'default'   => $default['page_title_bg_attachment'],
	'transport' => 'postMessage',
	'choices'   => [
		'scroll' => esc_html__( 'Scroll', 'golo' ),
		'fixed'  => esc_html__( 'Fixed', 'golo' ),
	],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'slider',
	'settings'  => 'page_title_font_size',
	'label'     => esc_html__( 'Font Size', 'golo' ),
	'section'   => 'page_title',
	'transport' => 'postMessage',
	'default'   => $default['page_title_font_size'],
	'choices'   => [
		'min'  => 12,
		'max'  => 50,
		'step' => 1,
	],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'slider',
	'settings'  => 'page_title_letter_spacing',
	'label'     => esc_html__( 'Letter Spacing', 'golo' ),
	'section'   => 'page_title',
	'transport' => 'postMessage',
	'default'   => $default['page_title_letter_spacing'],
	'choices'   => [
		'min'  => 0,
		'max'  => 10,
		'step' => 0.5,
	],
] );






