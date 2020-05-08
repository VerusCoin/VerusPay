<?php
/**
 * Verus PHP Tools - Functions
 *
 * @category Cryptocurrency
 * @package  VerusPHPTools
 * @author   J Oliver Westbrook <johnwestbrook@pm.me>
 * @copyright Copyright (c) 2019, John Oliver Westbrook
 * @link     https://github.com/joliverwestbrook/VerusPHPTools
 * 
 * MODIFIED: This script has been modified, disallowing indirect or ajax access to protect RPC for use within VerusPay and utilizing wp_remote_post for cURL
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
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wc_veruspay_verus_settings;
$wc_veruspay_phpextconfig = array(
    'explorer'  => 'https://explorer.veruscoin.io',
    'fiat_api'  => 'https://veruspay.io/api/',
    'currency'  => 'USD'
);
$wc_veruspay_serializedConfig = ltrim(file_get_contents(dirname(__FILE__) . '/veruspay_config.php'), '<?php ');
$wc_veruspay_verus_settings = unserialize($wc_veruspay_serializedConfig);

/**
 * Test RPC connection - Return status, 0 means not running, anything else is running even if errors occur.
 */
function wc_veruspay_testRPC() {
    global $wc_veruspay_verus_settings;
    // Create new RPC connection to Verus Daemon
    $url = 'http://'.$wc_veruspay_verus_settings[ 'rpc_user' ].':'.$wc_veruspay_verus_settings[ 'rpc_pass' ].'@'.$wc_veruspay_verus_settings[ 'rpc_host' ].':'.$wc_veruspay_verus_settings[ 'rpc_port' ];
    $body = json_encode(array(
        'jsonrpc' => '1.0',
        'method' => 'getinfo',
        'id' => 'veruspay',
    ));
    // Get response data
    $response = wp_remote_post( $url, array(
        'headers'     => array( 'Content-Type' => 'application/json; charset=utf-8' ),
        'body' => $body,
        'method' => 'POST',
	    'timeout' => 120,
    ));
    // Handle response and any errors
    if ( is_wp_error( $response ) ) {
        $error_message = $response->get_error_message();
        if ( strpos( $error_message, 'Connection refused' ) !== false ) {
            return 0;
        }
        else {
            return $error_message;
        }
    } else {
        return json_decode($response['response']['code'], true);
    }
}
/**
 * Primary data and exec function
 */
function wc_veruspay_go_verus( $command, $hash, $amt ) {
    global $wc_veruspay_verus_settings;
    $params = array();
    if ( isset( $hash ) ){ array_push($params, $hash); }
    if ( isset( $amt ) ){ array_push($params, (int)$amt); }
    // Execute commands availabel for to interact with Verus Daemon
    switch ( $command ) {
        case 'getnewaddress':
            return wp_veruspay_verus( 'getnewaddress', null );
            break;
        case 'getnewsapling':
	        array_push( $params, 'sapling' );
            return wp_veruspay_verus( 'z_getnewaddress', $params );
            break;
        case 'getbalance':
            if ( ! isset( $hash ) ) {
                return "Error 2 - Missing Hash";
            }
            else {
                return wp_veruspay_verus( 'z_getbalance', $params );
            }
        break;
        case 'lowestconfirm':
            if ( ! isset( $hash ) | ! isset( $amt ) ) {
                return "Error 2 - Missing Hash";
            }
            else if ( substr($hash, 0, 2) === 'zs' ) {
                $data = wp_veruspay_verus('z_listreceivedbyaddress', $params );
                $amounts = array();
                foreach ( $data as $item ) {
                    array_push($amounts,$item['amount']);
                }
            return array_sum($amounts);
            }
            else {
                return wp_veruspay_verus( 'getreceivedbyaddress', $params );
            }
        break;
        case 'getblockcount':
            return wp_veruspay_verus( 'getblockcount', null );
        break;
        case 'countaddresses':
	        array_push( $params, '' );
            return count( wp_veruspay_verus( 'getaddressesbyaccount', $params ) );
            break;
        case 'countzaddresses':
            return count( wp_veruspay_verus( 'z_listaddresses', null ) );
            break;
        case 'listaddresses':
	        array_push( $params, '' );
            $taddrlist = json_encode( wp_veruspay_verus( 'getaddressesbyaccount', $params ), true );
            return $taddrlist;
            break;
        case 'listzaddresses':
            $zaddrlist = json_encode( wp_veruspay_verus( 'z_listaddresses', null ), true );
            return $zaddrlist;
            break;
        case 'totalreceivedby':
            if ( ! isset( $hash ) ) {
                return "Error 2 - Hash";
            }
            else {
                return wp_veruspay_verus( 'getreceivedbyaddress', $params );
            }
            break;
        case 'getttotalbalance':
            return wp_veruspay_verus( 'getbalance', null );
            break;
        case 'getunconfirmedbalance':
            return wp_veruspay_verus( 'getunconfirmedbalance', null );
            break;
        case 'getztotalbalance':
            $zaddresses = wp_veruspay_verus( 'z_listaddresses', null );
            $zbal = array();
            foreach ( $zaddresses as $zaddress ) {
		        $zparam = array();
	  	        array_push( $zparam, $zaddress );
                $zbal[] = wp_veruspay_verus( 'z_getbalance', $zparam );
            };
            return array_sum( $zbal );
            break;
        case 'gettotalbalance':
            $tbal = array();
            $tbal[] = wp_veruspay_verus( 'getbalance', null );
            $zaddresses = wp_veruspay_verus( 'z_listaddresses', null );
            foreach ( $zaddresses as $zaddress ) {
		        $tparam = array();
		        array_push( $tparam, $zaddress );
                $tbal[] = wp_veruspay_verus( 'z_getbalance', $tparam );
            };
            $tbal = array_sum( $tbal );
            return $tbal;
    }
}
// Function to access blockchain using wordpress built-in wp_remote_post 
function wp_veruspay_verus( $method, $params ){
    global $wc_veruspay_verus_settings;
    $url = 'http://'.$wc_veruspay_verus_settings[ 'rpc_user' ].':'.$wc_veruspay_verus_settings[ 'rpc_pass' ].'@'.$wc_veruspay_verus_settings[ 'rpc_host' ].':'.$wc_veruspay_verus_settings[ 'rpc_port' ];
    $body = json_encode( array(
        'jsonrpc' => '1.0',
        'method' => $method,
		'params' => array_values( $params ),
        'id' => 'veruspay',
    ) );
    // Get blockchain data from method and params
    $response = wp_remote_post( $url, array(
        'headers' => array( 'Content-Type' => 'application/json; charset=utf-8' ),
	    'body' => $body,
	    'method' => 'POST',
	    'timeout' => 120,
	) );
    // Handle response and any errors
    if ( is_wp_error( $response ) ) {
        $error_message = $response->get_error_message();
        if ( strpos( $error_message, 'Connection refused' ) !== false ) {
            return 0;
        }
        else {
            return $error_message;
        }
    }
    else {
        $data = json_decode($response['body'], true);
        if ( $data['error'] !== null ) {
	        return $data['error']['code'];
        }
        else {
	        return $data['result'];
        }
    }
}
/**
 * Primary data and exec function for external apis
 */
function wc_veruspay_get_verus( $command, $hash, $amt ) {
    global $wc_veruspay_phpextconfig;
    
// Execute commands availabel for to interact with Verus Daemon
    switch ( $command ) {
        case 'getbalance':
        if ( ! isset( $hash ) ) {
            return "Error 2 - Hash Call";
        }
        else {
            return wc_veruspay_wp_get_curl( $wc_veruspay_phpextconfig['explorer'] . '/ext/getbalance/' . $hash );
        }
        break;
        case 'lowestconfirm':
        if ( ! isset( $hash ) ) {
            return "Error 2 - Hash";
        }
        else {
            $results = json_decode( wc_veruspay_wp_get_curl( $wc_veruspay_phpextconfig['explorer'] . '/ext/getaddress/' . $hash ), true );
            $results = $results['last_txs'];
            $wc_veruspay_confirmations = array();
            foreach ( $results as $item ) {
                $r = json_decode( wc_veruspay_wp_get_curl( $wc_veruspay_phpextconfig['explorer'] . '/api/getrawtransaction?txid=' . $item['addresses'] . '&decrypt=1' ), true );
                array_push($wc_veruspay_confirmations,$r['confirmations']);
            }
            return min($wc_veruspay_confirmations);
        }
        break;
        case 'getrawtx':
        if ( ! isset( $hash ) ) {
            return "Error 2 - Hash Call";
        }
        else {
            $results = json_decode( wc_veruspay_wp_get_curl( $wc_veruspay_phpextconfig['explorer'] . '/api/getrawtransaction?txid=' . $hash . '&decrypt=1' ), true );
            return $results['confirmations'];
        }
        break;
        case 'getblockcount':
            return wc_veruspay_wp_get_curl( $wc_veruspay_phpextconfig['explorer'] . '/api/getblockcount' );
        break;
        case 'getdifficulty':
            return wc_veruspay_wp_get_curl( $wc_veruspay_phpextconfig['explorer'] . '/api/getdifficulty' );
        break;
        case 'getsupply':
            return wc_veruspay_wp_get_curl( $wc_veruspay_phpextconfig['explorer'] . '/ext/getmoneysupply' );
    }
}

/**
 * Verus Real-time Price Data
 */
function wc_veruspay_verusPrice( $currency ) {
    global $wc_veruspay_phpextconfig;
    $currency = strtoupper($currency);

    if ( $currency == 'VRSC' | $currency == 'VERUS' ) {
        $results = json_decode( wc_veruspay_wp_get_curl( $wc_veruspay_phpextconfig['fiat_api'] . 'rawpricedata.php' ), true );
        return $results['data']['avg_btc'];
    }
    else {
        return wc_veruspay_wp_get_curl( $wc_veruspay_phpextconfig['fiat_api'] . '?currency=' . $currency );
    } 
}
/**
 * Create QR Code using the explorer
 */
function wc_veruspay_getQRCode( $qraddress, $size ) {
    $alt_text = 'Send VRSC to ' . $qraddress;
    return "\n" . '<img src="https://chart.googleapis.com/chart?chld=H|2&chs='.$size.'x'.$size.'&cht=qr&chl=' . $qraddress . '" alt="' . $alt_text . '" />' . "\n";
}
/**
 * Function for wp_remote_get CURL calls
 */
function wc_veruspay_wp_get_curl( $url ) {
    // Get response data
    $response = wp_remote_get( $url, array(
        'headers'     => array( 'Content-Type' => 'application/json; charset=utf-8' ),
        'method' => 'GET',
	    'timeout' => 120,
        'httpversion' => '1.1',
    ));
    // Handle response and any errors
    if ( is_wp_error( $response ) ) {
        $error_message = $response->get_error_message();
        return $error_message;
    }
    else {
        return $response['body'];
    }
}