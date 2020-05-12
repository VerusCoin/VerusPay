<?php
// No Direct Access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * VerusPay Price Refresh 
 * 
 * Ajax function for refreshing price data
 */
function wc_veruspay_price_refresh() {
	$_chain_up = strtoupper( sanitize_text_field( $_POST['coin'] ) );
	$_currency = get_woocommerce_currency();
	echo wc_veruspay_price( $_chain_up, $_currency );
}
/**
 * VerusPay Cashout Do
 * 
 * Ajax function for performing cashout requests
 */
function wc_veruspay_cashout_do() {
	global $wc_veruspay_global;
	$wc_veruspay_chains = get_option( $wc_veruspay_global['wc'] )['wc_veruspay_chains'];
	$vtype = sanitize_text_field( $_POST['type'] );
	$_chain_up = strtoupper( sanitize_text_field( $_POST['coin'] ) );
	$_chain_lo = strtolower( $_chain_up );
	if ( $vtype == 'cashout_t' ) {
		$wc_veruspay_cashout_results = wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, $vtype );
		require_once( $wc_veruspay_global['paths']['admin_modal-0'] );
	}
	if ( $vtype == 'cashout_z' ) {
		$wc_veruspay_cashout_results = json_decode( wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, $vtype ), TRUE );
		require_once( $wc_veruspay_global['paths']['admin_modal-1'] );
		foreach( $wc_veruspay_cashout_results as $key => $item ) {
			require_once( $wc_veruspay_global['paths']['admin_modal-2'] );
		}
	}
}
/**
 * VerusPay Balance Refresh
 * 
 * Ajax function for refreshing wallet balance data
 */
function wc_veruspay_balance_refresh() {
	global $wc_veruspay_global;
	$wc_veruspay_chains = get_option( $wc_veruspay_global['wc'] )['wc_veruspay_chains'];
	$ctype = sanitize_text_field( $_POST['type'] );
	$_chain_up = strtoupper( sanitize_text_field( $_POST['coin'] ) );
	$_chain_lo = strtolower( $_chain_up );
	$wc_veruspay_balance_refresh = json_decode( wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'bal' ), TRUE )[$ctype];
	if ( strpos( $wc_veruspay_balance_refresh, 'Not Found' ) !== FALSE ) {
		echo 'Err: ' . $wc_veruspay_global['text_help']['admin_0'];
	}
	else if ( number_format( $wc_veruspay_balance_refresh, 8) == NULL ) {
		echo 'Err: ' . $wc_veruspay_global['text_help']['admin_1'];
	}
	else {
		echo number_format( $wc_veruspay_balance_refresh, 8);
	}
}
/** 
 * VerusPay Generate Control
 * 
 * Ajax function for controlling generate/staking and getting status
 */
function wc_veruspay_generate_ctrl() {
	global $wc_veruspay_global;
	$wc_veruspay_chains = get_option( $wc_veruspay_global['wc'] )['wc_veruspay_chains'];
	$_gentype = sanitize_text_field( $_POST['gentype'] );
	$_threads = sanitize_text_field( $_POST['threads'] );
	$_chain_up = strtoupper( sanitize_text_field( $_POST['coin'] ) );
	$_chain_lo = strtolower( $_chain_up );
	// Disable all generate first
	wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'setgenerate', json_encode( array( FALSE ), TRUE ) );
	$_get_stat = json_decode( wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'getgenerate' ), TRUE );
	
	while ( $_get_stat['generate'] == TRUE ) {
		wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'setgenerate', json_encode( array( FALSE ), TRUE ) );
		$_get_stat = json_decode( wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'getgenerate' ), TRUE );
	}

	if ( ! isset( $_get_stat['generate'] ) || empty( $_get_stat ) || $_get_stat['generate'] != 0 || $_get_stat['generate'] != FALSE ) {
		echo '2';
		die();
	}
	switch( $_gentype ) {
		case 'stakeOn':
			wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'setgenerate', json_encode( array( TRUE, 0 ), TRUE ) );
			$_get_stat = json_decode( wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'getgenerate' ), TRUE );
			// Keep trying to start staking
			while ( $_get_stat['staking'] != TRUE ) {
				if ( $_get_stat['staking'] != 0 || $_get_stat['staking'] != FALSE ) {
					echo '2';
					die();
				}
				else {
					wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'setgenerate', json_encode( array( TRUE, 0 ), TRUE ) );
					$_get_stat = json_decode( wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'getgenerate' ), TRUE );
				}
			}
			echo '1';
			die();
		case 'mineOn':
			wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'setgenerate', json_encode( array( TRUE, (int)$_threads ), TRUE ) );
			$_get_stat = json_decode( wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'getgenerate' ), TRUE );
			// Keep trying to start staking
			while ( $_get_stat['generate'] != TRUE ) {
				if ( $_get_stat['generate'] != 0 || $_get_stat['generate'] != FALSE ) {
					echo '2';
					die();
				}
				else {
					wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'setgenerate', json_encode( array( TRUE, (int)$_threads ), TRUE ) );
					$_get_stat = json_decode( wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'getgenerate' ), TRUE );
				}
			}
			echo '1';
			die();
		case 'generateOn':
			wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'setgenerate', json_encode( array( TRUE, 0 ), TRUE ) );
			wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'setgenerate', json_encode( array( TRUE, (int)$_threads ), TRUE ) );
			$_get_stat = json_decode( wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'getgenerate' ), TRUE );
			// Keep trying to start staking
			while ( $_get_stat['staking'] != TRUE ) {
				if ( $_get_stat['staking'] != 0 || $_get_stat['staking'] != FALSE ) {
					echo '2';
					die();
				}
				else {
					wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'setgenerate', json_encode( array( TRUE, 0 ), TRUE ) );
					wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'setgenerate', json_encode( array( TRUE, (int)$_threads ), TRUE ) );
					$_get_stat = json_decode( wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'getgenerate' ), TRUE );
				}
			}
			echo '1';
			die();
		case 'generateOff':
			$_get_stat = json_decode( wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'getgenerate' ), TRUE );
			while ( $_get_stat['generate'] == TRUE ) {
				if ( $_get_stat['generate'] != 0 || $_get_stat['generate'] != FALSE ) {
					echo '2';
					die();
				}
				else {
					wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'setgenerate', json_encode( array( FALSE ), TRUE ) );
					$_get_stat = json_decode( wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'getgenerate' ), TRUE );
				}
			}
			echo '1';
			die();
	}
}