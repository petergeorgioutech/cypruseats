<?php
//==============================================================================
// LISTING TAXONOMY
//==============================================================================
if (!class_exists('Golo_Widget_Listing_Taxonomy')) {
    class Golo_Widget_Listing_Taxonomy extends Golo_Widget
    {
        public function __construct()
        {
            $this->widget_cssclass = 'golo-widget-listing_taxonomy';
            $this->widget_description = esc_html__("Listing taxonomy widget", 'golo-framework');
            $this->widget_id = 'golo_listing_taxonomy';
            $this->widget_name = esc_html__('Golo - Listing Taxonomy', 'golo-framework');
            $this->settings = array(
                'title' => array(
                    'type' => 'text',
                    'std' => '',
                    'label' => esc_html__('Title','golo-framework')
                ),
                'taxonomy' => array(
                    'type' => 'select-tax',
                    'std' => '',
                    'label' => esc_html__('Select taxonomy','golo-framework'),
                ),
                'column' => array(
                    'type' => 'select',
                    'std' => '1',
                    'label' => esc_html__('Column','golo-framework'),
                    'options' => array('1' => '1', '2' => '2')
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
            $taxonomy   = empty( $instance['taxonomy'] ) ? '' : apply_filters( 'widget_taxonomy', $instance['taxonomy'] );
            $column = empty( $instance['column'] ) ? '' : apply_filters( 'widget_column', $instance['column'] );
            ob_start();
            echo wp_kses_post($args['before_widget']);
            $arr_taxonomy = explode(",",$taxonomy);
            $col_class = array();
            if(!empty($column) && $column == '1'){
                $col_class[] = 'golo-column-1';
            }else{
                $col_class[] = 'golo-column-2';
            }
            if(!empty($title)) { ?>
                <h4 class="widget-title"><?php echo esc_html($title); ?></h4>
            <?php } ?>
            <?php if(!empty($taxonomy)){ ?>
                <div class="<?php echo join(' ',$col_class); ?>">
                    <ul class="categories">
                        <?php 
                        foreach ($arr_taxonomy as $value){
                        $term = get_term($value,'product_cat');
                        if(!empty($term)){
                        $term_link = get_term_link($term);
                        ?>
                            <li>
                                <a href="<?php echo esc_url($term_link); ?>"><?php echo esc_html($term->name); ?><span><?php echo esc_html($term->count); ?></span></a>
                                <?php 
                                $term_children = get_term_children( $term->term_id, 'product_cat' );
                                if(!empty($term_children)){
                                ?>
                                <span class="ti-plus"></span>
                                <ul class="sub-menu">
                                    <?php 
                                    foreach ( $term_children as $child ) {
                                        $term_child = get_term( $child, 'product_cat' );
                                        $child_link = get_term_link($term_child);
                                    ?>
                                        <li><a href="<?php echo esc_url($child_link); ?>"><?php echo esc_html($term_child->name); ?><span><?php if( $term_child->count > 0 ) : echo esc_html($term_child->count);endif; ?></span></a></li>
                                    <?php } ?>
                                </ul>
                                <?php } ?>
                            </li>
                        <?php } } ?>
                    </ul>
                </div>
            <?php }
            echo wp_kses_post($args['after_widget']);
            $content =  ob_get_clean();
            echo wp_kses_post($content);
            $this->cache_widget( $args, $content );
        }
    }
}