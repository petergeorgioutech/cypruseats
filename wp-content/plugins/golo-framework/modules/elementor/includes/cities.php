<?php

namespace Elementor;

use ElementorPro\Modules\QueryControl\Module;
use ElementorPro\Modules\QueryControl\Controls\Group_Control_Posts;
use ElementorPro\Modules\QueryControl\Controls\Group_Control_Related;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

Plugin::instance()->widgets_manager->register_widget_type( new Widget_Cities() );

/**
 * Elementor cities.
 *
 * Elementor widget that displays an eye-catching headlines.
 *
 * @since 1.0.0
 */
class Widget_Cities extends Widget_Base {

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
		return 'cities';
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
		return __( 'Cities', 'golo-framework' );
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
		return 'golo-badge eicon-map-pin';
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
		return [ 'city', 'title', 'text' ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_layout',
			[
				'label' => __( 'Layout', 'golo-framework' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		
		$this->add_control(
			'layout',
			[
				'label'   => __( 'Layout', 'golo-framework' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => [
					'grid' => __( 'Grid', 'golo-framework' ),
					'list' => __( 'List', 'golo-framework' ),
				],
			]
		);

		$this->add_control(
			'enable_slider',
			[
				'label'   => __( 'Enable Slider', 'golo-framework' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'        => __( 'Columns', 'golo-framework' ),
				'type'         => Controls_Manager::NUMBER,
				'prefix_class' => 'elementor-grid%s-',
				'min'          => 1,
				'max'          => 12,
				'default'      => 4,
				'required'     => true,
				'device_args'  => [
					Controls_Stack::RESPONSIVE_TABLET => [
						'required' => false,
					],
					Controls_Stack::RESPONSIVE_MOBILE => [
						'required' => false,
					],
				],
				'min_affected_device' => [
					Controls_Stack::RESPONSIVE_DESKTOP => Controls_Stack::RESPONSIVE_TABLET,
					Controls_Stack::RESPONSIVE_TABLET => Controls_Stack::RESPONSIVE_TABLET,
				],
				'condition' => [
					'enable_slider!' => 'yes',
				],
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label' => __( 'Posts Per Page', 'golo-framework' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 6,
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => __( 'Order by', 'golo-framework' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'name',
				'options' => [
					'name' => __( 'Title', 'golo-framework' ),
					'rand' => __( 'Random', 'golo-framework' ),
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label' => __( 'Order', 'golo-framework' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'desc',
				'options' => [
					'asc' => __( 'ASC', 'golo-framework' ),
					'desc' => __( 'DESC', 'golo-framework' ),
				],
				'condition' => [
					'orderby!' => 'rand',
				],
			]
		);

		$this->add_control(
			'thumbnail_size',
			[
				'label' => __( 'Image Size', 'golo-framework' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Example: 300x300', 'golo-framework' ),
			]
		);

		$this->add_control(
			'hide_empty',
			[
				'label' => __( 'Hide empty', 'golo-framework' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => false,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slider_options',
			[
				'label' => __( 'Slider Options', 'golo-framework' ),
				'type' => Controls_Manager::SECTION,
				'condition' => [
					'enable_slider' => 'yes',
				],
			]
		);

		$slides_to_show = range( 1, 10 );
		$slides_to_show = array_combine( $slides_to_show, $slides_to_show );

		$this->add_responsive_control(
			'slides_to_show',
			[
				'label'   => __( 'Slides to Show', 'golo-framework' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '2',
				'options' => [
					'' => __( 'Default', 'golo-framework' ),
				] + $slides_to_show,
			]
		);

		$this->add_responsive_control(
			'slides_to_scroll',
			[
				'label'       => __( 'Slides to Scroll', 'golo-framework' ),
				'type'        => Controls_Manager::SELECT,
				'description' => __( 'Set how many slides are scrolled per swipe.', 'golo-framework' ),
				'default'     => '2',
				'options'     => [
					'' => __( 'Default', 'golo-framework' ),
				] + $slides_to_show,
				'condition'   => [
					'slides_to_show!' => '1',
				],
			]
		);

		$this->add_control(
			'navigation',
			[
				'label'   => __( 'Navigation', 'golo-framework' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'both',
				'options' => [
					'both'   => __( 'Arrows and Dots', 'golo-framework' ),
					'arrows' => __( 'Arrows', 'golo-framework' ),
					'dots'   => __( 'Dots', 'golo-framework' ),
					'none'   => __( 'None', 'golo-framework' ),
				],
			]
		);

		$this->add_control(
			'variable_width',
			[
				'label'     => __( 'Variable Width', 'golo-framework' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'no',
			]
		);

		$this->add_control(
			'center_mode',
			[
				'label'     => __( 'Center Mode', 'golo-framework' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'no',
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label'     => __( 'Pause on Hover', 'golo-framework' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'     => __( 'Autoplay', 'golo-framework' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'no',
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label'     => __( 'Autoplay Speed', 'golo-framework' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'condition' => [
					'autoplay' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .slick-slide-bg' => 'animation-duration: calc({{VALUE}}ms*1.2); transition-duration: calc({{VALUE}}ms)',
				],
			]
		);

		$this->add_control(
			'infinite',
			[
				'label'     => __( 'Infinite Loop', 'golo-framework' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
			]
		);

		$this->add_control(
			'transition',
			[
				'label'   => __( 'Transition', 'golo-framework' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => [
					'slide' => __( 'Slide', 'golo-framework' ),
					'fade'  => __( 'Fade', 'golo-framework' ),
				],
			]
		);

		$this->add_control(
			'transition_speed',
			[
				'label'     => __( 'Transition Speed', 'golo-framework' ) . ' (ms)',
				'type'      => Controls_Manager::NUMBER,
				'default'   => 500,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_design_layout',
			[
				'label' => __( 'Items', 'golo-framework' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label' => __( 'Columns Gap', 'golo-framework' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-carousel .city-item' => 'padding-left: calc({{SIZE}}{{UNIT}}/2);padding-right: calc({{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .slick-list'                    => 'margin-left: calc(-{{SIZE}}{{UNIT}}/2);margin-right: calc(-{{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .elementor-grid'                => 'grid-column-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label' => __( 'Rows Gap', 'golo-framework' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 30,
				],
				'frontend_available' => true,
				'selectors' => [
					'{{WRAPPER}} .elementor-grid' => 'grid-row-gap: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'enable_slider!' => 'yes',
				],
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label' => __( 'Border Radius', 'golo-framework' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .city-item .city-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'color_background',
			[
				'label' => __( 'Background Color', 'golo-framework' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .city-item .entry-thumb a:after' => 'background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0), {{VALUE}});',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_design_title',
			[
				'label' => __( 'Title', 'golo-framework' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_spacing',
			[
				'label' => __( 'Spacing', 'golo-framework' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default' => [
					'size' => 0,
				],
				'frontend_available' => true,
				'selectors' => [
					'{{WRAPPER}} .city-inner .entry-detail>h3' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography_title',
				'scheme' => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .city-inner .entry-detail>h3',
			]
		);

		$this->add_control(
			'color_title',
			[
				'label' => __( 'Color', 'golo-framework' ),
				'separator' => 'before',
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .city-inner .entry-detail>h3' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_design_sub_title',
			[
				'label' => __( 'Sub Title', 'golo-framework' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'hidden_count',
			[
				'label'   => __( 'Hidden', 'golo-framework' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography_sub_title',
				'scheme' => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .city-inner .entry-detail>span',
			]
		);

		$this->add_control(
			'color_sub_title',
			[
				'label' => __( 'Color', 'golo-framework' ),
				'separator' => 'before',
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .city-inner .entry-detail>span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_design_country',
			[
				'label' => __( 'Country', 'golo-framework' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'layout' => 'grid',
				],
			]
		);

		$this->add_control(
			'hidden_country',
			[
				'label'   => __( 'Hidden', 'golo-framework' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'condition' => [
					'layout' => 'grid',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography_country',
				'scheme' => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .entry-country a',
				'condition' => [
					'layout' => 'grid',
				],
			]
		);

		$this->add_control(
			'color_country',
			[
				'label' => __( 'Color', 'golo-framework' ),
				'separator' => 'before',
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .entry-country a' => 'color: {{VALUE}};',
				],
				'condition' => [
					'layout' => 'grid',
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

		$settings = $this->get_settings();

		$posts_per_page = $this->get_settings( 'posts_per_page' );
		$thumbnail_size = $this->get_settings( 'thumbnail_size' );
		$orderby        = $this->get_settings( 'orderby' );
		$order          = $this->get_settings( 'order' );
		$hide_empty     = $this->get_settings( 'hide_empty' );
		$hidden_country = $this->get_settings( 'hidden_country' );
		$hidden_count   = $this->get_settings( 'hidden_count' );

		$custom_city_image_size = golo_get_option('archive_city_image_size', '540x740' );
		if( $thumbnail_size ) {
			$custom_city_image_size = $thumbnail_size;
		}

		$widget_classes = [];

		if( $hidden_country == 'yes' ) {
			$widget_classes[] = 'hidden-country';
		}

		if( $hidden_count == 'yes' ) {
			$widget_classes[] = 'hidden-count';
		}

		$args = array(
		    'taxonomy'   => 'place-city',
		    'hide_empty' => $hide_empty,
		    'order'      => $order,
		    'orderby'    => $orderby,
		);

		$args['number'] = $posts_per_page;

		$items = get_terms($args);

		if ( 'rand' == $orderby ) {
			shuffle($items);
		}

		$is_rtl      = is_rtl();
		$direction   = $is_rtl ? 'rtl' : 'ltr';
		$show_dots   = ( in_array( $settings['navigation'], [ 'dots', 'both' ] ) );
		$show_arrows = ( in_array( $settings['navigation'], [ 'arrows', 'both' ] ) );
		
		$layout = '';

		if( $settings['layout'] == 'grid' ) {
			$layout = 'city-grid';
		} else if( $settings['layout'] == 'list' ) {
			$layout = 'city-list';
		}

		if( empty($settings['slides_to_show_tablet']) ) : $settings['slides_to_show_tablet'] = $settings['slides_to_show'];endif;
		if( empty($settings['slides_to_show_mobile']) ) : $settings['slides_to_show_mobile'] = $settings['slides_to_show'];endif;
		if( empty($settings['slides_to_scroll_tablet']) ) : $settings['slides_to_scroll_tablet'] = $settings['slides_to_scroll'];endif;
		if( empty($settings['slides_to_scroll_mobile']) ) : $settings['slides_to_scroll_mobile'] = $settings['slides_to_scroll'];endif;

		$slick_options = [
			'"slidesToShow":' . absint( $settings['slides_to_show'] ),
			'"slidesToScroll":' . absint( $settings['slides_to_scroll'] ),
			'"autoplaySpeed":' . absint( $settings['autoplay_speed'] ),
			'"autoplay":' . (('yes' === $settings['autoplay']) ? 'true' : 'false'),
			'"infinite":' . (('yes' === $settings['infinite']) ? 'true' : 'false'),
			'"pauseOnHover":' . (('yes' === $settings['pause_on_hover']) ? 'true' : 'false'),
			'"centerMode":' . (('yes' === $settings['center_mode']) ? 'true' : 'false'),
			'"variableWidth":' . (('yes' === $settings['variable_width']) ? 'true' : 'false'),
			'"speed":' . absint( $settings['transition_speed'] ),
			'"arrows":' . ($show_arrows ? 'true' : 'false'),
			'"dots":' . ($show_dots ? 'true' : 'false'),
			'"rtl":' . ($is_rtl ? 'true' : 'false'),
			'"responsive": [{ "breakpoint":481, "settings":{ "slidesToShow":'. $settings["slides_to_show_mobile"] .', "slidesToScroll":'. $settings["slides_to_scroll_mobile"] .'}},{ "breakpoint":650, "settings":{ "slidesToShow": 2, "slidesToScroll": 2} }, { "breakpoint":769, "settings":{ "slidesToShow":'. $settings["slides_to_show_tablet"] .', "slidesToScroll":'. $settings["slides_to_scroll_tablet"] .' } } ]',
		];
		$slick_data = '{'.implode(', ', $slick_options).'}';

		if ( 'fade' === $settings['transition'] ) {
			$slick_options['fade'] = true;
		}

		$carousel_classes = [ 'elementor-carousel' ];

		if( $settings['variable_width'] == 'yes' ){
			$carousel_classes[] = 'variable-width';
		}

		$this->add_render_attribute( 'slides', [
			'class' => $carousel_classes,
			'data-slider_options' => $slick_data,
		] );

		?>
			<div class="elementor-place-cities <?php echo join(' ', $widget_classes); ?>">
			<?php if( $settings['enable_slider'] == 'yes' ) { ?>
				<div class="elementor-slick-slider" dir="<?php echo esc_attr( $direction ); ?>">
					<div <?php echo $this->get_render_attribute_string( 'slides' ); ?>>
			<?php }else{ ?>
	        	<div class="elementor-grid-cities" dir="<?php echo esc_attr( $direction ); ?>">
					<div class="elementor-grid">
	        <?php } ?>

					<?php if( $items ) :
	                    foreach ($items as $item) {
	                        $term_id = $item->term_id;
	                    ?>
	                        <div class="<?php echo $layout; ?>">
		                        <?php golo_get_template('content-city.php', array(
		                            'term_id'                	=> $term_id,
		                            'custom_city_image_size' 	=> $custom_city_image_size,
		                            'layout' 					=> $settings['layout'],
		                        )); ?>
		                    </div>
	                    <?php } ?>
	                <?php endif; ?>

					</div>
				</div>
			</div>
		<?php
	}
}
