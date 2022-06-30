<?php
namespace Elementor;

use Elementor\Group_Control_Image_Size;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Core\Schemes;

Plugin::instance()->widgets_manager->register_widget_type( new Widget_Golo_Testimonial() );

/**
 * Elementor testimonial widget.
 *
 * Elementor widget that displays customer testimonials that show social proof.
 *
 * @since 1.0.0
 */
class Widget_Golo_Testimonial extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve testimonial widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'golo-testimonial-carousel';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve testimonial widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Testimonial Carousel', 'golo-framework' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve testimonial widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'golo-badge eicon-testimonial';
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
		return [ 'testimonial', 'blockquote' ];
	}

	/**
	 * Register testimonial widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'section_testimonial',
			[
				'label' => __( 'Testimonial', 'golo-framework' ),
			]
		);

		$this->add_control(
			'layout',
			[
				'label' => __( 'Layout', 'golo-framework' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'layout-01' => 'Layout 01',
					'layout-02' => 'Layout 02',
				],
				'default' => 'layout-01',
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'content',
			[
				'label' => __( 'Content', 'golo-framework' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'rows' => '10',
				'default' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'golo-framework' ),
			]
		);

		$repeater->add_control(
			'image',
			[
				'label' => __( 'Choose Image', 'golo-framework' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_control(
			'name',
			[
				'label' => __( 'Name', 'golo-framework' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => 'John Doe',
			]
		);

		$repeater->add_control(
			'title',
			[
				'label' => __( 'Title', 'golo-framework' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => 'Designer',
			]
		);

		$this->add_control(
			'testimonial_list',
			[
				'label'   => '',
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => [
					[
						'text' => __( 'Category #1', 'golo-framework' ),
					],
					[
						'text' => __( 'Category #2', 'golo-framework' ),
					],
					[
						'text' => __( 'Category #3', 'golo-framework' ),
					],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_style',
			[
				'label' => __( 'Content', 'golo-framework' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_width',
			[
				'label' => __( 'Max Width', 'golo-framework' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial' => 'max-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label' => __( 'Padding', 'golo-framework' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '30',
					'bottom' => '20',
					'left' => '20',
					'right' => '20',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'content_color',
			[
				'label' => __( 'Text Color', 'golo-framework' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial__text' => 'color: {{VALUE}}',
				],
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_3,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'selector' => '{{WRAPPER}} .elementor-testimonial__text',
				'scheme' => Schemes\Typography::TYPOGRAPHY_3,
			]
		);

		$this->add_control(
			'name_title_style',
			[
				'label' => __( 'Name', 'golo-framework' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'name_color',
			[
				'label' => __( 'Text Color', 'golo-framework' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial__name' => 'color: {{VALUE}}',
				],
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_3,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'name_typography',
				'selector' => '{{WRAPPER}} .elementor-testimonial__name',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
			]
		);

		$this->add_control(
			'heading_title_style',
			[
				'label' => __( 'Title', 'golo-framework' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Text Color', 'golo-framework' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial__title' => 'color: {{VALUE}}',
				],
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .elementor-testimonial__title',
				'scheme' => Schemes\Typography::TYPOGRAPHY_2,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_image_style',
			[
				'label' => __( 'Nav Slider', 'golo-framework' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'nav_width',
			[
				'label' => __( 'Nav Width', 'golo-framework' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}} .testimonial-slider-nav' => 'max-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'nav_gap',
			[
				'label' => __( 'Spacing', 'golo-framework' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'   => [
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .testimonial-slider-nav' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'image_size',
			[
				'label' => __( 'Image Size', 'golo-framework' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial__image img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'image_center_border_color',
			[
				'label' => __( 'Border Color', 'golo-framework' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#39d7d8',
				'selectors' => [
					'{{WRAPPER}} .slick-center .elementor-testimonial__image img' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'image_border',
			[
				'label' => __( 'Border', 'golo-framework' ),
				'type' => Controls_Manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial__image img' => 'border-style: solid',
				],
			]
		);

		$this->add_control(
			'image_border_color',
			[
				'label' => __( 'Border Color', 'golo-framework' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial__image img' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'image_border' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'image_border_width',
			[
				'label' => __( 'Border Width', 'golo-framework' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial__image img' => 'border-width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'image_border' => 'yes',
				],
			]
		);

		$this->add_control(
			'image_border_radius',
			[
				'label' => __( 'Border Radius', 'golo-framework' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial__image img' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render testimonial widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings 			= $this->get_settings_for_display();
		$layout 			= $settings['layout'];
		$default_image 		= golo_get_option('default_place_image','');
		if (!$default_image) {
			$default_image  = GOLO_PLUGIN_URL . 'assets/images/no-image.jpg';
		} else {
			$default_image = $default_image['url'];
		}
		?>
		
		<?php if( $layout == 'layout-01' ) : ?>
		<div class="elementor-slick-slider elementor-testimonial--align-center">

			<div class="testimonial-slider-for">
				<?php foreach ( $settings['testimonial_list'] as $index => $slide ) : ?>
				<div class="elementor-item">
					<div class="elementor-testimonial">
						<?php if ( $slide['content'] ) : ?>
						<div class="elementor-testimonial__content">
							<div class="elementor-testimonial__text">
								<?php echo esc_html($slide['content']); ?>
							</div>
						</div>
						<?php endif; ?>

						<?php if ( !empty( $slide['name'] ) ) { ?>
						<div class="elementor-testimonial__footer">
							<cite class="elementor-testimonial__cite">
								<?php if ( ! empty( $slide['name'] ) ) { ?>
									<span class="elementor-testimonial__name"><?php echo esc_html($slide['name']); ?></span>
								<?php } ?>

								<?php if ( ! empty( $slide['title'] ) ) { ?>
									<span class="elementor-testimonial__title"><?php echo esc_html($slide['title']); ?></span>
								<?php } ?>
							</cite>
						</div>
						<?php } ?>
					</div>
				</div>
				<?php endforeach; ?>
			</div>

			<div class="testimonial-slider-nav">
				<?php foreach ( $settings['testimonial_list'] as $index => $slide ) : ?>

					<div class="elementor-item">
						<?php if ( $slide['image']['url'] ) : ?>
							<div class="elementor-testimonial__image">
								<img src="<?php echo esc_url($slide['image']['url']); ?>" alt="<?php echo esc_attr($slide['name']); ?>">
							</div>
						<?php else : ?>
							<div class="elementor-testimonial__image">
								<img src="<?php echo esc_url($default_image['url']); ?>" alt="<?php echo esc_attr($slide['name']); ?>">
							</div>
						<?php endif; ?>
					</div>

				<?php endforeach; ?>
			</div>
		</div>
		<?php endif; ?>

		<?php if( $layout == 'layout-02' ) : ?>
			<div class="golo-swiper swiper-slider-widget" data-lg-items="2" data-md-items="2" data-sm-items="1" data-lg-gutter="20" data-md-gutter="" data-sm-gutter="" data-nav="1" data-nav-aligned-by="slider" data-loop="1" data-simulate-touch="1" data-speed="800" data-effect="slide">
			    <div class="swiper-inner">
			    	<div class="swiper-container">
			    		<div class="swiper-wrapper">
					    	<?php foreach ( $settings['testimonial_list'] as $index => $slide ) : ?>
					        <div class="swiper-slide">
					        	<div class="inner-slide">
					        		<?php if ( $slide['image']['url'] ) : ?>
										<div class="elementor-testimonial__image">
											<img src="<?php echo esc_url($slide['image']['url']); ?>" alt="<?php echo esc_attr($slide['name']); ?>">
										</div>
									<?php else : ?>
										<div class="elementor-testimonial__image">
											<img src="<?php echo esc_url($default_image['url']); ?>" alt="<?php echo esc_attr($slide['name']); ?>">
										</div>
									<?php endif; ?>
					        		
					        		<div class="elementor-testimonial">
										<?php if ( $slide['content'] ) : ?>
										<div class="elementor-testimonial__content">
											<div class="elementor-testimonial__text">
												<?php echo esc_html($slide['content']); ?>
											</div>
										</div>
										<?php endif; ?>

										<?php if ( !empty( $slide['name'] ) ) { ?>
										<div class="elementor-testimonial__footer">
											<cite class="elementor-testimonial__cite">
												<?php if ( ! empty( $slide['name'] ) ) { ?>
													<span class="elementor-testimonial__name"><?php echo esc_html($slide['name']); ?></span>
												<?php } ?>

												<?php if ( ! empty( $slide['title'] ) ) { ?>
													<span class="elementor-testimonial__title"><?php echo esc_html($slide['title']); ?></span>
												<?php } ?>
											</cite>
										</div>
										<?php } ?>
									</div>
					        	</div>
					        </div>
					        <?php endforeach; ?>
					    </div>
			        </div>
			    </div>
			</div>
		<?php endif; ?>

		<?php
	}

}
