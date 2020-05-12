<?php
// No Direct Access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} 
if ( $wc_veruspay_order_mode == 'hold' ) {
    $_timeout = '';
    $_countdown_p = '<p class="wc_veruspay_processing-payment">Please send your payment within ' . $wc_veruspay_hold_time . ' min to avoid cancellation.</p>';
}
else {
    $_timeout = header("Refresh:15");
    $_countdown_p = '<p class="wc_veruspay_processing-payment">' . $wc_veruspay_global['text_help']['msg_waiting_payment'] .'<span id="wc_veruspay_timeleft">' . $wc_veruspay_time_remaining . '</span> mins</p>';
}
?>
<h1 class="entry-title"><?php echo $wc_veruspay_global['text_help']['title_on_hold']; ?><span class="wc_veruspay_blue"><?php echo $wc_veruspay_price; ?></span> <?php echo $_chain_up; ?>:</h1>
<div class="wc_veruspay_payment_container">
    <?php echo $_countdown_p; ?>
    <noscript><p class="wc_veruspay_timelimit" data-time="<?php echo $wc_veruspay_time_remaining; ?>"><form method="post" action=""><button type="submit" name="woocommerce_check_status" value="check_status"><?php echo $wc_veruspay_global['text_help']['check_status']; ?></button></form></p></noscript>
    <div id="wc-<?php echo esc_attr( $wc_veruspay_class->id ); ?>-vrsc-form" class="wc-payment-form wc_veruspay_transparent-bg">
        <pre class="wc_veruspay_address" id="verusAddress"><?php echo $wc_veruspay_address; ?></pre>
        <noscript><style>.wc_veruspay_address_tooltip{display:none;}</style></noscript>
        <div class="wc_veruspay_address_tooltip">
            <div class="wc_veruspay_address_copy-button">
                <button onclick="copyAddress()" onmouseout="outFunc()">
                <span class="wc_veruspay_address_tooltip-text" id="wc_veruspay_address_tooltip">Copy Address</span>
                Copy Address
                </button>
            </div>
        </div>
        <p class="wc_veruspay_qr_block <?php echo $wc_veruspay_qr_toggle_show; ?>"><span class="wc_veruspay_qr_title"><?php echo $wc_veruspay_global['text_help']['title_qr_invoice']; ?>:</span><?php echo $wc_veruspay_qr_inv_code; ?></p>
        <p class="wc_veruspay_qr_block <?php echo $wc_veruspay_qr_toggle_width; ?>"><span class="wc_veruspay_qr_title"><?php echo $wc_veruspay_global['text_help']['title_qr_address']; ?>:</span><?php echo $wc_veruspay_qr_code; ?></p>
        <p class="wc_veruspay_custom_msg"><?php echo $wc_veruspay_process_custom_msg; ?></p>
    </div>
</div>
<script>
function copyAddress() {
    var $temp = jQuery("<input>");
    var $addr = jQuery('#verusAddress').text();
    jQuery("body").append($temp);
    $temp.val(jQuery('#verusAddress').text()).select();
    document.execCommand("copy");
    $temp.remove();
    var tooltip = document.getElementById("wc_veruspay_address_tooltip");
    tooltip.innerHTML = "Copied!";
}
function outFunc() {
  var tooltip = document.getElementById("wc_veruspay_address_tooltip");
  tooltip.innerHTML = "Copy Address";
}
</script>
<?php echo $_timeout; ?>