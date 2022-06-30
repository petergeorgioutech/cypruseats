<?php
/**
 * Pagination - Show numbered pagination for catalog pages.
 */

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed dgoloctly
}

/**
 * @var $max_num_pages
 * @var $type
 * @var $layout
 */
if ( $max_num_pages < 1 ) {
	return;
}

if( empty($type) ){
	$type = 'normal';
}

$pagination_type = golo_get_option('pagination_type', 'loadmore');
if( !empty($layout) && $layout == 'number' ) {
	$pagination_type = 'number';
}

global $wp_rewrite;
global $paged;

if ( get_query_var('paged') ) { 
	$paged = get_query_var('paged'); 
} elseif ( get_query_var('page') ) { 
	$paged = get_query_var('page'); 
} else { 
	$paged = 1; 
}

$pagenum_link = html_entity_decode( get_pagenum_link() );
$query_args   = array();
$url_parts    = explode( '?', $pagenum_link );

$current_city  = isset( $_GET['city'] ) ? golo_clean(wp_unslash($_GET['city'])) : '';
$current_term  = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
$taxonomy_name = get_query_var('taxonomy');
$slug = '';
if( !empty($current_term->slug) ) {
	$slug = $current_term->slug;
}

if ( isset( $url_parts[1] ) ) {
	wp_parse_str( $url_parts[1], $query_args );
}

$pagenum_link = esc_url(remove_query_arg( array_keys( $query_args ), $pagenum_link ));
$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

$format  = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';

$pages = paginate_links( apply_filters( 'golo_pagination_args', array(
	'base'      => $pagenum_link,
	'format'    => $format,
	'total'     => $max_num_pages,
	'current'   => $paged,
	'mid_size'  => 1,
	'type'  	=> 'array',
	'add_args'  => array_map('urlencode', $query_args ),
	'prev_text' => __('<i class="fal fa-chevron-left"></i>', 'golo-framework'),
	'next_text' => __('<i class="fal fa-chevron-right"></i>', 'golo-framework'),
) ) );

?>

<div class="golo-pagination <?php echo esc_attr($type); ?>" data-type="<?php echo esc_attr($pagination_type); ?>">

	<?php if( $pagination_type == 'number' ) : ?>
		<div class="pagination">
		<?php if( is_array( $pages ) ) { ?>			

			<?php foreach ( $pages as $page ) { ?>

				<?php echo wp_kses_post($page); ?>

			<?php } ?>

		<?php } ?>
		</div>
	<?php endif; ?>
	
	<?php if( $pagination_type == 'loadmore' ) : ?>
		<div class="pagination loadmore">
			<a class="page-numbers next" href="#"><span><?php esc_html_e('Load More', 'golo-framework'); ?></span><i class="far fa-spinner-third fa-spin icon-large"></i></a>
		</div>
	<?php endif; ?>

	<input type="hidden" name="paged" value="1">
	<input type="hidden" name="city" value="<?php echo esc_attr($current_city); ?>">
	<input type="hidden" name="current_term" value="<?php echo esc_attr($current_term->term_id); ?>">
	<input type="hidden" name="type_term" value="<?php echo esc_attr($taxonomy_name); ?>">

</div>