<?php
if (!class_exists('Golo_Widget_Popular_Posts')) {
    class Golo_Widget_Popular_Posts extends Golo_Widget
    {
        public function __construct()
        {
            $this->widget_cssclass = 'golo-widget-popular_posts';
            $this->widget_description = esc_html__("Popular posts widget", 'golo-framework');
            $this->widget_id = 'golo_popular_posts';
            $this->widget_name = esc_html__('Golo - Popular Posts', 'golo-framework');
            $this->settings = array(
                'title' => array(
                    'type' => 'text',
                    'std' => esc_html__('Popular Posts','golo-framework'),
                    'label' => esc_html__('Title','golo-framework')
                ),
                'number' => array(
                    'type'  => 'number',
                    'std'   => '6',
                    'label' => esc_html__('Number of posts to show', 'golo-framework')
                ),
                'sort_by' => array(
                    'type'    => 'select',
                    'label'   => esc_html__('Sort By', 'golo-framework'),
                    'std'     => 'date',
                    'options' => array(
                        'date' => esc_html__('Date', 'golo-framework'),
                        'title'  => esc_html__('Title', 'golo-framework'),
                        'rand'  => esc_html__('Random', 'golo-framework'),
                    )
                ),
                'cate' => array(
                    'type' => 'checkbox',
                    'std' => 'true',
                    'label' => esc_html__('Show post categories', 'golo-framework')
                ),
            );
            parent::__construct();
        }

        function widget($args, $instance)
        {
            if ( $this->get_cached_widget( $args ) )
                return;
            extract( $args, EXTR_SKIP );
            $title   = empty( $instance['title'] ) ? '' : apply_filters( 'widget_title', $instance['title'] );
            $number   = empty( $instance['number'] ) ? '' : apply_filters( 'widget_number', $instance['number'] );
            $sort_by   = empty( $instance['sort_by'] ) ? '' : apply_filters( 'widget_sort_by', $instance['sort_by'] );
            $cate   = empty( $instance['cate'] ) ? '' : apply_filters( 'widget_cate', $instance['cate'] );
            ob_start();
            echo wp_kses_post($args['before_widget']);
            ?>

            <?php
            $arr = array(
                'post_type' => 'post',
                'numberposts' => $number,
                // 'meta_key' => 'post_views_count',
                'orderby' => $sort_by,
                'order' => 'DESC'
            );
            $posts = get_posts( $arr );

            ?>
            
            <?php if(!empty($title)) { ?>
                <h3 class="widget-title"><?php echo esc_html($title); ?></h3>
            <?php } ?>

            <div class="golo-popular-posts listing-posts">
                <?php 
                foreach( $posts as $post ) {
                    $postid    = $post->ID;
                    $size      = 'medium';
                    $categores = wp_get_post_categories($postid);
                    $size      = '140x140';
                    $attach_id = get_post_thumbnail_id($postid);
                    $thumb_url = Golo_Helper::golo_image_resize($attach_id, $size);
                    
                    $no_image_src    = GOLO_PLUGIN_URL . 'assets/images/no-image.jpg';
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

                    <article class="post">
                        <div class="inner-post-wrap">

                            <!-- post thumbnail -->
                            <?php if ( $cur_url ) : ?>
                            <div class="entry-post-thumbnail">
                                <a href="<?php echo get_the_permalink($postid); ?>">
                                    <img src="<?php echo esc_url( $cur_url ); ?>" alt="<?php the_title_attribute($postid); ?>">
                                </a>
                            </div>
                            <?php endif; ?>

                            <div class="entry-post-detail">
                                
                                <!-- list categories -->
                                <?php if( $categores && $cate ) : ?>
                                <ul class="post-categories">
                                    <?php 
                                    foreach ($categores as $category) {
                                        $cate = get_category($category);
                                    ?>
                                        <li><a href="<?php echo get_category_link($cate); ?>"><?php echo esc_html($cate->name); ?></a></li>
                                    <?php } ?>
                                </ul>
                                <?php endif; ?>
                                
                                <!-- post title -->
                                <h3 class="post-title"><a href="<?php echo get_the_permalink($postid); ?>" rel="bookmark"><?php echo get_the_title($postid); ?></a></h3>

                                <?php if( is_sticky($postid) ) { ?>
                                    <span class="is-sticky"><?php esc_html_e('Featured','golo-framework'); ?></span>
                                <?php } ?>

                            </div>

                        </div>
                    </article><!-- #post-## -->
                <?php } ?>
            </div>

            <?php
            echo wp_kses_post($args['after_widget']);
            $content = ob_get_clean();
            echo $content;
            $this->cache_widget( $args, $content );
        }
    }
}