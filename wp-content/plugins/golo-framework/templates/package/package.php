<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
global $current_user;
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$paid_submission_type = golo_get_option('paid_submission_type','no');
if ( $paid_submission_type != 'per_package' )
{
    echo golo_get_template_html('global/access-denied.php',array('type'=>'free_submit'));
    return;
}

?>
<div class="golo-package-wrap">
    <div class="golo-heading">
        <h2 class="entry-title"><?php esc_html_e('Find the plan that’s right for you', 'golo-framework') ?></h2>
    </div>
    <div class="row">
        <?php
        $user_package_id = get_the_author_meta(GOLO_METABOX_PREFIX . 'package_id', $user_id);
        $args = array(
            'post_type' => 'package',
            'posts_per_page' => -1,
            'orderby'=> 'meta_value_num',
            'meta_key'=> GOLO_METABOX_PREFIX . 'package_order_display',
            'order'=> 'ASC',
            'meta_query' => array(
                array(
                    'key' => GOLO_METABOX_PREFIX . 'package_visible',
                    'value' => '1',
                    'compare' => '=',
                )
            )
        );
        $data = new WP_Query($args);
        $total_records = $data->found_posts;
        if ($total_records == 4) {
            $css_class = 'col-md-3 col-sm-6';
        } else if ($total_records == 3) {
            $css_class = 'col-md-4 col-sm-6';
        } else if ($total_records == 2) {
            $css_class = 'col-md-4 col-sm-6';
        } else if ($total_records == 1) {
            $css_class = 'col-md-4 col-sm-12';
        } else {
            $css_class = 'col-md-3 col-sm-6';
        }
        while( $data->have_posts() ): $data->the_post();
            $package_id = get_the_ID();
            $package_time_unit = get_post_meta($package_id, GOLO_METABOX_PREFIX . 'package_time_unit', true);
            $package_period = get_post_meta($package_id, GOLO_METABOX_PREFIX . 'package_period', true);
            $package_num_places = get_post_meta($package_id, GOLO_METABOX_PREFIX . 'package_number_listings', true);
            $package_free = get_post_meta($package_id, GOLO_METABOX_PREFIX . 'package_free', true);
            if($package_free == 1)
            {
                $package_price = 0;
            }
            else
            {
                $package_price = get_post_meta($package_id, GOLO_METABOX_PREFIX . 'package_price', true);
            }
            $package_unlimited_listing = get_post_meta($package_id, GOLO_METABOX_PREFIX . 'package_unlimited_listing', true);
            $package_unlimited_time = get_post_meta($package_id, GOLO_METABOX_PREFIX . 'package_unlimited_time', true);
            $package_num_featured_listings = get_post_meta($package_id, GOLO_METABOX_PREFIX . 'package_number_featured', true);
            $package_featured = get_post_meta($package_id, GOLO_METABOX_PREFIX . 'package_featured', true);

            if ($package_period > 1) {
                $package_time_unit .= 's';
            }
            if ($package_featured == 1) {
                $is_featured = ' active';
            } else {
                $is_featured = '';
            }
            $golo_package = new Golo_Package();
            $get_expired_date = $golo_package->get_expired_date($package_id, $user_id);
            $current_date = date('Y-m-d');

            $d1 = strtotime( $get_expired_date );
            $d2 = strtotime( $current_date );

            if ($get_expired_date === 'Never Expires') {
                $d1 = 999999999999999999999999;
            }

            if ($user_package_id == $package_id && $d1 > $d2) {
                $is_current = ' current';
            } else {
                $is_current = '';
            }
            $payment_link = golo_get_permalink('payment');
            $payment_process_link = add_query_arg('package_id', $package_id, $payment_link);
            ?>
            <div class="<?php echo esc_attr($css_class); ?>">
                <div class="golo-package-item panel panel-default <?php echo esc_attr($is_current); ?> <?php echo esc_attr($is_featured); ?>">
                    <?php if( has_post_thumbnail() ) : ?>
                    <div class="golo-package-thumbnail"><?php the_post_thumbnail(); ?></div>
                    <?php endif; ?>

                    <div class="golo-package-title"><h2 class="entry-title"><?php the_title(); ?></h2></div>

                    <div class="golo-package-price">
                        <?php
                        if($package_price>0)
                        {
                            echo golo_get_format_money($package_price, '', 2, true);
                        }
                        else
                        {
                            esc_html_e('Free','golo-framework');
                        }
                        ?>
                    </div>

                    <div class="golo-package-choose golo-button">
                        <a href="<?php echo esc_url($payment_process_link); ?>"><?php esc_html_e('Get Started', 'golo-framework'); ?></a>
                    </div>
                    
                    <ul class="list-group">

                        <li class="list-group-item">
                            <span class="badge">
                                <?php if ($package_unlimited_time == 1) {
                                        esc_html_e('Never Expires', 'golo-framework');
                                    } else {
                                        echo esc_html($package_period) . ' ' . Golo_Package::get_time_unit($package_time_unit);
                                    }
                                ?>
                            </span>
                            <?php esc_html_e('Expiration Date', 'golo-framework'); ?>
                        </li>

                        <li class="list-group-item">
                            <span class="badge">
                            <?php if ($package_unlimited_listing == 1) {
                                esc_html_e('Unlimited', 'golo-framework');
                            } else {
                                echo esc_html($package_num_places);
                            } ?>
                            </span>
                            <?php esc_html_e('Place Listing', 'golo-framework'); ?>
                        </li>
                        <?php if ($package_num_featured_listings > 0) { ?>
                        <li class="list-group-item">
                            <span class="badge"><?php echo esc_html($package_num_featured_listings); ?></span>
                            <?php esc_html_e('Featured Listings', 'golo-framework') ?>
                        </li>

                        <li class="list-group-item">
                            <?php esc_html_e('Featured in Search Results', 'golo-framework') ?>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
    </div>
</div>