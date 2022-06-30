<?php

namespace Golo_Elementor;

use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color;

defined( 'ABSPATH' ) || exit;

class Widget_Separator extends Base {

	public function get_name() {
		return 'golo-separator';
	}

	public function get_title() {
		return esc_html__( 'Separator', 'golo' );
	}

	public function get_icon_part() {
		return 'eicon-divider';
	}

	public function get_keywords() {
		return [ 'divider', 'hr', 'line', 'border', 'separator' ];
	}

	protected function _register_controls() {
		$this->start_controls_section( 'separator_section', [
			'label' => esc_html__( 'Separator', 'golo' ),
		] );

		$this->add_control( 'style', [
			'label'   => esc_html__( 'Style', 'golo' ),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'vertical-line'   => esc_html__( 'Vertical Line', 'golo' ),
				'horizontal-line' => esc_html__( 'Horizontal Line', 'golo' ),
			],
			'default' => 'vertical-line',
		] );

		$this->add_control( 'play_animate', [
			'label'        => esc_html__( 'Play Animate', 'golo' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => '1',
			'condition'    => [
				'style' => array( 'vertical-line' ),
			],
		] );

		$this->add_control( 'color', [
			'label'     => esc_html__( 'Color', 'golo' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'scheme'    => [
				'type'  => Color::get_type(),
				'value' => Color::COLOR_1,
			],
			'selectors' => [
				'{{WRAPPER}} .golo-separator .inner' => 'color: {{VALUE}};',
			],
		] );

		$this->add_responsive_control( 'align', [
			'label'        => esc_html__( 'Alignment', 'golo' ),
			'type'         => Controls_Manager::CHOOSE,
			'options'      => Widget_Utils::get_control_options_horizontal_alignment(),
			'prefix_class' => 'elementor%s-align-',
			'default'      => 'center',
		] );

		$this->add_responsive_control( 'width', [
			'label'      => esc_html__( 'Width', 'golo' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ '%', 'px' ],
			'range'      => [
				'px' => [
					'max' => 1000,
				],
			],
			'selectors'  => [
				'{{WRAPPER}} .golo-separator .inner' => 'width: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( 'height', [
			'label'      => esc_html__( 'Height', 'golo' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ '%', 'px' ],
			'range'      => [
				'px' => [
					'max' => 1000,
				],
			],
			'selectors'  => [
				'{{WRAPPER}} .golo-separator .inner' => 'height: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', 'golo-separator' );
		$this->add_render_attribute( 'wrapper', 'class', $settings['style'] );

		if ( $settings['play_animate'] === '1' ) {
			$this->add_render_attribute( 'wrapper', 'class', 'play-animate' );
		}
		?>
		<div <?php $this->print_attributes_string( 'wrapper' ); ?>>
			<div class="inner"></div>
		</div>
		<?php
	}

	protected function _content_template() {
		// @formatter:off
		?>
		<#
		view.addRenderAttribute( 'wrapper', 'class', 'golo-separator' );

		view.addRenderAttribute( 'wrapper', 'class', settings.style );

		if( settings.play_animate === '1' ) {
			view.addRenderAttribute( 'wrapper', 'class', 'play-animate' );
		}
		#>
		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<div class="inner"></div>
		</div>
		<?php
		// @formatter:off
	}
}
