<?php

namespace Elementor;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

Plugin::instance()->widgets_manager->register_widget_type( new Widget_User_Manager() );

/**
 * Elementor user manager.
 *
 * Elementor widget that displays an eye-catching headlines.
 *
 * @since 1.0.0
 */
class Widget_User_Manager extends Widget_Base {

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
		return 'user-manager';
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
		return __( 'User Manager', 'golo-framework' );
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
		return 'golo-badge eicon-lock-user';
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
		return [ 'user', 'login', 'register', 'golo-framework' ];
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
				'label' => __( 'Typography', 'golo-framework' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'color',
			[
				'label' => __( 'Text Color', 'golo-framework' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Color::get_type(),
					'value' => Color::COLOR_1,
				],
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'{{WRAPPER}} .account a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'scheme' => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .account',
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
		?>
			<?php 
			if(is_user_logged_in()) {
				$current_user = wp_get_current_user();
				$user_name    = $current_user->display_name;
				$user_link    = get_edit_user_link($current_user->ID);
				$avatar_url   = get_avatar_url($current_user->ID);
				$author_avatar_image_url = get_the_author_meta('author_avatar_image_url', $current_user->ID);
				$author_avatar_image_id  = get_the_author_meta('author_avatar_image_id', $current_user->ID);
				if( !empty($author_avatar_image_url) ){
		            $avatar_url = $author_avatar_image_url;
		        }
		        
			?>
				<div class="account logged-in">
					<div class="user-show">
						<a class="avatar" href="<?php echo esc_url($user_link); ?>"><img src="<?php echo esc_url($avatar_url); ?>" title="<?php echo esc_attr($user_name); ?>" alt="<?php echo esc_attr($user_name); ?>" ></a>
						<a class="username" href="<?php echo esc_url($user_link); ?>" title="<?php echo esc_attr($user_name); ?>"><?php echo esc_html($user_name); ?></a>
					</div>
					<div class="user-control">
						<div class="inner-control">
							<ul>
								<li class="<?php if(is_page('my-profile')) : echo esc_attr('active');endif; ?>"><a href="<?php echo golo_get_permalink('my_profile'); ?>"><?php esc_html_e('Profile', 'golo-framework'); ?></a></li>
								<li class="<?php if(is_page('my-places')) : echo esc_attr('active');endif; ?>"><a href="<?php echo golo_get_permalink('my_places'); ?>"><?php esc_html_e('My Places', 'golo-framework'); ?></a></li>
								<li class="<?php if(is_page('my-wishlist')) : echo esc_attr('active');endif; ?>"><a href="<?php echo golo_get_permalink('my_wishlist'); ?>"><?php esc_html_e('My Wishlist', 'golo-framework'); ?></a></li>
								<li class="<?php if(is_page('my-booking')) : echo esc_attr('active');endif; ?>"><a href="<?php echo golo_get_permalink('my_booking'); ?>"><?php esc_html_e('My Booking', 'golo-framework'); ?></a></li>
							</ul>
							<div class="logout">
								<a href="<?php echo wp_logout_url(home_url()); ?>">
									<span><?php esc_html_e('Logout','golo-framework'); ?></span>
									<i class="fal fa-sign-out-alt"></i>
								</a>
							</div>
						</div>
					</div>
				</div>
			<?php } else { ?>
				<div class="account logged-out">
					<a href="#popup-form" class="btn-login"><?php esc_html_e('Login','golo-framework'); ?></a>
					<?php 
						$show_register = get_option_customize('show_register');
						if ($show_register) {
							?>                                             
								<span><?php esc_html_e('or', 'golo-framework'); ?></span>
								<a href="#popup-form" class="btn-register"><?php esc_html_e('Sign Up Now','golo-framework'); ?></a>
							<?php
						}
					?>
				</div>
			<?php }	?>
		<?php
	}
}
