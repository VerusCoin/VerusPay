<?php
/**
 * Plugin Name: VerusPay Verus Gateway
 * Plugin URI: https://wordpress.org/plugins/veruspay-verus-gateway/
 * Description: Accept Verus Coin (VRSC), Pirate (ARRR), and Komodo (KMD) cryptocurrencies in your online WooCommerce store for physical or digital products.
 * Version: 0.5.1
 * Author: Oliver Westbrook
 * Author URI: https://profiles.wordpress.org/veruspay/
 * Copyright: (c) 2019 John Oliver Westbrook (johnwestbrook@pm.me)
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: veruspay-verus-gateway
 * Domain Path: /i18n/languages/
 * Tested up to: 5.2.3
 * WC requires at least: 3.5.6
 * WC tested up to: 3.7.0
 */
// No Direct Access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Setup and Config
 * 
 * Setup globals, locale, lang helper, requires/includes, etc
 */
// Get variable and conditional paths
$wc_veruspay_id = 'veruspay_verus_gateway';
$wc_veruspay_pre = 'woocommerce_';
$wc_veruspay_apd = '_settings';
$wc_veruspay_woo = 'woocommerce/woocommerce.php';
$wc_veruspay_ver = '0.5.1';
$wc_veruspay_default_coin = 'VRSC';
$wc_veruspay_vrsc_qr_version = '0.1.0';
$wc_veruspay_io = 'https://veruspay.veruscoin.io/ext/';
$wc_veruspay_coingeckoapi = 'https://api.coingecko.com/api/v3/';
$wc_veruspay_root = plugin_dir_path( __FILE__ );
$wc_veruspay_extroot = plugins_url( '/', __FILE__ );
if ( file_exists( plugin_dir_path( __FILE__ ) . 'languages/' . get_locale() . '_helper_text.php' ) ) {
	$wc_veruspay_locale = get_locale();
}
else { // Default language/locale
	$wc_veruspay_locale = 'en_US';
}
require_once( $wc_veruspay_root . 'languages/' . $wc_veruspay_locale . '_helper_text.php' );
require_once( $wc_veruspay_root . 'includes/wc-veruspay-chaintools.php' );
// Setup Global Array
global $wc_veruspay_global;
$wc_veruspay_global = array(
	'version' => $wc_veruspay_ver,
	'default_coin' => $wc_veruspay_default_coin,
	'vrscqrver' => $wc_veruspay_vrsc_qr_version,
	'locale' => get_locale(),
	'id' => $wc_veruspay_id,
	'wc' => $wc_veruspay_pre . $wc_veruspay_id . $wc_veruspay_apd,
	'paths' => array(
		'root' => $wc_veruspay_root,
		'site' => get_site_url(),
		'class_path' => $wc_veruspay_root . 'includes/wc-veruspay-class.php',
		'chkt_path' => $wc_veruspay_root . 'includes/wc-veruspay-checkout.php',
		'conf_path' => $wc_veruspay_root . 'includes/wc-veruspay-confirming.php',
		'proc_path' => $wc_veruspay_root . 'includes/wc-veruspay-process.php',
		'admin_init' => $wc_veruspay_root . 'admin/includes/wc-veruspay-admin_init.php',
		'admin_plugin' => $wc_veruspay_root . 'admin/includes/wc-veruspay-admin_plugin.php',
		'admin_store' => $wc_veruspay_root . 'admin/includes/wc-veruspay-admin_store.php',
		'admin_ajax' => $wc_veruspay_root . 'admin/includes/wc-veruspay-admin_ajax.php',
		'admin_func' => $wc_veruspay_root . 'admin/includes/wc-veruspay-admin_func.php',
		'admin_modal-0' => $wc_veruspay_root . 'admin/includes/wc-veruspay-admin_modal-0.php',
		'admin_modal-1' => $wc_veruspay_root . 'admin/includes/wc-veruspay-admin_modal-1.php',
		'admin_modal-2' => $wc_veruspay_root . 'admin/includes/wc-veruspay-admin_modal-2.php',
		'admin_modal-3' => $wc_veruspay_root . 'admin/includes/wc-veruspay-admin_modal-3.php',
		'admin_modal-4' => $wc_veruspay_root . 'admin/includes/wc-veruspay-admin_modal-4.php',
		'public' => array(
			'css' => $wc_veruspay_extroot . 'public/css/',
			'js' => $wc_veruspay_extroot . 'public/js/',
			'img' => $wc_veruspay_extroot . 'public/img/'
		),
		'admin' => array(
			'css' => $wc_veruspay_extroot . 'admin/css/',
			'js' => $wc_veruspay_extroot . 'admin/js/',
		),
		'ext' => array(
			'coingeckoapi' => $wc_veruspay_coingeckoapi,
		),
	),
	'chain_list' => array('vrsc' => 1, 'fiat' => 4),
	'chain_dtls' => array(
		'vrsc' => array(
	'address' => 'https://explorer.veruscoin.io/address/',
	'getaddress' => 'https://explorer.veruscoin.io/ext/getaddress/',
	'gettx' => 'https://explorer.veruscoin.io/api/getrawtransaction?txid=',
	'txname' => 'last_txs',
	'txlevel' => 'addresses',
    	'txtrail' => '&decrypt=1',
    	'htname' => 'height',
    	'info' => 'https://vrsc.explorer.dexstats.info/insight-api-komodo/status?q=getinfo',
    	'blocks' => 'blocks',
	'difficulty' => 'difficulty',
	'supply' => 'https://explorer.veruscoin.io/ext/getmoneysupply',
	),
	'fiat' => array(
		'api'  => 'https://veruspay.veruscoin.io/',
		'currency'  => 'USD',
		),
	),
	'coinimg' => $wc_veruspay_io,
	'text_help' => $wc_veruspay_text_help[$wc_veruspay_locale],
	'chain_ref' => array(),
	'chains' => array(
		'daemon' => array(),
		'manual' => array(),
		'hosted' => array(),
	),
);
/**
 * WooCommerce Active
 * 
 * Check if woocommerce is active before loading plugin
 */
if ( ! in_array( $wc_veruspay_woo, apply_filters('active_plugins', get_option( 'active_plugins' ) ) ) ) {
	return;
}
/**
 * Filters & Actions
 * 
 * If WooCommerce is active, add filters and actions
 */
add_filter( 'woocommerce_payment_gateways', 'wc_veruspay_add_to_gateways' );
if ( is_admin() ) {
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wc_veruspay_plugin_links' );
	add_action( 'admin_menu', 'wc_veruspay_settings_menu' );
	require_once( $wc_veruspay_global['paths']['admin_plugin'] );
}
add_action( 'plugins_loaded', 'wc_veruspay_init', 11 );
add_action( 'woocommerce_cart_calculate_fees', 'wc_veruspay_order_total_update', 1, 1 );
add_filter( 'woocommerce_available_payment_gateways', 'wc_veruspay_button_text' );
add_action( 'woocommerce_checkout_update_order_meta', 'wc_veruspay_save_custom_meta' );
if ( is_admin() ) {
	add_action( 'woocommerce_admin_order_data_after_billing_address', 'wc_veruspay_display_crypto_address_in_admin', 10, 1 );
	add_filter( 'manage_edit-shop_order_columns', 'wc_veruspay_add_order_column_header', 20 );
	add_action( 'manage_shop_order_posts_custom_column', 'wc_veruspay_add_order_column_content' );
	add_action( 'admin_print_styles', 'wc_veruspay_address_column_style' );
	require_once( $wc_veruspay_global['paths']['admin_store'] );
}
add_action( 'woocommerce_order_status_on-hold', 'wc_veruspay_set_address' );
add_action( 'woocommerce_order_details_before_order_table', 'wc_veruspay_order_received_body' );
add_filter( 'the_title', 'wc_veruspay_title_order_received', 99, 2 );
add_filter( 'woocommerce_get_order_item_totals', 'wc_veruspay_add_total', 30, 3 );
add_action( 'woocommerce_order_status_changed', 'wc_veruspay_notify_order_status' );
add_filter( 'cron_schedules','wc_veruspay_cron_schedules' );
add_action( 'woocommerce_cancel_unpaid_submitted', 'wc_veruspay_check_order_status' );
/**
 * Add the VerusPay gateway to WC Available Gateways
 * 
 * @since 0.1.0
 * @param array $gateways
 * @return array $gateways
 */
function wc_veruspay_add_to_gateways( $gateways ) {
	$gateways[] = 'WC_Gateway_VerusPay';
	return $gateways;
}
/**
 * VerusPay Payment Gateway
 *
 * Main class provides a payment gateway for accepting cryptocurrencies - cryptos supported: VRSC, ARRR
 * We load it later to ensure WC is loaded first since we're extending it.
 *
 * @class 		WC_Gateway_VerusPay
 * @extends		WC_Payment_Gateway
 * @since		0.1.0
 * @package		WooCommerce/Classes/Payment
 * @author 		Oliver Westbrook
 * @param global $wc_veruspay_global
 */
function wc_veruspay_init() {
	global $wc_veruspay_global;
	require_once( $wc_veruspay_global['paths']['class_path'] );
	if ( is_admin() ) {
		add_action( 'wp_ajax_wc_veruspay_price_refresh', 'wc_veruspay_price_refresh' );
        add_action( 'wp_ajax_wc_veruspay_cashout_do', 'wc_veruspay_cashout_do' );
        add_action( 'wp_ajax_wc_veruspay_balance_refresh', 'wc_veruspay_balance_refresh' );
		add_action( 'wp_ajax_wc_veruspay_generate_ctrl', 'wc_veruspay_generate_ctrl' );
		require_once( $wc_veruspay_global['paths']['admin_ajax'] );
		require_once( $wc_veruspay_global['paths']['admin_func'] );
	}
}
/**
 * Update Order Total
 * @param global $wc_veruspay_global
 */
function wc_veruspay_order_total_update() {
	global $wc_veruspay_global;
	$wc_veruspay_settings = get_option( $wc_veruspay_global['wc'] );
	if ( $wc_veruspay_settings['enabled'] == 'yes' ) {
		$wc_veruspay_payment_method = WC()->session->get('chosen_payment_method');
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}
		if( is_checkout() && $wc_veruspay_payment_method == $wc_veruspay_global['id'] && $wc_veruspay_settings['discount_fee'] == 'yes' ) {
			$_dis_amnt = ( $wc_veruspay_settings['disc_amt'] / 100 );
			$wc_veruspay_discount = WC()->cart->subtotal * $_dis_amnt;
			WC()->cart->add_fee( __( $wc_veruspay_settings['disc_title'], 'veruspay-verus-gateway' ) , $wc_veruspay_settings['disc_type'] . $wc_veruspay_discount );
		}
	}
}
/**
 * Check if price Apis are working (disable VerusPay if not) and set VerusPay payment gateway button text
 * 
 * @param string[] $available_gateways
 * @param global $wc_veruspay_global
 * @return string[] $available_gateways
 */

function wc_veruspay_button_text( $available_gateways ) {
	global $wc_veruspay_global;
	$wc_veruspay_settings = get_option( $wc_veruspay_global['wc'] );
	if ( ! is_checkout() ) return $available_gateways;
	// Check if result from price API
	if ( wc_veruspay_price( 'VRSC', get_woocommerce_currency() ) == 0 ) {
		unset( $available_gateways[$wc_veruspay_global['id']] );
	}
	// Otherwise set button text
	else if ( array_key_exists( $wc_veruspay_global['id'], $available_gateways ) && $wc_veruspay_settings['test_mode'] == 'no' ) {
		$available_gateways[$wc_veruspay_global['id']]->order_button_text = __( $wc_veruspay_global['text_help']['payment_button'], 'woocommerce' );
	}
	// If test mode and not admint, disable gateway
	else if ( $wc_veruspay_settings['test_mode'] == 'yes' && ! current_user_can( 'administrator' ) ) {
		unset( $available_gateways[$wc_veruspay_global['id']] );
	}
	return $available_gateways;
}
/**
 * Save Order meta data 
 * 
 * @param string[] $order_id
 */
function wc_veruspay_save_custom_meta( $order_id ) {
	global $wc_veruspay_global;
	$wc_veruspay_payment_method = WC()->session->get('chosen_payment_method');
	if ( $wc_veruspay_payment_method == $wc_veruspay_global['id'] ){
		if ( ! empty( $_POST['wc_veruspay_coin'] ) ) {
			update_post_meta( $order_id, '_wc_veruspay_coin', strtoupper( sanitize_text_field( $_POST['wc_veruspay_coin'] ) ) );
		}
		if ( ! empty( $_POST['wc_veruspay_address'] ) ) {
			update_post_meta( $order_id, '_wc_veruspay_address', sanitize_text_field( $_POST['wc_veruspay_address'] ) );
		}
		if ( ! empty( $_POST['wc_veruspay_price'] ) ) {
			update_post_meta( $order_id, '_wc_veruspay_price', sanitize_text_field( $_POST['wc_veruspay_price'] ) );
		}
		if ( ! empty( $_POST['wc_veruspay_rate'] ) ) {
			update_post_meta( $order_id, '_wc_veruspay_rate', sanitize_text_field( $_POST['wc_veruspay_rate'] ) );
		}
		if ( ! empty( $_POST['wc_veruspay_pricestart'] ) ) {
			update_post_meta( $order_id, '_wc_veruspay_pricestart', sanitize_text_field( $_POST['wc_veruspay_pricestart'] ) );
		}
		if ( ! empty( $_POST['wc_veruspay_pricetime'] ) ) {
			update_post_meta( $order_id, '_wc_veruspay_pricetime', sanitize_text_field( $_POST['wc_veruspay_pricetime'] ) );
		}
		if ( ! empty( $_POST['wc_veruspay_orderholdtime'] ) ) {
			update_post_meta( $order_id, '_wc_veruspay_orderholdtime', sanitize_text_field( $_POST['wc_veruspay_orderholdtime'] ) );
		}
		if ( ! empty( $_POST['wc_veruspay_confirms'] ) ) {
			update_post_meta( $order_id, '_wc_veruspay_confirms', sanitize_text_field( $_POST['wc_veruspay_confirms'] ) );
		}
		if ( ! empty( $_POST['wc_veruspay_sapling'] ) ) {
			update_post_meta( $order_id, '_wc_veruspay_sapling', sanitize_text_field( $_POST['wc_veruspay_sapling'] ) );
		}
		if ( ! empty( $_POST['wc_veruspay_status'] ) ) {
			update_post_meta( $order_id, '_wc_veruspay_status', sanitize_text_field( $_POST['wc_veruspay_status'] ) );
		}
		if ( ! empty( $_POST['wc_veruspay_order_block'] ) ) {
			update_post_meta( $order_id, '_wc_veruspay_order_block', sanitize_text_field( $_POST['wc_veruspay_order_block'] ) );
		}
		if ( ! empty( $_POST['wc_veruspay_img'] ) ) {
			update_post_meta( $order_id, '_wc_veruspay_img', sanitize_text_field( $_POST['wc_veruspay_img'] ) );
		}
		if ( ! empty( $_POST['wc_veruspay_memo'] ) ) {
			update_post_meta( $order_id, '_wc_veruspay_memo', sanitize_text_field( $_POST['wc_veruspay_memo'] ) );
		}
	}
}
/** 
 * Build order - Get unused crypto payment address in selected coin
 * 
 * @param string[] $order_id
 * @param global $wc_veruspay_global
 */

function wc_veruspay_set_address( $order_id ) {
	global $wc_veruspay_global;
	$wc_veruspay_settings = get_option( $wc_veruspay_global['wc'] );
	$wc_veruspay_chains = $wc_veruspay_settings['wc_veruspay_chains'];
	// Only proceed if processing a VerusPay payment
	if ( ! empty( get_post_meta( $order_id, '_wc_veruspay_coin', TRUE ) ) ) {
		// If store is in Native mode and reachable, get a fresh address
		$_chain_up = strtoupper( get_post_meta( $order_id, '_wc_veruspay_coin', TRUE ) );
		$_chain_lo = strtolower( $_chain_up );
		if ( $wc_veruspay_chains['daemon'][$_chain_up]['EN'] == 'yes' ) {
			if ( $wc_veruspay_chains['daemon'][$_chain_up]['ST'] == 1 ) {
				if ( get_post_meta( $order_id, '_wc_veruspay_sapling', TRUE ) == 'yes' ) { // If Sapling is enabled, get a sapling address
					$wc_veruspay_address = wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'z_getnewaddress' );
					while ( wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'z_getbalance', json_encode( $wc_veruspay_address, TRUE ) ) > 0 ) {
						$wc_veruspay_address = wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'z_getnewaddress' );
					} //FUTURE-if ( check address format - if bad return default address; ) { update the meta } else { update the meta with gotten new address }
					update_post_meta( $order_id, '_wc_veruspay_address', sanitize_text_field( $wc_veruspay_address ) );
					update_post_meta( $order_id, '_wc_veruspay_mode', sanitize_text_field( 'live' ) ); // May not rely on this in the future, may change to live wallet uptime at checkout
					$wc_veruspay_order_time = strtotime(date("Y-m-d H:i:s", time()));
					update_post_meta( $order_id, '_wc_veruspay_ordertime', sanitize_text_field( $wc_veruspay_order_time ) );
				}
				else { // If Not Sapling, get Transparent
					$wc_veruspay_address = wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'getnewaddress' );
					while ( wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'z_getbalance', json_encode( $wc_veruspay_address, TRUE ) ) > 0 ) {
						$wc_veruspay_address = wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'getnewaddress' );
					}
						//FUTURE-if ( check address format - if bad return default address; ) { update the meta } else { update the meta with gotten new address }
					update_post_meta( $order_id, '_wc_veruspay_address', sanitize_text_field( $wc_veruspay_address ) );
					update_post_meta( $order_id, '_wc_veruspay_mode', sanitize_text_field( 'live' ) );
					$wc_veruspay_order_time = strtotime(date("Y-m-d H:i:s", time()));
					update_post_meta( $order_id, '_wc_veruspay_ordertime', sanitize_text_field( $wc_veruspay_order_time ) );
				}
			}
			// If wallet stat is false (manual mode) // $wc_veruspay_chains['daemon'][$_chain_up]['ST'] != 1 &&
			else if ( $wc_veruspay_chains['daemon'][$_chain_up]['ST'] != 1 ) {
				$wc_veruspay_address = reset( $wc_veruspay_chains['daemon'][$_chain_up]['AD'] );
				// Check if chain is supported in VerusPay API, if not manual approval by store owner
				if ( isset( $wc_veruspay_global['chain_dtls'][$_chain_lo] ) ) {
					// TODO: Future work, manual mode should not be set inside ['daemon'], this is a temporary configuration
					while ( is_numeric( wc_veruspay_get( $_chain_up, 'getbalance', $wc_veruspay_address ) ) && wc_veruspay_get( $_chain_up, 'getbalance', $wc_veruspay_address ) > 0 ) {
						if ( ( $wc_veruspay_key = array_search( $wc_veruspay_address, $wc_veruspay_chains['daemon'][$_chain_up]['AD'] ) ) !== FALSE ) {
							unset( $wc_veruspay_chains['daemon'][$_chain_up]['AD'][$wc_veruspay_key] );
						}
						$wc_veruspay_settings[$_chain_lo . '_storeaddresses'] = implode( ','.PHP_EOL, $wc_veruspay_chains['daemon'][$_chain_up]['AD'] );
						array_push( $wc_veruspay_chains['daemon'][$_chain_up]['UD'], $wc_veruspay_address );
						$wc_veruspay_settings[$_chain_lo . '_usedaddresses'] = trim( implode( ','.PHP_EOL, $wc_veruspay_chains['daemon'][$_chain_up]['UD'] ),"," );
						update_option( $wc_veruspay_global['wc'], $wc_veruspay_settings );
						$wc_veruspay_address = reset( $wc_veruspay_chains['daemon'][$_chain_up]['AD'] );
						if( strlen( $wc_veruspay_address ) < 10 ) {
							update_post_meta( $order_id, '_wc_veruspay_address', sanitize_text_field( 'MissingAddress-1' ) );
							update_post_meta( $order_id, '_wc_veruspay_status', sanitize_text_field( 'noaddress' ) );
							die( json_encode( 'Error1', TRUE ) );
						}
					}
					update_post_meta( $order_id, '_wc_veruspay_mode', sanitize_text_field( 'manual' ) );
				}
				else {
					if ( ( $wc_veruspay_key = array_search( $wc_veruspay_address, $wc_veruspay_chains['daemon'][$_chain_up]['AD'] ) ) !== FALSE ) {
						unset( $wc_veruspay_chains['daemon'][$_chain_up]['AD'][$wc_veruspay_key] );
					}
					$wc_veruspay_settings[$_chain_lo . '_storeaddresses'] = implode( ','.PHP_EOL, $wc_veruspay_chains['daemon'][$_chain_up]['AD'] );
					array_push( $wc_veruspay_chains['daemon'][$_chain_up]['UD'], $wc_veruspay_address );
					$wc_veruspay_settings[$_chain_lo . '_usedaddresses'] = trim( implode( ','.PHP_EOL, $wc_veruspay_chains['daemon'][$_chain_up]['UD'] ),"," );
					update_option( $wc_veruspay_global['wc'], $wc_veruspay_settings );
					if( strlen( $wc_veruspay_address ) < 10 ) {
						update_post_meta( $order_id, '_wc_veruspay_address', sanitize_text_field( 'MissingAddress-2' ) );
						update_post_meta( $order_id, '_wc_veruspay_status', sanitize_text_field( 'noaddress' ) );
						die( json_encode( 'Error2', TRUE ) );
					}
					update_post_meta( $order_id, '_wc_veruspay_mode', sanitize_text_field( 'hold' ) );
				}
				update_post_meta( $order_id, '_wc_veruspay_address', sanitize_text_field( $wc_veruspay_address ) );
				$wc_veruspay_order_time = strtotime(date("Y-m-d H:i:s", time()));
				update_post_meta( $order_id, '_wc_veruspay_ordertime', sanitize_text_field( $wc_veruspay_order_time ) );
			}
			else {
				update_post_meta( $order_id, '_wc_veruspay_address', sanitize_text_field( 'MissingAddress-3' ) );
				update_post_meta( $order_id, '_wc_veruspay_status', sanitize_text_field( 'noaddress' ) );
				die( json_encode( 'Error3', TRUE ) );
			}
		}
		else {
			update_post_meta( $order_id, '_wc_veruspay_address', sanitize_text_field( 'MissingAddress-4' ) );
			update_post_meta( $order_id, '_wc_veruspay_status', sanitize_text_field( 'noaddress' ) );
			die( json_encode( 'Error4', TRUE ) );
		}
	}
}
/** 
 * Setup order hold / received page - Check for payment
 * 
 * @param string[] $order
 * @param global $wc_veruspay_global
 */

function wc_veruspay_order_received_body( $order ) {
	global $wc_veruspay_global;
	$wc_veruspay_settings = get_option( $wc_veruspay_global['wc'] );
	$wc_veruspay_payment_method = $order->get_payment_method();
	// Get order data
	$order_id = $order->get_id();
	$wc_veruspay_order_status = get_post_meta( $order_id, '_wc_veruspay_status', TRUE );
	$_chain_up = strtoupper( get_post_meta( $order_id, '_wc_veruspay_coin', TRUE ) );
	$_chain_lo = strtolower( $_chain_up );
	if ( function_exists( 'is_order_received_page' ) && $wc_veruspay_payment_method == $wc_veruspay_global['id'] && is_order_received_page() ) {
		//
		// On order placed, on-hold while waiting for payment - Check for payment received on page (also in cron)
		if ( $order->has_status( 'on-hold' ) ) {
			$wc_veruspay_address = get_post_meta( $order_id, '_wc_veruspay_address', TRUE ); // Get the crypto payment address setup for this order at time order was placed
			if ( $wc_veruspay_order_status == 'noaddress' ) {
				foreach  ( $order->get_items() as $item_key => $item_values) {                             
					$wc_veruspay_stock = get_post_meta( $item_values['variation_id'], '_manage_stock', TRUE );                                
				}
				echo '<div class="wc_veruspay_overlay-light"></div>';
				update_post_meta( $order_id, '_wc_veruspay_status', sanitize_text_field( 'cancelled' ) );
				$order->update_status( 'cancelled', __( 'Missing Payment Address', 'woocommerce') );
				header("Refresh:0");
			}
			// Hide order overview if crypto payment 
			echo '<style>ul.woocommerce-thankyou-order-details,p.woocommerce-thankyou-order-received{display:none!important;}</style>';
			$wc_veruspay_price = get_post_meta( $order_id, '_wc_veruspay_price', TRUE ); // Get the crypto price price active at time order was placed (within the timeout period)
			$wc_veruspay_hold_time = get_post_meta( $order_id, '_wc_veruspay_orderholdtime', TRUE );  // Get order hold timeout value active at time order was placed
			$wc_veruspay_qr_inv_array = array( // Future Feature
				'verusQR' => $wc_veruspay_settings['vrscqrver'],
				'coinTicker' => $_chain_up,
				'address' => $wc_veruspay_address,
				'amount' => floor(round(str_replace(',', '', $wc_veruspay_price)*100000000)),
				'memo' => get_post_meta( $order_id, '_wc_veruspay_memo', TRUE ),
				'image' => urlencode( get_post_meta( $order_id, '_wc_veruspay_img', TRUE ) ),
			);
			if ( get_post_meta( $order_id, '_wc_veruspay_sapling', TRUE ) != 'yes' ) {
				$wc_veruspay_qr_inv_code = wc_veruspay_qr( urlencode( json_encode( $wc_veruspay_qr_inv_array, TRUE ) ), $wc_veruspay_settings['qr_max_size'] ); // Get QR code to match Verus invoice in VerusQR JSON format
				$wc_veruspay_qr_toggle_show = ' ';
				$wc_veruspay_qr_toggle_width = ' ';
			}
			if ( get_post_meta( $order_id, '_wc_veruspay_sapling', TRUE ) == 'yes' ) {
				$wc_veruspay_qr_toggle_show = 'wc_veruspay_qr_block_noinv';
				$wc_veruspay_qr_toggle_width = 'wc_veruspay_qr_width_noinv';
			}
			$wc_veruspay_qr_code = wc_veruspay_qr( $wc_veruspay_address, $wc_veruspay_settings['qr_max_size'] ); // Get QR code to match payment address, size set by store owner
			$wc_veruspay_order_mode = get_post_meta( $order_id, '_wc_veruspay_mode', TRUE );
			$wc_veruspay_confirmations = get_post_meta( $order_id, '_wc_veruspay_confirms', TRUE ); // Get current confirm requirement count
			// Setup time and countdown data
			$wc_veruspay_order_time = get_post_meta( $order_id, '_wc_veruspay_ordertime', TRUE ); //strtotime($order->order_date); // Get time of order start - start time to send payment
			$wc_veruspay_time_end = strtotime('+'.$wc_veruspay_hold_time.' minutes', $wc_veruspay_order_time); // Setup countdown target time using order hold time data
			$wc_veruspay_time_start = strtotime(date("Y-m-d H:i:s", time())); // Get time now, used in calculating countdown
			$wc_veruspay_sec_remaining = $wc_veruspay_time_end - $wc_veruspay_time_start; // Get difference between expire time and now in seconds        
			$wc_veruspay_time_remaining = gmdate("i:s", $wc_veruspay_sec_remaining); // Format time-remaining for view
			
			// Get balance of order address for live or manual
			if ( $wc_veruspay_order_mode == 'live' && $wc_veruspay_order_status == 'order' ) {
				$wc_veruspay_balance = wc_veruspay_go( $wc_veruspay_settings['wc_veruspay_chains']['daemon'][$_chain_up]['DC'], $wc_veruspay_settings['wc_veruspay_chains']['daemon'][$_chain_up]['IP'], $_chain_up, 'lowest', json_encode( array( $wc_veruspay_address, 0 ), TRUE ) );
				if ( $wc_veruspay_balance >= $wc_veruspay_price ) {
					$wc_veruspay_balance_in = TRUE;
					update_post_meta( $order_id, '_wc_veruspay_balance_in', sanitize_text_field( 'true' ) );
					update_post_meta( $order_id, '_wc_veruspay_status', sanitize_text_field( 'paid' ) );
					update_post_meta( $order_id, '_wc_veruspay_paid', sanitize_text_field( $wc_veruspay_balance ) );
					$wc_veruspay_blocknow = json_decode( wc_veruspay_go( $wc_veruspay_settings['wc_veruspay_chains']['daemon'][$_chain_up]['DC'], $wc_veruspay_settings['wc_veruspay_chains']['daemon'][$_chain_up]['IP'], $_chain_up, 'getinfo' ), TRUE )['blocks'];
					update_post_meta( $order_id, '_wc_veruspay_order_block', sanitize_text_field( $wc_veruspay_blocknow ) );
					$wc_veruspay_order_note = __( $wc_veruspay_price. ' ' .$wc_veruspay_global['text_help']['msg_received'] );
					$order->add_order_note( $wc_veruspay_order_note );
					$order->save();
					header("Refresh:0");
				}
				else {
					$wc_veruspay_balance_in = FALSE;
					update_post_meta( $order_id, '_wc_veruspay_balance_in', sanitize_text_field( 'false' ) );
				}
			}
			if ( $wc_veruspay_order_mode == 'manual' && $wc_veruspay_order_status == 'order' ) {
				$wc_veruspay_balance = wc_veruspay_get( $_chain_up, 'getbalance', $wc_veruspay_address );
				// If non-number data returned by explorer (case of new address) set returned balance as 0
				if ( ! is_numeric($wc_veruspay_balance) ) {
					$wc_veruspay_balance = 0;
				}
				if ( $wc_veruspay_balance >= $wc_veruspay_price ) {
					$wc_veruspay_balance_in = TRUE;
					update_post_meta( $order_id, '_wc_veruspay_balance_in', sanitize_text_field( 'true' ) );
					update_post_meta( $order_id, '_wc_veruspay_status', sanitize_text_field( 'paid' ) );
					update_post_meta( $order_id, '_wc_veruspay_paid', sanitize_text_field( $wc_veruspay_balance ) );
					$wc_veruspay_blocknow = wc_veruspay_get( $_chain_up, 'getblockcount' );
					update_post_meta( $order_id, '_wc_veruspay_order_block', sanitize_text_field( $wc_veruspay_blocknow ) );
					$wc_veruspay_order_note = __( $wc_veruspay_price. ' ' .$wc_veruspay_global['text_help']['msg_received'] );
					$order->add_order_note( $wc_veruspay_order_note );
					$order->save();
					header("Refresh:0");
				}
				if ( $wc_veruspay_balance <= 0 ) {
					$wc_veruspay_balance_in = FALSE;
					update_post_meta( $order_id, '_wc_veruspay_balance_in', sanitize_text_field( 'false' ) );
				}
			}
			if ( $wc_veruspay_order_mode == 'hold' && $wc_veruspay_order_status == 'order' ) {
				$wc_veruspay_balance_in = FALSE;
				update_post_meta( $order_id, '_wc_veruspay_balance_in', sanitize_text_field( 'false' ) );
			}
			// If balance matches payment due, check confirmations and either keep on-hold or complete
			if ( $wc_veruspay_order_status == 'paid' ) {
				if ( $wc_veruspay_order_mode == 'live' ) {
					$wc_veruspay_balance = wc_veruspay_go( $wc_veruspay_settings['wc_veruspay_chains']['daemon'][$_chain_up]['DC'], $wc_veruspay_settings['wc_veruspay_chains']['daemon'][$_chain_up]['IP'], $_chain_up, 'lowest', json_encode( array( $wc_veruspay_address, $wc_veruspay_confirmations ), TRUE ) );
					if ( $wc_veruspay_balance >= $wc_veruspay_price ) {
						update_post_meta( $order_id, '_wc_veruspay_status', sanitize_text_field( 'completed' ) );
						echo '<div class="wc_veruspay_overlay-light"></div>';
						$order->update_status( 'completed', __( $wc_veruspay_global['text_help']['order_processing'], 'woocommerce') );
						header("Refresh:0");
					}
					else {
						echo '<input type="hidden" name="wc_veruspay_orderholdtime" value="' . $wc_veruspay_hold_time . '">';
						$wc_veruspay_order_block = get_post_meta( $order_id, '_wc_veruspay_order_block', TRUE );
						$wc_veruspay_block_progress = $wc_veruspay_confirmations - ( ( $wc_veruspay_order_block + $wc_veruspay_confirmations + 1 ) - json_decode( wc_veruspay_go( $wc_veruspay_settings['wc_veruspay_chains']['daemon'][$_chain_up]['DC'], $wc_veruspay_settings['wc_veruspay_chains']['daemon'][$_chain_up]['IP'], $_chain_up, 'getinfo' ), TRUE )['blocks'] );
						require_once( $wc_veruspay_global['paths']['conf_path'] );
					}
				}

				if ( $wc_veruspay_order_mode == 'manual' ) {
					$wc_veruspay_confirms = wc_veruspay_get( $_chain_up, 'lowestconfirm', $wc_veruspay_address );
					if ( $wc_veruspay_confirms >= $wc_veruspay_confirmations ) {
						$order->update_status( 'completed', __( $wc_veruspay_global['text_help']['order_processing'], 'woocommerce') );
						update_post_meta( $order_id, '_wc_veruspay_status', sanitize_text_field( 'completed' ) );
						echo '<div class="wc_veruspay_overlay-light"></div>';
						header("Refresh:0");
					}
					else {
						echo '<input type="hidden" name="wc_veruspay_orderholdtime" value="' . $wc_veruspay_hold_time . '">';
						$wc_veruspay_order_block = get_post_meta( $order_id, '_wc_veruspay_order_block', TRUE );
						$wc_veruspay_block_progress = $wc_veruspay_confirmations - ( ( $wc_veruspay_order_block + $wc_veruspay_confirmations + 1 ) - wc_veruspay_get(  $_chain_up, 'getblockcount' ) );
						require_once( $wc_veruspay_global['paths']['conf_path'] );
					}
				}
					
			}

			// If balance is less than payment due within timelimit, cancel the order and set the reason variable
			if ( ! isset( $wc_veruspay_balance_in ) ) {
				$wc_veruspay_balance_in = get_post_meta( $order_id, '_wc_veruspay_balance_in', TRUE );
			}
			if ( $wc_veruspay_balance_in === FALSE && $wc_veruspay_order_mode == 'hold' && $wc_veruspay_sec_remaining <= 0 ) {
				// Add custom set additional post complete sale message
				if ( $wc_veruspay_settings['msg_before_sale'] ) {
					$wc_veruspay_process_custom_msg = $wc_veruspay_settings['msg_before_sale'];
				}
				echo '<input type="hidden" name="wc_veruspay_orderholdtime" value="' . $wc_veruspay_hold_time . '">';
				require_once( $wc_veruspay_global['paths']['proc_path'] );
			}
			if ( $wc_veruspay_balance_in === FALSE && $wc_veruspay_order_mode == 'hold' ) {
				// Add custom set additional post complete sale message
				if ( $wc_veruspay_settings['msg_before_sale'] ) {
					$wc_veruspay_process_custom_msg = $wc_veruspay_settings['msg_before_sale'];
				}
				echo '<input type="hidden" name="wc_veruspay_orderholdtime" value="' . $wc_veruspay_hold_time . '">';
				require_once( $wc_veruspay_global['paths']['proc_path'] );
			}
			if ( $wc_veruspay_balance_in === FALSE && $wc_veruspay_sec_remaining <= 0 && $wc_veruspay_order_mode != 'hold' ) {
				foreach  ( $order->get_items() as $item_key => $item_values) {                             
					$wc_veruspay_stock = get_post_meta( $item_values['variation_id'], '_manage_stock', TRUE );                                
				}
				echo '<div class="wc_veruspay_overlay-light"></div>';
				update_post_meta( $order_id, '_wc_veruspay_status', sanitize_text_field( 'cancelled' ) );
				$order->update_status( 'cancelled', __( $wc_veruspay_global['text_help']['msg_order_cancel_timeout'].$wc_veruspay_hold_time.' min', 'woocommerce') );
				header("Refresh:0");
			}
			if ( $wc_veruspay_balance_in === FALSE && $wc_veruspay_sec_remaining > 0 ) {
				// Add custom set additional post complete sale message
				if ( $wc_veruspay_settings['msg_before_sale'] ) {
					$wc_veruspay_process_custom_msg = $wc_veruspay_settings['msg_before_sale'];
				}
				echo '<input type="hidden" name="wc_veruspay_orderholdtime" value="' . $wc_veruspay_hold_time . '">';
				require_once( $wc_veruspay_global['paths']['proc_path'] );
			}

		}
		// If order is completed it is paid in full and has all confirmations per store owner settings
		else if ( $order->has_status( 'completed' ) ) {
			echo '<style>ul.woocommerce-thankyou-order-details,p.woocommerce-thankyou-order-received{display:none!important;}</style>';
			$order_id = $order->get_id();
			$wc_veruspay_price = get_post_meta( $order_id, '_wc_veruspay_paid', TRUE );
			echo $wc_veruspay_global['text_help']['msg_thank_payment_of'] . $wc_veruspay_price . $wc_veruspay_global['text_help']['msg_received'];
			if ( $wc_veruspay_settings['msg_after_sale'] ) {
				echo wpautop( wptexturize( $wc_veruspay_settings['msg_after_sale'] ) );
			}
		}
		// If order is cancelled for non-full-payment, no payment, or not enough block confirmations
		else if ( $order->has_status( 'cancelled' ) ) {
			echo '<style>ul.woocommerce-thankyou-order-details,p.woocommerce-thankyou-order-received{display:none!important;}</style>';
			echo '<p class="wc_veruspay_cancel_msg">'.$wc_veruspay_global['text_help']['msg_your_order_num'].$order->get_order_number() . $wc_veruspay_global['text_help']['msg_has_cancelled_reason'] . '</p>';
			echo '<p class="wc_veruspay_custom_cancel_msg">' . $wc_veruspay_settings['msg_cancel'] . '</p>';
		}
	}
}

/** 
 * Change Title for Order Status
 * 
 * @param string[] $title, $id
 * @param global $wc_veruspay_global
 * @return string[] $title
 */

function wc_veruspay_title_order_received( $title, $id ) {
	global $wc_veruspay_global;
	if ( function_exists( 'is_order_received_page' ) && is_order_received_page() && get_the_ID() == $id ) {
		global $wp;
		$order_id  = apply_filters( 'woocommerce_thankyou_order_id', absint( $wp->query_vars['order-received'] ) );
		$order = wc_get_order( $order_id );
		if ( $order->get_payment_method() == $wc_veruspay_global['id'] && get_post_meta( $order_id, '_wc_veruspay_status', TRUE ) == 'order' ) {
			$title = $wc_veruspay_global['text_help']['title_ordered'];
		}
		else if ( $order->get_payment_method() == $wc_veruspay_global['id'] && get_post_meta( $order_id, '_wc_veruspay_status', TRUE ) == 'paid' ) {
			$title = $wc_veruspay_global['text_help']['title_pending'];
		}
		else if ( $order->get_payment_method() == $wc_veruspay_global['id'] && $order->has_status( 'completed' ) ) {
			$title = $wc_veruspay_global['text_help']['title_completed'];
		}
		else if ( $order->get_payment_method() == $wc_veruspay_global['id'] && $order->has_status( 'cancelled' ) ) {
			$title = $wc_veruspay_global['text_help']['title_cancelled'];
		}
	}
	return $title;
}
/** 
 * Add crypto total to order details table
 * 
 * @param string[] $total_rows, $order, $tax_display
 * @param global $wc_veruspay_global
 * @return string[] $total_rows
 */

function wc_veruspay_add_total( $total_rows, $order, $tax_display ) {
	global $wc_veruspay_global;
	if ( $order->get_payment_method() == $wc_veruspay_global['id'] ) {
		$order_id = $order->get_id();
		$wc_veruspay_price = get_post_meta( $order_id, '_wc_veruspay_price', TRUE );
		$_chain_up = strtoupper( get_post_meta( $order_id, '_wc_veruspay_coin', TRUE ) );
		$_chain_lo = strtolower( $_chain_up );
		unset( $total_rows['payment_method'] );
		$total_rows['recurr_not'] = array(
			'label' => __( $wc_veruspay_global['text_help']['total_in'] . $_chain_up . ' (@ '.get_woocommerce_currency_symbol() . get_post_meta( $order_id, '_wc_veruspay_rate', TRUE ) . '/' . $_chain_up . ') :', 'woocommerce' ), 
			'value' => $wc_veruspay_price,
		);
	}
	return $total_rows;
}
/**
 * Send email based on order status change
 * @param $order_id, $checkout
 */
function wc_veruspay_notify_order_status($order_id, $checkout=NULL) {
	global $woocommerce;
	global $wc_veruspay_global;   
   	$order = new WC_Order( $order_id );
	$wc_veruspay_settings = get_option( $wc_veruspay_global['wc'] );
   	if($order->status == 'on-hold' ) {
		$wc_veruspay_mailer = $woocommerce->mailer();
		$wc_veruspay_message_body = __( $wc_veruspay_settings['email_order'] );
		$wc_veruspay_message = $wc_veruspay_mailer->wrap_message( sprintf( __( 'Order %s Received' ), $order->get_order_number() ), $wc_veruspay_message_body );
		$wc_veruspay_mailer->send( $order->billing_email, sprintf( __( 'Order %s Received' ), $order->get_order_number() ), $wc_veruspay_message );
 	}
   	if($order->status == 'completed' ) {
	   	$wc_veruspay_mailer = $woocommerce->mailer();
	   	$wc_veruspay_message_body = __( $wc_veruspay_settings['email_completed'] );
	   	$wc_veruspay_message = $wc_veruspay_mailer->wrap_message( sprintf( __( 'Order %s Successful' ), $order->get_order_number() ), $wc_veruspay_message_body );
	   	$wc_veruspay_mailer->send( $order->billing_email, sprintf( __( 'Order %s Successful' ), $order->get_order_number() ), $wc_veruspay_message );
	}
	if($order->status == 'cancelled' ) {
		$wc_veruspay_mailer = $woocommerce->mailer();
		$wc_veruspay_message_body = __( $wc_veruspay_settings['email_cancelled'] );
		$wc_veruspay_message = $wc_veruspay_mailer->wrap_message( sprintf( __( 'Order %s Cancelled' ), $order->get_order_number() ), $wc_veruspay_message_body );
		$wc_veruspay_mailer->send( $order->billing_email, sprintf( __( 'Order %s Cancelled' ), $order->get_order_number() ), $wc_veruspay_message );
	}
}
/** 
 * Create New Cron Schedules
 * 
 * @param string[] $schedules
 * @return string[] $schedules
 */

function wc_veruspay_cron_schedules($schedules){
    if(! isset($schedules["1min"])){
        $schedules["1min"] = array(
            'interval' => 1*60,
            'display' => __('1min'));
	}
	if(! isset($schedules["2min"])){
        $schedules["2min"] = array(
            'interval' => 2*60,
            'display' => __('2min'));
	}
	if(! isset($schedules["3min"])){
        $schedules["3min"] = array(
            'interval' => 3*60,
            'display' => __('3min'));
	}
	if(! isset($schedules["4min"])){
        $schedules["4min"] = array(
            'interval' => 4*60,
            'display' => __('4min'));
	}
	if(! isset($schedules["5min"])){
        $schedules["5min"] = array(
            'interval' => 5*60,
            'display' => __('5min'));
	}
	if(! isset($schedules["10min"])){
        $schedules["10min"] = array(
            'interval' => 10*60,
            'display' => __('10min'));
	}
	if(! isset($schedules["15min"])){
        $schedules["15min"] = array(
            'interval' => 15*60,
            'display' => __('15min'));
    }
    return $schedules;
}
 
if ( ! wp_next_scheduled( 'woocommerce_cancel_unpaid_submitted' ) ) {
    wp_schedule_event( time(), '1min', 'woocommerce_cancel_unpaid_submitted' );
}
/** 
 * Get list of on-hold VerusPay orders
 * 
 * @return string[] $wc_veruspay_unpaid
 */
function wc_veruspay_get_unpaid_submitted() {        
	global $wpdb;

	$wc_veruspay_unpaid = $wpdb->get_col( $wpdb->prepare( "
			SELECT posts.ID
			FROM {$wpdb->posts} AS posts
			WHERE posts.post_status = 'wc-on-hold'
			AND posts.post_date < %s
	", date( 'Y-m-d H:i:s', strtotime('-5 minutes') ) ) );
	
	return $wc_veruspay_unpaid;
}
/** 
 * Cancel or Complete orders based on status criteria of payment received, timeliness, etc
 * @param global $wc_veruspay_global
 */

function wc_veruspay_check_order_status() {
	global $wc_veruspay_global;
	$wc_veruspay_chains = get_option( $wc_veruspay_global['wc'] )['wc_veruspay_chains'];
	$wc_veruspay_unpaid = wc_veruspay_get_unpaid_submitted();
	if ( $wc_veruspay_unpaid ) {
		foreach ( $wc_veruspay_unpaid as $wc_veruspay_unpaid_order ) {
			// Similar function from order page                    
			$order = wc_get_order( $wc_veruspay_unpaid_order );
			$wc_veruspay_payment_method = $order->get_payment_method();
			if ( $wc_veruspay_payment_method == "veruspay_verus_gateway" ) {
				$order_id = $order->get_id();
				$_chain_up = strtoupper( get_post_meta( $order_id, '_wc_veruspay_coin', TRUE ) );
				$_chain_lo = strtolower( $_chain_up );
				$wc_veruspay_address = get_post_meta( $order_id, '_wc_veruspay_address', TRUE );
				$wc_veruspay_price = get_post_meta( $order_id, '_wc_veruspay_price', TRUE );
				$wc_veruspay_hold_time = get_post_meta( $order_id, '_wc_veruspay_orderholdtime', TRUE );
				$wc_veruspay_order_mode = get_post_meta( $order_id, '_wc_veruspay_mode', TRUE );
				$wc_veruspay_confirmations = get_post_meta( $order_id, '_wc_veruspay_confirms', TRUE );
				$wc_veruspay_order_status = get_post_meta( $order_id, '_wc_veruspay_status', TRUE );
				$wc_veruspay_order_time = get_post_meta( $order_id, '_wc_veruspay_ordertime', TRUE );
				$wc_veruspay_time_end = strtotime('+'.$wc_veruspay_hold_time.' minutes', $wc_veruspay_order_time);
				$wc_veruspay_time_start = strtotime(date("Y-m-d H:i:s", time()));
				$wc_veruspay_sec_remaining = $wc_veruspay_time_end - $wc_veruspay_time_start;
				if ( $wc_veruspay_order_mode == 'live' && $wc_veruspay_order_status == 'order' ) {
					$wc_veruspay_balance = wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'lowest', json_encode( array( $wc_veruspay_address, 0 ), TRUE ) );
					if ( $wc_veruspay_balance >= $wc_veruspay_price ) {
						$wc_veruspay_balance_in = TRUE;
						update_post_meta( $order_id, '_wc_veruspay_status', sanitize_text_field( 'paid' ) );
						update_post_meta( $order_id, '_wc_veruspay_paid', sanitize_text_field( $wc_veruspay_balance ) );
						$wc_veruspay_blocknow = json_decode( wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'getinfo' ), TRUE )['blocks'];
						update_post_meta( $order_id, '_wc_veruspay_order_block', sanitize_text_field( $wc_veruspay_blocknow ) );
						$wc_veruspay_order_note = __( $wc_veruspay_price. ' ' .$wc_veruspay_global['text_help']['msg_received'] );
						$order->add_order_note( $wc_veruspay_order_note );
						$order->save();
					}
					else {
						$wc_veruspay_balance_in = FALSE;
					}
				}
				else if ( $wc_veruspay_order_mode == 'manual' && $wc_veruspay_order_status == 'order' ) {
					$wc_veruspay_balance = wc_veruspay_get( $_chain_up, 'getbalance', $wc_veruspay_address );
					if ( ! is_numeric($wc_veruspay_balance) ) {
						$wc_veruspay_balance = 0;
					}
					if ( $wc_veruspay_balance >= $wc_veruspay_price ) {
						$wc_veruspay_balance_in = TRUE;
						update_post_meta( $order_id, '_wc_veruspay_status', sanitize_text_field( 'paid' ) );
						update_post_meta( $order_id, '_wc_veruspay_paid', sanitize_text_field( $wc_veruspay_balance ) );
						$wc_veruspay_blocknow = wc_veruspay_get( $_chain_up, 'getblockcount' );
						update_post_meta( $order_id, '_wc_veruspay_order_block', sanitize_text_field( $wc_veruspay_blocknow ) );
						$wc_veruspay_order_note = __( $wc_veruspay_price. ' ' .$wc_veruspay_global['text_help']['msg_received'] );
						$order->add_order_note( $wc_veruspay_order_note );
						$order->save();
					}
					if ( $wc_veruspay_balance <= 0 ) {
						$wc_veruspay_balance_in = FALSE;
					}
				}
				if ( $wc_veruspay_order_status == 'paid' ) {
					if ( $wc_veruspay_order_mode == 'live' ) {
						$wc_veruspay_balance = wc_veruspay_go( $wc_veruspay_chains['daemon'][$_chain_up]['DC'], $wc_veruspay_chains['daemon'][$_chain_up]['IP'], $_chain_up, 'lowest', json_encode( array( $wc_veruspay_address, $wc_veruspay_confirmations ), TRUE ) );
						if ( $wc_veruspay_balance >= $wc_veruspay_price ) {
							$order->update_status( 'completed', __( $wc_veruspay_global['text_help']['order_processing'], 'woocommerce') );
							update_post_meta( $order_id, '_wc_veruspay_status', sanitize_text_field( 'completed' ) );
						}
						else {
							return;
						}
					}
					if ( $wc_veruspay_order_mode == 'manual' ) {
						$wc_veruspay_confirms = wc_veruspay_get( $_chain_up, 'lowestconfirm', $wc_veruspay_address );
						if ( $wc_veruspay_confirms >= $wc_veruspay_confirmations ) {
							$order->update_status( 'completed', __( $wc_veruspay_global['text_help']['order_processing'], 'woocommerce') );
							update_post_meta( $order_id, '_wc_veruspay_status', sanitize_text_field( 'completed' ) );
						}
						else {
							return;
						}
					}
						
				}
				if ( $wc_veruspay_order_status == 'order' && $wc_veruspay_sec_remaining <= 0 && $wc_veruspay_order_mode != 'hold' ) {
					foreach  ( $order->get_items() as $item_key => $item_values) {                             
						$wc_veruspay_stock = get_post_meta( $item_values['variation_id'], '_manage_stock', TRUE );                                
					}
					$order->update_status( 'cancelled', __( $wc_veruspay_global['text_help']['msg_order_cancel_timeout'].$wc_veruspay_hold_time.' min', 'woocommerce') );
					update_post_meta( $order_id, '_wc_veruspay_status', sanitize_text_field( 'cancelled' ) );
				}
				if ( $wc_veruspay_order_status == 'noaddress' ) {
					foreach  ( $order->get_items() as $item_key => $item_values) {                             
						$wc_veruspay_stock = get_post_meta( $item_values['variation_id'], '_manage_stock', TRUE );                                
					}
					$order->update_status( 'cancelled', __( 'Missing Payment Address', 'woocommerce') );
					update_post_meta( $order_id, '_wc_veruspay_status', sanitize_text_field( 'cancelled' ) );
				}
			}	
		}
	}        
}
