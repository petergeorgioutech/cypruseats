<?php
/**
 * This file define demos for the theme.
 */

function golo_import_list_demos() {
	return array(
		'01' => array(
			'name'              => esc_html__( 'City Guide', 'golo' ),
			'description'       => esc_html__( 'After importing this demo, your site will have all data like wp.getgolo.com', 'golo' ),
			'preview_image_url' => GOLO_THEME_URI . '/assets/import/01/screenshot.png',
			'media_package_url' => 'https://data.uxper.co/golo/golo-media-01.zip',
		),
		'02' => array(
			'name'              => esc_html__( 'Bussiness Listing', 'golo' ),
			'description'       => esc_html__( 'After importing this demo, your site will have all data like wp.getgolo.com', 'golo' ),
			'preview_image_url' => GOLO_THEME_URI . '/assets/import/02/screenshot.png',
			'media_package_url' => 'https://data.uxper.co/golo/golo-media-02.zip',
		),
		'03' => array(
			'name'              => esc_html__( 'Country Travel Guide', 'golo' ),
			'description'       => esc_html__( 'After importing this demo, your site will have all data like wp.getgolo.com', 'golo' ),
			'preview_image_url' => GOLO_THEME_URI . '/assets/import/03/screenshot.png',
			'media_package_url' => 'https://data.uxper.co/golo/golo-media-03.zip',
		),
		'04' => array(
			'name'              => esc_html__( 'Restaurant Listing', 'golo' ),
			'description'       => esc_html__( 'After importing this demo, your site will have all data like wp.getgolo.com', 'golo' ),
			'preview_image_url' => GOLO_THEME_URI . '/assets/import/04/screenshot.png',
			'media_package_url' => 'https://data.uxper.co/golo/golo-media-04.zip',
		),
		'05' => array(
			'name'              => esc_html__( 'Workspace', 'golo' ),
			'description'       => esc_html__( 'After importing this demo, your site will have all data like wp.getgolo.com', 'golo' ),
			'preview_image_url' => GOLO_THEME_URI . '/assets/import/05/screenshot.png',
			'media_package_url' => 'https://data.uxper.co/golo/golo-media-05.zip',
		),
		'06' => array(
			'name'              => esc_html__( 'Health-Medical', 'golo' ),
			'description'       => esc_html__( 'After importing this demo, your site will have all data like wp.getgolo.com', 'golo' ),
			'preview_image_url' => GOLO_THEME_URI . '/assets/import/06/screenshot.png',
			'media_package_url' => 'https://data.uxper.co/golo/golo-media-06.zip',
		),
	);
}
add_filter( 'golo_import_demos', 'golo_import_list_demos' );