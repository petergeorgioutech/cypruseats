<?php

namespace Elementor;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

Plugin::instance()->widgets_manager->register_widget_type( new Widget_Place_Search() );

/**
 * Elementor place search.
 *
 * Elementor widget that displays an eye-catching headlines.
 *
 * @since 1.0.0
 */
class Widget_Place_Search extends Widget_Base {

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
		return 'place-search';
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
		return __( 'Place Search', 'golo-framework' );
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
		return 'golo-badge eicon-search';
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
		return [ 'search', 'ajax', 'golo' ];
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
			'section_title',
			[
				'label' => __( 'Title', 'golo-framework' ),
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
					'layout-03' => 'Layout 03',
				],
				'default' => 'layout-01',
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'golo-framework' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your title', 'golo-framework' ),
				'default' => __( 'Add Your Heading Text Here', 'golo-framework' ),
			]
		);

		$this->add_control(
			'show_cities',
			[
				'label' => __( 'Show Popular City', 'golo-framework' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'header_size',
			[
				'label' => __( 'HTML Tag', 'golo-framework' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h3',
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => __( 'Alignment', 'golo-framework' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'golo-framework' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'golo-framework' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'golo-framework' ),
						'icon' => 'fa fa-align-right',
					],
					'justify' => [
						'title' => __( 'Justified', 'golo-framework' ),
						'icon' => 'fa fa-align-justify',
					],
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'view',
			[
				'label'   => __( 'View', 'golo-framework' ),
				'type'    => Controls_Manager::HIDDEN,
				'default' => 'traditional',
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

		$this->add_responsive_control(
			'min_width',
			[
				'label' => __( 'Min Width', 'golo-framework' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 2000,
					],
				],
				'default'   => [
					'size' => 300,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-place-search .area-search' => 'min-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'max_width',
			[
				'label' => __( 'Max Width', 'golo-framework' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 2000,
					],
				],
				'default'   => [
					'size' => 520,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-place-search' => 'max-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'max_height',
			[
				'label' => __( 'Max Height', 'golo-framework' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 45,
						'max' => 100,
					],
				],
				'default'   => [
					'size' => 62,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-place-search .layout-02 .area-search.form-field' => 'max-height: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .elementor-place-search .layout-02 .icon-search' => 'height: calc({{SIZE}}{{UNIT}} - 10px); width: calc({{SIZE}}{{UNIT}} - 10px)',
					'{{WRAPPER}} .elementor-place-search .layout-03 .area-search.form-field' => 'max-height: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .elementor-place-search .layout-03 .icon-search' => 'height: calc({{SIZE}}{{UNIT}} - 10px); width: calc({{SIZE}}{{UNIT}} - 10px)',
				],
				'condition' => [
					'layout' => 'layout-02',
				],
			]
		);

		$this->add_control(
			'title_spacing',
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
					'{{WRAPPER}} .elementor-heading-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'search_align',
			[
				'label' => __( 'Align Search', 'golo-framework' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'left' => 'Left',
					'center' => 'Center',
					'right' => 'Right',
				],
				'default' => 'left',
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
					'{{WRAPPER}} .elementor-heading-title,{{WRAPPER}} .popular-city span,{{WRAPPER}} .popular-city .list-city a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'typography',
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .elementor-heading-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'text_shadow',
				'selector' => '{{WRAPPER}} .elementor-heading-title',
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

		$post_type = 'place';

		$layout = $settings['layout'];

		?>
			<div class="elementor-place-search <?php echo esc_attr($settings['search_align']); ?>">
				<?php 
					$title_html = '';
					if( $title ){
						$title_html .= sprintf( '<%1$s %2$s>%3$s</%1$s>', $settings['header_size'], $this->get_render_attribute_string( 'title' ), $title );
					}
					echo $title_html;
				?>

				<div class="block-search search-input golo-ajax-search <?php echo esc_attr($layout); ?>">
					<form action="<?php echo esc_url( home_url( '/' ) ); ?>" class="search-form" method="get">
						<div class="area-search form-field">
							<?php if( $layout == 'layout-01' ) : ?>

								<div class="icon-search">
									<i class="la la-search large"></i>
								</div>
								
								<div class="form-field input-field">
									<input name="s" class="input-search" type="text" value="<?php echo get_search_query(); ?>" placeholder="<?php esc_attr_e( 'Type a city or location', 'golo-framework' ); ?>" autocomplete="off" />
									<input type="hidden" name="post_type" class="post-type" value="<?php echo esc_attr( $post_type ); ?>"/>

									<div class="search-result area-result"></div>

									<div class="golo-loading-effect"><span class="golo-dual-ring"></span></div>

									<?php 
										$place_categories = get_categories(array(
				                            'taxonomy'   => 'place-categories',
				                            'hide_empty' => 1,
				                            'orderby'    => 'name',
				                            'order'      => 'ASC'
				                        ));
									?>
									<?php if($place_categories) : ?>
									<div class="list-categories">
										<ul>
											<?php
											$image_src = GOLO_PLUGIN_URL . 'assets/images/no-image.jpg';
											foreach ($place_categories as $cate) {
												$cate_id   		= $cate->term_id;
								                $cate_name 		= $cate->name;
								                $cate_slug 		= $cate->slug;
								                $cate      		= get_term_by( 'id', $cate_id, 'place-categories');
								                $cate_icon 		= get_term_meta( $cate_id, 'place_categories_icon_marker', true );
								                $link      		= home_url('/') . '?s=&post_type=place&category=' . $cate_slug . '&caid=' . $cate_id;

								                $cate_icon_url = '';
								                if (!$cate_icon) {
								                	$cate_icon_url = $image_src;
								                }
				                            ?>
				                                <li>
				                                    <a class="entry-category" href="<?php echo esc_url($link); ?>" data-caid="<?php echo esc_html($cate_id) ?>">
								                        <img src="<?php echo esc_url($cate_icon_url) ?>" alt="<?php echo esc_attr($cate_name); ?>">
								                        <span><?php echo esc_html($cate_name); ?></span>
								                    </a>
				                                </li>
				                            <?php } ?>
										</ul>
									</div>
									<?php endif; ?>
								</div>

							<?php endif; ?>

							<?php if( $layout == 'layout-02' ) : ?>

								<div class="form-field location-field">
									<label class="location-area" for="find_city">
										<span><?php esc_html_e('Where', 'golo-framework'); ?></span>
										<input name="place_location" id="find_city" class="location-search" type="text" placeholder="<?php esc_attr_e( 'Your city', 'golo-framework' ); ?>" autocomplete="off" />
										<input type="hidden" name="ciid">
									</label>

									<div class="location-result area-result"></div>

									<?php 
										$place_cities = get_categories(array(
				                            'taxonomy'   => 'place-city',
				                            'hide_empty' => 1,
				                            'orderby'    => 'name',
				                            'order'      => 'ASC'
				                        ));
									?>
									<?php if($place_cities) : ?>
									<div class="location-result focus-result">
										<ul>
											<?php
											foreach ($place_cities as $cate) {
												$cate_id   = $cate->term_id;
								                $cate_name = $cate->name;
								                $cate_slug = $cate->slug;
								                $cate      = get_term_by( 'id', $cate_id, 'place-city');
				                            ?>
				                                <li>
				                                    <a class="entry-city" href="<?php echo esc_url($link); ?>" data-ciid="<?php echo esc_html($cate_id) ?>">
								                        <?php echo esc_html($cate_name); ?>
								                    </a>
				                                </li>
				                            <?php } ?>
										</ul>
									</div>
									<?php endif; ?>
								</div>

								<div class="form-field input-field">
									<label class="input-area" for="find_input">
										<span><?php esc_html_e('Find', 'golo-framework'); ?></span>
										<input id="find_input" name="category" class="input-search" type="text" placeholder="<?php esc_attr_e( 'Ex: fastfood, beer', 'golo-framework' ); ?>" autocomplete="off" />
										<input type="hidden" name="caid">
										<div class="golo-loading-effect"><span class="golo-dual-ring"></span></div>
									</label>
									<div class="search-result area-result"></div>

									<?php 
										$place_categories = get_categories(array(
				                            'taxonomy'   => 'place-categories',
				                            'hide_empty' => 1,
				                            'orderby'    => 'name',
				                            'order'      => 'ASC'
				                        ));
									?>
									<?php if($place_categories) : ?>
									<div class="list-categories focus-result">
										<ul>
											<?php
											$image_src = GOLO_PLUGIN_URL . 'assets/images/no-image.jpg';
											$default_image = golo_get_option('default_place_image','');
											foreach ($place_categories as $cate) {
												$cate_id   = $cate->term_id;
								                $cate_name = $cate->name;
								                $cate_slug = $cate->slug;
								                $cate      = get_term_by( 'id', $cate_id, 'place-categories');
								                $cate_icon = get_term_meta( $cate_id, 'place_categories_icon_marker', true );
								                if ($cate_icon) {
								                	$cate_icon_url = $cate_icon['url'];
								                } else {
								                    if($default_image != '')
                                        			    {
                                        			        if(is_array($default_image) && $default_image['url'] != '')
                                        			        {
                                        			            $cate_icon_url = $default_image['url'];
                                        			        }
                                        			    } else {
                                        			        $cate_icon_url = $image_src;
                                        			    }
								                }
				                            ?>
				                                <li>
				                                    <a class="entry-category" href="<?php echo esc_url($link); ?>" data-caid="<?php echo esc_html($cate_id) ?>">
								                        <img src="<?php echo esc_url($cate_icon_url) ?>" alt="<?php echo esc_attr($cate_name); ?>">
								                        <span><?php echo esc_html($cate_name); ?></span>
								                    </a>
				                                </li>
				                            <?php } ?>
										</ul>
									</div>
									<?php endif; ?>									
									
									<input type="hidden" name="s">
									<button type="submit" class="icon-search">
										<i class="la la-search large"></i>
									</button>
								</div>

								<input type="hidden" name="post_type" class="post-type" value="<?php echo esc_attr( $post_type ); ?>"/>

							<?php endif; ?>

							<?php if( $layout == 'layout-03' ) : ?>

								<div class="form-field location-field">
									<label class="location-area" for="find_city">
										<span><?php esc_html_e('Where', 'golo-framework'); ?></span>
										<input name="place_location" id="find_city" class="location-search" type="text" placeholder="<?php esc_attr_e( 'Your city', 'golo-framework' ); ?>" autocomplete="off" />
										<input type="hidden" name="ciid">
									</label>

									<div class="location-result"></div>

									<?php 
										$place_cities = get_categories(array(
				                            'taxonomy'   => 'place-city',
				                            'hide_empty' => 1,
				                            'orderby'    => 'name',
				                            'order'      => 'ASC'
				                        ));
									?>
									<?php if($place_cities) : ?>
									<div class="location-result focus-result">
										<ul>
											<?php
											foreach ($place_cities as $cate) {
												$cate_id   = $cate->term_id;
								                $cate_name = $cate->name;
								                $cate_slug = $cate->slug;
								                $cate      = get_term_by( 'id', $cate_id, 'place-city');
				                            ?>
				                                <li>
				                                    <a class="entry-city" href="<?php echo esc_url($link); ?>" data-ciid="<?php echo esc_html($cate_id) ?>">
								                        <?php echo esc_html($cate_name); ?>
								                    </a>
				                                </li>
				                            <?php } ?>
										</ul>
									</div>
									<?php endif; ?>
								</div>

								<div class="form-field type-field">
									<label class="type-area" for="find_type">
										<span><?php esc_html_e('Type', 'golo-framework'); ?></span>
										<input name="place_type" id="find_type" class="type-search" type="text" placeholder="<?php esc_attr_e( 'Place type', 'golo-framework' ); ?>" autocomplete="off" />
										<input type="hidden" name="tyid">
									</label>

									<div class="type-result"></div>

									<?php 
										$place_cities = get_categories(array(
				                            'taxonomy'   => 'place-type',
				                            'hide_empty' => 1,
				                            'orderby'    => 'name',
				                            'order'      => 'ASC'
				                        ));
									?>
									<?php if($place_cities) : ?>
									<div class="type-result focus-result">
										<ul>
											<?php
											foreach ($place_cities as $cate) {
												$cate_id   = $cate->term_id;
								                $cate_name = $cate->name;
								                $cate_slug = $cate->slug;
								                $cate      = get_term_by( 'id', $cate_id, 'place-type');
				                            ?>
				                                <li>
				                                    <a class="entry-city" href="<?php echo esc_url($link); ?>" data-tyid="<?php echo esc_html($cate_id) ?>">
								                        <?php echo esc_html($cate_name); ?>
								                    </a>
				                                </li>
				                            <?php } ?>
										</ul>
									</div>
									<?php endif; ?>
									<input type="hidden" name="s">
									<button type="submit" class="icon-search">
										<i class="la la-search large"></i>
									</button>
								</div>

								<input type="hidden" name="post_type" class="post-type" value="<?php echo esc_attr( $post_type ); ?>"/>

							<?php endif; ?>
						</div>
					</form>
				</div>

				<?php if( $settings['show_cities'] == 'yes' ) { ?>
				<div class="popular-city">
					<span><?php esc_html_e('Popular:', 'golo-framework'); ?></span>
					<div class="list-city">
					<?php 
					$taxonomy_terms = get_categories(
			            array(
							'taxonomy'   => 'place-city',
							'order'      => 'DESC',
							'orderby'    => 'rand',
							'hide_empty' => false,
			            )
			        );
			        shuffle($taxonomy_terms);

			        if (!empty($taxonomy_terms)) {
			            foreach ($taxonomy_terms as $index => $term) {
			            	if( $index < 3 ) {
			            		$term_link = get_term_link($term);
			                ?>
								<a href="<?php echo esc_url($term_link); ?>"><?php echo esc_html($term->name); ?></a>
			                <?php
			            	}
			            }
			        }
					?>
					</div>
				</div>
				<?php } ?>
				
			</div>
		<?php
	}
}
