<?php 

$panel = 'woocommerce';

$default = golo_get_default_theme_options();

// Products
Golo_Kirki::add_panel( $panel, array(
	'title'    => esc_html__( 'Woocommerce', 'golo' ),
	'priority' => 70,
) );

// Products archive
Golo_Kirki::add_section( 'product_archive', array(
	'title' => esc_html__( 'Product Archive', 'golo' ),
	'panel' => $panel,
) );

Golo_Kirki::add_field( 'theme', [
	'type'            => 'notice',
	'settings'        => 'shop_customize',
	'label'           => esc_html__( 'Shop Customize', 'golo' ),
	'section'         => 'product_archive',
	'partial_refresh' => [
		'blog_customize' => [
			'selector'        => '#primary.content-products',
			'render_callback' => 'wp_get_document_title',
		],
	],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'radio-image',
	'settings'  => 'shop_layout_content',
	'label'     => esc_html__( 'Layout Content', 'golo' ),
	'section'   => 'product_archive',
	'transport' => 'postMessage',
	'default'   => $default['shop_layout_content'],
	'choices'   => [
		'container'       => get_template_directory_uri() . '/customizer/assets/images/boxed.png',
		'container-fluid' => get_template_directory_uri() . '/customizer/assets/images/full-width.png',
	],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'radio-image',
	'settings'  => 'shop_sidebar',
	'label'     => esc_html__( 'Sidebar Layout', 'golo' ),
	'section'   => 'product_archive',
	'transport' => 'postMessage',
	'default'   => $default['shop_sidebar'],
	'choices'   => [
		'left-sidebar'  => get_template_directory_uri() . '/customizer/assets/images/left-sidebar.png',
		'no-sidebar' 	=> get_template_directory_uri() . '/customizer/assets/images/no-sidebar.png',
		'right-sidebar' => get_template_directory_uri() . '/customizer/assets/images/right-sidebar.png',
	],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'slider',
	'settings'  => 'shop_sidebar_width',
	'label'     => esc_html__( 'Sidebar Width', 'golo' ),
	'section'   => 'product_archive',
	'transport' => 'postMessage',
	'default'   => $default['shop_sidebar_width'],
	'choices'   => [
		'min'  => 270,
		'max'  => 420,
		'step' => 1,
	],
	'active_callback' => [
		[
			'setting'  => 'shop_sidebar',
			'operator' => '!=',
			'value'    => 'no-sidebar',
		]
	],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'radio-image',
	'settings'  => 'shop_number_column',
	'label'     => esc_html__( 'Columns', 'golo' ),
	'section'   => 'product_archive',
	'transport' => 'postMessage',
	'default'   => $default['shop_number_column'],
	'choices'   => [
		'columns-2' => get_template_directory_uri() . '/customizer/assets/images/col-2.png',
		'columns-3' => get_template_directory_uri() . '/customizer/assets/images/col-3.png',
		'columns-4' => get_template_directory_uri() . '/customizer/assets/images/col-4.png',
		'columns-5' => get_template_directory_uri() . '/customizer/assets/images/col-5.png',
	],
	'active_callback' => [
		[
			'setting'  => 'shop_content_layout',
			'operator' => 'in',
			'value'    => array('layout-grid','layout-masonry'),
		]
	],
] );

// Products single
Golo_Kirki::add_section( 'product_single', array(
	'title'    => esc_html__( 'Product Single', 'golo' ),
	'panel'    => $panel,
) );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'radio-image',
	'settings'  => 'single_sidebar',
	'label'     => esc_html__( 'Sidebar Layout', 'golo' ),
	'section'   => 'product_single',
	'transport' => 'postMessage',
	'default'   => $default['single_sidebar'],
	'choices'   => [
		'left-sidebar'  => get_template_directory_uri() . '/customizer/assets/images/left-sidebar.png',
		'no-sidebar' 	=> get_template_directory_uri() . '/customizer/assets/images/no-sidebar.png',
		'right-sidebar' => get_template_directory_uri() . '/customizer/assets/images/right-sidebar.png',
	],
] );

// Page Title
Golo_Kirki::add_section( 'page_title_shop', array(
	'title' => esc_html__( 'Page Title', 'golo' ),
	'panel' => $panel,
) );

Golo_Kirki::add_field( 'theme', [
	'type'            => 'notice',
	'settings'        => 'page_title_shop',
	'label'           => esc_html__( 'Page Title', 'golo' ),
	'section'         => 'page_title_shop',
	'partial_refresh' => [
		'page_title_shop' => [
			'selector'        => '.page-title-blog',
			'render_callback' => 'wp_get_document_title',
		],
	],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'toggle',
	'settings'  => 'enable_page_title_shop',
	'label'     => esc_html__( 'Enable Page Title', 'golo' ),
	'section'   => 'page_title_shop',
	'transport' => 'postMessage',
	'default'   => $default['enable_page_title_shop'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'text',
	'settings'  => 'page_title_shop_name',
	'label'     => esc_html__( 'Title', 'golo' ),
	'section'   => 'page_title_shop',
	'transport' => 'postMessage',
	'default'   => $default['page_title_shop_name'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'text',
	'settings'  => 'page_title_shop_des',
	'label'     => esc_html__( 'Description', 'golo' ),
	'section'   => 'page_title_shop',
	'transport' => 'postMessage',
	'default'   => $default['page_title_shop_des'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'radio-image',
	'settings'  => 'style_page_title_shop',
	'section'   => 'page_title_shop',
	'transport' => 'postMessage',
	'multiple'  => 1,
	'default'   => $default['style_page_title_shop'],
	'choices'   => [
		'normal' => get_template_directory_uri() . '/customizer/assets/images/text-uppercase.png',
		'italic' => get_template_directory_uri() . '/customizer/assets/images/text-italic.png',
	],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'color',
	'settings'  => 'bg_page_title_shop',
	'label'     => esc_html__( 'Background Color', 'golo' ),
	'section'   => 'page_title_shop',
	'transport' => 'postMessage',
	'default'   => $default['bg_page_title_shop'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'color',
	'settings'  => 'color_page_title_shop',
	'label'     => esc_html__( 'Text Color', 'golo' ),
	'section'   => 'page_title_shop',
	'transport' => 'postMessage',
	'default'   => $default['color_page_title_shop'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'image',
	'settings'  => 'bg_image_page_title_shop',
	'label'     => esc_html__( 'Background Image', 'golo' ),
	'section'   => 'page_title_shop',
	'transport' => 'postMessage',
	'default'   => $default['bg_image_page_title_shop'],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'select',
	'settings'  => 'bg_size_page_title_shop',
	'label'     => esc_html__( 'Background Size', 'golo' ),
	'section'   => 'page_title_shop',
	'transport' => 'postMessage',
	'default'   => $default['bg_size_page_title_shop'],
	'choices'   => [
		'auto'    => esc_html__( 'Auto', 'golo' ),
		'cover'   => esc_html__( 'Cover', 'golo' ),
		'contain' => esc_html__( 'Contain', 'golo' ),
		'initial' => esc_html__( 'Initial', 'golo' ),
	],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'select',
	'settings'  => 'bg_repeat_page_title_shop',
	'label'     => esc_html__( 'Background Repeat', 'golo' ),
	'section'   => 'page_title_shop',
	'transport' => 'postMessage',
	'default'   => $default['bg_repeat_page_title_shop'],
	'choices'   => [
		'no-repeat' => esc_html__( 'No Repeat', 'golo' ),
		'repeat'    => esc_html__( 'Repeat', 'golo' ),
		'repeat-x'  => esc_html__( 'Repeat X', 'golo' ),
		'repeat-y'  => esc_html__( 'Repeat Y', 'golo' ),
	],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'select',
	'settings'  => 'bg_position_page_title_shop',
	'label'     => esc_html__( 'Background Position', 'golo' ),
	'section'   => 'page_title_shop',
	'transport' => 'postMessage',
	'default'   => $default['bg_position_page_title_shop'],
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
	'settings'  => 'bg_attachment_page_title_shop',
	'label'     => esc_html__( 'Background Attachment', 'golo' ),
	'section'   => 'page_title_shop',
	'transport' => 'postMessage',
	'default'   => $default['bg_attachment_page_title_shop'],
	'choices'   => [
		'scroll' => esc_html__( 'Scroll', 'golo' ),
		'fixed'  => esc_html__( 'Fixed', 'golo' ),
	],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'slider',
	'settings'  => 'font_size_page_title_shop',
	'label'     => esc_html__( 'Font Size', 'golo' ),
	'section'   => 'page_title_shop',
	'transport' => 'postMessage',
	'default'   => $default['font_size_page_title_shop'],
	'choices'   => [
		'min'  => 12,
		'max'  => 50,
		'step' => 1,
	],
] );

Golo_Kirki::add_field( 'theme', [
	'type'      => 'slider',
	'settings'  => 'letter_spacing_page_title_shop',
	'label'     => esc_html__( 'Letter Spacing', 'golo' ),
	'section'   => 'page_title_shop',
	'transport' => 'postMessage',
	'default'   => $default['letter_spacing_page_title_shop'],
	'choices'   => [
		'min'  => 0,
		'max'  => 10,
		'step' => 0.5,
	],
] );