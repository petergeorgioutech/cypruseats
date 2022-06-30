<?php

namespace Elementor;

use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

Plugin::instance()->widgets_manager->register_widget_type( new Widget_Dropdown_Cities() );

/**
 * Elementor place search.
 *
 * Elementor widget that displays an eye-catching headlines.
 *
 * @since 1.0.0
 */
class Widget_Dropdown_Cities extends Widget_Base {

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
		return 'dropdown-cities';
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
		return __( 'Dropdown Cities', 'golo-framework' );
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
		return 'golo-badge eicon-select';
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
		return [ 'city', 'select', 'golo' ];
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
			'section_style',
			[
				'label' => __( 'Style', 'golo-framework' ),
				'tab'   => Controls_Manager::TAB_STYLE,
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
					'{{WRAPPER}} .entry-show' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'typography',
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .entry-show',
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

		$this->add_render_attribute( 'title', 'class', [ 'elementor-heading-title' ] );

		$this->add_inline_editing_attributes( 'title' );

		$title = $settings['title'];

		$text = esc_html__('Destinations', 'golo-framework');

		$terms = get_terms( 'place-city',
			array(
				'hide_empty'   => false,
				'hierarchical' => true,
			) 
		);

		$current_city = isset( $_GET['city'] ) ? golo_clean(wp_unslash($_GET['city'])) : '';
		if( $current_city ){
			$current_term = get_term_by('slug', $current_city, 'place-city');
		}else{
			$current_term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
		}

		if( $current_term ) {
			$text = $current_term->name;
		}

		$city_slug = '';
		$sub_link  = '';
		if( is_single() ){
            $id = get_the_ID();
			$place_city = get_the_terms( $id, 'place-city');
			if( $place_city ) {
				$city_slug = $place_city[0]->slug;
				$text      = $place_city[0]->name;
			}
        }

		$categories = array();

		?>
			<div class="dropdown-categories dropdown-select">
				<div class="entry-show">
					<span><?php echo esc_html($text); ?></span>
					<i class="la la-angle-down"></i>
				</div>
				<ul class="entry-select custom-scrollbar">
					<?php
						foreach ( $terms as $term ) {
							$categories[ $term->name ] = $term->slug;
							$term_link = get_term_link($term);
							?>
								<li class="<?php if( $current_term ) { if( $current_term->slug == $term->slug ) : echo esc_attr('active');endif; } ?>"><a href="<?php echo esc_url($term_link); ?>"><?php echo esc_html($term->name); ?></a></li>
							<?php
						} 
					?>
				</ul>
			</div>
		<?php
	}
}
