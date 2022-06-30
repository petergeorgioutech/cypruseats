<?php

namespace Elementor;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

Plugin::instance()->widgets_manager->register_widget_type( new Widget_Canvas_Cart() );

/**
 * Elementor canvas cart.
 *
 * Elementor widget that displays an eye-catching headlines.
 *
 * @since 1.0.0
 */
class Widget_Canvas_Cart extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve heading widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'canvas-cart';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve heading widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Canvas Cart', 'golo-framework' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve heading widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'golo-badge eicon-cart-light';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the heading widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'golo-framework' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'cart', 'canvas', 'golo' ];
	}

	/**
	 * Register heading widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'section_icon',
			[
				'label' => __( 'Icon Box', 'golo-framework' ),
			]
		);

		$this->add_control(
			'selected_icon',
			[
				'label' => __( 'Icon', 'golo-framework' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'la la-shopping-cart',
					'library' => 'fa-solid',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Typography', 'golo-framework' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'color',
			[
				'label'  => __( 'Color', 'golo-framework' ),
				'type'   => Controls_Manager::COLOR,
				'scheme' => [
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .icon-menu' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_size',
			[
				'label' => __( 'Icon Size', 'golo-framework' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default'   => [
					'size' => 24,
				],
				'selectors' => [
					'{{WRAPPER}} .icon-menu i' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'font_size',
			[
				'label' => __( 'Font Size', 'golo-framework' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default'   => [
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .icon-menu span' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render heading widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		if ( !class_exists('WooCommerce') ) {
			return;
		}

		$settings = $this->get_settings_for_display();

		$has_icon = ! empty( $settings['icon'] );

		if ( ! $has_icon && ! empty( $settings['selected_icon']['value'] ) ) {
			$has_icon = true;
		}
		
		$migrated = isset( $settings['__fa4_migrated']['selected_icon'] );
		$is_new = ! isset( $settings['icon'] ) && Icons_Manager::is_migration_allowed();
		?>
			<div class="minicart canvas-menu canvas-right">
				<a href="<?php echo esc_url( get_permalink( wc_get_page_id('cart') ) ); ?>" class="icon-menu toggle" aria-label="<?php esc_attr_e('Shopping Cart', 'golo-framework') ?>">

					<?php 
					if ( $has_icon ) :
						if ( $is_new || $migrated ) {
							Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] );
						} elseif ( ! empty( $settings['icon'] ) ) {
							?><i <?php echo $this->get_render_attribute_string( 'i' ); ?>></i><?php
						}
					endif; 
					?>

					<span class="cart-count">(<?php echo WC()->cart->cart_contents_count; ?>)</span>
				</a>

				<div class="bg-overlay"></div>

				<div class="area-menu">
					
					<div class="inner-menu custom-scrollbar">
						
						<div class="top-mb-menu">

							<a href="#" class="btn-close">
								<i class="la la-times"></i>
							</a>

							<div class="your-cart">
								<a href="<?php echo esc_url( get_permalink( wc_get_page_id('cart') ) ); ?>">
									<i class="la la-shopping-cart"></i>
									<span><?php esc_html_e('Your cart', 'golo-framework'); ?></span>
									<span class="cart-count">(<?php echo WC()->cart->cart_contents_count; ?>)</span>
								</a>
							</div>
						</div>
						
						<?php wc_get_template('cart/mini-cart.php'); ?>
					</div>
				</div>
			</div>
		<?php
	}
}
