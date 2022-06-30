<?php
/**
 * Template part for displaying blog list.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 */

$image_size      = Golo_Helper::get_setting('blog_image_size');
$excerpt         = get_the_excerpt();
$numbers_excerpt = 25;
$attach_id       = get_post_thumbnail_id($post->ID);
$thumb_url       = Golo_Helper::golo_image_resize($attach_id, $image_size);
$no_image_src    = GOLO_IMAGES . 'no-image.jpg';
$default_image   = golo_get_option('default_place_image','');

if( $thumb_url ) {
    $cur_url = $thumb_url;
} else {
    if($default_image != '') {
        if(is_array($default_image) && $default_image['url'] != '')
        {
            $cur_url = $default_image['url'];
        }
    } else {
        $cur_url = $no_image_src;
    }
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="inner-post-wrap">

		<!-- post thumbnail -->

		<div class="entry-post-thumbnail">
			<a href="<?php the_permalink(); ?>">
				<img src="<?php echo esc_url( $cur_url ); ?>" alt="<?php the_title_attribute(); ?>">
			</a>
		</div>

		<div class="entry-post-detail">
			
			<!-- list categories -->
			<?php echo get_the_category_list(); ?>
			
			<!-- post title -->
			<div class="entry-title">
				<?php
				the_title( '<h3 class="post-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' );
				?>

				<?php if( is_sticky($post->ID) ) { ?>
		            <span class="is-sticky"><?php esc_html_e('Featured', 'golo'); ?></span>
		        <?php } ?>
			</div>
			
	        <!-- post meta -->
			<?php 
			if ( 'post' === get_post_type() ) :
				get_template_part( 'templates/post/content', 'meta' );
			endif; 
			?>
			
			<!-- post excerpt -->
			<?php if( !empty($excerpt) ){ ?>
			<div class="post-excerpt">
				<p><?php echo get_the_excerpt($post->ID); ?></p>
			</div>
			<?php } ?>

			<!-- button readmore -->
			<div class="btn-readmore">
				<a href="<?php the_permalink(); ?>">
					<?php esc_html_e('Read More', 'golo'); ?>
				</a>
			</div>
			
		</div>
	</div>
</article><!-- #post-## -->
