<!-- post author -->
<?php 
$author_id  = get_the_author_meta('ID');
$user_name  = get_the_author_meta('display_name');
$avatar_url = get_avatar_url($author_id);
$author_avatar_image_url = get_the_author_meta('author_avatar_image_url', $author_id);
if( !empty($author_avatar_image_url) ){
    $avatar_url = $author_avatar_image_url;
}

$user_facebook_url  = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_facebook_url', $author_id);
$user_twitter_url   = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_twitter_url', $author_id);
$user_linkedin_url  = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_linkedin_url', $author_id);
$user_pinterest_url = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_pinterest_url', $author_id);
$user_instagram_url = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_instagram_url', $author_id);
$user_youtube_url   = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_youtube_url', $author_id);
$user_skype         = get_the_author_meta(GOLO_METABOX_PREFIX . 'author_skype', $author_id);
?>

<?php if( !empty(get_the_author_meta( 'description' )) ) { ?>
<div class="post-author block-line">
	<?php if($avatar_url) : ?>
	<div class="inner-left text-center">
		<div class="entry-avatar">
			<a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>">
				<img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($user_name); ?>">
			</a>
		</div>
	</div>
	<?php endif; ?>

	<div class="inner-right">
		<div class="head-author">
			<h3 class="entry-title"><?php the_author_posts_link(); ?></h3>
			<div class="author-socials-profile">
				<ul>
					<?php if( $user_facebook_url ) : ?>
					<li>
						<a href="<?php echo esc_url($user_facebook_url); ?>">
							<i class="la la-facebook-f"></i>
						</a>
					</li>
					<?php endif; ?>

					<?php if( $user_twitter_url ) : ?>
					<li>
						<a href="<?php echo esc_url($user_twitter_url); ?>">
							<i class="la la-twitter"></i>
						</a>
					</li>
					<?php endif; ?>

					<?php if( $user_instagram_url ) : ?>
					<li>
						<a href="<?php echo esc_url($user_instagram_url); ?>">
							<i class="la la-instagram"></i>
						</a>
					</li>
					<?php endif; ?>

					<?php if( $user_youtube_url ) : ?>
					<li>
						<a href="<?php echo esc_url($user_youtube_url); ?>">
							<i class="la la-youtube"></i>
						</a>
					</li>
					<?php endif; ?>

					<?php if( $user_linkedin_url ) : ?>
					<li>
						<a href="<?php echo esc_url($user_linkedin_url); ?>">
							<i class="la la-linkedin"></i>
						</a>
					</li>
					<?php endif; ?>

					<?php if( $user_pinterest_url ) : ?>
					<li>
						<a href="<?php echo esc_url($user_pinterest_url); ?>">
							<i class="la la-pinterest"></i>
						</a>
					</li>
					<?php endif; ?>

					<?php if( $user_skype ) : ?>
					<li>
						<a href="skype:<?php echo esc_url($author_skype); ?>?call">
							<i class="la la-skype"></i>
						</a>
					</li>
					<?php endif; ?>
				</ul>
			</div>
		</div>

        <p class="entry-bio">
            <?php the_author_meta( 'description' ); ?>
        </p><!-- .author-bio -->
    </div>
</div>
<?php } ?>