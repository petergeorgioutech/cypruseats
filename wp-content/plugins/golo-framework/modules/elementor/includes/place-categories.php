<?php

namespace Elementor;

use Elementor\Core\Schemes;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

Plugin::instance()->widgets_manager->register_widget_type( new Widget_Place_Categories() );

/**
 * Elementor place categories.
 *
 * Elementor widget that displays an eye-catching headlines.
 *
 * @since 1.0.0
 */
class Widget_Place_Categories extends Widget_Base {

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
		return 'place-categories';
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
		return __( 'Place Categories', 'golo-framework' );
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
		return 'golo-badge eicon-slider-push';
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
		return [ 'golo', 'category', 'place', 'slide' ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_layout',
			[
				'label' => __( 'Layout', 'golo-framework' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]   
		);

		$repeater = new Repeater();

		$taxonomy_terms = get_categories(
		    array(
		        'taxonomy'   => 'place-categories',
		        'orderby'    => 'name',
		        'order'      => 'ASC',
		        'hide_empty' => true,
		        'parent'     => 0,
		    )
		);

		$categories = [];

		foreach ( $taxonomy_terms as $category ) {
			$categories[ $category->slug ] = $category->name;
		}

		$repeater->add_control(
			'category',
			[
				'label'       	=> __( 'Categories', 'golo-framework' ),
				'type'        	=> Controls_Manager::SELECT,
				'options'     	=> $categories,
				'label_block' 	=> true,
			]
		);

		$repeater->add_control(
			'selected_icon',
			[
				'label' => __( 'Icon', 'golo-framework' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'la la-star',
					'library' => 'fa-solid',
				],
			]
		);

		$repeater->add_control(
			'background_color',
			[
				'label'     => __( 'Background Color', 'golo-framework' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .inner-item a:before' => 'background-color: {{VALUE}}',
				],
			]
		);

		$repeater->add_control(
			'image',
			[
				'label'   => __( 'Choose Image', 'golo-framework' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_control(
			'line_image',
			[
				'label'   => __( 'Line Image', 'golo-framework' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => '',
				],
			]
		);

		$repeater->add_control(
			'link',
			[
				'label' => __( 'Link', 'golo-framework' ),
				'type' => Controls_Manager::TEXT,
				'default' => '#',
			]
		);

		$this->add_control(
			'layout_style',
			[
				'label'   => __( 'Layout', 'golo-framework' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'rectangle-01' => __( 'Rectangle 01', 'golo-framework' ),
					'rectangle-02' => __( 'Rectangle 02', 'golo-framework' ),
					'circle-01' => __( 'Circle 01', 'golo-framework' ),
					'circle-02' => __( 'Circle 02', 'golo-framework' ),
				],
				'default' => 'rectangle-01',
			]
		);

		$this->add_control(
			'categories_list',
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

		$this->add_control(
			'thumbnail_size',
			[
				'label'       => __( 'Image Size', 'golo-framework' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Example: 300x300', 'golo-framework' ),
				'default' 	  => '370x250',
			]
		);

		$this->add_control(
			'show_icon',
			[
				'label' => __( 'Show Icon', 'golo-framework' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'false',
			]
		);
		
		$this->add_control(
			'show_button',
			[
				'label' => __( 'Show Button', 'golo-framework' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'false',
				'condition' => [
					'layout_style' => 'rectangle-02',
				],
			]
		);

		$this->add_control(
			'count_items',
			[
				'label' => __( 'Enable Count', 'golo-framework' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_line_image',
			[
				'label' => __( 'Enable Line Image', 'golo-framework' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'false',
			]
		);

		$this->add_control(
			'search_link',
			[
				'label' => __( 'Direct Search Page', 'golo-framework' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slider_options',
			[
				'label' => __( 'Slider Options', 'golo-framework' ),
				'type' => Controls_Manager::SECTION,
			]
		);

		$this->add_control(
			'enable_slider',
			[
				'label' => __( 'Enable Slider', 'golo-framework' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$slides_to_show = range( 1, 10 );
		$slides_to_show = array_combine( $slides_to_show, $slides_to_show );

		$this->add_responsive_control(
			'slides_to_show',
			[
				'label' => __( 'Slides to Show', 'golo-framework' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'options' => [
					'' => __( 'Default', 'golo-framework' ),
				] + $slides_to_show,
			]
		);

		$this->add_responsive_control(
			'slides_to_scroll',
			[
				'label' => __( 'Slides to Scroll', 'golo-framework' ),
				'type' => Controls_Manager::SELECT,
				'description' => __( 'Set how many slides are scrolled per swipe.', 'golo-framework' ),
				'default' => '3',
				'options' => [
					'' => __( 'Default', 'golo-framework' ),
				] + $slides_to_show,
				'condition' => [
					'slides_to_show!' => '1',
				],
			]
		);

		$this->add_control(
			'navigation',
			[
				'label' => __( 'Navigation', 'golo-framework' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'both',
				'options' => [
					'both' => __( 'Arrows and Dots', 'golo-framework' ),
					'arrows' => __( 'Arrows', 'golo-framework' ),
					'dots' => __( 'Dots', 'golo-framework' ),
					'none' => __( 'None', 'golo-framework' ),
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
				'label' => __( 'Pause on Hover', 'golo-framework' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => __( 'Autoplay', 'golo-framework' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label' => __( 'Autoplay Speed', 'golo-framework' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 5000,
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
				'label' => __( 'Infinite Loop', 'golo-framework' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'transition_speed',
			[
				'label' => __( 'Transition Speed', 'golo-framework' ) . ' (ms)',
				'type' => Controls_Manager::NUMBER,
				'default' => 500,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_general_style',
			[
				'label' => __( 'General', 'golo-framework' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label' => __( 'Column Gap', 'golo-framework' ),
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
					'{{WRAPPER}} .elementor-item' => 'padding-left: calc({{SIZE}}{{UNIT}}/2);padding-right: calc({{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .slick-list'     => 'margin-left: calc(-{{SIZE}}{{UNIT}}/2);margin-right: calc(-{{SIZE}}{{UNIT}}/2)',
				],
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label' => __( 'Border Radius', 'golo-framework' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default' => [
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .inner-item .entry-image,{{WRAPPER}} .circle-01 .inner-item' => 'border-radius: {{SIZE}}{{UNIT}};-webkit-backface-visibility: hidden;-moz-backface-visibility: hidden;-webkit-transform: translate3d(0, 0, 0);-moz-transform: translate3d(0, 0, 0)',
					'{{WRAPPER}} .inner-item .entry-image,{{WRAPPER}} .rectangle-01 .inner-item' => 'border-radius: {{SIZE}}{{UNIT}};-webkit-backface-visibility: hidden;-moz-backface-visibility: hidden;-webkit-transform: translate3d(0, 0, 0);-moz-transform: translate3d(0, 0, 0)',
					'{{WRAPPER}} .inner-item .entry-image,{{WRAPPER}} .rectangle-02 .inner-item' => 'border-radius: {{SIZE}}{{UNIT}};-webkit-backface-visibility: hidden;-moz-backface-visibility: hidden;-webkit-transform: translate3d(0, 0, 0);-moz-transform: translate3d(0, 0, 0)',
				],
			]
		);

		$this->add_control(
			'align_items',
			[
				'label' => __( 'Align', 'golo-framework' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'golo-framework' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'golo-framework' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'golo-framework' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'prefix_class' => 'elementor-nav-menu__align-',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			[
				'label' => __( 'Title', 'golo-framework' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'filter_spacing',
			[
				'label' => __( 'Spacing', 'golo-framework' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .entry-count,{{WRAPPER}} .entry-title' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'  => __( 'Text Color', 'golo-framework' ),
				'type'   => Controls_Manager::COLOR,
				'scheme' => [
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				],
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'{{WRAPPER}} .rectangle-01 .inner-item a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .circle-01 .inner-item a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .circle-02 .inner-item a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .rectangle-02 .inner-item span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'typography',
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .entry-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon_style',
			[
				'label' => __( 'Icon', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'icon_spacing',
			[
				'label' => __( 'Spacing', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .inner-item .entry-detail i' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .inner-item .entry-detail svg' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .inner-item .entry-detail i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .inner-item .entry-detail svg' => 'fill: {{VALUE}};',
				],
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
			]
		);

		$this->add_control(
			'icon_color_hover',
			[
				'label' => __( 'Hover', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .inner-item:hover .entry-detail i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .inner-item:hover .entry-detail svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Size', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 24,
				],
				'range' => [
					'px' => [
						'min' => 6,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .inner-item .entry-detail i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .inner-item .entry-detail svg' => 'width: {{SIZE}}{{UNIT}};',
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

		$settings = $this->get_settings_for_display();

		$layout_style   		= $this->get_settings( 'layout_style' );
		$thumbnail_size 		= $this->get_settings( 'thumbnail_size' );
		$show_icon      		= $this->get_settings( 'show_icon' );
		$show_button      		= $this->get_settings( 'show_button' );
		$show_line_image      	= $this->get_settings( 'show_line_image' );
		$search_link    		= $this->get_settings( 'search_link' );
		$count_items    		= $this->get_settings( 'count_items' );

		if( preg_match('/\d+x\d+/', $thumbnail_size) ) {
		    $thumbnail_size = explode('x', $thumbnail_size);
			$width  = $thumbnail_size[0];
			$height = $thumbnail_size[1];
     	}

		$is_rtl      = is_rtl();
		$direction   = $is_rtl ? 'rtl' : 'ltr';
		$show_dots   = ( in_array( $settings['navigation'], [ 'dots', 'both' ] ) );
		$show_arrows = ( in_array( $settings['navigation'], [ 'arrows', 'both' ] ) );

		if( empty($settings['slides_to_show_tablet']) ) : $settings['slides_to_show_tablet'] = $settings['slides_to_show'];endif;
		if( empty($settings['slides_to_show_mobile']) ) : $settings['slides_to_show_mobile'] = $settings['slides_to_show'];endif;
		if( empty($settings['slides_to_scroll_tablet']) ) : $settings['slides_to_scroll_tablet'] = $settings['slides_to_scroll'];endif;
		if( empty($settings['slides_to_scroll_mobile']) ) : $settings['slides_to_scroll_mobile'] = $settings['slides_to_scroll'];endif;

		$slick_options = [
			'"isslick":' . (('yes' === $settings['enable_slider']) ? 'true' : 'false'),
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
			'"responsive": [{ "breakpoint":481, "settings":{ "slidesToShow":'. $settings["slides_to_show_mobile"] .', "slidesToScroll":'. $settings["slides_to_scroll_mobile"] .'} }, { "breakpoint":769, "settings":{ "slidesToShow":'. $settings["slides_to_show_tablet"] .', "slidesToScroll":'. $settings["slides_to_scroll_tablet"] .' } }, { "breakpoint":1200, "settings":{ "slidesToShow": '. $settings["slides_to_show"] .', "slidesToScroll": '. $settings["slides_to_scroll"] .'} } ]',
		];
		$slick_data = '{'.implode(', ', $slick_options).'}';

		$carousel_classes = [ 'elementor-carousel', $layout_style ];

		$this->add_render_attribute( 'slides', [
			'class' => $carousel_classes,
			'data-slider_options' => $slick_data,
		] );

		?>

		<div class="elementor-slick-slider" dir="<?php echo esc_attr( $direction ); ?>">

			<div <?php echo $this->get_render_attribute_string( 'slides' ); ?>>

			<?php
				$default_image = golo_get_option('default_place_image','');

				
				foreach ( $settings['categories_list'] as $index => $item ) :
					$category_slug = $item['category'];
					$link = $item['link'];

					if( $item['image']['url'] ) {
						$image_src_full = golo_image_resize_url($item['image']['url'], $width, $height);
						$image_src = $image_src_full['url'];

					} else {

						if($default_image != '') {
					        if(is_array($default_image) && $default_image['url'] != '') {
					            $image_src = $default_image['url'];

					        }
					    } else {
					    	if( $layout_style == 'rectangle-01' || $layout_style == 'rectangle-02' ) {
					    		$image_src = GOLO_PLUGIN_URL . 'assets/images/no-image.jpg';
					    	} else {
					    		$image_src = GOLO_PLUGIN_URL . 'assets/images/no-image-500x500.jpg';
					    	}
					    }
					}

					if ($item['line_image']['url']) {
						$line_image = $item['line_image']['url'];
					} else {
						$line_image = '';
					}
					

					$has_icon = ! empty( $item['icon'] );
					if ( ! $has_icon && ! empty( $item['selected_icon']['value'] ) ) {
						$has_icon = true;
					}
					$migrated = isset( $item['__fa4_migrated']['selected_icon'] );
					$is_new = ! isset( $item['icon'] ) && Icons_Manager::is_migration_allowed();
					
					if( $category_slug ) {
						$cate = get_term_by('slug', $category_slug, 'place-categories');

						if( $cate ) {
							$term_id    = $cate->term_id;
							$term_slug  = $cate->slug;
							$term_count = $cate->count;
							$link       = get_term_link( $cate );

							if( $search_link == 'yes' ) {
								$link = home_url('/') . '?s=&post_type=place&category=' . $term_slug . '&caid=' . $term_id;
							}

						?>

							<div class="elementor-item elementor-repeater-item-<?php echo $item['_id']; ?> <?php if( $show_line_image == 'yes' ) { echo 'show_line'; } ?>">
								<?php if( $layout_style == 'rectangle-01') : ?>
								<div class="inner-item">
									<a href="<?php echo esc_url($link); ?>">
										<img src="<?php echo esc_url($image_src); ?>" alt="<?php echo esc_attr($cate->name); ?>">
										<span class="entry-detail" style="background-image: url(<?php echo $line_image ?>);">
											<?php 
											if ( $has_icon && $show_icon == 'yes' ) :
												if ( $is_new || $migrated ) {
													Icons_Manager::render_icon( $item['selected_icon'], [ 'aria-hidden' => 'true' ] );
												} elseif ( ! empty( $item['icon'] ) ) {
													?><i <?php echo $this->get_render_attribute_string( 'i' ); ?>></i><?php
												}
											endif;
											?>
											<span class="entry-title"><?php echo esc_html($cate->name); ?></span>
											<?php if( $count_items ) : ?>
											<span class="entry-count"><?php printf( _n( '%s place', '%s places', $term_count, 'golo-framework' ), esc_html( $term_count ) ); ?></span>
											<?php endif; ?>
										</span>
									</a>
								</div>
								<?php endif; ?>
								
								<?php if( $layout_style == 'rectangle-02' ) : ?>
								<div class="inner-item">
								    <a href="<?php echo esc_url($link); ?>">
    									<img src="<?php echo esc_url($image_src); ?>" alt="<?php echo esc_attr($cate->name); ?>">
    									<span class="entry-detail" style="background-image: url(<?php echo $line_image ?>);">
    										<?php 
    										if ( $has_icon && $show_icon == 'yes' ) :
    											if ( $is_new || $migrated ) {
    												Icons_Manager::render_icon( $item['selected_icon'], [ 'aria-hidden' => 'true' ] );
    											} elseif ( ! empty( $item['icon'] ) ) {
    												?><i <?php echo $this->get_render_attribute_string( 'i' ); ?>></i><?php
    											}
    										endif;
    										?>
    										<span class="entry-title"><?php echo esc_html($cate->name); ?></span>
    										<?php if( $count_items ) : ?>
    										<span class="entry-count"><?php printf( _n( '%s place', '%s places', $term_count, 'golo-framework' ), esc_html( $term_count ) ); ?></span>
    										<?php endif; ?>
    										<?php if( $show_button == 'yes' ) : ?>
    										<span class="btn"><?php echo esc_html( 'Explorer', 'golo-framework' ); ?></span>
    										<?php endif; ?>
    									</span>
									</a>
								</div>
								<?php endif; ?>

								<?php if( $layout_style == 'circle-01' ) : ?>
								<div class="inner-item">
									<a href="<?php echo esc_url($link); ?>">
										<img src="<?php echo esc_url($image_src); ?>" alt="<?php echo esc_attr($cate->name); ?>">
										<span class="entry-detail" style="background-image: url(<?php echo $line_image ?>);">
											<?php 
											if ( $has_icon && $show_icon == 'yes' ) :
												if ( $is_new || $migrated ) {
													Icons_Manager::render_icon( $item['selected_icon'], [ 'aria-hidden' => 'true' ] );
												} elseif ( ! empty( $item['icon'] ) ) {
													?><i <?php echo $this->get_render_attribute_string( 'i' ); ?>></i><?php
												}
											endif; 
											?>
											<span class="entry-title"><?php echo esc_html($cate->name); ?></span>
											<?php if( $count_items ) : ?>
											<span class="entry-count"><?php printf( _n( '%s place', '%s places', $term_count, 'golo-framework' ), esc_html( $term_count ) ); ?></span>
											<?php endif; ?>
										</span>
									</a>
								</div>
								<?php endif; ?>

								<?php if( $layout_style == 'circle-02' ) : ?>
								<div class="inner-item">
									<div class="entry-image">
										<a href="<?php echo esc_url($link); ?>">
											<img src="<?php echo esc_url($image_src); ?>" alt="<?php echo esc_attr($cate->name); ?>">
										</a>
									</div>

									<div class="entry-detail" style="background-image: url(<?php echo $line_image ?>);">
										<?php 
										if ( $has_icon && $show_icon == 'yes' ) :
											if ( $is_new || $migrated ) {
												Icons_Manager::render_icon( $item['selected_icon'], [ 'aria-hidden' => 'true' ] );
											} elseif ( ! empty( $item['icon'] ) ) {
												?><i <?php echo $this->get_render_attribute_string( 'i' ); ?>></i><?php
											}
										endif; 
										?>

										<h3 class="entry-title">
											<a href="<?php echo esc_url($link); ?>">
												<?php echo esc_html($cate->name); ?>
												<?php echo sprintf( __( '(%s)', 'golo-framework' ),  $term_count ); ?>
											</a>
										</h3>
									</div>
								</div>
								<?php endif; ?>
							</div>

						<?php

						}
					}
				endforeach;
			?>

			</div>

		</div>

		<?php
	}
}
