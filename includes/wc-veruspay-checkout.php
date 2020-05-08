<?php 
/**
 * VerusPay Verus Gateway Checkout
 *
 * Checkout page.
 *
 * @package VerusPay Verus Gateway\Checkout
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
<div id="wc-<?php echo esc_attr( $this->id ); ?>-cc-form" class="wc-payment-form" style="background:transparent;">
    <span class="woocommerce-Price-amount amount wc_veruspay_price"><span class="wc_veruspay_verus_logo"></span>Verus <?php echo $wc_veruspay_text_helper['price']; ?>: <span class="vrsc-total-price"><?php echo $wc_veruspay_verus_price; ?></span> VRSC</span>
    <?php echo $wc_veruspay_sapling_option; ?>
    <div class="wc_veruspay_icon-sml">
        <img id="wc_veruspay_icon-price" class="wc_veruspay_icon-sml" src="<?php echo plugins_url( '../public/img/wc-veruspay-refresh.png', __FILE__ ) ?>" border="0" alt="Refresh" /><noscript><button type="submit" class="button alt wc_veruspay_noscript" name="woocommerce_checkout_update_totals" value="Refresh VRSC Price"><?php echo $wc_veruspay_text_helper['refresh']; ?></button></noscript>
    </div>
    <p class="wc_veruspay_checkout-sml">
        <span class="wc_veruspay_checkout-vsml"><?php echo $wc_veruspay_text_helper['exchange_rate']; ?>: <?php echo $wc_veruspay_currency_symbol; ?><span class="vrsc-rate"><?php echo $wc_veruspay_verus_rate; ?></span> / VRSC - <?php echo $wc_veruspay_text_helper['msg_valid_for']; ?>: <span id="vrsc_last_updated_time" class="wc_veruspay_price_time" data-expiretime="<?php echo $wc_veruspay_time_end; ?>"><?php echo $wc_veruspay_time_remaining; ?> min</span></span>
        <span class="wc_veruspay_checkout-vvsml wc_veruspay_poweredby"><i><a href="https://veruspay.io/api/" target="_BLANK"><?php echo $wc_veruspay_text_helper['price_powered_by']; ?></a></i></span>
    </p>
    <p class="wc_veruspay_custom_msg">
        <noscript><?php echo $wc_veruspay_text_helper['msg_noscript_refresh']; ?></noscript>
    </p>
    <p class="wc_veruspay_custom_msg">
        <?php echo $wc_veruspay_text_helper['msg_send_on_purchase']; ?> <?php echo $wc_veruspay_verus_price; ?> VRSC within <?php echo $this->orderholdtime; ?> min
    </p>
</div>
