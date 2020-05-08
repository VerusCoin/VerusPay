<?php 
/**
 * VerusPay Verus Gateway Process
 *
 * Processing a Verus payment.
 *
 * @package VerusPay Verus Gateway\Process
 * @version 0.1.2
 * @author    J Oliver Westbrook
 * @category  Cryptocurrency
 * @copyright Copyright (c) 2019, John Oliver Westbrook
 * 
 * ====================
 * 
 * The MIT License (MIT)
 * 
 * Copyright (c) 2019 John Oliver Westbrook
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 * ====================
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>
<h1 class="entry-title"><?php echo $wc_veruspay_text_helper['title_on_hold']; ?><span style="color:#007bff !important;"><?php echo $wc_veruspay_verus_price; ?></span> VRSC:</h1>
<div class="wc_veruspay_payment_container">
    <p class="wc_veruspay_processing-payment"><?php echo $wc_veruspay_text_helper['msg_waiting_payment']; ?> <span id="wc_veruspay_timeleft"><?php echo $wc_veruspay_time_remaining; ?></span> mins</p> 
    <noscript><p class="wc_veruspay_timelimit" data-time="<?php echo $wc_veruspay_time_remaining; ?>">
        <form method="post" action=""><button type="submit" name="woocommerce_check_status" value="check_status"><?php echo $wc_veruspay_text_helper['check_status']; ?></button></form>
    </p></noscript>
    <div id="wc-<?php echo esc_attr( $wc_veruspay_class->id ); ?>-vrsc-form" class="wc-payment-form" style="background:transparent;">
        <pre class="wc_veruspay_verus_address" id="verusAddress"><?php echo $wc_veruspay_verus_address; ?></pre>
        <noscript><style>.wc_veruspay_address_tooltip{display:none;}</style></noscript>
        <div class="wc_veruspay_address_tooltip">
            <div class="wc_veruspay_address_copy-button">
                <button onclick="copyAddress()" onmouseout="outFunc()">
                <span class="wc_veruspay_address_tooltip-text" id="wc_veruspay_address_tooltip">Copy Verus Address</span>
                Copy Address
                </button>
            </div>
        </div>
        <p class="wc_veruspay_qr_block <?php echo $wc_veruspay_qr_toggle_show; ?>"><span class="wc_veruspay_qr_title"><?php echo $wc_veruspay_text_helper['title_qr_invoice']; ?>:</span><?php echo $wc_veruspay_qr_inv_code; ?></p>
        <p class="wc_veruspay_qr_block <?php echo $wc_veruspay_qr_toggle_width; ?>"><span class="wc_veruspay_qr_title"><?php echo $wc_veruspay_text_helper['title_qr_address']; ?>:</span><?php echo $wc_veruspay_qr_code; ?></p>
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
  tooltip.innerHTML = "Copy Verus Address";
}

</script>
<?php echo header("Refresh:15"); ?>
