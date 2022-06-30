<?php

namespace Elementor;

use ElementorPro\Modules\QueryControl\Module;
use ElementorPro\Modules\QueryControl\Controls\Group_Control_Posts;
use ElementorPro\Modules\QueryControl\Controls\Group_Control_Related;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

Plugin::instance()->widgets_manager->register_widget_type( new Widget_Places() );

/**
 * Elementor places.
 *
 * Elementor widget that displays an eye-catching headlines.
 *
 * @since 1.0.0
 */
class Widget_Places extends Widget_Base {

	const QUERY_CONTROL_ID = 'query';
	const QUERY_OBJECT_POST = 'post';

	public function get_post_type() {
		return 'place';
	}

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
		return 'places';
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
		return __( 'Places', 'golo-framework' );
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
		return 'golo-badge eicon-gallery-grid';
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
		return [ 'place', 'slide', 'carousel', 'golo-framework' ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_layout',
			[
				'label' => __( 'Layout', 'golo-framework' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'place_layout',
			[
				'label'   => __( 'Place Layout', 'golo-framework' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'layout-01',
				'options' => [
					'layout-01' => __( '01', 'golo-framework' ),
					'layout-02' => __( '02', 'golo-framework' ),
				],
			]
		);

		$terms = get_terms( 'place-categories' );
		$count = count( $terms );
		$place_categories = array(
			'0'		=> __( 'All', 'golo-framework' ),
		);
		if ( $count > 0 ) {
		    foreach ( $terms as $term ) {
		        $place_categories[$term->term_id] = $term->name;
		    }
		}

		$this->add_control(
			'place_category',
			[
				'label'   => __( 'Place Category', 'golo-framework' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '0',
				'options' => $place_categories,
			]
		);

		$terms_city 		= get_terms( 'place-city' );
		$count_city 		= count( $terms_city );
		$place_city 		= array(
			'0'		=> __( 'All', 'golo-framework' ),
		);

		if ( $count_city > 0 ) {
		    foreach ( $terms_city as $term_city ) {
		        $place_city[$term_city->term_id] = $term_city->name;
		    }
		}

		$this->add_control(
			'place_city',
			[
				'label'   => __( 'Place City', 'golo-framework' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '0',
				'options' => $place_city,
			]
		);

		$terms_type 		= get_terms( 'place-type' );
		$count_type 		= count( $terms_type );
		$place_type 		= array(
			'0'		=> __( 'All', 'golo-framework' ),
		);
		if ( $count_type > 0 ) {
		    foreach ( $terms_type as $term_type ) {
		        $place_type[$term_type->term_id] = $term_type->name;
		    }
		}

		$this->add_control(
			'place_type',
			[
				'label'   => __( 'Place Type', 'golo-framework' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '0',
				'options' => $place_type,
			]
		);

		$this->add_control(
			'enable_slider',
			[
				'label'   => __( 'Enable Slider', 'golo-framework' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
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
				'label'   => __( 'Posts Per Page', 'golo-framework' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 6,
			]
		);

		$this->add_control(
			'thumbnail_size',
			[
				'label'       => __( 'Image Size', 'golo-framework' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Example: 300x300', 'golo-framework' ),
				'default'     => '540x480',
			]
		);

		$this->end_controls_section();

		$this->register_query_section();

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
				'tab'   => Controls_Manager::TAB_STYLE,
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
					'{{WRAPPER}} .elementor-carousel .place-item' => 'padding-left: calc({{SIZE}}{{UNIT}}/2); padding-right: calc({{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .slick-list'                     => 'margin-left: calc(-{{SIZE}}{{UNIT}}/2);margin-right: calc(-{{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .elementor-grid'                 => 'grid-column-gap: {{SIZE}}{{UNIT}}',
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

		$this->end_controls_section();

	}

	protected function register_query_section() {
		$this->start_controls_section( 'query_section', [
			'label' => esc_html__( 'Query', 'golo' ),
		] );

		$this->add_control(
			'type_query',
			[
				'label'   => __( 'Order by', 'golo-framework' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'use_filter',
				'options' => [
					'manual_select' => __( 'Manual Select', 'golo-framework' ),
					'use_filter'   => __( 'Use Filter', 'golo-framework' ),
				],
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => __( 'Filter', 'golo-framework' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'newest',
				'options' => [
					'featured'         => __( 'Featured', 'golo-framework' ),
					'rating'           => __( 'Rating', 'golo-framework' ),
					'newest'           => __( 'Newest', 'golo-framework' ),
					'price_asc'        => __( 'Low to High', 'golo-framework' ),
					'price_desc'       => __( 'High to Low', 'golo-framework' ),
					'price_range_asc'  => __( 'Range Low to High', 'golo-framework' ),
					'price_range_desc' => __( 'Range High to Low', 'golo-framework' ),
					'random' 		   => __( 'Random', 'golo-framework' ),
				],
				'condition' => [
					'type_query' => 'use_filter',
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label'   => __( 'Order', 'golo-framework' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'desc',
				'options' => [
					'asc'  => __( 'ASC', 'golo-framework' ),
					'desc' => __( 'DESC', 'golo-framework' ),
				],
				'condition' => [
					'orderby' => [ 'featured', 'rating' ],
				],
			]
		);

		$this->add_control(
			'include_ids',
			[
				'label' => __( 'Search & Select', 'elementor-pro' ),
				'type' => self::QUERY_CONTROL_ID,
				'autocomplete' => [
					'object' => self::QUERY_OBJECT_POST,
					'query'   => [
						'post_type' => $this->get_post_type(),
					],
				],
				'options' => [],
				'label_block' => true,
				'multiple' => true,
				'condition' => [
					'type_query' => 'manual_select',
				],
			]
		);

		$this->add_control(
			'exclude',
			[
				'label' => __( 'Exclude', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => [
					'current_post' => __( 'Current Post', 'elementor-pro' ),
					'manual_selection' => __( 'Manual Selection', 'elementor-pro' ),
				],
				'label_block' => true,
				'condition' => [
					'type_query' => 'use_filter',
				],
			]
		);

		$this->add_control(
			'exclude_ids',
			[
				'label' => __( 'Search & Select', 'elementor-pro' ),
				'type' => self::QUERY_CONTROL_ID,
				'autocomplete' => [
					'object' => self::QUERY_OBJECT_POST,
					'query'   => [
						'post_type' => $this->get_post_type(),
					],
				],
				'options' => [],
				'label_block' => true,
				'multiple' => true,
				'condition' => [
					'exclude' => 'manual_selection',
					'type_query' => 'use_filter',
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

		$place_layout 	= $this->get_settings( 'place_layout' );
		$place_category = $this->get_settings( 'place_category' );
		$place_type 	= $this->get_settings( 'place_type' );
		$place_city 	= $this->get_settings( 'place_city' );
		$columns 		= $this->get_settings( 'columns' );
		$posts_per_page = $this->get_settings( 'posts_per_page' );
		$thumbnail_size = $this->get_settings( 'thumbnail_size' );

		$type_query     = $this->get_settings( 'type_query' );
		$orderby        = $this->get_settings( 'orderby' );
		$order          = $this->get_settings( 'order' );
		$exclude_ids    = $this->get_settings( 'exclude_ids' );
		$include_ids    = $this->get_settings( 'include_ids' );

		$item_class = '';

		$custom_place_image_size = golo_get_option('archive_city_image_size', '540x480' );
		if( $thumbnail_size ) {
			$custom_place_image_size = $thumbnail_size;
		}

		$tax_query  = array();
		$meta_query = array();

		if (isset($place_category) && $place_category != '0') {
			$tax_query[] = array(
	            'taxonomy' => 'place-categories',
	            'field'    => 'term_id',
	            'terms'    => $place_category,
	        );
		}

		if (isset($place_city) && $place_city != '0') {
			$tax_query[] = array(
	            'taxonomy' => 'place-city',
	            'field'    => 'term_id',
	            'terms'    => $place_city,
	        );
		}

		if (isset($place_type) && $place_type != '0') {
			$tax_query[] = array(
	            'taxonomy' => 'place-type',
	            'field'    => 'term_id',
	            'terms'    => $place_type,
	        );
		}

		$args = array(
		    'posts_per_page'      => $posts_per_page,
		    'post_type'           => 'place',
		    'ignore_sticky_posts' => 1,
		    'post_status'         => 'publish',
		    'orderby'             => array(
		        'menu_order' => 'ASC',
		        'date'       => 'DESC',
		    ),
		);

		if( $include_ids && $type_query == 'manual_select' ) {
			$args['post__in'] = $include_ids;
		}

		if( $type_query == 'use_filter' ) {
			if( $exclude_ids ) {
				$args['post__not_in'] = $exclude_ids;
			}

			if (!empty($orderby)) {
	            if( $orderby == 'featured' ) {
	                $meta_query[] = array(
	                    'key'     => GOLO_METABOX_PREFIX . 'place_featured',
	                    'value'   => 1,
	                    'type'    => 'NUMERIC',
	                    'compare' => '=',
	                );
	            }
	            if( $orderby == 'rating' ) {
	                $args['meta_key'] = GOLO_METABOX_PREFIX . 'place_rating';
	                $args['orderby']  = 'meta_value_num';
	                $args['order']    = $order;
	            }
	            if( $orderby == 'newest' ) {
	                $args['orderby'] = array(
	                    'menu_order' => 'ASC',
	                    'date'       => 'DESC',
	                );
	            }
	            if( $orderby == 'price_asc' ) {
	                $args['meta_key'] = GOLO_METABOX_PREFIX . 'place_price_short';
	                $args['orderby']  = 'meta_value_num';
	                $args['order']    = 'ASC';
	            }
	            if( $orderby == 'price_desc' ) {
	                $args['meta_key'] = GOLO_METABOX_PREFIX . 'place_price_short';
	                $args['orderby']  = 'meta_value_num';
	                $args['order']    = 'DESC';
	            }
	            if( $orderby == 'price_range_asc' ) {
	                $args['meta_key'] = GOLO_METABOX_PREFIX . 'place_price_range';
	                $args['orderby']  = 'meta_value_num';
	                $args['order']    = 'ASC';
	            }
	            if( $orderby == 'price_range_desc' ) {
	                $args['meta_key'] = GOLO_METABOX_PREFIX . 'place_price_range';
	                $args['orderby']  = 'meta_value_num';
	                $args['order']    = 'DESC';
	            }
	            if( $orderby == 'random' ) {
	                $args['meta_key'] = '';
	                $args['orderby']  = 'rand';
	                $args['order']    = 'ASC';
	            }
	        }
	    }

	    if (!empty($tax_query)) {
	    	$args['tax_query'] = array(
	            'relation' => 'AND',
	            $tax_query
	        );
	    }

        $args['meta_query'] = array(
            'relation' => 'AND',
            $meta_query
        );

		$data       = new \WP_Query($args);
		$total_post = $data->found_posts;

		$is_rtl      = is_rtl();
		$direction   = $is_rtl ? 'rtl' : 'ltr';
		$show_dots   = ( in_array( $settings['navigation'], [ 'dots', 'both' ] ) );
		$show_arrows = ( in_array( $settings['navigation'], [ 'arrows', 'both' ] ) );

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
			<div class="elementor-places">
				<?php if ($data->have_posts()) { ?>
    				
    				<?php if( $settings['enable_slider'] == 'yes' ) { ?>
						<div class="elementor-slick-slider" dir="<?php echo esc_attr( $direction ); ?>">
							<div <?php echo $this->get_render_attribute_string( 'slides' ); ?>>
    				<?php }else{ ?>
			        	<div class="elementor-grid-places" dir="<?php echo esc_attr( $direction ); ?>">
							<div class="elementor-grid">
			        <?php } ?>
	
							<?php while ($data->have_posts()): $data->the_post(); ?>
						
								<?php 
								golo_get_template( 'content-place/' . $place_layout . '.php', array(
									'place_id'                			=> get_the_ID(),
									'custom_place_image_size' 			=> $custom_place_image_size,
									'layout'                  			=> $place_layout,
								) );
			                    ?>

					        <?php endwhile; ?>

						</div>
					</div>

			    <?php } else { ?>

			        <div class="item-not-found"><?php esc_html_e('No item found', 'golo-framework'); ?></div>

			    <?php } ?>
			</div>
		<?php
	}
}
