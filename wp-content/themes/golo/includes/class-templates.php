<?php 

if ( !defined('ABSPATH') ) {
	exit;
}

if ( !class_exists('Golo_Templates') ) {
	/**
     *  Class Golo_Templates
     */
	class Golo_Templates {

		public static function site_logo( $type = '' ) {

			$logo        = '';
			$logo_retina = '';

			if( $type == 'dark') {

				$logo_dark        = Golo_Helper::get_setting('logo_dark');
				$logo_dark_retina = Golo_Helper::get_setting('logo_dark_retina');

				if( $logo_dark ) {
					$logo = $logo_dark;
				}
				
				if( $logo_dark_retina ) {
					$logo_retina = $logo_dark_retina;
				}
			}

			if( $type == 'light') {

				$logo_light        = Golo_Helper::get_setting('logo_light');
				$logo_light_retina = Golo_Helper::get_setting('logo_light_retina');

				if( $logo_light ) {
					$logo = $logo_light;
				}
				
				if( $logo_light_retina ) {
					$logo_retina = $logo_light_retina;
				}
			}

			$site_name = get_bloginfo('name', 'display');

			ob_start();

			?>
				<?php if ( !empty($logo) ) : ?>
	                <div class="site-logo"><a href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr($site_name); ?>"><img src="<?php echo esc_url($logo); ?>" data-retina="<?php echo esc_attr($logo_retina); ?>" alt="<?php echo esc_attr($site_name); ?>"></a></div>
	            <?php else : ?>
	            	<div class="site-logo">
		                <?php $blog_info = get_bloginfo( 'name' ); ?>
		                <?php if ( !empty($blog_info) ) : ?>
		                    <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr($site_name); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		                    <p><?php bloginfo( 'description' ); ?></p>
		                <?php endif; ?>
		            </div>
	            <?php endif; ?>
			<?php

			return ob_get_clean();
		}

		public static function main_menu() {

			$show_main_menu = Golo_Helper::get_setting('show_main_menu');
			
			if( !$show_main_menu ) {
				return;
			}

			ob_start();
			?>
				<div class="site-menu main-menu desktop-menu default-menu">
					<?php
						$args = array();

						$defaults = array(
							'menu_class'     => 'menu',
							'container'      => '',
							'theme_location' => 'main_menu',
			            );

			            $args = wp_parse_args( $args, $defaults );

						if ( has_nav_menu( 'main_menu' ) && class_exists( 'Golo_Walker_Nav_Menu' ) ) {
				            $args['walker'] = new Golo_Walker_Nav_Menu;
				        }

				        if ( has_nav_menu( 'main_menu' ) ) {
				        	wp_nav_menu( $args );
				        }
					?>
				</div>
			<?php
			return ob_get_clean();
		}

		public static function site_menu() {

			if( !class_exists('Golo_Framework') ) {
		        return;
		    }

			ob_start();
			?>
				<div class="site-menu desktop-menu default-menu">
					<?php
						$args = array();

						$defaults = array(
							'menu_class'     => 'menu',
							'container'      => '',
							'theme_location' => 'primary',
			            );

			            $args = wp_parse_args( $args, $defaults );

						if ( has_nav_menu( 'primary' ) && class_exists( 'Golo_Walker_Nav_Menu' ) ) {
				            $args['walker'] = new Golo_Walker_Nav_Menu;
				        }

				        wp_nav_menu( $args );
					?>
				</div>
			<?php
			return ob_get_clean();
		}

		public static function mobile_menu() {
			$show_destinations = Golo_Helper::get_setting('show_destinations');

			ob_start();
			?>
				<div class="bg-overlay"></div>

				<div class="site-menu area-menu mobile-menu default-menu">
					
					<div class="inner-menu custom-scrollbar">

						<a href="#" class="btn-close">
							<i class="la la-times"></i>
						</a>

						<?php if( !class_exists('Golo_Framework') ) : ?>
							<?php echo self::site_logo('dark'); ?>
						<?php endif; ?>
						
						<?php if( class_exists('Golo_Framework') ) : ?>
						<div class="top-mb-menu">
							<?php echo self::account(); ?>
						</div>
						<?php endif; ?>
						
						<?php if( class_exists('Golo_Framework') && $show_destinations ) : ?>
						<div class="mb-destinations">
							<?php echo self::dropdown_categories('place-city', __('Destinations', 'golo')); ?>
						</div>
						<?php endif; ?>

						<?php
							$args = array(
								'menu_class'     => 'menu',
								'container'      => '',
								'theme_location' => 'mobile_menu',
				            );
							wp_nav_menu( $args );
						?>

						<?php echo self::add_place(); ?>
					</div>
				</div>
			<?php
			return ob_get_clean();
		}

		public static function canvas_menu() {
			$show_canvas_menu = Golo_Helper::get_setting('show_canvas_menu');

			ob_start();
			?>
				<div class="mb-menu canvas-menu canvas-left <?php if(!$show_canvas_menu){ echo 'd-hidden'; } ?>">
					<a href="#" class="icon-menu">
						<i class="la la-bars"></i>
					</a>

					<?php echo self::mobile_menu(); ?>
				</div>
			<?php
			return ob_get_clean();
		}

		public static function block_search( $search_type = 'input', $ajax = false ) {
			$ajax_class = '';
			if( $ajax ) {
				$ajax_class = 'golo-ajax-search';
			}

			$show_search_form = Golo_Helper::get_setting('show_search_form');
			$hidden_search_form_homepage = Golo_Helper::get_setting('hidden_search_form_homepage');

			if( !$show_search_form ) {
				return;
			}

			if( $hidden_search_form_homepage && is_front_page() ) {
				return;
			}

			ob_start();
			?>
				<div class="block-search search-<?php echo esc_attr($search_type); ?> <?php echo esc_attr($ajax_class); ?>">
					<div class="icon-search">
						<i class="la la-search large"></i>
					</div>
					
					<?php if( $search_type == 'input' ) : ?>
						<?php echo self::search_form($search_type); ?>
					<?php endif; ?>
				</div>				
			<?php
			return ob_get_clean();
		}

		public static function search_form( $search_type = 'input' ) {

			$ajax_search = true;

			$default_image 		= Golo_Helper::golo_get_option('default_place_image','');

			$layout = Golo_Helper::get_setting('layout_search');

			$classes = array( 'search-form', 'block-search', $layout );

			if ( $ajax_search ) {
				$classes[] = ' ajax-search-form';
			}

			$post_type   = 'post';
			$place_holder = esc_html__( 'Search posts...', 'golo' );

			if ( class_exists('WooCommerce') ) {
				$post_type   = 'product';
				$place_holder = esc_html__( 'Search products...', 'golo' );
			}

			if ( class_exists('Golo_Framework') ) {
				$post_type   = 'place';
				$place_holder = esc_html__( 'Search places, cities', 'golo' );
			}

			ob_start();
			?>
				<?php if( $search_type == 'input' ) : ?>
				<div class="<?php echo join( ' ', $classes ); ?>">
					<form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" class="search-form">
						<div class="area-search form-field">
							<?php if( $layout == 'layout-01' ) : ?>

								<div class="icon-search">
									<i class="la la-search large"></i>
								</div>
								
								<div class="icon-clear">
									<a href="#">
										<i class="las la-times-circle large"></i>
									</a>
								</div>

								<div class="form-field input-field">
									<input name="s" class="input-search" type="text" value="<?php echo get_search_query(); ?>" placeholder="<?php echo esc_attr( $place_holder ); ?>" autocomplete="off" />
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
											$image_src = GOLO_IMAGES . 'no-image.jpg';
											foreach ($place_categories as $cate) {
												$cate_id   = $cate->term_id;
								                $cate_name = $cate->name;
								                $cate_slug = $cate->slug;
								                $cate      = get_term_by( 'id', $cate_id, 'place-categories');
								                $cate_icon = get_term_meta( $cate_id, 'place_categories_icon_marker', true );
								                $link      = home_url('/') . '?s=&post_type=place&category=' . $cate_slug . '&caid=' . $cate_id;
								                $cate_icon_url = '';
								                if (!$cate_icon) {
								                	$cate_icon_url = $image_src;
								                }
				                            ?>
				                                <li>
				                                	<?php if( $cate_icon_url ) : ?>
				                                    <a class="entry-category" href="<?php echo esc_url($link); ?>" data-caid="<?php echo esc_html($cate_id) ?>">
								                        <img src="<?php echo esc_url($cate_icon_url) ?>" alt="<?php echo esc_attr($cate_name); ?>">
								                        <span><?php echo esc_html($cate_name); ?></span>
								                    </a>
								                	<?php endif; ?>
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
									<?php 
									$location = isset($_GET['location']) ? Golo_Helper::golo_clean(wp_unslash($_GET['location'])) : '';
									?>
									<label class="location-area" for="find_city">
										<span><?php esc_html_e('Where', 'golo'); ?></span>
										<input name="place_location" id="find_city" value="<?php echo esc_attr($location); ?>" class="location-search" type="text" placeholder="<?php esc_attr_e( 'Your city', 'golo' ); ?>" autocomplete="off" />
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
								                $link      = home_url('/') . '?s=&post_type=place&place_location=' . $cate_slug . '&ciid=' . $cate_id;
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
									<?php 
									$place_type = isset($_GET['place_type']) ? Golo_Helper::golo_clean(wp_unslash($_GET['place_type'])) : '';
									?>
									<label class="type-area" for="find_type">
										<span><?php esc_html_e('Type', 'golo'); ?></span>
										<input name="place_type" id="find_type" value="<?php echo esc_attr($place_type); ?>" class="type-search" type="text" placeholder="<?php esc_attr_e( 'Your type', 'golo' ); ?>" autocomplete="off" />
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
								                $link      = home_url('/') . '?s=&post_type=place&place_type=' . $cate_slug . '&tyid=' . $cate_id;
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
				<?php endif; ?>
			<?php
			return ob_get_clean();
		}

		public static function dropdown_categories($cate, $text = 'Categories') {

			$show_destinations = Golo_Helper::get_setting('show_destinations');
			
			if( !taxonomy_exists($cate) || !$show_destinations ) {
				return;
			}

			$terms = get_terms( $cate,
				array(
					'hide_empty'   => false,
					'hierarchical' => true,
				) 
			);

			$current_city = isset( $_GET['city'] ) ? Golo_Helper::golo_clean(wp_unslash($_GET['city'])) : '';

			if( get_query_var('taxonomy') == 'place-city' ) {
				$current_city = get_query_var('term');
			}

			if( !empty($current_city) ){
				$current_term = get_term_by('slug', $current_city, 'place-city');
			}

			if( !empty($current_term) ) {
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
			ob_start();

			if( !empty($terms) ) :
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
									<li class="<?php if( !empty($current_term) ) { if( $current_term->slug == $term->slug ) : echo esc_attr('active');endif; } ?>"><a href="<?php echo esc_url($term_link); ?>"><?php echo esc_html($term->name); ?></a></li>
								<?php
							} 
						?>
					</ul>
				</div>
			<?php
			endif;
			return ob_get_clean();
		}

		public static function post_categories() {

			ob_start();

			$count_posts  = wp_count_posts();
			$category_id  = '';
			$blog_sidebar = Golo_Helper::get_setting('blog_sidebar');
			$sidebar      = !empty($_GET['sidebar']) ? Golo_Helper::golo_clean(wp_unslash($_GET['sidebar'])) : $blog_sidebar;
			
			if( is_category() ) 
			{
				$cate  		 = get_category( get_query_var( 'cat' ) );
				$category_id = $cate->cat_ID;
			}
			$categories  = get_categories( array(
				'orderby'      => 'count',
				'order'        => 'DESC',
				'number' 	   => 5,
				'parent'       => 0,
				'hide_empty'   => true,
				'hierarchical' => true,
			) );

			if( $sidebar == 'no-sidebar' && is_active_sidebar('sidebar') ) {
				if( $categories ) :
				?>
					<div class="golo-categories">
						<ul class="list-categories">
							<li class="<?php if( !is_front_page() && is_home() ) : echo esc_attr('active');endif; ?>">
								<a href="<?php echo get_post_type_archive_link('post'); ?>">
									<span class="entry-name"><?php esc_html_e('All ', 'golo'); ?></span>
									<span class="count"><?php echo sprintf( esc_html__( '(%s)', 'golo' ), $count_posts->publish ); ?></span>
								</a>
							</li>
							<?php 
							foreach( $categories as $category ) {
								$category_link = get_category_link( $category->term_id );
							?>
								<li class="<?php if( $category_id == $category->term_id ) : echo esc_attr('active');endif; ?>">
									<a href="<?php echo esc_url($category_link); ?>">
										<span class="entry-name"><?php echo esc_html($category->name); ?></span>
										<span class="count"><?php echo sprintf( esc_html__( '(%s)', 'golo' ), $category->category_count ); ?></span>
									</a>
								</li>
							<?php } ?>
						</ul>
					</div>
				<?php endif;
			}else{
				?>
					<div class="count-posts">
						<span><?php printf( _n( '%s article', '%s articles', $count_posts->publish, 'golo' ), esc_html( $count_posts->publish ) ); ?></span>
					</div>
				<?php
			}
			return ob_get_clean();
		}
		
		public static function account() {

			$show_login = Golo_Helper::get_setting('show_login');
			$show_register = Golo_Helper::get_setting('show_register');

			if( !class_exists('Golo_Framework') || (!$show_login && !$show_register) ) {
		        return;
		    }

		    $show_dashboard   = Golo_Helper::golo_get_option('show_dashboard', 1);
			$show_profile     = Golo_Helper::golo_get_option('show_profile', 1);
			$show_my_places   = Golo_Helper::golo_get_option('show_my_places', 1);
			$show_my_booking  = Golo_Helper::golo_get_option('show_my_booking', 1);
			$show_booking     = Golo_Helper::golo_get_option('show_booking', 1);
			$show_my_wishlist = Golo_Helper::golo_get_option('show_my_wishlist', 1);
			$show_add_new_place = Golo_Helper::golo_get_option('show_add_new_place', 1);
			
			ob_start();
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
			        $avatar_url = golo_image_resize_url($avatar_url, 30, 30, true);

			        
				?>
					<div class="account logged-in">
						<?php if( $avatar_url['url'] ) : ?>
						<div class="user-show">
							<a class="avatar" href="<?php echo golo_get_permalink('dashboard'); ?>">
								<img src="<?php echo esc_url($avatar_url['url']); ?>" title="<?php echo esc_attr($user_name); ?>" alt="<?php echo esc_attr($user_name); ?>" >
								<span><?php echo esc_html($user_name); ?></span>
							</a>
						</div>
						<?php endif; ?>

						<div class="user-control">
							<div class="inner-control">
								<?php if( $show_dashboard || $show_profile || $show_my_places || $show_my_booking || $show_booking || $show_my_wishlist || $show_add_new_place ) : ?>
								<ul>
									<?php if( $show_dashboard && !in_array( 'customer', (array) $current_user->roles ) ) : ?><li class="<?php if(is_page('dashboard')) : echo esc_attr('active');endif; ?>"><a href="<?php echo golo_get_permalink('dashboard'); ?>"><?php esc_html_e('Dashboard', 'golo'); ?></a></li><?php endif; ?>
									<?php if( $show_profile ) : ?><li class="<?php if(is_page('my-profile')) : echo esc_attr('active');endif; ?>"><a href="<?php echo golo_get_permalink('my_profile'); ?>"><?php esc_html_e('Profile', 'golo'); ?></a></li><?php endif; ?>
									<?php if( $show_my_places && !in_array( 'customer', (array) $current_user->roles ) ) : ?><li class="<?php if(is_page('my-places')) : echo esc_attr('active');endif; ?>"><a href="<?php echo golo_get_permalink('my_places'); ?>"><?php esc_html_e('My Places', 'golo'); ?></a></li><?php endif; ?>
									<?php if( $show_my_booking && !in_array( 'customer', (array) $current_user->roles ) ) : ?><li class="<?php if(is_page('bookings')) : echo esc_attr('active');endif; ?>"><a href="<?php echo golo_get_permalink('bookings'); ?>"><?php esc_html_e('Bookings', 'golo'); ?></a></li><?php endif; ?>
									<?php if( $show_my_wishlist ) : ?><li class="<?php if(is_page('my-wishlist')) : echo esc_attr('active');endif; ?>"><a href="<?php echo golo_get_permalink('my_wishlist'); ?>"><?php esc_html_e('My Wishlist', 'golo'); ?></a></li><?php endif; ?>
									<?php if( $show_booking ) : ?><li class="<?php if(is_page('my-booking')) : echo esc_attr('active');endif; ?>"><a href="<?php echo golo_get_permalink('my_booking'); ?>"><?php esc_html_e('My Bookings', 'golo'); ?></a></li><?php endif; ?>
									<?php if( $show_add_new_place ) : ?><li class="<?php if(is_page('new-place')) : echo esc_attr('active');endif; ?>"><a href="<?php echo golo_get_permalink('submit_place'); ?>"><?php esc_html_e('New Place', 'golo'); ?></a></li><?php endif; ?>
								</ul>
								<?php endif; ?>
								
								<div class="logout">
									<a href="<?php echo wp_logout_url(home_url()); ?>">
										<span><?php esc_html_e('Logout','golo'); ?></span>
										<i class="fal fa-sign-out-alt"></i>
									</a>
								</div>
							</div>
						</div>
					</div>
				<?php } else { ?>
					<div class="account logged-out">
						<?php if( $show_login ) : ?>
							<a href="#popup-form" class="btn-login"><?php esc_html_e('Login','golo'); ?></a>
						<?php endif ?>

						<?php if( $show_register ) : ?>
							<a href="#popup-form" class="btn-register"><?php esc_html_e('Sign Up','golo'); ?></a>
						<?php endif ?>
					</div>
				<?php }	?>
			<?php
			return ob_get_clean();
		}

		public static function notifications() {

			$show_login_register = Golo_Helper::get_setting('show_login_register');

			if( !class_exists('Golo_Framework') || !$show_login_register ) {
		        return;
		    }

			ob_start();
			?>
				<?php 
				if(is_user_logged_in()) {
				?>
					<div class="user-notifications">
						<div class="entry-show">
							<i class="la la-bell large"></i>
							<span class="count">8</span>
						</div>
						
						<div class="entry-list-notifications">
							
						</div>
					</div>
				<?php } ?>
			<?php
			return ob_get_clean();
		}

		public static function wc_cart() {

			$show_icon_cart = Golo_Helper::get_setting('show_icon_cart');

			if ( !class_exists('WooCommerce') || !$show_icon_cart ) {
				return;
			}

			ob_start();
			?>
				<div class="minicart canvas-menu canvas-right">
					<a href="<?php echo esc_url( get_permalink( wc_get_page_id('cart') ) ); ?>" class="icon-menu toggle" aria-label="<?php esc_attr_e('Shopping Cart', 'golo') ?>">
						<i class="la la-shopping-cart"></i>
						<span class="cart-count">(<span><?php echo WC()->cart->cart_contents_count; ?></span>)</span>
					</a>

					<div class="bg-overlay"></div>

					<div class="area-menu">
						
						<div class="inner-menu custom-scrollbar">
							
							<div class="top-mb-menu">

								<a href="#" class="btn-close"><i class="la la-times"></i></a>

								<div class="your-cart">
									<a href="<?php echo esc_url( get_permalink( wc_get_page_id('cart') ) ); ?>">
										<i class="la la-shopping-cart"></i>
										<span><?php esc_html_e('Your cart', 'golo'); ?></span>
										<span class="cart-count">(<span><?php echo WC()->cart->cart_contents_count; ?></span>)</span>
									</a>
								</div>
							</div>
							
							<div class="widget_shopping_cart_content"><?php wc_get_template('cart/mini-cart.php'); ?></div>
						</div>
					</div>
				</div>
			<?php
			return ob_get_clean();
		}

		public static function add_place() {

			$show_add_place_button = Golo_Helper::get_setting('show_add_place_button');

			if ( !class_exists('Golo_Framework') || !$show_add_place_button ) {
				return;
			}

			$enable_login_to_submit = Golo_Helper::golo_get_option('enable_login_to_submit', '1');

			ob_start();
			?>
                <?php if( $enable_login_to_submit == '1' && !is_user_logged_in() ) { ?>
                    <div class="add-place golo-button account logged-out">
                        <a href="#popup-form" class="btn-login">
                        	<i class="la la-plus"></i>
							<?php esc_html_e('Add place', 'golo'); ?>
                        </a>
                    </div>
                <?php }else{ ?>
					<div class="add-place golo-button">
						<a href="<?php echo golo_get_permalink('submit_place'); ?>">
							<i class="la la-plus"></i>
							<?php esc_html_e('Add place', 'golo'); ?>		
						</a>
					</div>
				<?php } ?>
			<?php
			return ob_get_clean();
		}

		public static function page_title() {

			ob_start();
			
			get_template_part( 'templates/page/page-title' );

			return ob_get_clean();
		}

		public static function post_thumbnail() {

			ob_start();
			
			get_template_part( 'templates/post/post-thumbnail' );

			return ob_get_clean();
		}

		/**
		 * Render comments
		 * *******************************************************
		 */
		public static function render_comments($comment, $args, $depth) {
			self::golo_get_template('post/comment', array('comment' => $comment, 'args' => $args, 'depth' => $depth));
		}

		/**
		 * Get template
		 * *******************************************************
		 */
		public static function golo_get_template($slug, $args = array()) {
			if ($args && is_array($args)) {
				extract($args);
			}
			$located = locate_template(array("templates/{$slug}.php"));
			
			if ( !file_exists($located) ) {
				_doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $slug), '1.0');
				return;
			}
			include($located);
		}

		/**
		 * Display navigation to next/previous set of posts when applicable.
		 */
		public static function pagination() {

			global $wp_query, $wp_rewrite;

			// Don't print empty markup if there's only one page.
			if ( $wp_query->max_num_pages < 2 ) {
				return;
			}

			$paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
			$pagenum_link = wp_kses( get_pagenum_link(), Golo_Helper::golo_kses_allowed_html() );
			$query_args   = array();
			$url_parts    = explode( '?', $pagenum_link );

			if ( isset( $url_parts[1] ) ) {
				wp_parse_str( $url_parts[1], $query_args );
			}

			$pagenum_link = esc_url( remove_query_arg( array_keys( $query_args ), $pagenum_link ) );
			$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

			$format = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link,
				'index.php' ) ? 'index.php/' : '';
			$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%',
				'paged' ) : '?paged=%#%';

			// Set up paginated links.
			$links = paginate_links( array(
				'format'    => $format,
				'total'     => $wp_query->max_num_pages,
				'current'   => $paged,
				'add_args'  => array_map( 'urlencode', $query_args ),
				'prev_text' => '<i class="las la-angle-left"></i>',
				'next_text' => '<i class="las la-angle-right"></i>',
				'type'      => 'list',
				'end_size'  => 3,
				'mid_size'  => 3,
			) );

			if ( $links ) {

				?>

				<div class="posts-pagination">
					<?php echo wp_kses($links, Golo_Helper::golo_kses_allowed_html()); ?>
				</div><!-- .pagination -->

				<?php
			}
		}


	}
}