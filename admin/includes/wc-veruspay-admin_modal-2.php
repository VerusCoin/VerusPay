<?php 
// No Direct Access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>
<p>
    <span class="wc_veruspay_weight-bold">Store Address: </span><?php echo $key; ?>
</p>
<p>
    <span class="wc_veruspay_weight-bold">Cashout Address: </span><?php echo $item['cashout_address']; ?>
</p>
<p>
    <span class="wc_veruspay_weight-bold">Amount: </span><?php echo $item['amount']; ?>
</p>
<p>
    <span class="wc_veruspay_weight-bold">Opid: </span><?php echo $item['opid']; ?>
</p>
<p class="wc_veruspay_border-black"></p>';