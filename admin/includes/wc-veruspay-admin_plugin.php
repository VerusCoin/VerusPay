<?php
// No Direct Access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Add plugin links
 * 
 * @since 0.1.0
 * @param array $links
 * @return array $links
 */
function wc_veruspay_plugin_links( $links ) {
	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=veruspay_verus_gateway' ) . '">' . __( 'Configure', 'veruspay-verus-gateway' ) . '</a>'
	);
	return array_merge( $plugin_links, $links );
}
/**
 * Add plugin menu item for easier access to VerusPay settings
 * 
 * @since 0.1.1-a
 * @param global $wc_veruspay_global
 * 
 */
function wc_veruspay_settings_menu(){
	global $wc_veruspay_global;
	add_menu_page( 'WooCommerce Settings', 'VerusPay', 'administrator', 'wc-settings&tab=checkout&section=veruspay_verus_gateway', 'wc_veruspay_init', $wc_veruspay_global['paths']['public']['img'] . 'wc-verus-icon-16x.png' );
}