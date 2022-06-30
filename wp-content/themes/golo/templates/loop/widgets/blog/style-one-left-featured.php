<?php
if ( ! isset( $settings ) ) {
	$settings = array();
}
$loop_count        = 0;
$left_box_template = $right_box_template = '';
?>
<?php while ( $golo_query->have_posts() ) : $golo_query->the_post(); ?>
	<?php if ( $loop_count === 0 ) : ?>
		<?php ob_start(); ?>
		<div <?php post_class( 'grid-item' ); ?>>
			<div class="post-wrapper golo-box">
				<div class="post-feature post-thumbnail golo-image">
					<a href="<?php the_permalink(); ?>" class="link-secret">
						<?php \Golo_Image::the_post_thumbnail( [
							'size' => '570x330',
						] ); ?>

						<div class="post-overlay-background"></div>
					</a>

					<div class="post-overlay-content">
						<div class="post-overlay-content-inner">
							<div class="post-overlay-info">
								<div class="post-overlay-meta">
									<?php Golo_Post::instance()->the_category( [
										'classes' => 'post-overlay-categories',
									] ); ?>

									<?php Golo_Post::instance()->meta_date_template(); ?>

									<?php Golo_Post::instance()->meta_view_count_template(); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="post-caption">
					<h3 class="post-title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h3>

					<div class="post-excerpt">
						<?php Golo_Templates::excerpt( array(
							'limit' => 20,
							'type'  => 'word',
						) ); ?>
					</div>

					<?php
					$read_more_text = ! empty( $settings['read_more_text'] ) ? $settings['read_more_text'] : esc_html__( 'Read more', 'golo' );

					Golo_Templates::render_button( [
						'style'         => 'bottom-line',
						'text'          => $read_more_text,
						'icon'          => 'far fa-long-arrow-right',
						'icon_align'    => 'right',
						'link'          => [
							'url' => get_the_permalink(),
						],
						'size'          => 'nm',
						'wrapper_class' => 'post-read-more',
					] );
					?>
				</div>
			</div>
		</div>
		<?php $left_box_template .= ob_get_clean(); ?>
	<?php else: ?>
		<?php ob_start(); ?>
		<div <?php post_class( 'grid-item' ); ?>>
			<div class="golo-box">
				<div class="post-thumbnail-wrap">
					<div class="post-feature post-thumbnail golo-image">
						<a href="<?php the_permalink(); ?>" class="link-secret">
							<?php \Golo_Image::the_post_thumbnail( [
								'size' => '200x130',
							] ); ?>
						</a>

						<?php if ( 'yes' === $settings['show_overlay'] ) : ?>
							<?php get_template_part( 'templates/loop/blog/overlay', $settings['overlay_style'] ); ?>
						<?php endif; ?>
					</div>
				</div>
				<?php if ( 'yes' === $settings['show_caption'] ) : ?>
					<div class="post-info">
						<?php get_template_part( 'templates/loop/blog/caption', $settings['caption_style'] ); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php $right_box_template .= ob_get_clean(); ?>
	<?php endif; ?>
	<?php $loop_count++; ?>
<?php endwhile; ?>
<div class="row row-no-gutter">
	<div class="col-md-6 featured-post">
		<?php Golo_Helper::e( $left_box_template ); ?>
	</div>
	<div class="col-md-6 normal-posts">
		<?php Golo_Helper::e( $right_box_template ); ?>
	</div>
</div>
