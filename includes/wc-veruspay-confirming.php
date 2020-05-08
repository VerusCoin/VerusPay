<?php 
/**
 * VerusPay Verus Gateway Confirming
 *
 * Confirming a Verus payment on the blockchain.
 *
 * @package VerusPay Verus Gateway\Confirming
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
<h1 class="entry-title"><?php echo $wc_veruspay_text_helper['title_paid']; ?></h1>
<div class="wc_veruspay_payment_container">
    <p class="wc_veruspay_processing-payment"><?php echo $wc_veruspay_text_helper['msg_confirming_payment']; ?><?php echo $wc_veruspay_block_progress.' / '.$wc_veruspay_confirmations; ?></p> 
    <noscript>
        <form method="post" action=""><button type="submit" name="woocommerce_check_status" value="check_status"><?php echo $wc_veruspay_text_helper['check_status']; ?></button></form>
    </noscript>
    <div id="wc-<?php echo esc_attr( $wc_veruspay_class->id ); ?>-vrsc-form" class="wc-payment-form" style="background:transparent;">
        <pre class="wc_veruspay_verus_address" id="verusAddress"><?php if ( substr($wc_veruspay_verus_address, 0, 2) !== 'zs' ) { echo '<a style="color:#fff!important" target="_BLANK" href="https://explorer.veruscoin.io/address/'.$wc_veruspay_verus_address.'">'.$wc_veruspay_verus_address.'</a>'; } else { echo $wc_veruspay_verus_address; } ?></pre>
    </div>
</div>
<?php echo header("Refresh:15"); ?>
