<?php

namespace Golo_Elementor;

use Elementor\Group_Control_Base;
use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

/**
 * Elementor tooltip control.
 *
 * A base control for creating tooltip control.
 *
 * @since 1.0.0
 */
class Group_Control_Tooltip extends Group_Control_Base {

	protected static $fields;

	public static function get_type() {
		return 'tooltip';
	}

	protected function init_fields() {
		$fields = [];

		$fields['skin'] = [
			'label'   => esc_html__( 'Tooltip Skin', 'golo' ),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				''        => esc_html__( 'Black', 'golo' ),
				'white'   => esc_html__( 'White', 'golo' ),
				'primary' => esc_html__( 'Primary', 'golo' ),
			],
			'default' => '',
		];

		$fields['position'] = [
			'label'   => esc_html__( 'Tooltip Position', 'golo' ),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'top'          => esc_html__( 'Top', 'golo' ),
				'right'        => esc_html__( 'Right', 'golo' ),
				'bottom'       => esc_html__( 'Bottom', 'golo' ),
				'left'         => esc_html__( 'Left', 'golo' ),
				'top-left'     => esc_html__( 'Top Left', 'golo' ),
				'top-right'    => esc_html__( 'Top Right', 'golo' ),
				'bottom-left'  => esc_html__( 'Bottom Left', 'golo' ),
				'bottom-right' => esc_html__( 'Bottom Right', 'golo' ),
			],
			'default' => 'top',
		];

		return $fields;
	}

	protected function get_default_options() {
		return [
			'popover' => [
				'starter_title' => _x( 'Tooltip', 'Tooltip Control', 'golo' ),
				'starter_name'  => 'enable',
				'starter_value' => 'yes',
				'settings'      => [
					'render_type' => 'template',
				],
			],
		];
	}
}
