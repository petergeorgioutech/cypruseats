<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$place_meta_data = get_post_custom($id);
$place_logged    = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_logged']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_logged'][0] : '0';

if( $place_logged && !is_user_logged_in() )
{
    return esc_html_e('This item required loggin to view!', 'golo-framework');
}

global $post;

$id = get_the_ID();

$type_single_place = golo_get_option('type_single_place', 'type-1' );

$classes = array('golo-place-wrap', 'single-place-area');

?>
<div id="place-<?php the_ID(); ?>" <?php post_class($classes); ?>>

	<?php
		/**
		* Hook: golo_single_place_summary hook.
		*/
		do_action( 'golo_single_place_summary' );
	?>
	
</div>