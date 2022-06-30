<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if(!is_user_logged_in()){
    echo golo_get_template_html('global/access-denied.php',array('type' => 'not_login'));
    return;
}
$allow_submit = golo_allow_submit();
if (!$allow_submit)
{
    echo golo_get_template_html('global/access-denied.php',array('type' => 'not_permission'));
    return;
}
$package_id = isset($_GET['package_id']) ? absint(wp_unslash($_GET['package_id']))  : '';
$place_id   = isset($_GET['place_id']) ? absint(wp_unslash($_GET['place_id']))  : '';
$is_upgrade = isset($_GET['is_upgrade']) ? absint(wp_unslash($_GET['is_upgrade']))  : '';
if ($is_upgrade == 1) {
    $prop_featured = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_featured', true);
    if ($prop_featured == 1) {
        echo("<script>location.href = '".home_url()."'</script>");
    }
}
if (empty($package_id) && empty($place_id)) {
    echo("<script>location.href = '".home_url()."'</script>");
}
$golo_place = new Golo_Place();

if (!empty($place_id) && !$golo_place->user_can_edit_place($place_id)) {
    echo("<script>location.href = '".home_url()."'</script>");
}
wp_enqueue_script(GOLO_PLUGIN_PREFIX . 'payment');
set_time_limit(700);
$paid_submission_type = golo_get_option('paid_submission_type','no');
?>
<div class="payment-wrap">
    <?php
    do_action('golo_payment_before');
    if ($paid_submission_type == 'per_package') {
        golo_get_template('payment/per-package.php');
    }
    wp_nonce_field('golo_payment_ajax_nonce', 'golo_security_payment');
    do_action('golo_payment_after');
    ?>
</div>