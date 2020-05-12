<?php
// No Direct Access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Display Crypto Address and amount owed or received on Admin order edit page  
 * 
 * @param string[] $order
 */

function wc_veruspay_display_crypto_address_in_admin( $order ) {
	global $wc_veruspay_global;
	$order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
	$wc_veruspay_payment_method = $order->get_payment_method();
	if ( $wc_veruspay_payment_method == $wc_veruspay_global['id'] ){
		$wc_veruspay_order_status = get_post_meta( $order_id, '_wc_veruspay_status', TRUE );
		if ( $wc_veruspay_order_status == 'noaddress' ) {
			foreach  ( $order->get_items() as $item_key => $item_values) {                             
				$wc_veruspay_stock = get_post_meta( $item_values['variation_id'], '_manage_stock', TRUE );                                
			}
			update_post_meta( $order_id, '_wc_veruspay_status', sanitize_text_field( 'cancelled' ) );
			$order->update_status( 'cancelled', __( 'Missing Payment Address', 'woocommerce') );
			header("Refresh:0");
		}
		else {
			if ( $order->has_status( 'completed' ) ) {
				$wc_veruspay_payment_status = 'Received';
				$wc_veruspay_price = get_post_meta( $order_id, '_wc_veruspay_paid', TRUE );
			}
			else {
				$wc_veruspay_payment_status = 'Pending'; 
				$wc_veruspay_price = get_post_meta( $order_id, '_wc_veruspay_price', TRUE ); 
			}
			$wc_veruspay_address = get_post_meta( $order_id, '_wc_veruspay_address', TRUE );
			$_chain_up = strtoupper( get_post_meta( $order_id, '_wc_veruspay_coin', TRUE ) );
			$_chain_lo = strtolower( $_chain_up );
			echo '<style>.wc-order-totals-items{height:3rem!important}.wc-order-totals-items:after{content: "' . $_chain_up . ' ' . $wc_veruspay_payment_status . ': ' . $wc_veruspay_price . '"!important;position:relative;font-size:1rem;font-weight:bold;color:#007bff!important;top:0;float:right;width:200px;height:30px;}</style>';
			echo '<p><strong>'.__( $_chain_up . ' Price', 'woocommerce' ).':</strong>' . $wc_veruspay_price . ' with exchange rate of ' . get_post_meta( $order_id, '_wc_veruspay_rate', TRUE ) . '</p>';
			if ( substr($wc_veruspay_address, 0, 2) !== 'zs' ) {
				echo '<p><strong>'.__( $_chain_up . ' Address', 'woocommerce' ).':</strong> <a target="_BLANK" href="' . $wc_veruspay_global['chain_dtls'][$_chain_lo]['address'] . $wc_veruspay_address . '">' . $wc_veruspay_address . '</a></p>';
			}
			else {
				echo '<p><strong>'.__( $_chain_up . ' Address', 'woocommerce' ).':</strong> '.$wc_veruspay_address.'</p';
			}
		}
	}
}			
/**
 * Add 'Crypto' column header to 'Orders' page immediately after 'Total' column.
 * 
 * @param string[] $columns
 * @return string[] $wc_veruspay_new_columns
 */

function wc_veruspay_add_order_column_header( $columns ) {
    $wc_veruspay_new_columns = array();
    foreach ( $columns as $column_name => $column_info ) {
		$wc_veruspay_new_columns[ $column_name ] = $column_info;
		if ( 'order_status' == $column_name ) {
			$wc_veruspay_new_columns['verus_addr'] = __( 'Crypto Address', 'veruspay-verus-gateway' );
		}
        if ( 'order_total' == $column_name ) {
            $wc_veruspay_new_columns['verus_total'] = __( 'Crypto Total', 'veruspay-verus-gateway' );
        }
    }
    return $wc_veruspay_new_columns;
}
/** 
 * Add 'VRSC' column content to 'Orders' page immediately after 'Total' column.
 * 
 * @param string[] $column
 */

function wc_veruspay_add_order_column_content( $column ) {
		global $post;
		global $wc_veruspay_global;
		$order = wc_get_order( $post->ID );
		if ( $order->get_payment_method() == $wc_veruspay_global['id'] ){
			$order_id = $post->ID;
			$_chain_up = strtoupper( get_post_meta( $order_id, '_wc_veruspay_coin', TRUE ) );
			$_chain_lo = strtolower( $_chain_up );
			$wc_veruspay_order_status = get_post_meta( $order_id, '_wc_veruspay_status', TRUE );
			if ( $wc_veruspay_order_status == 'noaddress' ) {
				update_post_meta( $order_id, '_wc_veruspay_status', sanitize_text_field( 'cancelled' ) );
				$order->update_status( 'cancelled', __( 'Missing Payment Address', 'woocommerce') );
				header("Refresh:0");
			}
			if ( 'verus_addr' == $column ) {
				$wc_veruspay_address = get_post_meta( $order_id, '_wc_veruspay_address', TRUE );
				if ( isset( $wc_veruspay_global['chain_dtls'][$_chain_lo] ) ) {
					if ( substr( $wc_veruspay_address, 0, 2 ) !== 'zs' ) {
						echo '<span class="wc_veruspay_blue"><a target="_BLANK" href="' . $wc_veruspay_global['chain_dtls'][$_chain_lo]['address'] . $wc_veruspay_address . '">' . $wc_veruspay_address . '</a></span>';
					} 
					else {
						echo '<span class="wc_veruspay_blue">' . $wc_veruspay_address . '</span>';
					}
				}
				else {
					echo '<span class="wc_veruspay_blue">' . $_chain_up . ' / rec addr: ' . $wc_veruspay_address . '</span>';
				}
			}
			if ( 'verus_total' == $column ) {
				if ( get_post_meta( $order_id, '_wc_veruspay_status', TRUE ) == 'paid' ) {
					echo '<span class="wc_veruspay_dark">' . get_post_meta( $order_id, '_wc_veruspay_paid', TRUE ) . ' ' . $_chain_up . '</span>';
				}
				else if ( $order->has_status( 'completed' ) ) {
					echo '<span class="wc_veruspay_blue">' . get_post_meta( $order_id, '_wc_veruspay_paid', TRUE ) . ' ' . $_chain_up . '</span>';
				}
				else {
					echo '<span class="wc_veruspay_dark wc_veruspay_italic">' . get_post_meta( $order_id, '_wc_veruspay_price', TRUE ) . ' ' . $_chain_up . '</span>';
				}
			}
		}
}
/**update_post_meta( $order_id, '_wc_veruspay_paid', sanitize_text_field( $wc_veruspay_balance ) );
 * Adjust the style for the Crypto Address column
 */

function wc_veruspay_address_column_style() {

	$wc_veruspay_css = '.column-order_status{width:6ch!important;}
			.column-verus_addr{width:16ch!important;}
			.column-order_total{width:4ch!important;}
			.column-verus_total{width:8ch!important;text-align:right!important;}';
    wp_add_inline_style( 'woocommerce_admin_styles', $wc_veruspay_css );
}