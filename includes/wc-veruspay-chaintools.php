<?php
/**
 * Blockchain Functions (Methods) Available to VerusPay
 * 
 * Following is a list of all whitelisted methods and custom methods 
 * defined for VerusPay within VerusChainTools API (VCT) v0.4.0+. 
 * The VCT API must be configured and running on the wallet server 
 * you will use in conjuction with this installation of VerusPay.
 * To learn more, visit: https://github.com/joliverwestbrook/VerusChainTools
 * 
 * ===================
 * =   WHITELISTED   =
 * ===================
 *  getinfo
 *  setgenerate
 *  getgenerate
 *  getnewaddress
 *  z_getnewaddress
 *  z_getbalance
 *  getunconfirmedbalance
 *  getaddressesbyaccount
 *  z_listaddresses
 *  getreceivedbyaddress
 * 
 * +++++++++++++++++++
 * ++ NEW For PBaaS ++
 * +++++++++++++++++++
 *  definechain
 *  getchaindefinition
 *  getdefinedchains
 * 
 * ===================
 * =     CUSTOM      =
 * ===================
 *  test        returns status of blockchain daemon
 *  type        returns an integer representing the type of transactions capable by the blockchain (0 = transparent + private, 1 = transparent only, 2 = private only)
 *  version     returns version # of blockchain daemon
 *  lowest      returns the lowest confirm for the given address
 *  t_count     returns the number of T addresses created
 *  z_count     returns the number of Z addresses created
 *  bal         returns the balance of each address created and totals for Transparent, Interest, Private and total wallet
 *  show_taddr  returns the Cashout Transparent address set by the store owner
 *  show_zaddr  returns the Cashout Private address set by the store owner
 *  cashout_t   sends the total transparent balance, minus tx fee of 0.0001, to the Cashout Transparent address
 *  cashout_z   sends the total private balance, minus tx fee of 0.0001 per address, to the Cashout Private address
 */
// No Direct Access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Go
 * Function to access VCT API using wordpress built-in wp_remote_post
 */
function wc_veruspay_go( $chaindc, $url, $chain, $method, $params = NULL ) {
    global $wc_veruspay_global;
    $body = array(
        'a' => $chaindc,
        'c' => $chain,
        'm' => $method,
        'p' => $params, // Passed as json_encode string of correct array layout of parameters in question
        'o' => null, // TODO: Usage?
    );
    // Pass method and params to VCT API and get return
    $response = wp_remote_post( $url, array(
		'sslverify' => FALSE,
		'headers'     => array( 'Content-Type' => 'application/json', 'Accept' => 'application/json' ),
    	'body'        => json_encode( $body ),
    	'method'      => 'POST',
    	'data_format' => 'body',
		'blocking' => TRUE,
	    'timeout' => 120,
        )
    );
    // Handle return
    if ( is_wp_error( $response ) ) {
        $error_message = $response->get_error_message();
        if ( strpos( $error_message, 'Connection refused' ) !== FALSE ) {
            return 0;
        }
        else {
            return $error_message;
        }
    }
    else {
        $data = json_decode( $response['body'], TRUE );
        if ( isset( $data['error'] ) ) {
	        return $data['error']['code'];
        }
        else if ( ! isset( $data['result'] ) ) {
            return 'ERR: ' . $response['body'];//json_encode($response,true);//$wc_veruspay_global['text_help']['vct_0'];
        }
        else if ( $data['result'] == 'error' ) {
            return 'ERR: ' . $data['return'];
        }
        else if ( $data['result'] == 'success' ) {
            return $data['return'];
        }
		else {
			return 'ERR: ' . $wc_veruspay_global['text_help']['vct_1'];
		}
    }
}
/**
 * Get
 * Primary data and method function for external apis
 */
function wc_veruspay_get( $chain, $method, $params = NULL ) {
    global $wc_veruspay_global;
    $cl = strtolower( $chain );
    if( ! array_key_exists( $cl, $wc_veruspay_global['chain_list'] ) ) {
        return strtoupper( $chain ) . $wc_veruspay_global['text_help']['vct_2'];
    }
    switch ( $method ) {
        case 'getbalance':
        if ( ! isset( $params ) ) {
            return $wc_veruspay_global['text_help']['vct_3'];
        }
        else {
            $results = json_decode( wc_veruspay_wp_get_curl( $wc_veruspay_global['chain_dtls'][$cl]['getaddress'] . $params ), TRUE );
            $results = $results['balance'];
            return $results;
        }
        break;
        case 'lowestconfirm':
        if ( ! isset( $params ) ) {
            return $wc_veruspay_global['text_help']['vct_3'];
        }
        else {
            $results = json_decode( wc_veruspay_wp_get_curl( $wc_veruspay_global['chain_dtls'][$cl]['getaddress'] . $params ), TRUE );
            $results = $results[ $wc_veruspay_global['chain_dtls'][$cl]['txname'] ];
            $confs = array();
            foreach ( $results as $item ) {
                usleep(500);
                if ( $wc_veruspay_global['chain_dtls'][$cl]['txlevel'] === NULL ) {
                    $txhash = $item;
                }
                else {
                    $txhash = $item[ $wc_veruspay_global['chain_dtls'][$cl]['txlevel'] ];
                }
                $txresults = json_decode( wc_veruspay_wp_get_curl( $wc_veruspay_global['chain_dtls'][$cl]['gettx'] . $txhash . $wc_veruspay_global['chain_dtls'][$cl]['txtrail'] ), TRUE );
                if ( $txresults[$wc_veruspay_global['chain_dtls'][$cl]['htname']] > 1 ) {
                    $confs[$txhash] = $txresults['confirmations'];
                }
            }
            return min($confs);
        }
        break;
        case 'getrawtx':
        if ( ! isset( $params ) ) {
            return $wc_veruspay_global['text_help']['vct_3'];
        }
        else {
            $results = json_decode( wc_veruspay_wp_get_curl( $wc_veruspay_global['chain_dtls'][$cl]['gettx'] . $params . $wc_veruspay_global['chain_dtls'][$cl]['txtrail'] ), TRUE );
            return $results['confirmations'];
        }
        break;
        case 'getblockcount':
            $results = json_decode( wc_veruspay_wp_get_curl( $wc_veruspay_global['chain_dtls'][$cl]['info'] ), TRUE );
            return $results['info'][$wc_veruspay_global['chain_dtls'][$cl]['blocks']];
        break;
        case 'getdifficulty':
            $results = json_decode( wc_veruspay_wp_get_curl( $wc_veruspay_global['chain_dtls'][$cl]['info'] ), TRUE );
            return $results['info'][$wc_veruspay_global['chain_dtls'][$cl]['difficulty']];
        break;
        case 'getsupply':
            return wc_veruspay_wp_get_curl( $wc_veruspay_global['chain_dtls'][$cl]['supply'] );
    }
}

/**
 * Price
 * Real-time Price Data via VerusPay API or CoinGecko API. Supported coins via VerusPay API: VRSC, ARRR, KMD, ZEC
 */
function wc_veruspay_price( $chain, $currency ) {
    global $wc_veruspay_global;
    // For Debugging and usage with VRSCTEST (testnet)
    if ( $chain == 'VRSCTEST' || $chain == 'vrsctest' ) {
        $chain = 'vrsc';
    }
    $_cur_up = strtoupper( $currency );
    $_cur_lo = strtolower( $currency );
    $_chain_up = strtoupper( $chain );
    $_chain_lo = strtolower( $chain );
    // Default to VerusCoin
    if ( ! isset( $_chain_up ) ) {
        $_chain_up = 'VRSC';
    }
    if ( in_array( $_chain_lo, $wc_veruspay_global['chain_list'] ) ) {
        $r = wc_veruspay_wp_get_curl( $wc_veruspay_global['chain_dtls']['fiat']['api'] . '?currency=' . $_cur_up . '&ticker=' . $_chain_up );
        if ( is_numeric( $r ) ) {
            return $r;
        }
        else {
            return 'NaN';
        }
    }
    else {
        $lc = json_decode( wc_veruspay_wp_get_curl( $wc_veruspay_global['paths']['ext']['coingeckoapi'] . 'simple/supported_vs_currencies' ), TRUE );
        if ( in_array( $_cur_lo, $lc ) ) {
            $l = json_decode( wc_veruspay_wp_get_curl( $wc_veruspay_global['paths']['ext']['coingeckoapi'] . 'coins/list' ), TRUE );
            foreach( $l as $key => $item ) {
                if ( $item['symbol'] == $_chain_lo ) {
                    $rkey = $key;
                }
            }
            if ( isset( $rkey ) ) {
                $_chain_id = $l[$rkey]['id'];
                $r = json_decode( wc_veruspay_wp_get_curl( $wc_veruspay_global['paths']['ext']['coingeckoapi'] . 'simple/price?ids=' . $_chain_id . '&vs_currencies=' . $_cur_lo ), TRUE )[$_chain_id][$_cur_lo];
                if ( is_numeric( $r ) ) {
                    return $r;
                }
                else {
                    return 'NaN';
                }
            }
            else {
                return 'NaN';
            }
        }
        else {
            return 'NaN';
        }
    }
}
/**
 * QR API
 * Create QR Code using the explorer
 */
function wc_veruspay_qr( $qraddress, $size ) {
    $alt_text = 'Send VRSC to ' . $qraddress;
    return "\n" . '<img src="https://api.qrserver.com/v1/create-qr-code/?data=' . $qraddress . '&amp;size=200x200'. '" alt="' . $alt_text . '" title="" />' . "\n";
}//  return "\n" . '<img src="https://api.qrserver.com/v1/create-qr-code/?data=HelloWorld&amp;size=100x100" alt="" title="" />' . "\n";
/**
 * cURL Get
 * Function for wp_remote_get CURL calls
 */
function wc_veruspay_wp_get_curl( $url ) {
    // Get response data
    $response = wp_remote_get( $url, array(
        'headers'     => array( 'Content-Type' => 'application/json; charset=utf-8' ),
        'method' => 'GET',
	    'timeout' => 120,
        'httpversion' => '1.1',
        )
    );
    // Handle response and any errors
    if ( is_wp_error( $response ) ) {
        $error_message = $response->get_error_message();
        return $error_message;
    }
    else {
        return $response['body'];
    }
}
