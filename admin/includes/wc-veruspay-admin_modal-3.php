<?php 
// No Direct Access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>
<div class="wc_veruspay_cashout-modalback" style="display:none;">
    <div class="wc_veruspay_cashout-modalinner">
        <h4>Verify Before Proceeding</h4>
        <p>You are about to send <span class="wc_veruspay_modal_amount"></span> <span class="wc_veruspay_modal_coin"></span> to your <span class="wc_veruspay_modal_coin"></span> <span class="wc_veruspay_modal_type"></span> address.  Please verify the coin, amount, and type (Private or Transparent) are correct, and that you have access to/own the receive address, before you continue:</p>
        <p>Coin: <span class="wc_veruspay_modal_coin"></span></p>
        <p>Amount: <span class="wc_veruspay_modal_amount"></span></p>
        <p>Type: <span class="wc_veruspay_modal_type"></span></p>
        <p>Receive Address: <span class="wc_veruspay_modal_address"></span></p>
        <p></p>
        <p><span class="wc_veruspay_modal_button" id="wc_veruspay_modal_button-cancel">Cancel</span><span class="wc_veruspay_modal_button" id="wc_veruspay_modal_button-cashout">Cashout</span></p>
    </div>
</div>
<div class="wc_veruspay_cashout_processing-modalback" style="display:none;">
    <div class="wc_veruspay_cashout_processing-modalinner">
        <h4>Processing...</h4>
    </div>
</div>
<div class="wc_veruspay_cashout_complete-modalback" style="display:none;">
    <div class="wc_veruspay_cashout_complete-modalinner">
        <div class="wc_veruspay_cashout_complete-modalcontent"></div>
        <p><span class="wc_veruspay_modal_button" id="wc_veruspay_modal_complete_button-close">Close</span></p>
    </div>
</div>