<?php 
// No Direct Access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>
<h1 class="entry-title"><?php echo $wc_veruspay_global['text_help']['title_paid']; ?></h1>
<div class="wc_veruspay_payment_container">
    <p class="wc_veruspay_processing-payment"><?php echo $wc_veruspay_global['text_help']['msg_confirming_payment']; ?><?php echo $wc_veruspay_block_progress.' / '.$wc_veruspay_confirmations; ?></p> 
    <noscript><form method="post" action=""><button type="submit" name="woocommerce_check_status" value="check_status"><?php echo $wc_veruspay_global['text_help']['check_status']; ?></button></form></noscript>
    <div id="wc-<?php echo esc_attr( $wc_veruspay_class->id ); ?>-vrsc-form" class="wc-payment-form wc_veruspay_transparent-bg">
        <pre class="wc_veruspay_address" id="verusAddress"><?php if ( substr( $wc_veruspay_address, 0, 2 ) !== 'zs' ) { echo '<a class="wc_veruspay_white" target="_BLANK" href="' . $wc_veruspay_global['chain_dtls'][$_chain_lo]['address'] . $wc_veruspay_address . '">' . $wc_veruspay_address . '</a>'; } else { echo $wc_veruspay_address; } ?></pre>
    </div>
</div>
<?php echo header("Refresh:15"); ?>