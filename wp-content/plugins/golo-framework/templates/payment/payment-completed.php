<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
$golo_payment = new Golo_Payment();
$payment_method = isset($_GET['payment_method']) ? absint(wp_unslash($_GET['payment_method'])) : -1;
if ($payment_method == 1) {
    $golo_payment->paypal_payment_completed();
} elseif ($payment_method == 2) {
    $golo_payment->stripe_payment_completed();
}
?>
<div class="golo-payment-completed-wrap">
    <div class="inner-payment-completed">
    <?php
    do_action('golo_before_payment_completed');
    if (isset($_GET['order_id']) && $_GET['order_id'] != ''):
        $order_id = absint(wp_unslash($_GET['order_id']));
        $golo_invoice = new Golo_Invoice();
        $invoice_meta = $golo_invoice->get_invoice_meta($order_id);
        $wire_transfer_card_number = golo_get_option('wire_transfer_card_number','');
        $wire_transfer_card_name = golo_get_option('wire_transfer_card_name','');
        $wire_transfer_bank_name = golo_get_option('wire_transfer_bank_name','');
        ?>

        <div class="panel panel-default">
            <div class="panel-heading"><h2><?php esc_html_e('Thank you for your order!', 'golo-framework'); ?></h2></div>
            <p><?php esc_html_e('Please transfer to our account number with the "Order Number" and wait for us to confirm.', 'golo-framework'); ?></p>

            <?php if( $wire_transfer_card_number || $wire_transfer_card_name || $wire_transfer_bank_name ) : ?>
            <div class="card-info">
                <table>
                    <tr>
                        <th><?php esc_html_e('Card Number', 'golo-framework'); ?></th>
                        <td><?php echo esc_html($wire_transfer_card_number); ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Card Name', 'golo-framework'); ?></th>
                        <td><?php echo esc_html($wire_transfer_card_name); ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Bank Name', 'golo-framework'); ?></th>
                        <td><?php echo esc_html($wire_transfer_bank_name); ?></td>
                    </tr>
                </table>
            </div>
            <?php endif; ?>

            <div class="entry-title"><h3><?php esc_html_e('Order Detail', 'golo-framework'); ?></h3></div>
            <ul class="list-group">
                <li class="list-group-item">
                    <span><?php esc_html_e('Order Number', 'golo-framework'); ?></span>
                    <strong class="pull-right"><?php echo esc_html($order_id); ?></strong>
                </li>
                <li class="list-group-item">
                    <span><?php esc_html_e('Date', 'golo-framework'); ?></span>
                    <strong class="pull-right"><?php echo get_the_date('', $order_id); ?></strong>
                </li>
                <li class="list-group-item">
                    <span><?php esc_html_e('Payment Method', 'golo-framework'); ?></span>
                    <strong class="pull-right">
                        <?php echo Golo_Invoice::get_invoice_payment_method($invoice_meta['invoice_payment_method']);  ?>
                    </strong>
                </li>
                <li class="list-group-item">
                    <span><?php esc_html_e('Payment Type', 'golo-framework'); ?></span>
                    <strong class="pull-right">
                        <?php echo Golo_Invoice::get_invoice_payment_type($invoice_meta['invoice_payment_type']);  ?>
                    </strong>
                </li>
                <li class="list-group-item">
                    <span><?php esc_html_e('Total', 'golo-framework'); ?></span>
                    <strong class="pull-right"><?php echo golo_get_format_money($invoice_meta['invoice_item_price']); ?></strong>
                </li>
            </ul>
        </div>

        <a href="<?php echo golo_get_permalink('dashboard'); ?>" class="btn btn-primary gl-button"><?php esc_html_e('Go to Dashboard', 'golo-framework'); ?></a>
    <?php else: ?>
        <div class="golo-heading">
            <h2><?php echo golo_get_option('thankyou_title',''); ?></h2>
        </div>
        <div class="golo-thankyou-content">
            <?php
            $html_info = golo_get_option('thankyou_content','');
            echo wpautop($html_info); ?>
           </div>
        <a href="<?php echo golo_get_permalink('dashboard'); ?>" class="btn btn-primary gl-button"> <?php esc_html_e('Go to Dashboard', 'golo-framework'); ?> </a>
    <?php endif;
    do_action('golo_after_payment_completed');
    ?>
    </div>
</div>