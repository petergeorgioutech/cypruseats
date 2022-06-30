<?php 

$section = 'footer';

$default = golo_get_default_theme_options();

// Footer
Golo_Kirki::add_section( $section, array(
	'title'    => esc_html__( 'Footer', 'golo' ),
	'priority' => 50,
) );

Golo_Kirki::add_field( 'theme', [
	'type'            => 'notice',
	'settings'        => 'footer_customize',
	'label'           => esc_html__( 'Footer Customize', 'golo' ),
	'section'         => $section,
	'partial_refresh' => [
		'header_type' => [
			'selector'        => 'footer.site-footer',
			'render_callback' => 'wp_get_document_title',
		],
	],
] );

Golo_Kirki::add_field( 'theme', [
	'type'     => 'select',
	'settings' => 'footer_type',
	'label'    => esc_html__( 'Footer Type', 'golo' ),
	'section'  => $section,
	'default'  => $default['footer_type'],
	'choices'  => golo_get_footer_elementor(),
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'toggle',
	'settings'  => 'footer_copyright_enable',
	'label'     => esc_html__( 'Enable Copyright', 'golo' ),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['footer_copyright_enable'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'     => 'text',
	'settings' => 'footer_copyright_text',
	'label'    => esc_html__( 'Copyright', 'golo' ),
	'section'  => $section,
	'default'  => $default['footer_copyright_text'],
] );

