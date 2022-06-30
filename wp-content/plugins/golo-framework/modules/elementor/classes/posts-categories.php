<?php 

class Golo_Posts_Categories extends \ElementorPro\Modules\Posts\Skins\Skin_Base {

	public function get_id() {
		return 'golo-posts-categories';
	}

	public function get_title() {
		return __( 'Has Categories', 'golo-framework' );
	}

	public function get_container_class() {
		if( $this->get_id() == 'golo-posts-categories' ){
			if( $this->get_instance_value( 'masonry' ) ) {
				return 'elementor-posts--skin-classic elementor-posts-masonry elementor-posts--skin-' . $this->get_id();
			}
			return 'elementor-posts--skin-classic elementor-has-item-ratio elementor-posts--skin-' . $this->get_id();
		}
		return 'elementor-posts--skin-' . $this->get_id();
	}

	protected function render_title() {
		if ( ! $this->get_instance_value( 'show_title' ) ) {
			return;
		}

		$tag = $this->get_instance_value( 'title_tag' );
		$categories = get_the_category();

		$image_no_src  = GOLO_PLUGIN_URL . 'assets/images/no-image.jpg';
		
		$default_image = golo_get_option('default_place_image','');
		
		if($default_image != '')
    	    {
    	        if(is_array($default_image) && $default_image['url'] != '')
    	        {
    	            $cur_image = $default_image['url'];
    	        }
    	    } else {
	            $cur_image = $image_no_src;
	    }

		?>

		<?php if ( !has_post_thumbnail() ) : ?>
		<a class="elementor-post__thumbnail__link" href="<?php echo get_the_permalink(); ?>">
				<div class="elementor-post__thumbnail">
					<img src="<?php echo $cur_image; ?>" alt="Thumbnail">
				</div>
		</a>
		<?php endif; ?>

		<?php if($categories) : ?>
		<ul class="post-categories">
			<?php foreach ( $categories as $cat ) : ?>
				<li><a href="<?php echo get_category_link($cat); ?>"><?php echo esc_html($cat->name); ?></a></li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>

		<<?php echo $tag; ?> class="elementor-post__title">
			<a href="<?php echo $this->current_permalink; ?>">
				<?php the_title(); ?>
			</a>
		</<?php echo $tag; ?>>
		<?php
	}
}