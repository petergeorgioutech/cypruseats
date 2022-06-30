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

Plugin::instance()->widgets_manager->register_widget_type( new Widget_City() );

/**
 * Elementor city.
 *
 * Elementor widget that displays an eye-catching headlines.
 *
 * @since 1.0.0
 */
class Widget_City extends Widget_Base {

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
		return 'city';
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
		return __( 'City', 'golo-framework' );
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

		$taxonomy_terms = get_categories(
		    array(
		        'taxonomy'   => 'place-city',
		        'orderby'    => 'name',
		        'order'      => 'ASC',
		        'hide_empty' => true,
		        'parent'     => 0,
		    )
		);

		$cities = [];
		foreach ( $taxonomy_terms as $city ) {
			$cities[ $city->slug ] = $city->name;
		}

		$this->add_control(
			'city',
			[
				'label'       => __( 'City', 'golo-framework' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => $cities,
				'label_block' => true,
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
			'count_items',
			[
				'label' => __( 'Enable Count', 'golo-framework' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
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

		$this->add_control(
			'border_radius',
			[
				'label' => __( 'Border Radius', 'golo-framework' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .inner-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'color_background',
			[
				'label' => __( 'Background Color', 'golo-framework' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .inner-item a:after' => 'background: {{VALUE}};',
				],
				'default' => '',
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
					'{{WRAPPER}} .inner-item .entry-detail .entry-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography_title',
				'scheme' => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .inner-item .entry-detail .entry-title',
			]
		);

		$this->add_control(
			'color_title',
			[
				'label' => __( 'Color', 'golo-framework' ),
				'separator' => 'before',
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .inner-item .entry-detail .entry-title' => 'color: {{VALUE}};',
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

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography_sub_title',
				'scheme' => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .inner-item .entry-detail>span',
			]
		);

		$this->add_control(
			'color_sub_title',
			[
				'label' => __( 'Color', 'golo-framework' ),
				'separator' => 'before',
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .inner-item .entry-detail>span' => 'color: {{VALUE}};',
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

		$thumbnail_size = $this->get_settings( 'thumbnail_size' );
		$city_slug = $this->get_settings( 'city' );
		$count_items = $this->get_settings( 'count_items' );

		$city = get_term_by('slug', $city_slug, 'place-city');

		$widget_classes = [];

		if( $city ) {
			$term_id    = $city->term_id;
			$term_slug  = $city->slug;
			$term_count = $city->count;
			$link       = get_term_link($city);

			$attach_id = '';
			$featured_image = get_term_meta( $term_id, 'place_city_featured_image', true );
			if ($featured_image && !empty($featured_image['url'])) {
			    $attach_id = $featured_image['id'];
			}

			$no_image_src  = GOLO_PLUGIN_URL . 'assets/images/no-image.jpg';
			$default_image = golo_get_option('default_place_image','');

			if (preg_match('/\d+x\d+/', $thumbnail_size)) {
			    $image_sizes = explode('x', $thumbnail_size);
			    $width       = $image_sizes[0];
			    $height      = $image_sizes[1];
			    $image_src   = golo_image_resize_id($attach_id, $width, $height, true);
			    if( $default_image != '' )
			    {
			        if( is_array($default_image) && $default_image['url'] != '' )
			        {
			            $resize = golo_image_resize_url($default_image['url'], $width, $height, true);
			            if ($resize != null && is_array($resize)) {
			                $no_image_src = $resize['url'];
			            }
			        }
			    }
			} else {
			    if (!in_array($thumbnail_size, array('full', 'thumbnail'))) {
			        $thumbnail_size = 'full';
			    }
			    $image_src = wp_get_attachment_image_src($attach_id, $thumbnail_size);
			    if ($image_src && !empty($image_src[0])) {
			        $image_src = $image_src[0];
			    }
			    if (!empty($image_src)) {
			        list($width, $height) = getimagesize($image_src);
			    }
			    if($default_image != '')
			    {
			        if(is_array($default_image) && $default_image['url'] != '')
			        {
			            $no_image_src = $default_image['url'];
			        }
			    }
			}

			if (!$image_src) {
				$image_src = $no_image_src;
			}
		?>
			<div class="elementor-single-city <?php echo join(' ', $widget_classes); ?>">
				<div class="inner-item">
					<a href="<?php echo esc_url($link); ?>">
						<img src="<?php echo esc_url($image_src); ?>" alt="<?php echo esc_attr($city->name); ?>">
						<span class="entry-detail">
							<h3 class="entry-title"><?php echo esc_html($city->name); ?></h3>
							<?php if( $count_items == 'yes' ) : ?>
							<span class="entry-count"><?php printf( _n( '%s place', '%s places', $term_count, 'golo-framework' ), esc_html( $term_count ) ); ?></span>
							<?php endif; ?>
						</span>
					</a>
				</div>
			</div>
		<?php
		}
	}
}
