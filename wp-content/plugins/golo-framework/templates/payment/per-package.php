<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
global $current_user;
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$package_id = isset($_GET['package_id']) ? golo_clean(wp_unslash($_GET['package_id'])) : '';
$user_package_id = get_the_author_meta(GOLO_METABOX_PREFIX . 'package_id', $user_id);
$golo_profile = new Golo_Profile();
$check_package = $golo_profile->user_package_available($user_id);

$package_free = get_post_meta($package_id, GOLO_METABOX_PREFIX . 'package_free', true);

if($package_free == 1)
{
    $package_price = 0;
}
else
{
    $package_price = get_post_meta($package_id, GOLO_METABOX_PREFIX . 'package_price', true);
}

$package_listings = get_post_meta($package_id, GOLO_METABOX_PREFIX . 'package_number_listings', true);
$package_featured_listings = get_post_meta($package_id, GOLO_METABOX_PREFIX . 'package_number_featured', true);
$package_unlimited_listing = get_post_meta($package_id, GOLO_METABOX_PREFIX . 'package_unlimited_listing', true);
$package_unlimited_time = get_post_meta($package_id, GOLO_METABOX_PREFIX . 'package_unlimited_time', true);
$package_time_unit = get_post_meta($package_id, GOLO_METABOX_PREFIX . 'package_time_unit', true);
$package_title = get_the_title($package_id);
$package_billing_frquency = get_post_meta($package_id, GOLO_METABOX_PREFIX . 'package_period', true);

if ($package_billing_frquency > 1) {
    $package_time_unit .= 's';
}
$terms_conditions = golo_get_option('payment_terms_condition');
$allowed_html = array(
    'a' => array(
        'href' => array(),
        'title' => array(),
        'target' => array()
    ),
    'strong' => array()
);
$enable_paypal = golo_get_option('enable_paypal', 1);
$enable_stripe = golo_get_option('enable_stripe', 1);
$enable_woocheckout = golo_get_option('enable_woocheckout', 1);
$enable_wire_transfer = golo_get_option('enable_wire_transfer', 1);
$select_packages_link = golo_get_permalink('packages');

?>

<div class="row">
    <div class="col-lg-8 col-md-7 col-sm-6">
        <?php if( ($package_id == $user_package_id) && $check_package == 1 ):?>
            <div class="entry-heading">
                <h2 class="entry-title"><?php esc_html_e('Checked Package', 'golo-framework'); ?></h2>
            </div>

            <div class="alert alert-warning" role="alert"><?php echo sprintf( __( 'You currently have "%s" package. The package hasn\'t expired yet, so you cannot buy it at this time. If you would like, you can buy another package.', 'golo-framework' ), $package_title); ?></div>
        <?php else: ?>

        <?php if ($package_price > 0): ?>
            <div class="golo-payment-method-wrap">
                <div class="entry-heading">
                    <h2 class="entry-title"><?php esc_html_e('Payment Method', 'golo-framework'); ?></h2>
                </div>
                <?php if ($enable_paypal != 0) : ?>
                    <div class="radio active">
                        <label>
                            <input type="radio" class="payment-paypal" name="golo_payment_method" value="paypal" checked>
                            <img src="<?php echo esc_attr(GOLO_PLUGIN_URL . 'assets/images/paypal.png'); ?>" alt="<?php esc_html_e('Paypal', 'golo-framework'); ?>">
                            <?php esc_html_e('Pay With Paypal', 'golo-framework'); ?>
                        </label>
                    </div>
                <?php endif; ?>

                <?php if ($enable_stripe != 0): ?>
                    <div class="radio">
                        <label>
                            <input type="radio" class="payment-stripe" name="golo_payment_method" value="stripe">
                            <img src="<?php echo esc_attr(GOLO_PLUGIN_URL . 'assets/images/stripe.png'); ?>" alt="<?php esc_html_e('Stripe', 'golo-framework'); ?>">
                            <?php esc_html_e('Pay with Credit Card', 'golo-framework'); ?>
                        </label>
                        <?php
                        $golo_payment = new Golo_Payment();
                        $golo_payment->stripe_payment_per_package($package_id); 
                        ?>
                    </div>
                <?php endif; ?>

                <?php if ($enable_woocheckout != 0): ?>
                    <div class="radio">
                        <label>
                            <input type="radio" class="payment-woocheckout" name="golo_payment_method" value="woocheckout">
                            <img src="<?php echo esc_attr(GOLO_PLUGIN_URL . 'assets/images/woocommerce-logo.png'); ?>" alt="<?php esc_html_e('Woocommerce', 'golo-framework'); ?>">
                            <?php esc_html_e('Pay with Woocommerce', 'golo-framework'); ?>
                        </label>
                    </div>
                <?php endif; ?>

                <?php if ($enable_wire_transfer != 0) : ?>
                    <div class="radio">
                        <label>
                            <input type="radio" name="golo_payment_method" value="wire_transfer">
                            <i class="fa fa-send-o"></i> <?php esc_html_e('Wire Transfer', 'golo-framework'); ?>
                        </label>
                    </div>
                    <div class="golo-wire-transfer-info">
                        <?php
                        $html_info = golo_get_option('wire_transfer_info','');
                        echo wpautop($html_info); 
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <input type="hidden" name="golo_package_id" value="<?php echo esc_attr($package_id); ?>">

        <p class="terms-conditions"><i class="fa fa-hand-o-right"></i> <?php echo sprintf(wp_kses(__('Please read <a target="_blank" href="%s"><strong>Terms & Conditions</strong></a> first', 'golo-framework'), $allowed_html), get_permalink($terms_conditions)); ?></p>
        <?php if ($package_price > 0) : ?>
            <button id="golo_payment_package" type="submit" class="btn btn-success btn-submit gl-button"><?php esc_html_e('Pay Now', 'golo-framework'); ?></button>
        <?php else :
            $user_free_package = get_the_author_meta(GOLO_METABOX_PREFIX . 'free_package', $user_id);
            if ($user_free_package == 'yes' && $check_package == 1) : ?>
                <div class="golo-message alert alert-warning" role="alert"><?php esc_html_e('You have already used your first free package, please choose different package.', 'golo-framework'); ?></div>
            <?php else : ?>
                <button id="golo_free_package" type="submit" class="btn btn-success btn-submit"><?php esc_html_e('Get Free Listing Package', 'golo-framework'); ?></button>
            <?php endif; ?>
        <?php endif; ?>

        <?php endif;?>
    </div>

    <div class="col-lg-4 col-md-5 col-sm-6">
        <div class="golo-payment-for golo-package-wrap panel panel-default">
            <div class="entry-heading">
                <h2 class="entry-title"><?php esc_html_e('Selected Package', 'golo-framework'); ?></h2>
            </div>

            <div class="golo-package-item panel panel-default <?php echo esc_attr($is_featured); ?>">
                <?php if( has_post_thumbnail($package_id) ) : ?>
                <div class="golo-package-thumbnail"><?php echo get_the_post_thumbnail($package_id); ?></div>
                <?php endif; ?>

                <div class="golo-package-title"><h2 class="entry-title"><?php echo get_the_title($package_id); ?></h2></div>
                
                <ul class="list-group">

                    <li class="list-group-item">
                        <span class="badge">
                            <?php if ($package_unlimited_time == 1) {
                                    esc_html_e('Never Expires', 'golo-framework');
                                } else {
                                    echo esc_html($package_billing_frquency) . ' ' . Golo_Package::get_time_unit($package_time_unit);
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
                            echo esc_html($package_listings);
                        } ?>
                        </span>
                        <?php esc_html_e('Place Listing', 'golo-framework'); ?>
                    </li>

                    <li class="list-group-item"><span class="badge"><?php echo esc_html($package_featured_listings); ?></span><?php esc_html_e('Featured Listings', 'golo-framework') ?></li>
                </ul>

                <div class="golo-total-price">
                    <span><?php esc_html_e('Total', 'golo-framework'); ?></span>
                    <span class="price">
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
                    </span>
                </div>

                <a class="btn btn-default" href="<?php echo esc_url($select_packages_link); ?>"><?php esc_html_e('Change Package', 'golo-framework'); ?></a>
            </div>  
        </div>
    </div>
</div>