<?php 
/**
 * The template for displaying page title
 */

$page_title = $page_title_des = $page_title_blog_name = $page_title_blog_des = '';
$page_title_classes = array();
$page_title_css = 'page-title-orther';
$elementor_page = get_post_meta( get_the_ID(), '_elementor_edit_mode', true ); 

if( !empty($elementor_page) ) {
	return;
}

if( class_exists( 'WooCommerce' ) && ( is_shop() || is_product() ) ) {
	$page_title_shop_name   = Golo_Helper::get_setting('page_title_shop_name');
	$page_title_shop_des    = Golo_Helper::get_setting('page_title_shop_des');
	$enable_page_title_blog = Golo_Helper::get_setting('enable_page_title_blog');
	$page_title             = $page_title_shop_name;
	$page_title_des         = $page_title_shop_des;
	$page_title_css 		= 'page-title-shop';
	
}else{
	
	if( is_home() ){
		$page_title_blog_name   = Golo_Helper::get_setting('page_title_blog_name');
		$page_title_blog_des    = Golo_Helper::get_setting('page_title_blog_des');
		$enable_page_title_blog = Golo_Helper::get_setting('enable_page_title_blog');
		$page_title             = $page_title_blog_name;
		$page_title_des         = $page_title_blog_des;
		$page_title_css 		= 'page-title-blog';

		if( empty($enable_page_title_blog) ){
			$page_title_classes[] = 'hide';
		}
	}

	if ( !is_singular() && !is_front_page() ) {
		if( !is_404() ) {
			$page_title_css = 'page-title-blog';
		}
		if ( is_404() ) {
			$page_title_css 	  = 'page-title-other';
			$page_title           = esc_html__('404 Error', 'golo');
			$page_title_des       = esc_html__("Sorry, we couldn't find that page.", 'golo');
		} elseif (is_tag()) {
			$page_title = single_tag_title(esc_html__("Tags: ", 'golo'), false);
		} elseif (is_category() || is_tax()) {
			$page_title = single_cat_title('', false);
		} elseif (is_author()) {
			global $wp_query;
			$current_author = $wp_query->get_queried_object();
			$current_author_meta = get_user_meta($current_author->ID);
			if (empty($current_author->first_name) && empty($current_author->last_name)) {
				$page_title = $current_author->user_login;
			} else {
				$page_title = $current_author->first_name . ' ' . $current_author->last_name;
			}
		} elseif (is_day()) {
			$page_title = sprintf(esc_html__('Daily Archives: %s', 'golo'), get_the_date());
		} elseif (is_month()) {
			$page_title = sprintf(esc_html__('Monthly Archives: %s', 'golo'), get_the_date(_x('F Y', 'monthly archives date format', 'golo')));
		} elseif (is_year()) {
			$page_title = sprintf(esc_html__('Yearly Archives: %s', 'golo'), get_the_date(_x('Y', 'yearly archives date format', 'golo')));
		} elseif (is_search()) {
			$key = isset( $_GET['s'] ) ? Golo_Helper::golo_clean(wp_unslash($_GET['s'])) : '';
			$page_title = sprintf(esc_html__('Search Results: "%s"', 'golo'), $key);
		} elseif (is_tax('post_format', 'post-format-aside')) {
			$page_title = esc_html__('Asides', 'golo');
		} elseif (is_tax('post_format', 'post-format-gallery')) {
			$page_title = esc_html__('Galleries', 'golo');
		} elseif (is_tax('post_format', 'post-format-image')) {
			$page_title = esc_html__('Images', 'golo');
		} elseif (is_tax('post_format', 'post-format-video')) {
			$page_title = esc_html__('Videos', 'golo');
		} elseif (is_tax('post_format', 'post-format-quote')) {
			$page_title = esc_html__('Quotes', 'golo');
		} elseif (is_tax('post_format', 'post-format-link')) {
			$page_title = esc_html__('Links', 'golo');
		} elseif (is_tax('post_format', 'post-format-status')) {
			$page_title = esc_html__('Statuses', 'golo');
		} elseif (is_tax('post_format', 'post-format-audio')) {
			$page_title = esc_html__('Audios', 'golo');
		} elseif (is_tax('post_format', 'post-format-chat')) {
			$page_title = esc_html__('Chats', 'golo');
		}
	}
}

if ( is_singular() ) {
	if (!$page_title) {
		$page_title = get_the_title(get_the_ID());
	}
}

$page_title_classes[] = $page_title_css;

?>

<div class="page-title <?php echo join(' ',$page_title_classes); ?>">
	<div class="container">
		<div class="entry-detail">
			<?php if( !empty($page_title) ){ ?>
				<h1 class="entry-title">
					<?php echo wp_kses($page_title, Golo_Helper::golo_kses_allowed_html()); ?>
				</h1>
			<?php }elseif( class_exists( 'WooCommerce' ) ) { ?>
				<?php if( is_shop() ) : ?>
					<h1 class="entry-title"><?php woocommerce_page_title(); ?></h1>
				<?php endif; ?>
			<?php } ?>
			
			<?php if( !empty($page_title_des) ){ ?>
			<div class="sub-title">
				<p><?php echo esc_html($page_title_des); ?></p>
			</div>
			<?php } ?>
		</div>
	</div>
</div>