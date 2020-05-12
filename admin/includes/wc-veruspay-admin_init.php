<?php
// No Direct Access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// If no mode set, run Setup
if ( empty( $this->mode ) || ! isset( $this->mode ) ) {
    $this->form_fields = apply_filters( 'wc_veruspay_form_fields', wc_veruspay_setup() );
    $this->update_option( 'enabled', 'no' );
    return;
}
// If Full Daemon Mode
if ( $this->mode == 'daemon' ) {
    $_mode_tab_title = ucfirst( $this->mode ) . 's';
    // Setup vars and arrays
    $wc_veruspay_daemon_code_1 = $this->get_option( 'daemon_code_1' );
    $wc_veruspay_daemon_ip_1 = $this->get_option( 'daemon_ip_1' );
    $wc_veruspay_store_stat_array = array();
    $_statsArray = array('1' => array('','',),'2' => array('','',), '3' => array('','',), '4' => array('','',), '5' => array('','',), '6' => array('','',), '7' => array('','',),);
    $_classArray = array('','','','','','',);
    $_daemonsArray = array(
        '2' => $this->get_option( 'daemon_ip_2' ),
        '3' => $this->get_option( 'daemon_ip_3' ),
        '4' => $this->get_option( 'daemon_ip_4' ),
        '5' => $this->get_option( 'daemon_ip_5' ),
        '6' => $this->get_option( 'daemon_ip_6' ),
        '7' => $this->get_option( 'daemon_ip_7' ),
    );
    $_proto1 = 'http://';
    if ( $this->get_option( 'daemon_ssl_1' ) == 'yes' ) {
        $_proto1 = 'https://';
    }
    $wc_veruspay_daemon_fullip_1 = $_proto1 . $wc_veruspay_daemon_ip_1;
    $wc_veruspay_global['chains']['daemon'] = wc_veruspay_go( $wc_veruspay_daemon_code_1, $wc_veruspay_daemon_fullip_1, '_stat_', 'chainlist' );
    // Begin checking primary daemon (required)
    if ( empty( $wc_veruspay_global['chains']['daemon'] ) || ! is_array( $wc_veruspay_global['chains']['daemon'] ) ) {
        $_statsArray['1'][0] = ' - Status: <span class="wc_veruspay_stat wc_veruspay_white_on_orange">UNREACHABLE</span>';
        if ( ! array_filter( $_daemonsArray ) ) {
            $this->form_fields = apply_filters( 'wc_veruspay_form_fields', wc_veruspay_setup() );
            $this->update_option( 'enabled', 'no' );
        }
        $this->update_option( 'wc_veruspay_dchains', $wc_veruspay_global['chains']['daemon'] );
    }
    else if ( $wc_veruspay_global['chains']['daemon'] == '_no_chains_found_' ) {
        $_vctversion = wc_veruspay_go( $wc_veruspay_daemon_code_1, $wc_veruspay_daemon_fullip_1, '_stat_', 'vct_version' );
        $_statsArray['1'][0] = ' - Status: <span class="wc_veruspay_stat wc_veruspay_white_on_red">OFFLINE</span><span class="wc_veruspay_version">v' . $_vctversion . '</span>';
        if ( ! array_filter( $_daemonsArray ) ) {
            $this->update_option( 'enabled', 'no' );
        }
        $this->update_option( 'wc_veruspay_dchains', $wc_veruspay_global['chains']['daemon'] );
    }
    else {
        $_vctversion = wc_veruspay_go( $wc_veruspay_daemon_code_1, $wc_veruspay_daemon_fullip_1, '_stat_', 'vct_version' );
        $_statsArray['1'][0] = ' - Status: <span class="wc_veruspay_stat wc_veruspay_white_on_green">ONLINE</span><span class="wc_veruspay_version">v' . $_vctversion . '</span>';
        // Add primary daemon chains to global array
        foreach ( $wc_veruspay_global['chains']['daemon'] as $key => $item ) {
            $_chain_up = strtoupper( $key );
            $_chain_lo = strtolower( $key );
            $wc_veruspay_global['chains']['daemon'][$_chain_up]['S'] = $this->get_option( 'daemon_fn_1' );
            $wc_veruspay_global['chains']['daemon'][$_chain_up]['IP'] = $wc_veruspay_daemon_fullip_1;
            $wc_veruspay_global['chains']['daemon'][$_chain_up]['DC'] = $wc_veruspay_daemon_code_1;
            $wc_veruspay_global['chains']['daemon'][$_chain_up]['ST'] = json_decode( wc_veruspay_go( $wc_veruspay_daemon_code_1, $wc_veruspay_daemon_fullip_1, $_chain_up, 'test' ), TRUE )['stat'];
            if ( $wc_veruspay_global['chains']['daemon'][$_chain_up]['ST'] == 1 ) {
                $_stat = 'border-color: #13f413';
                $_tooltip = $_chain_up . ': ONLINE';
                if ( $this->get_option( $_chain_lo . '_enable' ) == 'yes' ) {
                    $wc_veruspay_store_stat_array[] = 'online';
                }
            }
            else {
                $wc_veruspay_global['chains']['daemon'][$_chain_up]['ST'] = 0;
                $_stat = 'border-color:#f92a2a;opacity:0.6;';
                $_tooltip = $_chain_up . ': OFFLINE';
                if ( $this->get_option( $_chain_lo . '_enable' ) == 'yes' && strlen( $this->get_option( $_chain_lo . '_storeaddresses' ) ) > 30 ) {
                    $wc_veruspay_store_stat_array[] = 'online';
                    $_tooltip = $_chain_up . ': OFFLINE/MANUAL';
                }
                else {
                    $this->update_option( $_chain_lo . '_enable', 'no' );
                }
            }
            $_statsArray['1'][1] = $_statsArray['1'][1] . '<span title="' . $_tooltip . '" class="wc_veruspay_coinlist" style="background-image: url(' . $wc_veruspay_global['coinimg'] . $_chain_lo .'.png);'.$_stat.'"></span>';
        }
        $_statsArray['1'][1] = '<span>' . $_statsArray['1'][1] . '</span>';
    }
    // Iterate through any other live daemons and add to global array and set classes
    foreach ( $_daemonsArray as $key => $item ) {
        if ( empty( $item ) ) {
            $_classArray[$key] = 'wc_veruspay_daemon_add';
        }
        else {
            if ( $item == $wc_veruspay_daemon_ip_1 ) {
                $this->update_option( 'daemon_ip_' . $key, 'duplicate daemon' );
                header("Refresh: 0");
            }
            $_code = $this->get_option( 'daemon_code_' . $key );
            $_proto = 'http://';
            if ( $this->get_option( 'daemon_ssl_' . $key ) == 'yes' ) {
                $_proto = 'https://';
            }
            $_ip = $_proto . $item;
            if ( empty( $_code ) ) {
                $_statsArray[$key][0] = '<span class="wc_veruspay_red">Access Code Required</span>';
            }
            else {
                $_list = wc_veruspay_go( $_code, $_ip, '_stat_', 'chainlist' );
                if ( empty( $_list ) || is_string( $_list ) || $item == 'duplicate daemon' ) {
                    $_statsArray[$key][0] = ' - Status: <span class="wc_veruspay_stat wc_veruspay_white_on_red">UNREACHABLE</span>';
                }
                else if ( $_list == '_no_chains_found_' ) {
                    $_vctversion = wc_veruspay_go( $_code, $_ip, '_stat_', 'vct_version' );
                    $_statsArray[$key][0] = ' - Status: <span class="wc_veruspay_stat wc_veruspay_white_on_orange">OFFLINE</span><span class="wc_veruspay_version">v' . $_vctversion . '</span>';
                }
                else {
                    $_vctversion = wc_veruspay_go( $_code, $_ip, '_stat_', 'vct_version' );
                    $_statsArray[$key][0] = ' - Status: <span class="wc_veruspay_stat wc_veruspay_white_on_green">ONLINE</span><span class="wc_veruspay_version">v' . $_vctversion . '</span>';
                    foreach ( $_list as $_key => $_item ) {
                        $_chain_up = strtoupper( $_key );
                        $_chain_lo = strtolower( $_key );
                        $_list[$_chain_up]['S'] = $this->get_option( 'daemon_fn_' . $key );
                        $_list[$_chain_up]['IP'] = $_ip;
                        $_list[$_chain_up]['DC'] = $_code;
                        $_list[$_chain_up]['ST'] = json_decode( wc_veruspay_go( $_code, $_ip, $_chain_up, 'test' ), TRUE )['stat'];
                        if ( $_list[$_chain_up]['ST'] == 1 ) {
                            $_stat = 'border-color: #13f413';
                            $_tooltip = $_chain_up . ': ONLINE';
                            if ( $this->get_option( $_chain_lo . '_enable' ) == 'yes' ) {
                                $wc_veruspay_store_stat_array[] = 'online';
                            }
                        }
                        else {
                            $_list[$_chain_up]['ST'] = 0;
                            $_stat = 'border-color:#f92a2a;opacity:0.6;';
                            $_tooltip = $_chain_up . ': OFFLINE';
                            if ( $this->get_option( $_chain_lo . '_enable' ) == 'yes' && strlen( $this->get_option( $_chain_lo . '_storeaddresses' ) ) > 30 ) {
                                $wc_veruspay_store_stat_array[] = 'online';
                                $_tooltip = $_chain_up . ': OFFLINE/MANUAL';
                            }
                            else {
                                $this->update_option( $_chain_lo . '_enable', 'no' );
                            }
                        }
                        $_statsArray[$key][1] = $_statsArray[$key][1] . '<span title="' . $_tooltip . '" class="wc_veruspay_coinlist" style="background-image: url(' . $wc_veruspay_global['coinimg'] . $_chain_lo .'.png);'.$_stat.'"></span>';
                    }
                    $_statsArray[$key][1] = '<span>' . $_statsArray[$key][1] . '</span>';
                    $wc_veruspay_global['chains']['daemon'] = array_merge( $wc_veruspay_global['chains']['daemon'], $_list );
                }
            }
        }
    }
    // If No Online Daemons, disable VerusPay
    if ( $this->enabled == 'yes' ) {
        if( ! in_array( 'online', $wc_veruspay_store_stat_array ) ) {
            $this->update_option( 'enabled', 'no' );
        }
    }
    // Deamon Paths array
    $wc_veruspay_init_mode = array(
        // Primary Daemon Server
        // css id: woocommerce_veruspay_verus_gateway_primary_daemon_heading
        'mode_heading' => array(
            'title' => __( 'Primary Daemon Server Settings' . $_statsArray['1'][0], 'veruspay-verus-gateway' ),
            'type' => 'title',
            'description' => '',
            'class' => 'wc_veruspay_title-sub wc_veruspay_title-sub-toggle-heading wc_veruspay_daemonsettings-toggle',
        ),
        'mode_stat' => array(
            'title' => __( 'Configured Coins: ' . $_statsArray['1'][1] .'<span class="wc_veruspay_edit_daemon" data-root="' . $wc_veruspay_global['paths']['site'] . '" data-url="' . $this->get_option( 'daemon_ip_1' ) . '">+</span>', 'veruspay-verus-gateway' ),
            'type' => 'title',
            'description' => '',
            'class' => 'wc_veruspay_section_heading wc_veruspay_title-sub wc_veruspay_title-sub-toggle-under wc_veruspay_daemonsettings-toggle',
        ),
        'daemon_fn_1' => array(
            'title' => __( 'Custom Name', 'veruspay-verus-gateway' ),
            'type' => 'text',
            'default' => 'My Primary Daemon Server',
            'class' => 'wc_veruspay_daemonsettings-toggle',
        ),
        'daemon_ip_1' => array(
            'title' => __( 'Server IP/Path', 'veruspay-verus-gateway' ),
            'type' => 'text',
            'default' => 'IP or local folder name (e.g. enter just verustools if at /var/www/html/verustools)',
            'class' => 'wc_veruspay_daemonsettings-toggle',
        ),
        'daemon_ssl_1' => array(
            'title' => __( 'Enable SSL?', 'veruspay-verus-gateway' ),
            'type' => 'checkbox',
            'label' => ' ',
            'default' => 'yes',
            'class' => 'wc_veruspay_checkbox_option wc_veruspay_daemonsettings-toggle',
        ),
        'daemon_code_1' => array(
            'title' => __( 'Access Code', 'veruspay-verus-gateway' ),
            'type' => 'text',
            'label' => __( 'Access Code', 'veruspay-verus-gateway' ),
            'class' => 'wc_veruspay_daemonsettings-toggle',
        ),
        // Daemon 2
        'daemon_heading_2' => array(
            'title' => __( 'Daemon Server 2 Settings' . $_statsArray['2'][0], 'veruspay-verus-gateway' ),
            'type' => 'title',
            'description' => '',
            'class' => 'wc_veruspay_title-sub wc_veruspay_title-sub-toggle-heading wc_veruspay_daemonsettings-toggle ' . $_classArray['2'] . '-title',
        ),
        'daemon_stat_2' => array(
            'title' => __( 'Configured Coins: ' . $_statsArray['2'][1] .'<span class="wc_veruspay_edit_daemon" data-root="' . $wc_veruspay_global['paths']['site'] . '" data-url="' . $this->get_option( 'daemon_ip_2' ) . '">+</span>', 'veruspay-verus-gateway' ),
            'type' => 'title',
            'description' => '',
            'class' => 'wc_veruspay_section_heading wc_veruspay_title-sub wc_veruspay_title-sub-toggle-under wc_veruspay_daemonsettings-toggle ' . $_classArray['2'] . '-status',
        ),
        'daemon_fn_2' => array(
            'title' => __( 'Custom Name', 'veruspay-verus-gateway' ),
            'type' => 'text',
            'default' => 'Daemon Server 2',
            'class' => 'wc_veruspay_daemonsettings-toggle ' . $_classArray['2'] . '-fn',
        ),
        'daemon_ip_2' => array(
            'title' => __( 'Server IP/Path', 'veruspay-verus-gateway' ),
            'type' => 'text',
            'default' => 'IP or local folder name (e.g. enter just verustools if at /var/www/html/verustools)',
            'class' => 'wc_veruspay_daemonsettings-toggle ' . $_classArray['2'] . '-ip',
        ),
        'daemon_ssl_2' => array(
            'title' => __( 'Enable SSL?', 'veruspay-verus-gateway' ),
            'type' => 'checkbox',
            'label' => ' ',
            'default' => 'yes',
            'class' => 'wc_veruspay_checkbox_option wc_veruspay_daemonsettings-toggle ' . $_classArray['2'] . '-ssl',
        ),
        'daemon_code_2' => array(
            'title' => __( 'Access Code', 'veruspay-verus-gateway' ),
            'type' => 'text',
            'label' => __( 'Access Code', 'veruspay-verus-gateway' ),
            'class' => 'wc_veruspay_daemonsettings-toggle ' . $_classArray['2'] . '-code',
        ),
        // Daemon 3
        'daemon_heading_3' => array(
            'title' => __( 'Daemon Server 3 Settings' . $_statsArray['3'][0], 'veruspay-verus-gateway' ),
            'type' => 'title',
            'description' => '',
            'class' => 'wc_veruspay_title-sub wc_veruspay_title-sub-toggle-heading wc_veruspay_daemonsettings-toggle ' . $_classArray['3'] . '-title',
        ),
        'daemon_stat_3' => array(
            'title' => __( 'Configured Coins: ' . $_statsArray['3'][1] .'<span class="wc_veruspay_edit_daemon" data-root="' . $wc_veruspay_global['paths']['site'] . '" data-url="' . $this->get_option( 'daemon_ip_3' ) . '">+</span>', 'veruspay-verus-gateway' ),
            'type' => 'title',
            'description' => '',
            'class' => 'wc_veruspay_section_heading wc_veruspay_title-sub wc_veruspay_title-sub-toggle-under wc_veruspay_daemonsettings-toggle ' . $_classArray['3'] . '-status',
        ),
        'daemon_fn_3' => array(
            'title' => __( 'Custom Name', 'veruspay-verus-gateway' ),
            'type' => 'text',
            'default' => 'Daemon Server 3',
            'class' => 'wc_veruspay_daemonsettings-toggle ' . $_classArray['3'] . '-fn',
        ),
        'daemon_ip_3' => array(
            'title' => __( 'Server IP/Path', 'veruspay-verus-gateway' ),
            'type' => 'text',
            'default' => 'IP or local folder name (e.g. enter just verustools if at /var/www/html/verustools)',
            'class' => 'wc_veruspay_daemonsettings-toggle ' . $_classArray['3'] . '-ip',
        ),
        'daemon_ssl_3' => array(
            'title' => __( 'Enable SSL?', 'veruspay-verus-gateway' ),
            'type' => 'checkbox',
            'label' => ' ',
            'default' => 'yes',
            'class' => 'wc_veruspay_checkbox_option wc_veruspay_daemonsettings-toggle ' . $_classArray['3'] . '-ssl',
        ),
        'daemon_code_3' => array(
            'title' => __( 'Access Code', 'veruspay-verus-gateway' ),
            'type' => 'text',
            'label' => __( 'Access Code', 'veruspay-verus-gateway' ),
            'class' => 'wc_veruspay_daemonsettings-toggle ' . $_classArray['3'] . '-code',
        ),
        // Daemon 4
        'daemon_heading_4' => array(
            'title' => __( 'Daemon Server 4 Settings' . $_statsArray['4'][0], 'veruspay-verus-gateway' ),
            'type' => 'title',
            'description' => '',
            'class' => 'wc_veruspay_title-sub wc_veruspay_title-sub-toggle-heading wc_veruspay_daemonsettings-toggle ' . $_classArray['4'] . '-title',
        ),
        'daemon_stat_4' => array(
            'title' => __( 'Configured Coins: ' . $_statsArray['4'][1] .'<span class="wc_veruspay_edit_daemon" data-root="' . $wc_veruspay_global['paths']['site'] . '" data-url="' . $this->get_option( 'daemon_ip_4' ) . '">+</span>', 'veruspay-verus-gateway' ),
            'type' => 'title',
            'description' => '',
            'class' => 'wc_veruspay_section_heading wc_veruspay_title-sub wc_veruspay_title-sub-toggle-under wc_veruspay_daemonsettings-toggle ' . $_classArray['4'] . '-status',
        ),
        'daemon_fn_4' => array(
            'title' => __( 'Custom Name', 'veruspay-verus-gateway' ),
            'type' => 'text',
            'default' => 'Daemon Server 4',
            'class' => 'wc_veruspay_daemonsettings-toggle ' . $_classArray['4'] . '-fn',
        ),
        'daemon_ip_4' => array(
            'title' => __( 'Server IP/Path', 'veruspay-verus-gateway' ),
            'type' => 'text',
            'default' => 'IP or local folder name (e.g. enter just verustools if at /var/www/html/verustools)',
            'class' => 'wc_veruspay_daemonsettings-toggle ' . $_classArray['4'] . '-ip',
        ),
        'daemon_ssl_4' => array(
            'title' => __( 'Enable SSL?', 'veruspay-verus-gateway' ),
            'type' => 'checkbox',
            'label' => ' ',
            'default' => 'yes',
            'class' => 'wc_veruspay_checkbox_option wc_veruspay_daemonsettings-toggle ' . $_classArray['4'] . '-ssl',
        ),
        'daemon_code_4' => array(
            'title' => __( 'Access Code', 'veruspay-verus-gateway' ),
            'type' => 'text',
            'label' => __( 'Access Code', 'veruspay-verus-gateway' ),
            'class' => 'wc_veruspay_daemonsettings-toggle ' . $_classArray['4'] . '-code',
        ),
        // Daemon 5
        'daemon_heading_5' => array(
            'title' => __( 'Daemon Server 5 Settings' . $_statsArray['5'][0], 'veruspay-verus-gateway' ),
            'type' => 'title',
            'description' => '',
            'class' => 'wc_veruspay_title-sub wc_veruspay_title-sub-toggle-heading wc_veruspay_daemonsettings-toggle ' . $_classArray['5'] . '-title',
        ),
        'daemon_stat_5' => array(
            'title' => __( 'Configured Coins: ' . $_statsArray['5'][1] .'<span class="wc_veruspay_edit_daemon" data-root="' . $wc_veruspay_global['paths']['site'] . '" data-url="' . $this->get_option( 'daemon_ip_5' ) . '">+</span>', 'veruspay-verus-gateway' ),
            'type' => 'title',
            'description' => '',
            'class' => 'wc_veruspay_section_heading wc_veruspay_title-sub wc_veruspay_title-sub-toggle-under wc_veruspay_daemonsettings-toggle ' . $_classArray['5'] . '-status',
        ),
        'daemon_fn_5' => array(
            'title' => __( 'Custom Name', 'veruspay-verus-gateway' ),
            'type' => 'text',
            'default' => 'Daemon Server 5',
            'class' => 'wc_veruspay_daemonsettings-toggle ' . $_classArray['5'] . '-fn',
        ),
        'daemon_ip_5' => array(
            'title' => __( 'Server IP/Path', 'veruspay-verus-gateway' ),
            'type' => 'text',
            'default' => 'IP or local folder name (e.g. enter just verustools if at /var/www/html/verustools)',
            'class' => 'wc_veruspay_daemonsettings-toggle ' . $_classArray['5'] . '-ip',
        ),
        'daemon_ssl_5' => array(
            'title' => __( 'Enable SSL?', 'veruspay-verus-gateway' ),
            'type' => 'checkbox',
            'label' => ' ',
            'default' => 'yes',
            'class' => 'wc_veruspay_checkbox_option wc_veruspay_daemonsettings-toggle ' . $_classArray['5'] . '-ssl',
        ),
        'daemon_code_5' => array(
            'title' => __( 'Access Code', 'veruspay-verus-gateway' ),
            'type' => 'text',
            'label' => __( 'Access Code', 'veruspay-verus-gateway' ),
            'class' => 'wc_veruspay_daemonsettings-toggle ' . $_classArray['5'] . '-code',
        ),
        // Daemon 6
        'daemon_heading_6' => array(
            'title' => __( 'Daemon Server 6 Settings' . $_statsArray['6'][0], 'veruspay-verus-gateway' ),
            'type' => 'title',
            'description' => '',
            'class' => 'wc_veruspay_title-sub wc_veruspay_title-sub-toggle-heading wc_veruspay_daemonsettings-toggle ' . $_classArray['6'] . '-title',
        ),
        'daemon_stat_6' => array(
            'title' => __( 'Configured Coins: ' . $_statsArray['6'][1] .'<span class="wc_veruspay_edit_daemon" data-root="' . $wc_veruspay_global['paths']['site'] . '" data-url="' . $this->get_option( 'daemon_ip_6' ) . '">+</span>', 'veruspay-verus-gateway' ),
            'type' => 'title',
            'description' => '',
            'class' => 'wc_veruspay_section_heading wc_veruspay_title-sub wc_veruspay_title-sub-toggle-under wc_veruspay_daemonsettings-toggle ' . $_classArray['6'] . '-status',
        ),
        'daemon_fn_6' => array(
            'title' => __( 'Custom Name', 'veruspay-verus-gateway' ),
            'type' => 'text',
            'default' => 'Daemon Server 6',
            'class' => 'wc_veruspay_daemonsettings-toggle ' . $_classArray['6'] . '-fn',
        ),
        'daemon_ip_6' => array(
            'title' => __( 'Server IP/Path', 'veruspay-verus-gateway' ),
            'type' => 'text',
            'default' => 'IP or local folder name (e.g. enter just verustools if at /var/www/html/verustools)',
            'class' => 'wc_veruspay_daemonsettings-toggle ' . $_classArray['6'] . '-ip',
        ),
        'daemon_ssl_6' => array(
            'title' => __( 'Enable SSL?', 'veruspay-verus-gateway' ),
            'type' => 'checkbox',
            'label' => ' ',
            'default' => 'yes',
            'class' => 'wc_veruspay_checkbox_option wc_veruspay_daemonsettings-toggle ' . $_classArray['6'] . '-ssl',
        ),
        'daemon_code_6' => array(
            'title' => __( 'Access Code', 'veruspay-verus-gateway' ),
            'type' => 'text',
            'label' => __( 'Access Code', 'veruspay-verus-gateway' ),
            'class' => 'wc_veruspay_daemonsettings-toggle ' . $_classArray['6'] . '-code',
        ),
        // Daemon 7 
        'daemon_heading_7' => array(
            'title' => __( 'Daemon Server 7 Settings' . $_statsArray['7'][0], 'veruspay-verus-gateway' ),
            'type' => 'title',
            'description' => '',
            'class' => 'wc_veruspay_title-sub wc_veruspay_title-sub-toggle-heading wc_veruspay_daemonsettings-toggle ' . $_classArray['7'] . '-title',
        ),
        'daemon_stat_7' => array(
            'title' => __( 'Configured Coins: ' . $_statsArray['7'][1] .'<span class="wc_veruspay_edit_daemon" data-root="' . $wc_veruspay_global['paths']['site'] . '" data-url="' . $this->get_option( 'daemon_ip_7' ) . '">+</span>', 'veruspay-verus-gateway' ),
            'type' => 'title',
            'description' => '',
            'class' => 'wc_veruspay_section_heading wc_veruspay_title-sub wc_veruspay_title-sub-toggle-under wc_veruspay_daemonsettings-toggle ' . $_classArray['7'] . '-status',
        ),
        'daemon_fn_7' => array(
            'title' => __( 'Custom Name', 'veruspay-verus-gateway' ),
            'type' => 'text',
            'default' => 'Daemon Server 7',
            'class' => 'wc_veruspay_daemonsettings-toggle ' . $_classArray['7'] . '-fn',
        ),
        'daemon_ip_7' => array(
            'title' => __( 'Server IP/Path', 'veruspay-verus-gateway' ),
            'type' => 'text',
            'default' => 'IP or local folder name (e.g. enter just verustools if at /var/www/html/verustools)',
            'class' => 'wc_veruspay_daemonsettings-toggle ' . $_classArray['7'] . '-ip',
        ),
        'daemon_ssl_7' => array(
            'title' => __( 'Enable SSL?', 'veruspay-verus-gateway' ),
            'type' => 'checkbox',
            'label' => ' ',
            'default' => 'yes',
            'class' => 'wc_veruspay_checkbox_option wc_veruspay_daemonsettings-toggle ' . $_classArray['7'] . '-ssl',
        ),
        'daemon_code_7' => array(
            'title' => __( 'Access Code', 'veruspay-verus-gateway' ),
            'type' => 'text',
            'label' => __( 'Access Code', 'veruspay-verus-gateway' ),
            'class' => 'wc_veruspay_daemonsettings-toggle ' . $_classArray['7'] . '-code',
        ),
        // Add Daemon Button
        'add_daemon' => array(
            'title' => __( '+ Add Daemon Server (7 max)', 'veruspay-verus-gateway' ),
            'type' => 'title',
            'class' => 'wc_veruspay_daemonsettings-toggle wc_veruspay_daemon_add-button',
        ),
    );
    $wc_veruspay_init_firsttabs = array(
        // Mode Server Title (interactive to show/hide Mode section)
        'mode_settings_show' => array(
            'title' => __( '<span class="wc_veruspay_tab-title">' . $_mode_tab_title . '</span>', 'veruspay-verus-gateway' ),
            'type' => 'title',
            'description' => '',
            'class' => 'wc_veruspay_tab-container wc_veruspay_admin_section wc_veruspay_toggledaemon wc_veruspay_pointer',
        ),
/** TODO : Future coin manage section
 *     'coin_settings_show' => array(
 *            'title' => __( '<span class="wc_veruspay_tab-title">Manage Coins</span>', 'veruspay-verus-gateway' ),
 *            'type' => 'title',
 *            'description' => '',
 *            'class' => 'wc_veruspay_tab-container wc_veruspay_admin_section wc_veruspay_togglecoins wc_veruspay_pointer',
 *        ),
 */
    );
}
// If Manual Mode
else if ( $this->mode == 'manual' ) {
    $_manualArray = array('','',);
    $wc_veruspay_init_firsttabs = array(
/** TODO : Future coin manage section
 *        'coin_settings_show' => array(
 *            'title' => __( '<span class="wc_veruspay_tab-title">Manage Coins</span>', 'veruspay-verus-gateway' ),
 *            'type' => 'title',
 *            'description' => '',
 *            'class' => 'wc_veruspay_tab-container wc_veruspay_admin_section wc_veruspay_togglecoins wc_veruspay_pointer',
 *        ),
 */
    );
}
// If Hosted Mode
else if ( $this->mode == 'hosted' ) {
    $_mode_tab_title = ucfirst( $this->mode );
    $_hostedArray = array('','',);
    // Hosted VerusPay Service array
    $wc_veruspay_init_mode = array(
        'mode_heading' => array(
            'title' => __( 'VerusPay Hosted - Status: coming soon' . $_hostedArray[0], 'veruspay-verus-gateway' ),
            'type' => 'title',
            'description' => '',
            'class' => 'wc_veruspay_title-sub wc_veruspay_title-sub-toggle-heading wc_veruspay_hostedsettings-toggle',
        ),
        'mode_stat' => array(
            'title' => __( 'Configured Coins: ' . $_hostedArray[1], 'veruspay-verus-gateway' ),
            'type' => 'title',
            'description' => '',
            'class' => 'wc_veruspay_section_heading wc_veruspay_title-sub wc_veruspay_title-sub-toggle-under wc_veruspay_hostedsettings-toggle',
        ),
        'hosted_access' => array(
            'title' => __( 'Access Code', 'veruspay-verus-gateway' ),
            'type' => 'text',
            'label' => __( 'Access Code', 'veruspay-verus-gateway' ),
            'class' => 'wc_veruspay_hostedsettings-toggle',
        ),
    );
    $wc_veruspay_init_firsttabs = array(
        // Mode Server Title (interactive to show/hide Mode section)
        'mode_settings_show' => array(
            'title' => __( '<span class="wc_veruspay_tab-title">' . $_mode_tab_title . '</span>', 'veruspay-verus-gateway' ),
            'type' => 'title',
            'description' => '',
            'class' => 'wc_veruspay_tab-container wc_veruspay_admin_section wc_veruspay_toggledaemon wc_veruspay_pointer',
        ),
/** TODO : Future coin manage section
 *       'coin_settings_show' => array(
 *            'title' => __( '<span class="wc_veruspay_tab-title">Manage Coins</span>', 'veruspay-verus-gateway' ),
 *            'type' => 'title',
 *            'description' => '',
 *            'class' => 'wc_veruspay_tab-container wc_veruspay_admin_section wc_veruspay_togglecoins wc_veruspay_pointer',
 *        ),
 */        
    );
}
// TODO : Create error management for this else
else {
    $this->form_fields = apply_filters( 'wc_veruspay_form_fields', wc_veruspay_setup() );
    $this->update_option( 'enabled', 'no' );
    return;
}
$wc_veruspay_init_top = array(
    // Used for feedback and settings within admin
    '_top_of_form' => array(
        'title'	=> '',
        'type' => 'title',
        'class' => 'wc_veruspay_set_css wc_veruspay_form_top'
    ),
    'hidden_set_css' => array(
        'title'	=> '',
        'type' => 'title',
        'class' => 'wc_veruspay_set_css',
    ),
    // Enable/Disable the VerusPay gateway
    'enabled' => array(
        'title' => __( 'ENABLE VERUSPAY', 'veruspay-verus-gateway' ),
        'type' => 'checkbox',
        'label' => ' ',
        'default' => 'yes',
        'class' => 'wc_veruspay_checkbox_option wc_veruspay_enable-check',
    ),
    // Enable/Disable Testmode
    'test_mode' => array(
        'title' => __( $this->testmsg, 'veruspay-verus-gateway' ),
        'type' => 'checkbox',
        'label' => ' ',
        'default' => 'no',
        'class' => 'wc_veruspay_checkbox_option wc_veruspay_testmode-check',
    ),
);
$wc_veruspay_init_tabs = array_merge( $wc_veruspay_init_firsttabs, array(
    // Show/Hide Tabs Headings
    // Wallet Management Title (interactive to show or hide the Wallet section)
    'wallet_settings_show' => array(
        'title' => __( '<span class="wc_veruspay_tab-title">Wallets</span>', 'veruspay-verus-gateway' ),
        'type' => 'title',
        'description' => '',
        'class' => 'wc_veruspay_tab-container wc_veruspay_admin_section wc_veruspay_togglewallet wc_veruspay_pointer',
    ),
    // Addresses Title
    'addr_show' => array(
        'title' => __( '<span class="wc_veruspay_tab-title">Addresses</span>', 'veruspay-verus-gateway' ),
        'type' => 'title',
        'description' => '',
        'class' => 'wc_veruspay_tab-container wc_veruspay_admin_section wc_veruspay_toggleaddr wc_veruspay_pointer',
    ),
    // Content title, interactive (show or hide content customization fields)
    'cust_show' => array(
        'title' => __( '<span class="wc_veruspay_tab-title">Content</span>', 'veruspay-verus-gateway' ),
        'type' => 'title',
        'description' => '',
        'class' => 'wc_veruspay_tab-container wc_veruspay_admin_section wc_veruspay_togglecust wc_veruspay_pointer',
    ),
    // Store Option title, interactive (show or hide options)
    'options_show' => array(
        'title' => __( '<span class="wc_veruspay_tab-title">Options</span>', 'veruspay-verus-gateway' ),
        'type' => 'title',
        'description' => '',
        'class' => 'wc_veruspay_tab-container wc_veruspay_admin_section wc_veruspay_toggleoptions wc_veruspay_pointer',
    ),
) );
// TODO : Future work with manage coin section
/** 
 * $wc_veruspay_coins = array(
 *    'coin_heading' => array(
 *        'title' => __( 'Manage Coins', 'veruspay-verus-gateway' ),
 *        'type' => 'title',
 *        'description' => '',
 *        'class' => 'wc_veruspay_title-sub wc_veruspay_title-sub-toggle-heading wc_veruspay_coinsettings-toggle',
 *    ),
 *    'coin_stat' => array(
 *        'title' => __( 'Manually Configured Coins: ECHOADDEDCOINS', 'veruspay-verus-gateway' ),
 *        'type' => 'title',
 *        'description' => '',
 *        'class' => 'wc_veruspay_section_heading wc_veruspay_title-sub wc_veruspay_title-sub-toggle-under wc_veruspay_coinsettings-toggle',
 *    ),
 *    'coin_fn_1' => array(
 *        'title' => __( 'Custom Name', 'veruspay-verus-gateway' ),
 *        'type' => 'text',
 *        'default' => 'My Primary Daemon Server',
 *        'class' => 'wc_veruspay_coinsettings-toggle',
 *    ),
 *    'coin_ip_1' => array(
 *        'title' => __( 'Server IP/Path', 'veruspay-verus-gateway' ),
 *        'type' => 'text',
 *        'default' => 'IP or local folder name (e.g. enter just verustools if at /var/www/html/verustools)',
 *        'class' => 'wc_veruspay_coinsettings-toggle',
 *    ),
 *    'coin_ssl_1' => array(
 *        'title' => __( 'Enable SSL?', 'veruspay-verus-gateway' ),
 *        'type' => 'checkbox',
 *        'label' => ' ',
 *        'default' => 'yes',
 *        'class' => 'wc_veruspay_checkbox_option wc_veruspay_coinsettings-toggle',
 *    ),
 *    'coin_code_1' => array(
 *        'title' => __( 'Access Code', 'veruspay-verus-gateway' ),
 *        'type' => 'text',
 *        'label' => __( 'Access Code', 'veruspay-verus-gateway' ),
 *        'class' => 'wc_veruspay_coinsettings-toggle',
 *    ),
 *    // Add Coin Button
 *    'add_coin' => array(
 *        'title' => __( '+ Add Coin Manually', 'veruspay-verus-gateway' ),
 *        'type' => 'title',
 *        'class' => 'wc_veruspay_coinsettings-toggle wc_veruspay_coin_add-button',
 *    ),
 *);
 */
// Splice-in placeholder array
$wc_veruspay_init_inserts = array(
    // ***************************
    // Wallet Management Spliced in
    // ***************************
    'wallet_insert' => array(
        'title' => __( '', 'veruspay-verus-gateway' ),
        'type' => 'title',
        'description' => '',
        'class' => 'wc_veruspay_noshow',
    ),
    // ***************************
    // Address Settings Spliced in
    // ***************************
    'addr_insert' => array(
        'title' => __( '', 'veruspay-verus-gateway' ),
        'type' => 'title',
        'description' => '',
        'class' => 'wc_veruspay_noshow',
    ),
);
// Content Customizations array
$wc_veruspay_init_content = array(
    'content_title' => array(
        'title' => __( 'Store Content Customizations', 'veruspay-verus-gateway' ),
        'type' => 'title',
        'description' => '',
        'class' => 'wc_veruspay_section_heading wc_veruspay_title-sub wc_veruspay_title-sub-toggle-heading wc_veruspay_customization-toggle',
    ),
    'description' => array(
        'title' => __( 'Checkout Message', 'veruspay-verus-gateway' ),
        'type' => 'textarea',
        'description' => __( 'A message and any instructions which the customer will see at checkout.', 'veruspay-verus-gateway' ),
        'default' => __( 'After you place your order you will see a screen with a valid cryptocurrency store wallet address to send your cryptocurrency payment within the time allowed.', 'veruspay-verus-gateway' ),
        'desc_tip' => TRUE,
        'class' => 'wc_veruspay_customization-toggle',
    ),
    'msg_before_sale' => array(
        'title' => __( 'Payment Page Message', 'veruspay-verus-gateway' ),
        'type' => 'textarea',
        'description' => __( 'VerusPay-specific message that will be added to the payment page and email, just below the payment address.', 'veruspay-verus-gateway' ),
        'default' => 'Message appears below QR codes at payment processing page',
        'desc_tip' => TRUE,
        'class' => 'wc_veruspay_customization-toggle',
    ),
    'msg_after_sale' => array(
        'title' => __( 'Payment Complete Message', 'veruspay-verus-gateway' ),
        'type' => 'textarea',
        'description' => __( 'VerusPay-specific message that will be added to the payment completed page and email, the final Thank You page', 'veruspay-verus-gateway' ),
        'default' => 'Some additional Thank You message to your customer after payment completes!',
        'desc_tip' => TRUE,
        'class' => 'wc_veruspay_customization-toggle',
    ),
    'msg_cancel' => array(
        'title' => __( 'Order Timeout Cancel Message', 'veruspay-verus-gateway' ),
        'type' => 'textarea',
        'description'	=> __( 'Text to display to an online shopper who waits too long to send crypto during the purchase', 'veruspay-verus-gateway' ),
        'default' => 'It looks like your purchase timed-out waiting for payment in the correct amount.  Sorry for any inconvenience this may have caused and please use the order details below to review your order and place a new one.',
        'desc_tip' => TRUE,
        'class' => 'wc_veruspay_customization-toggle',
    ),
    'email_order' => array(
        'title' => __( 'Custom Email Message When Order is Placed', 'veruspay-verus-gateway' ),
        'type' => 'textarea',
        'description'	=> __( 'Text to send to the customer when they place an order.', 'veruspay-verus-gateway' ),
        'default' => 'Text to send to the customer upon order.',
        'desc_tip' => TRUE,
        'class' => 'wc_veruspay_customization-toggle',
      ),
    'email_cancelled' => array(
        'title' => __( 'Custom Email Message When Order is Cancelled', 'veruspay-verus-gateway' ),
        'type' => 'textarea',
        'description'	=> __( 'Text to send to the customer when an order cancels (usually due to non-payment within timeframe).', 'veruspay-verus-gateway' ),
        'default' => 'Text to send to the customer when an order cancels.',
        'desc_tip' => TRUE,
        'class' => 'wc_veruspay_customization-toggle',
    ),
    'email_completed' => array(
        'title' => __( 'Custom Email Message When Order is Completed', 'veruspay-verus-gateway' ),
        'type' => 'textarea',
        'description'	=> __( 'Text to send to the customer when their order is complete (all blocks confirmed).', 'veruspay-verus-gateway' ),
        'default' => 'Text to send to the customer after all blocks are confirmed and order is complete.',
        'desc_tip' => TRUE,
        'class' => 'wc_veruspay_customization-toggle',
    ),
);
// Store options array
$wc_veruspay_init_options = array(
    'options_title' => array(
        'title' => __( 'VerusPay Gateway Store Options', 'veruspay-verus-gateway' ),
        'type' => 'title',
        'description' => '',
        'class' => 'wc_veruspay_section_heading wc_veruspay_title-sub wc_veruspay_title-sub-toggle-heading wc_veruspay_options-toggle',
    ),
    'decimals' => array(
        'title' => __( 'Crypto Decimals', 'veruspay-verus-gateway' ),
        'type' => 'select',
        'description'	=> __( 'Choose the max decimals to use for crypto prices (up to 8).', 'veruspay-verus-gateway' ),
        'default' => '6',
        'desc_tip' => TRUE,
        'options' => array(
            '1'	=> __( '1', 'veruspay-verus-gateway' ),
            '2'	=> __( '2', 'veruspay-verus-gateway' ),
            '3'	=> __( '3', 'veruspay-verus-gateway' ),
            '4'	=> __( '4', 'veruspay-verus-gateway' ),
            '5'	=> __( '5', 'veruspay-verus-gateway' ),
            '6'	=> __( '6', 'veruspay-verus-gateway' ),
            '7'	=> __( '7', 'veruspay-verus-gateway' ),
            '8'	=> __( '8', 'veruspay-verus-gateway' ),
        ),
        'class' => 'wc_veruspay_options-toggle wc-enhanced-select',
    ),
    'pricetime' => array(
        'title' => __( 'Price timeout', 'veruspay-verus-gateway' ),
        'type' => 'text',
        'description' => __( 'Set the time (in minutes) before realtime crypto calculated price expires at checkout.', 'veruspay-verus-gateway' ),
        'default'	=> '5',
        'desc_tip' => TRUE,
        'class' => 'wc_veruspay_options-toggle',
    ),
    'orderholdtime'	=> array(
        'title' => __( 'Order Wait Time', 'veruspay-verus-gateway' ),
        'type' => 'select',
        'description'	=> __( 'Set the time (in minutes) to wait for the customer to make payment before cancelling the order. Does not impact already placed/pending orders.', 'veruspay-verus-gateway' ),
        'default' => '20',
        'desc_tip' => TRUE,
        'options' => array(
            '20' => __( '20', 'veruspay-verus-gateway' ),
            '25' => __( '25', 'veruspay-verus-gateway' ),
            '30' => __( '30', 'veruspay-verus-gateway' ),
            '45' => __( '45', 'veruspay-verus-gateway' ),
            '60' => __( '60', 'veruspay-verus-gateway' ),
        ),
        'class' => 'wc_veruspay_options-toggle wc-enhanced-select',
    ),
    'confirms' => array(
        'title' => __( 'Confirmations Required', 'veruspay-verus-gateway' ),
        'type' => 'text',
        'description' => __( 'Set the number of block confirmations required before an order is considered complete.', 'veruspay-verus-gateway' ),
        'default'	=> '20',
        'desc_tip' => TRUE,
        'class' => 'wc_veruspay_options-toggle',
    ),
    'qr_max_size' => array(
        'title' => __( 'Default QR Max Size', 'veruspay-verus-gateway' ),
        'type' => 'select',
        'description'	=> __( 'Choose the max size of QR images generated during customer checkout', 'veruspay-verus-gateway' ),
        'default' => 'Large',
        'desc_tip' => TRUE,
        'options' => array(
            '8' => __( 'Small', 'veruspay-verus-gateway' ),
            '10' => __( 'Medium', 'veruspay-verus-gateway' ),
            '12' => __( 'Large', 'veruspay-verus-gateway' ),
        ),
        'class' => 'wc_veruspay_options-toggle wc-enhanced-select',
    ),
    'qr_invoice_memo' => array(
        'title' => __( 'Invoice QR Memo Text', 'veruspay-verus-gateway' ),
        'type' => 'text',
        'description'	=> __( 'Enter a message or store identification to display in the Invoice memo', 'veruspay-verus-gateway' ),
        'default' => 'Thank you for using our Verus-enabled store!',
        'desc_tip' => TRUE,
        'class' => 'wc_veruspay_options-toggle',
    ),
    'qr_invoice_image' => array(
        'title' => __( 'Invoice QR Image', 'veruspay-verus-gateway' ),
        'type' => 'text',
        'description'	=> __( 'Enter url to store logo image', 'veruspay-verus-gateway' ),
        'default' => 'https://veruscoin.io/img/VRSClogo.svg',
        'desc_tip' => TRUE,
        'class' => 'wc_veruspay_options-toggle',
      ),
    'title_sub' => array(
        'title' => __( 'Custom Payment Option Subtitle', 'veruspay-verus-gateway' ),
        'type' => 'text',
        'description' => __( 'Optional sub title displayed under VerusPay Logo at checkout', 'veruspay-verus-gateway' ),
        'default' => '',
        'desc_tip' => TRUE,
        'class' => 'wc_veruspay_options-toggle',
    ),
    // Discount or Fee Options
    'discount_fee' => array(
        'title' => __( 'Set Discount/Fee?', 'veruspay-verus-gateway' ),
        'type' => 'checkbox',
        'label' => ' ',
        'class' => 'wc_veruspay_checkbox_option wc_veruspay_setdiscount wc_veruspay_options-toggle',
    ),
    'disc_title' => array(
        'title' => __( 'Title (visible in checkout)', 'veruspay-verus-gateway' ),
        'type' => 'text',
        'description'	=> __( 'This is the title, seen in checkout, of your discount or fee (should be short)', 'veruspay-verus-gateway' ),
        'default' => __( 'This is the title, seen in checkout, of your discount or fee (should be short)', 'veruspay-verus-gateway' ),
        'desc_tip' => TRUE,
        'class' => 'wc_veruspay_discount-toggle wc_veruspay_options-toggle',
    ),  
    'disc_type'	=> array(
        'title' => __( 'Type (Discount or Fee?)', 'veruspay-verus-gateway' ),
        'type' => 'select',
        'description'	=> __( 'Choose whether to discount or charge an extra fee for using Verus', 'veruspay-verus-gateway' ),
        'default' => 'Discount',
        'desc_tip' => TRUE,
        'options' => array(
            '-' => __( 'Discount', 'veruspay-verus-gateway' ),
            '+'	=> __( 'Fee', 'veruspay-verus-gateway' ),
        ),
        'class' => 'wc_veruspay_discount-toggle wc_veruspay_options-toggle wc-enhanced-select',
    ),
    'disc_amt' => array(
        'title' => __( 'Amount (%)', 'veruspay-verus-gateway' ),
        'type' => 'text',
        'description'	=> __( 'Amount to discount or charge as a fee for using Crypto (in a number representing %)', 'veruspay-verus-gateway' ),
        'default' => __( 'Amount to charge or discount as a % e.g. for 10% enter simple 10', 'veruspay-verus-gateway' ),
        'desc_tip' => TRUE,
        'class' => 'wc_veruspay_discount-toggle wc_veruspay_options-toggle',
    ),
);
// Merge arrays before processing wallet data
if ( $this->mode == 'manual' ) {
    $wc_veruspay_init_mode = array(); // TODO: Coin managmeent: $wc_veruspay_coins;
}
/** TODO: Coin management future work
 * else {
 *    $wc_veruspay_init_mode = array_merge( $wc_veruspay_init_mode, $wc_veruspay_coins );
 * }
 */
$this->form_fields = apply_filters( 'wc_veruspay_form_fields', array_merge( $wc_veruspay_init_top, $wc_veruspay_init_tabs, $wc_veruspay_init_mode, $wc_veruspay_init_inserts, $wc_veruspay_init_content, $wc_veruspay_init_options ) );
/**********************
 * Build Wallet Data
 * 
 * For either daemon/hosted or manual, populate all wallet data as chosen and configured by the store owner
 */
// Splice wallet settings and address settings using foreach
if ( is_array( $wc_veruspay_global['chains'][$this->mode] ) ) {
    foreach( $wc_veruspay_global['chains'][$this->mode] as $key => $item ) {
        $_chain_up = strtoupper( $key );
        $_chain_lo = strtolower( $key );
        $wc_veruspay_set_t = 0;
        $wc_veruspay_set_z = 0;
        // TODO : Fix the chain var building functionalities
        if ( $this->mode == 'daemon' ) {
            if ( $wc_veruspay_global['chains'][$this->mode][$_chain_up]['ST'] == 1 ) {
                $wc_veruspay_global['chains'][$this->mode][$_chain_up]['VV'] = wc_veruspay_go( $wc_veruspay_global['chains'][$this->mode][$_chain_up]['DC'], $wc_veruspay_global['chains'][$this->mode][$_chain_up]['IP'], '_stat_', 'vct_version' );
                $wc_veruspay_global['chains'][$this->mode][$_chain_up]['BV'] = wc_veruspay_go( $wc_veruspay_global['chains'][$this->mode][$_chain_up]['DC'], $wc_veruspay_global['chains'][$this->mode][$_chain_up]['IP'], $_chain_up, 'version' );
                $wc_veruspay_wallet_stat = '<span class="wc_veruspay_stat wc_veruspay_white_on_green">ONLINE</span><span class="wc_veruspay_version">v' . $wc_veruspay_global['chains'][$this->mode][$_chain_up]['BV'] . '</span>';
            }
            else if ( strlen( $this->get_option( $_chain_lo . '_storeaddresses' ) ) > 30 ) {
                $wc_veruspay_wallet_stat = '<span class="wc_veruspay_stat wc_veruspay_white_on_orange">OFFLINE/MANUAL</span>';
            }
            else {
                $wc_veruspay_wallet_stat = '<span class="wc_veruspay_stat wc_veruspay_white_on_red">OFFLINE</span>';
            }
        }
        else {
            $wc_veruspay_wallet_stat = '<span class="wc_veruspay_stat wc_veruspay_white_on_orange">MANUAL</span>';
        }
        // Check for live API price, disable wallet if unable to retrieve
        $wc_veruspay_price_init = wc_veruspay_price( $_chain_up,  get_woocommerce_currency() );
        if ( (int)$wc_veruspay_price_init < 0.00000000 || ! is_numeric( $wc_veruspay_price_init ) || $wc_veruspay_price_init == NULL || empty( $wc_veruspay_price_init ) ) {
            $wc_veruspay_price_init = 'NaN';
            $this->update_option( $_chain_lo . '_enable', 'no' );
        }
        // Add to Wallet Settings array
        $wc_veruspay_add_wallet_data = array(
            $_chain_lo.'_wallet_title' => array(
                'title' => __( '<img class="wc_veruspay_mr10" src="' . $wc_veruspay_global['coinimg'] . $_chain_lo .'.png" />' . $_chain_up . ' ' . $item['FN'] . ' Wallet - Fiat Price: ' . get_woocommerce_currency_symbol() . '<span class="wc_veruspay_fiat_rate" data-coin="'.$_chain_up.'">' . $wc_veruspay_price_init . '</span>', 'veruspay-verus-gateway' ),
                'type' => 'title',
                'description' => '',
                'class' => 'wc_veruspay_section_heading wc_veruspay_title-sub wc_veruspay_title-sub-toggle-heading wc_veruspay_walletsettings-toggle',
            ),
            $_chain_lo.'_status' => array(
                'title' => __( 'Wallet Status: ' . $wc_veruspay_wallet_stat, 'veruspay-verus-gateway' ),
                'type' => 'title',
                'description' => '',
                'class' => 'wc_veruspay_section_heading wc_veruspay_title-sub wc_veruspay_title-sub-toggle-under wc_veruspay_walletsettings-toggle',
            ),
            $_chain_lo.'_enable' => array(
                'title' => __( 'Enable '.$item['FN'].' Payments', 'veruspay-verus-gateway' ),
                'type' => 'checkbox',
                'label' => ' ',
                'default' => 'yes',
                'class' => 'wc_veruspay_checkbox_option wc_veruspay_setwalletip wc_veruspay_walletsettings-toggle',
            ),
        );
        if ( $this->mode == 'daemon' ) {
            // Sapling Enforce (if applicable)
            if ( $item['TX'] == 0 ) { // If both transparent and sapling/private
                $wc_veruspay_set_t = 1;
                $wc_veruspay_set_z = 1;
                $wc_veruspay_add_wallet_data[$_chain_lo.'_default_sapling'] = array(
                    'title' => __( 'Default to Private for ' . $_chain_up, 'veruspay-verus-gateway' ),
                    'type' => 'checkbox',
                    'label' => ' ',
                    'default' => 'no',
                    'class' => 'wc_veruspay_checkbox_option wc-veruspay-sapling-option wc_veruspay_walletsettings-toggle',
                );
                $wc_veruspay_add_wallet_data[$_chain_lo.'_sapling']	= array(
                    'title' => __( 'Enforce Privacy for ' . $_chain_up, 'veruspay-verus-gateway' ),
                    'type' => 'checkbox',
                    'label' => ' ',
                    'default' => 'no',
                    'class' => 'wc_veruspay_checkbox_option wc-veruspay-sapling-option wc_veruspay_walletsettings-toggle',
                );
                $wc_veruspay_sapling_settings = array(
                    'ZC' => 1,
                    'SD' => $this->get_option( $_chain_lo.'_default_sapling' ),
                    'SP' => $this->get_option( $_chain_lo.'_sapling' ),
                    'TC' => 1,
                );
            }
            else if ( $item['TX'] == 2 ) { // If only sapling/private
                $wc_veruspay_set_z = 1;
                $this->update_option( $_chain_lo.'_sapling', 'yes' );
                $wc_veruspay_sapling_settings = array(
                    'ZC' => 1,
                    'SP' => 'yes',
                    'TC' => 0,
                );
            }
            else if ( $item['TX'] == 1 ) { // If only transparent
                $wc_veruspay_set_t = 1;
                $this->update_option( $_chain_lo.'_sapling', 'no' );
                $wc_veruspay_sapling_settings = array(
                    'ZC' => 0,
                    'TC' => 1,
                );
            }
            if ( isset( $item['GS'] ) || isset( $item['GM'] ) ) {
                $wc_veruspay_generate_stat = json_decode( wc_veruspay_go( $wc_veruspay_global['chains'][$this->mode][$_chain_up]['DC'], $wc_veruspay_global['chains'][$this->mode][$_chain_up]['IP'], $_chain_up, 'getgenerate' ), TRUE );
                if ( isset( $item['GS'] ) ) {
                    if ( $wc_veruspay_generate_stat['staking'] == TRUE ) {
                        $_gs_title = '<span class="wc_veruspay_green wc_veruspay_stake_' . $_chain_up . '">Staking Active</span>';
                        $_gs_class = ' wc_veruspay_is_checked';
                    }
                    else {
                        $_gs_title = '<span class="wc_veruspay_stake_' . $_chain_up . '">Activate Staking</span>';
                        $_gs_class = ' wc_veruspay_is_unchecked';
                    }
                    $wc_veruspay_add_wallet_data['stake_enable_' . $_chain_up] = array(
                        'title' => __( $_gs_title, 'veruspay-verus-gateway' ),
                        'type' => 'checkbox',
                        'label' => ' ',
                        'default' => 'no',
                        'class' => 'wc_veruspay_checkbox_option wc_veruspay_setstake wc_veruspay_walletsettings-toggle' . $_gs_class,
                    );
                }
                if ( isset( $item['GM'] ) ) {
                    if ( (int)$wc_veruspay_generate_stat['numthreads'] > 0 ) {
                        $_gm_title = '<span class="wc_veruspay_green wc_veruspay_mine_' . $_chain_up . '" data-threads="' . $wc_veruspay_generate_stat['numthreads'] . '">Mining on ' . $wc_veruspay_generate_stat['numthreads'] . ' threads</span>';
                        $_gm_class = ' wc_veruspay_is_active';
                        $_gm_stat = 'Active';
                        $_gm_stop = 'Stop Mining';
                    }
                    else {
                        $_gm_title = '<span class="wc_veruspay_mine_' . $_chain_up . '" data-threads="' . $wc_veruspay_generate_stat['numthreads'] . '">Activate Mining</span>';
                        $_gm_class = ' wc_veruspay_is_inactive';
                        $_gm_stat = 'Inactive (Select Threads to Begin)';
                        $_gm_stop = 'Stop Mining';
                    }
                    $wc_veruspay_add_wallet_data['generate_threads_' . $_chain_up] = array(
                        'title' => __( $_gm_title, 'veruspay-verus-gateway' ),
                        'type' => 'select',
                        'description' => __( 'Enter the number of threads to dedicate to mining this coin', 'veruspay-verus-gateway' ),
                        'default' => '0',
                        'desc_tip' => TRUE,
                        'options' => array(
                            $_gm_stat => __( $_gm_stat, 'veruspay-verus-gateway' ),
                            $_gm_stop => __( $_gm_stop, 'veruspay-verus-gateway' ),
                            '1' => __( '1', 'veruspay-verus-gateway' ),
                            '2' => __( '2', 'veruspay-verus-gateway' ),
                            '3' => __( '3', 'veruspay-verus-gateway' ),
                            '4' => __( '4', 'veruspay-verus-gateway' ),
                            '5' => __( '5', 'veruspay-verus-gateway' ),
                            '6' => __( '6', 'veruspay-verus-gateway' ),
                            '7' => __( '7', 'veruspay-verus-gateway' ),
                            '8' => __( '8', 'veruspay-verus-gateway' ),
                            '9' => __( '9', 'veruspay-verus-gateway' ),
                            '10' => __( '10', 'veruspay-verus-gateway' ),
                            '11' => __( '11', 'veruspay-verus-gateway' ),
                            '12' => __( '12', 'veruspay-verus-gateway' ),
                            '13' => __( '13', 'veruspay-verus-gateway' ),
                            '14' => __( '14', 'veruspay-verus-gateway' ),
                            '15' => __( '15', 'veruspay-verus-gateway' ),
                            '16' => __( '16', 'veruspay-verus-gateway' ),
                            '17' => __( '17', 'veruspay-verus-gateway' ),
                            '18' => __( '18', 'veruspay-verus-gateway' ),
                            '19' => __( '19', 'veruspay-verus-gateway' ),
                            '20' => __( '20', 'veruspay-verus-gateway' ),
                            '21' => __( '21', 'veruspay-verus-gateway' ),
                            '22' => __( '22', 'veruspay-verus-gateway' ),
                            '23' => __( '23', 'veruspay-verus-gateway' ),
                            '24' => __( '24', 'veruspay-verus-gateway' ),
                            '25' => __( '25', 'veruspay-verus-gateway' ),
                            '26' => __( '26', 'veruspay-verus-gateway' ),
                            '27' => __( '27', 'veruspay-verus-gateway' ),
                            '28' => __( '28', 'veruspay-verus-gateway' ),
                            '29' => __( '29', 'veruspay-verus-gateway' ),
                            '30' => __( '30', 'veruspay-verus-gateway' ),
                            '31' => __( '31', 'veruspay-verus-gateway' ),
                            '32' => __( '32', 'veruspay-verus-gateway' ),
                        ),
                        'class' => 'wc_veruspay_setgenerate wc_veruspay_walletsettings-toggle wc-enhanced-select' . $_gm_class,
                    );
                }
            }
        }
        // Add to Wallet Addresses array
        $wc_veruspay_add_address_data = array();
        if ( $item['TX'] == 0 || $item['TX'] == 1 ) {
            $wc_veruspay_add_address_data[$_chain_lo.'_addresses_title'] = array(
                'title' => __( '<img class="wc_veruspay_mr10" src="' . $wc_veruspay_global['coinimg'] . $_chain_lo .'.png" />' . $item['FN'] . ' ' . $_chain_up . ' Transparent Backup Addresses', 'veruspay-verus-gateway' ),
                'type' => 'title',
                'description' => '',
                'class' => 'wc_veruspay_section_heading wc_veruspay_title-sub wc_veruspay_title-sub-toggle-heading wc_veruspay_addresses-toggle',
            );
            // Store address fields, unused and used
            $wc_veruspay_add_address_data[$_chain_lo.'_storeaddresses'] = array(
                'title' => __( 'Store ' . $_chain_up . ' Addresses', 'veruspay-verus-gateway' ),
                'type' => 'textarea',
                'description' => __( 'Enter ' . $_chain_up . ' addresses you own. If your store has a lot of traffic, we recommend 500 min.  These will also act as a fallback payment method in case there are issues with the wallet for Live stores.', 'veruspay-verus-gateway' ),
                'default' => 'e.g. RHZe298HEehoeHFHEHOEWHOIHEEIHSSLKHF etc',
                'desc_tip' => TRUE,
                'class' => 'wc_veruspay_addresses-toggle',
            );
            $wc_veruspay_add_address_data[$_chain_lo.'_usedaddresses'] = array(	
                'title' => __( 'Used ' . $_chain_up . ' Addresses', 'veruspay-verus-gateway' ),
                'type' => 'textarea',
                'description'	=> __( 'These are manually entered ' . $_chain_up . ' addresses which have been used', 'veruspay-verus-gateway' ),
                'default' => '',
                'desc_tip' => TRUE,
                'class' => 'wc-veruspay-disabled-input wc_veruspay_addresses-toggle'
            );
        }
        if ( isset( $wc_veruspay_global['chain_dtls'][$_chain_lo] ) ) {
            $wc_veruspay_global['chains'][$this->mode][$_chain_up]['EX'] = $wc_veruspay_global['chain_dtls'][$_chain_lo];
        }
        else {
            $wc_veruspay_global['chains'][$this->mode][$_chain_up]['EX'] = '0';
        }
        $wc_veruspay_chain_addtl_settings = array(
            'EN' => $this->get_option( $_chain_lo.'_enable' ),
        );
        $wc_veruspay_global['chains'][$this->mode][$_chain_up] = array_merge( $wc_veruspay_global['chains'][$this->mode][$_chain_up], $wc_veruspay_chain_addtl_settings, $wc_veruspay_sapling_settings );
        // Insert Wallet Management Sections
        if ( $this->mode == 'daemon' && $wc_veruspay_global['chains'][$this->mode][$_chain_up]['ST'] == 1 ) {
            $wc_veruspay_formfields_bal_t = json_decode( wc_veruspay_go( $wc_veruspay_global['chains'][$this->mode][$_chain_up]['DC'], $wc_veruspay_global['chains'][$this->mode][$_chain_up]['IP'], $_chain_up, 'bal' ), TRUE )['transparent'];
            $wc_veruspay_formfields_bal_z = json_decode( wc_veruspay_go( $wc_veruspay_global['chains'][$this->mode][$_chain_up]['DC'], $wc_veruspay_global['chains'][$this->mode][$_chain_up]['IP'], $_chain_up, 'bal' ), TRUE )['private'];
            $wc_veruspay_formfields_bal_u = json_decode( wc_veruspay_go( $wc_veruspay_global['chains'][$this->mode][$_chain_up]['DC'], $wc_veruspay_global['chains'][$this->mode][$_chain_up]['IP'], $_chain_up, 'bal' ), TRUE )['unconfirmed'];
        
            // Validate connection data
            if ( strpos( $item['T'], 'Not Found' ) !== FALSE ) {
                $item['T'] = 'Err: Bad Connection to VerusChainTools or Out of Date!';
                $item['Z'] = 'Err: Bad Connection to VerusChainTools or Out of Date!';
            }
            if ( strpos( $wc_veruspay_formfields_bal_t, 'Not Found' ) !== FALSE || strpos( $wc_veruspay_formfields_bal_z, 'Not Found' ) !== FALSE ) {
                $wc_veruspay_bal_red_css = 'wc_veruspay_red';
                $wc_veruspay_formfields_bal_t = 'Err: Bad Connection to VerusChainTools or Not Installed!';
                $wc_veruspay_formfields_bal_z = 'Err: Bad Connection to VerusChainTools or Not Installed!';
                $wc_veruspay_formfields_bal_u = 'Err: Bad Connection to VerusChainTools or Not Installed!';
            }
            else if ( number_format( $wc_veruspay_formfields_bal_t, 8) == NULL || number_format( $wc_veruspay_formfields_bal_z, 8) == NULL ) {
                $wc_veruspay_bal_red_css = 'wc_veruspay_red';
                $wc_veruspay_formfields_bal_t = 'Err: Wallet Unreachable';
                $wc_veruspay_formfields_bal_z = 'Err: Wallet Unreachable';
                $wc_veruspay_formfields_bal_u = 'Err: Wallet Unreachable';
            }
            else {
                $wc_veruspay_bal_red_css = '';
                $wc_veruspay_formfields_bal_t = number_format( $wc_veruspay_formfields_bal_t, 8 );
                $wc_veruspay_formfields_bal_z = number_format( $wc_veruspay_formfields_bal_z, 8 );
                $wc_veruspay_formfields_bal_u = number_format( $wc_veruspay_formfields_bal_u, 8 );
            }
            if($wc_veruspay_formfields_bal_t > 0){
                if ( strlen( $item['T'] ) < 10 ) {
                    $wc_veruspay_withdraw_t = '<br><span class="wc_veruspay_cashout_text" id="wc_veruspay_cashout_text-'.$_chain_lo.'-getttotalbalance"><span class="wc_veruspay_red">Cashout T-Addr Not Found</span> Set on your wallet server using the UpdateCashout.sh script.</span>';
                }
                else {
                    $wc_veruspay_withdraw_t = '<br><span class="wc_veruspay_cashout_text" id="wc_veruspay_cashout_text-'.$_chain_lo.'-getttotalbalance">Cashout to <span class="wc_veruspay_weight-bold">'.$item['T'].'?</span> <div id="wc_veruspay_tbal-'.$_chain_lo.'-button" class="wc_veruspay_cashout" data-coin="' . $_chain_lo . '" data-type="cashout_t" data-addrtype="Transparent" data-amount="'.$wc_veruspay_formfields_bal_t.'" data-address="'.$item['T'].'">GO</div></span>';
                }
            }
            else {
                $wc_veruspay_withdraw_t = '<br><span class="wc_veruspay_cashout_text wc_veruspay_hidden" id="wc_veruspay_cashout_text-'.$_chain_lo.'-getttotalbalance">Cashout to <span class="wc_veruspay_weight-bold">'.$item['T'].'?</span> <div id="wc_veruspay_tbal-'.$_chain_lo.'-button" class="wc_veruspay_cashout" data-coin="' . $_chain_lo . '" data-type="cashout_t" data-addrtype="Transparent" data-amount="'.$wc_veruspay_formfields_bal_t.'" data-address="'.$item['T'].'">GO</div></span>';
            }
            if($wc_veruspay_formfields_bal_z > 0){
                if ( strlen( $item['Z'] ) < 10 ) {
                    $wc_veruspay_withdraw_z = '<br><span class="wc_veruspay_cashout_text" id="wc_veruspay_cashout_text-'.$_chain_lo.'-getztotalbalance"><span class="wc_veruspay_red">Cashout Z-Addr Not Found</span> Set on your wallet server using the UpdateCashout.sh script.</span>';
                }
                else {
                    $wc_veruspay_withdraw_z = '<br><span class="wc_veruspay_cashout_text" id="wc_veruspay_cashout_text-'.$_chain_lo.'-getztotalbalance">Cashout to <span class="wc_veruspay_weight-bold">'.$item['Z'].'?</span> <div id="wc_veruspay_zbal-'.$_chain_lo.'-button" class="wc_veruspay_cashout" data-coin="' . $_chain_lo . '" data-type="cashout_z" data-addrtype="Private" data-amount="'.$wc_veruspay_formfields_bal_z.'" data-address="'.$item['Z'].'">GO</div></span>';
                }
            }
            else {
                $wc_veruspay_withdraw_z = '<br><span class="wc_veruspay_cashout_text wc_veruspay_hidden" id="wc_veruspay_cashout_text-'.$_chain_lo.'-getztotalbalance">Cashout to <span class="wc_veruspay_weight-bold">'.$item['Z'].'?</span> <div id="wc_veruspay_zbal-'.$_chain_lo.'-button" class="wc_veruspay_cashout" data-coin="' . $_chain_lo . '" data-type="cashout_z" data-addrtype="Private" data-amount="'.$wc_veruspay_formfields_bal_z.'" data-address="'.$item['Z'].'">GO</div></span>';
            }				
        }
        else {
            $wc_veruspay_bal_red_css = 'wc_veruspay_red';
            $wc_veruspay_formfields_bal_t = 'Offline';
            $wc_veruspay_formfields_bal_z = 'Offline';
            $wc_veruspay_formfields_bal_u = 'Offline';
            $wc_veruspay_withdraw_t = '';
            $wc_veruspay_withdraw_z = '';
            echo '<div class="wc_veruspay_noheight" id="wc_veruspay_'.$_chain_lo.'_nostat"></div>';
        }
        // Setup sub array
        $wc_veruspay_wallet_management_data = array();
        $wc_veruspay_wallet_management_data[$_chain_lo.'_wallet_balances'] = array(
            'title' => __( '<span>Wallet Balances</span>' , 'veruspay-verus-gateway' ),
            'type' => 'text',
            'default' => '',
            'description' => '',
            'class' => 'wc_veruspay_noinput wc_veruspay_title-sub_normal wc_veruspay_walletsettings-toggle',
        );
        if ( $wc_veruspay_set_t == 1 ) {
            $wc_veruspay_transparent_chain_settings = array(
                'AD' => $this->get_option( $_chain_lo . '_storeaddresses' ), // Addresses (used for T capable only)
                'AC' => '', // Address count (used for T capable only )
                'UD' => $this->get_option( $_chain_lo . '_usedaddresses' ), // Used addresses (used for T capable only)
            );
            $wc_veruspay_global['chains'][$this->mode][$_chain_up] = array_merge( $wc_veruspay_global['chains'][$this->mode][$_chain_up], $wc_veruspay_transparent_chain_settings );
            $wc_veruspay_wallet_management_data[$_chain_lo.'_wallet_tbalance'] = array(
                'title' => __( 'Transparent Balance: <span class="wc_veruspay_weight-normal"><span class="wc_veruspay_bal_admin '.$wc_veruspay_bal_red_css.'" id="wc_veruspay_tbal-'.$_chain_lo.'" data-coin="'.$_chain_lo.'" data-type="transparent">' . $wc_veruspay_formfields_bal_t . '</span> ' . $_chain_up . $wc_veruspay_withdraw_t . '</span>' , 'veruspay-verus-gateway' ),
                'type' => 'text',
                'default' => '',
                'description' => '',
                'class' => 'wc_veruspay_noinput wc_veruspay_walletsettings-toggle',
            );
        }
        if ( $wc_veruspay_set_z == 1 ) {
            $wc_veruspay_wallet_management_data[$_chain_lo.'_wallet_zbalance'] = array(
                'title' => __( 'Private (Sapling) Balance: <span class="wc_veruspay_weight-normal"><span class="wc_veruspay_bal_admin '.$wc_veruspay_bal_red_css.'" id="wc_veruspay_zbal-'.$_chain_lo.'" data-coin="'.$_chain_lo.'" data-type="private">' . $wc_veruspay_formfields_bal_z . '</span> ' . $_chain_up . $wc_veruspay_withdraw_z . '</span>' , 'veruspay-verus-gateway' ),
                'type' => 'text',
                'default' => '',
                'description' => '',
                'class' => 'wc_veruspay_noinput wc_veruspay_walletsettings-toggle',
            );
        }
        $wc_veruspay_wallet_management_data[$_chain_lo.'_wallet_unbalance'] = array(
            'title' => __( 'Unconfirmed Incoming Balance: <span class="wc_veruspay_weight-normal"><span class="wc_veruspay_bal_admin '.$wc_veruspay_bal_red_css.'" id="wc_veruspay_ubal-'.$_chain_lo.'" data-coin="'.$_chain_lo.'" data-type="unconfirmed">' . $wc_veruspay_formfields_bal_u . '</span> ' . $_chain_up . '</span>' , 'veruspay-verus-gateway' ),
            'type' => 'text',
            'default' => '',
            'description' => '',
            'class' => 'wc_veruspay_noinput wc_veruspay_walletsettings-toggle',
        );
        $wc_veruspay_position = array_search( 'wallet_insert', array_keys( $this->form_fields ) );
        $wc_veruspay_position_after = array_search( 'addr_insert', array_keys( $this->form_fields ) );
        $wc_veruspay_add_wallet_data = array_merge( $wc_veruspay_add_wallet_data, $wc_veruspay_wallet_management_data );
        apply_filters( 'wc_veruspay_form_fields', wc_veruspay_array_splice( $this->form_fields, $wc_veruspay_position_after, 0, $wc_veruspay_add_address_data ) );
        apply_filters( 'wc_veruspay_form_fields', wc_veruspay_array_splice( $this->form_fields, $wc_veruspay_position, 0, $wc_veruspay_add_wallet_data ) );
    }
    // Check store status and wallet connection variants and update stat variable and store settings accordingly
    foreach ( $wc_veruspay_global['chains'][$this->mode] as $key => $item ) {
        $_chain_up = strtoupper( $key );
        $_chain_lo = strtolower( $key );
        // If wallet supports transparent addresses, check for manually entered addrs and cleanup addresses, prep and format before saving
        if ( $wc_veruspay_global['chains'][$this->mode][$_chain_up]['EN'] == 'yes' ) {
            if ( $wc_veruspay_global['chains'][$this->mode][$_chain_up]['TC'] == 1 ){
                if ( ( strpos( $wc_veruspay_global['chains'][$this->mode][$_chain_up]['AD'], 'e.g.') ) === FALSE ) {
                    if ( strlen( $wc_veruspay_global['chains'][$this->mode][$_chain_up]['AD'] ) < 30 ) {
                        $this->update_option( $_chain_lo . '_storeaddresses', '' );
                        $wc_veruspay_global['chains'][$this->mode][$_chain_up]['AD'] = '';
                        $wc_veruspay_global['chains'][$this->mode][$_chain_up]['AC'] = 0;
                    }
                    else {
                        $wc_veruspay_clean_addresses = rtrim( str_replace( ' ', '', str_replace( '"', "", $wc_veruspay_global['chains'][$this->mode][$_chain_up]['AD'] ) ), ',');
                        $this->update_option( $_chain_lo . '_storeaddresses', $wc_veruspay_clean_addresses );
                        $wc_veruspay_global['chains'][$this->mode][$_chain_up]['AC'] = count( explode( ',', $wc_veruspay_clean_addresses ) );
                    }
                    $this->form_fields[ $_chain_lo . '_storeaddresses' ][ 'title' ] = __( 'Store Addresses (' . $wc_veruspay_global['chains'][$this->mode][$_chain_up]['AC'] . ')', 'veruspay-verus-gateway' );
                }
                $wc_veruspay_store_data = $this->get_option( $_chain_lo . '_storeaddresses' );
                $wc_veruspay_global['chains'][$this->mode][$_chain_up]['AD'] = preg_replace( '/\s+/', '', $wc_veruspay_store_data );
                if ( strlen( $wc_veruspay_store_data ) < 30 ) {
                    $wc_veruspay_global['chains'][$this->mode][$_chain_up]['AC'] = 0;
                }
                else if ( strlen( $wc_veruspay_store_data ) > 30 ) {
                    $wc_veruspay_global['chains'][$this->mode][$_chain_up]['AD'] = explode( ',', $wc_veruspay_global['chains'][$this->mode][$_chain_up]['AD'] );
                    $wc_veruspay_global['chains'][$this->mode][$_chain_up]['AC'] = count( $wc_veruspay_global['chains'][$this->mode][$_chain_up]['AD'] );
                }
                $wc_veruspay_global['chains'][$this->mode][$_chain_up]['UD'] = explode( ',', $this->get_option( $_chain_lo . '_usedaddresses' ));
            }
        }
    }
}
$this->update_option( 'wc_veruspay_chains', $wc_veruspay_global['chains'] );

/**
 * VerusPay Setup
 * 
 * Function to provide the setup form fields
 */
function wc_veruspay_setup() {
    $r = array(
        'hidden_set_css' => array(
            'title'	=> '',
            'type' => 'title',
            'class' => 'wc_veruspay_set_css',
            ),
        'stat' => array(
            'title' => 'Initial Setup (VerusPay Currently Disabled)',
            'type' => 'title',
            'description' => '',
            'class' => 'wc_veruspay_setup_title wc_veruspay_initialSetup',
        ),
        'access_code_instructions' => array(
            'title' => __( '<span class="wc_veruspay_center-txt">Configuration Instructions</span>', 'veruspay-verus-gateways' ),
            'type' => 'title',
            'description' => '<span class="wc_veruspay_size-normal">Thank you for installing VerusPay!<br><br>To use this cryptocurrency payment plugin, you must install VerusChainTools on a server running the blockchain daemon for the chain (or chains) you wish to accept. Up to seven (7) daemon servers are supported, so you can run each chain on it\'s own server or limit how many chains run on each daemon server.  <br><br>You may also configure VerusPay to accept payments "manually" without an active daemon server. This is not recommended, but to do so, simply choose the option "No Daemons" below and enter the coins you wish to accept, with their accompanying receive addresses and block explorers. If you do not enter a block explorer for a coin, you will need to manually approve each purchase for that particular coin.<br><br>For daemon support, the recommended config, at the successful completion of VerusChainTools installation and config on each daemon server, you\'ll be provided with an Access Code. Enter that code for the primary server and save...once you\'ve saved your settings you\'ll be able to add up to 6 additioanl Daemon Servers and complete the configuration and customization of your VerusPay plugin.<br><br><span class="wc_veruspay_weight-bold">For for thorough setup/configuration instructions, visit <a href="https://veruspay.io/setup/" target="_BLANK">VerusPay.io/setup</a></span></span><br><br><br>',
            'class' => 'wc_veruspay_title-sub',
        ),
        'enabled' => array(
            'title' => __( 'ENABLE VERUSPAY', 'veruspay-verus-gateway' ),
            'type' => 'checkbox',
            'label' => ' ',
            'default' => 'no',
            'class' => 'wc_veruspay_hide_all',
        ),
        'veruspay_mode'	=> array(
            'title' => __( 'VerusPay Mode', 'veruspay-verus-gateway' ),
            'type' => 'select',
            'default' => 'daemon',
            'options' => array(
                'daemon' => __( 'Full Daemon+Manual Support (recommended)', 'veruspay-verus-gateway' ),
                'manual' => __( 'Manual Support Only', 'veruspay-verus-gateway' ),
                //Not Yet Implemented: '2' => __( 'Hosted Support Only (coming soon)', 'veruspay-verus-gateway' ),
            ),
            'class' => 'wc_veruspay_mode_select wc-enhanced-select',
        ),
        // Deamon Path
        'daemon_fn_1' => array(
            'title' => __( 'Primary Daemon Custom Name', 'veruspay-verus-gateway' ),
            'type' => 'text',
            'default' => 'Give this daemon server a custom name, e.g. "VRSC and ARRR Server"',
            'class' => 'wc_veruspay_daemonsettings-toggle',
        ),
        'daemon_ip_1' => array(
            'title' => __( 'Primary Daemon IP/Path', 'veruspay-verus-gateway' ),
            'type' => 'text',
            'default' => 'IP or local folder name (e.g. enter just verustools if at /var/www/html/verustools)',
            'class' => 'wc_veruspay_daemonsettings-toggle',
        ),
        // SSL Setting
        'daemon_ssl_1' => array(
            'title' => __( 'Enable SSL?', 'veruspay-verus-gateway' ),
            'type' => 'checkbox',
            'label' => ' ',
            'default' => 'yes',
            'class' => 'wc_veruspay_checkbox_option wc_veruspay_daemonsettings-toggle'
        ),
        'daemon_code_1' => array(
            'title' => __( 'Access Code', 'veruspay-verus-gateway' ),
            'type' => 'text',
            'label' => __( 'Access Code', 'veruspay-verus-gateway' ),
            'class' => 'wc_veruspay_daemonsettings-toggle',
        ),
    );
    return $r;
}