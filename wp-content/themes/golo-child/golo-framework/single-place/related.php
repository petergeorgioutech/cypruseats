<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>


<?php

// Related Posts

$content_selections = get_field('content_selection');
if( $content_selections ): ?>
    <div class="related-place custom-place-block">
        <div class="container">   
            <div class="block-heading">
                <h3 class="entry-title"><?php esc_html_e('Related Posts', 'golo-framework'); ?></h3>
            </div>
            <div class="inner-related">
                <?php 
                    $slick_attributes = array(
                        '"slidesToShow": 4',
                        '"slidesToScroll": 1',
                        '"autoplay": true',
                        '"infinite": false',
                        '"autoplaySpeed": 5000',
                        '"arrows": true',
                        '"responsive": [{ "breakpoint": 376, "settings": {"slidesToShow": 1,"infinite": false, "swipeToSlide": true, "dots": true} },{ "breakpoint": 479, "settings": {"slidesToShow": 1,"infinite": false, "swipeToSlide": true, "dots": true} },{ "breakpoint": 650, "settings": {"slidesToShow": 2} },{ "breakpoint": 768, "settings": {"slidesToShow": 2} },{ "breakpoint": 1200, "settings": {"slidesToShow": 3} } ]'
                    );
                    $wrapper_attributes[] = "data-slick='{". implode(', ', $slick_attributes) ."}'";
                ?>
                <div class="list-posts slick-carousel" <?php echo implode(' ', $wrapper_attributes); ?>>
                    <?php foreach( $content_selections as $content_selection ): 
                        $permalink = get_permalink( $content_selection->ID );
                        $title = get_the_title( $content_selection->ID );
                        $excerpt = get_the_excerpt( $content_selection->ID );
                        $trimmed_content = wp_trim_words( $excerpt, $num_words = 40, $more = null );
                        // $custom_field = get_field( 'field_name', $content_selection->ID );

                        $post_id =  $content_selection->ID;
                        $size      = 'medium';
                        $categores = wp_get_post_categories($content_selection->ID);
                        $size      = '480x520';
                        $attach_id = get_post_thumbnail_id($content_selection->ID);
                        $thumb_url = Golo_Helper::golo_image_resize($attach_id, $size);
                        
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
                    
                    <article id="post-<?php echo esc_attr($content_selection->ID); ?>" aria-hidden="false" class="related-slide place-item layout-02 golo-place-featured place-5156 place-item">
                        <div class="place-inner">
                            <div class="place-thumb">
                                <a class="entry-thumb" href="<?php echo esc_url( $permalink ); ?>" tabindex="0">
                                    <img src="<?php echo esc_url( $cur_url ); ?>" alt="<?php the_title_attribute($postid); ?>">
                                </a>

                                <a class="entry-category" href="https://cypruseats.local/place-categories/international/?city=nicosia" tabindex="0">
                                    <img alt="International" src="https://cypruseats.local/wp-content/uploads/2022/06/International-Food@3x.png">
                                </a>
                            </div>
                            <div class="entry-detail">
                                <div class="entry-head">
                                    <h3 class="place-title"><a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a></h3>
                                    <div class="place-content place-address">
                                        <p><?php echo esc_html( $trimmed_content ); ?></p>
                                    </div>
                                </div>
                                <div class="entry-bottom">
                                    <p class="mb-0"><a href="<?php echo esc_url( $permalink ); ?>" class="read-more" >Read more</a></p>
                                </div>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php

$city_id = $city_name = $city_slug = '';

$place_id = get_the_ID();

$place_city       = get_the_terms( $place_id, 'place-city');
$place_amenities  = get_the_terms( $place_id, 'place-amenities');
$place_categories = get_the_terms( $place_id, 'place-categories');

$enable_single_place_related             = golo_get_option('enable_single_place_related', '1' );

if( $place_city ) {
    $city_id      = $place_city[0]->term_id;
    $city_name    = $place_city[0]->name;
    $city_slug    = $place_city[0]->slug;
}

$categories = array();
if( $place_categories ) :
    foreach ($place_categories as $cate) {
        $cate_id = $cate->term_id;
        $categories[] = $cate_id;
    }
endif;

$args = array(
    'posts_per_page'      => 4,
    'post_type'           => 'place',
    'post_status'         => 'publish',
    'ignore_sticky_posts' => 1,
    'exclude'             => $place_id,
    'orderby' => array(
        'menu_order' => 'ASC',
        'date'       => 'DESC',
    ),
    'tax_query' => array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'place-city',
            'field'    => 'id',
            'terms'    => $city_id
        ),
        array(
            'taxonomy' => 'place-categories',
            'field'    => 'id',
            'terms'    => $categories
        )
     )
);
$places = get_posts( $args );

?>

<?php if( $places && $enable_single_place_related ) : ?>
<div class="related-place">
    <div class="container">   
        <div class="block-heading">
            <h3 class="entry-title"><?php esc_html_e('Similar places', 'golo-framework'); ?></h3>
        </div>

        <div class="inner-related">
            <?php echo golo_get_place_by_category( 4, 4, $city_id, $categories, '540x480' ); ?>
        </div>
    </div>
</div>
<?php endif; ?>  