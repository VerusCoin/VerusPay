<?php
// No Direct Access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class WC_Gateway_VerusPay extends WC_Payment_Gateway {
    /**
     * Gateway constructor
     * 
     * @access public
     * @param global $wc_veruspay_global
     */
    public function __construct() {
        global $wc_veruspay_global;
        // Begin plugin definitions and define primary method title and description
        $this->id = $wc_veruspay_global['id'];
        $this->mode = $this->get_option( 'veruspay_mode' );
        $this->update_option( 'vrscqrver', $wc_veruspay_global['vrscqrver'] );
        $this->update_option( 'default_coin', $wc_veruspay_global['default_coin'] );
        $this->icon = apply_filters( 'woocommerce_veruspay_icon', $wc_veruspay_global['paths']['public']['img'] . 'wc-veruspay-icon-32x.png' );
        $this->has_fields = TRUE;
        $this->method_title = __( 'VerusPay', 'veruspay-verus-gateway' );
        $this->method_description = __( $wc_veruspay_global['text_help']['method_desc'] . ' Version: <span class="wc_veruspay_blue wc_veruspay_weight-bold">' . $wc_veruspay_global['version'] . '</span>' . $wc_veruspay_global['text_help']['method_desc2'], 'veruspay-verus-gateway' );
        $this->supports = array(
            'products'
        );
        $this->enabled = $this->get_option( 'enabled' );
        $this->test_mode = 'yes' == $this->get_option( 'test_mode' );
        if ( $this->test_mode ) {
            $this->title = __( 'TEST MODE<span id=wc_veruspay_title_sub>'.$this->get_option( 'title_sub').'</span>', 'veruspay-verus-gateway' );
            $this->testmsg = '<span sclass="wc_veruspay_red">TEST MODE ENABLED</span>';
        }
        else {
            $this->title = __( 'VerusPay<span id=wc_veruspay_title_sub>'.$this->get_option( 'title_sub').'</span>', 'veruspay-verus-gateway' );
            $this->testmsg = 'TEST MODE';
        }
        $this->verusQR = $this->get_option( 'vrscqrver'); // For Invoice QR codes
        $this->coin = $this->get_option( 'default_coin' );
        $this->store_inv_msg = $this->get_option( 'qr_invoice_memo' );
        $this->store_img = $this->get_option( 'qr_invoice_image' );
        $this->description  = $this->get_option( 'description' );
        $this->msg_before_sale = $this->get_option( 'msg_before_sale' );
        $this->msg_after_sale = $this->get_option( 'msg_after_sale' );
        $this->msg_cancel = $this->get_option( 'msg_cancel' );
        $this->email_order = $this->get_option( 'email_order' );
        $this->email_cancelled = $this->get_option( 'email_cancelled' );
        $this->email_completed = $this->get_option( 'email_completed' );
        // Clean and count store addresses for backup / manual use
        $this->chains = $this->get_option('wc_veruspay_chains');
        $this->decimals = $this->get_option( 'decimals' ); 
        $this->pricetime = $this->get_option( 'pricetime' );
        $this->orderholdtime = $this->get_option( 'orderholdtime' );
        $this->confirms = $this->get_option( 'confirms' );
        $this->qr_max_size = $this->get_option( 'qr_max_size' );
        $this->discount_fee = 'yes' == $this->get_option( 'discount_fee' );
        $this->verus_dis_title = $this->get_option( 'disc_title' );
        $this->verus_dis_type = $this->get_option( 'disc_type' );
        if ( is_numeric( $this->get_option( 'disc_amt' ) ) ) {
            $this->verus_dis_amt = ( $this->get_option( 'disc_amt' ) / 100 );
        }
        if ( is_admin() && isset( $_GET['section'] ) && $_GET['section'] == $wc_veruspay_global['id'] ) {
            // Load admin-only content
            echo '<div id="wc_veruspay_loading"></div><div id="wc_veruspay_setup_modal" style="display:none;"><p id="wc_veruspay_mode-full">Full Mode Selected: Provide your first (primary) Daemon Server details then click Continue.</p><p id="wc_veruspay_mode-manual">Manual Mode Selected, click Continue.</p><p id="wc_veruspay_mode-hosted">Not Yet Available (coming soon)</p></div><div id="wc_veruspay_gen_modal" style="display:none;"><p id="wc_veruspay-activatingstake">Activating Staking</p><p id="wc_veruspay-deactivatingstake">Deactivating Staking</p><p id="wc_veruspay-activatingmine">Activating Mining</p><p id="wc_veruspay-deactivatingmine">Deactivating Mining</p></div><style>#mainform{opacity:0;}</style>';
            require_once( $wc_veruspay_global['paths']['admin_modal-3'] );
            require_once( $wc_veruspay_global['paths']['admin_modal-4'] );
            // Enqueue Admin JS and CSS; Initialize form
            wp_register_script( 'wc_veruspay_admin_scripts', $wc_veruspay_global['paths']['admin']['js'] . 'wc-veruspay-admin-scripts.js' );
            wp_localize_script( 'wc_veruspay_admin_scripts', 'veruspay_admin_params', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'storecurrency' => get_woocommerce_currency() ) );
            wp_enqueue_style( 'veruspay_admin_css', $wc_veruspay_global['paths']['admin']['css'] . 'wc-veruspay-admin.css' );
            wp_enqueue_script( 'wc_veruspay_admin_scripts' );
            echo '<div id="wc_veruspay_admin_menu" class="wc_veruspay_noheight"></div>';
            $this->init_form_fields();
        }
        // Add actions for payment gateway, scripts, thank you page, and emails
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
        add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
        add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
    }
    /**
     * Init Form Fields
     * 
     * Initialize form fields for the admin settings area only
     * @access public
     * @param global $wc_veruspay_global
     */
    public function init_form_fields() {
        global $wc_veruspay_global;
        require_once( $wc_veruspay_global['paths']['admin_init'] );
    }
    /**
     * Payment Scripts
     * Enqueue JS and localize data and data paths
     * @access public
     * @param global $wc_veruspay_global
     */
    public function payment_scripts() {
        global $wc_veruspay_global;
        // Only initialize javascript on checkout and cart
        if ( ! is_cart() && ! is_checkout() && ! isset( $_GET[ 'pay_for_order' ] ) ) {
            return;
        }
        // Do not enqueue if plugin is disabled
        if ( $this->enabled == 'no' ) {
            return;
        }
        // Setup fee or discount if it exists
        if ( $this->discount_fee ) {
            $wc_veruspay_discount = WC()->cart->subtotal * $this->verus_dis_amt;
            WC()->cart->add_fee( __( $this->verus_dis_title, 'veruspay-verus-gateway' ) , $this->verus_dis_type . $wc_veruspay_discount );
        }
        // Enqueue the scripts and styles for store/front end use
        add_action( 'wp_enqueue_scripts', 'so_enqueue_scripts' );
        wp_register_script( 'wc_veruspay_scripts', $wc_veruspay_global['paths']['public']['js'] . 'wc-veruspay-scripts.js' );
        wp_localize_script( 'wc_veruspay_scripts', 'veruspay_params', array( 'pricetime' => $this->pricetime ) );
        wp_enqueue_style( 'veruspay_css', $wc_veruspay_global['paths']['public']['css'] . 'wc-veruspay.css' );
        wp_enqueue_script( 'wc_veruspay_scripts' );
    }
    /**
     * Customize checkout experience and allow for postbacks by js ajax during checkout
     * 
     * @access public
     * @param global $wc_veruspay_global
     * @param WC_Payment_Gateway $wc_veruspay_price
     */
    public function payment_fields() {
        global $wc_veruspay_global;
        // Get cart total including tax and shipping where applicable 
        $wc_veruspay_price = WC_Payment_Gateway::get_order_total();
        
        // Get payment method and store currency and symbol
        $wc_veruspay_payment_method = WC()->session->get( 'chosen_payment_method' );

        // Check if coin has been selected, if not attempt to activate VRSC
        // TODO: For now use ['daemon'] - future seperate modes for each coin, how to store and act on different modes
        if ( ! empty( $_POST['wc_veruspay_coin'] ) && $this->chains['daemon'][strtoupper( sanitize_text_field( $_POST['wc_veruspay_coin'] ) )]['EN'] == 'yes' ) {
            $_chain_up = strtoupper( sanitize_text_field( $_POST['wc_veruspay_coin'] ) );
            echo '<script>window.location = "#payment";</script>';
            WC()->session->set( 'wc_veruspay_coin', $_chain_up );
        }
        else if ( ! empty( WC()->session->get( 'wc_veruspay_coin' ) ) && $this->chains['daemon'][strtoupper( WC()->session->get( 'wc_veruspay_coin' ) )]['EN'] == 'yes' ) {
            $_chain_up = strtoupper( WC()->session->get( 'wc_veruspay_coin' ) );
        }
        else {
            // Try to default to Verus if no post data
            if ( $this->chains['daemon'][$this->coin]['EN'] == 'yes' ) {
                $_chain_up = strtoupper( $this->coin );
            }
            else {
                // Check for another available coin if Verus is not enabled, set first available as default
                foreach ( $this->chains['daemon'] as $key => $item ) {
                    if ( $item['EN'] == 'yes' ) {
                        $_chain_up = strtoupper( $key );
                        break;
                    }
                }
            }
            if ( ! isset( $_chain_up ) || empty( $_chain_up ) ) {
                $this->update_option( 'enabled', 'no' );
                header("Refresh: 0");
            }
        }
        $_chain_lo = strtolower( $_chain_up );
        // Get the current rate of selected coin from the phpext script and api call
        $wc_veruspay_rate = wc_veruspay_price( $_chain_up, get_woocommerce_currency() );
        if ( (int)$wc_veruspay_rate < 0.00000000 || ! is_numeric( $wc_veruspay_rate ) || $wc_veruspay_rate == NULL || empty( $wc_veruspay_rate ) ) {
            $wc_veruspay_rate = 'NaN';
            $this->update_option( $_chain_lo . '_enable', 'no' );
        }

        // Calculate the total cart in selected crypto 
        $wc_veruspay_price = number_format( ( $wc_veruspay_price / $wc_veruspay_rate ), $this->decimals );

        // Calculate order times and timeouts
        $wc_veruspay_time_start = strtotime( date( 'Y-m-d H:i:s', time() ) ); // Get time now, used in calculating countdown
        $wc_veruspay_time_end = strtotime( '+' . $this->pricetime . ' minutes', $wc_veruspay_time_start ); // Setup countdown target time using order hold time data
        $wc_veruspay_sec_remaining = $wc_veruspay_time_end - $wc_veruspay_time_start; // Get difference between expire time and now in seconds        
        $wc_veruspay_time_remaining = gmdate( 'i:s', $wc_veruspay_sec_remaining ); // Format time-remaining for view
        $wc_veruspay_pricetimesec = ( $this->pricetime * 60 );

        // Setup refresh to occur on store-owner-defined price timeout
        header( 'Refresh:' . $wc_veruspay_pricetimesec );
        
        // Hidden divs for price and address generating feedback on click
        echo '<div class="wc_veruspay_processing-address" id="wc_veruspay_generate_order">' . $wc_veruspay_global['text_help']['msg_modal_gen_addr'] . '</div>';
        echo '<div class="wc_veruspay_processing-address" id="wc_veruspay_updating_price">' . $wc_veruspay_global['text_help']['msg_modal_update_price'] . '</div>';

        // Create hidden fields for price and time updated data
        echo '<input type="hidden" name="wc_veruspay_address" value="">
            <input type="hidden" name="wc_veruspay_price" value="' . $wc_veruspay_price . '">
            <input type="hidden" name="wc_veruspay_rate" value="' . $wc_veruspay_rate . '">
            <input type="hidden" name="wc_veruspay_pricestart" value="' . $wc_veruspay_time_start . '">
            <input type="hidden" name="wc_veruspay_pricetime" value="' . $this->pricetime . '">
            <input type="hidden" name="wc_veruspay_orderholdtime" value="' . $this->orderholdtime . '">
            <input type="hidden" name="wc_veruspay_confirms" value="' . $this->confirms . '">
            <input type="hidden" name="wc_veruspay_status" value="order">
            <input type="hidden" name="wc_veruspay_memo" value="' . $this->store_inv_msg . '">
            <input type="hidden" name="wc_veruspay_img" value="' . $this->store_img . '">'; 
        
        // Setup Sapling checkbox if Sapling is not enforced by store owner setting, unless enforced by coin (ARRR)
        $wc_veruspay_sapling_option = '';
        if( is_checkout() && $wc_veruspay_payment_method == $wc_veruspay_global['id'] && $this->chains['daemon'][$_chain_up]['ST'] == 1 && $this->chains['daemon'][$_chain_up]['ZC'] == 1 && $this->chains['daemon'][$_chain_up]['SP'] == 'no' ) {
            if ( $this->chains['daemon'][$_chain_up]['SD'] == 'no' ) {
                $_sapling_checked = ' ';
            }
            else {
                $_sapling_checked = ' checked';
            }
            $wc_veruspay_sapling_option = '<div class="wc_veruspay_sapling-option"><div class="wc_veruspay_sapling-checkbox wc_veruspay_sapling_tooltip"><label><input id="veruspay_sapling" type="checkbox" class="checkbox" name="wc_veruspay_sapling" value="yes"'.$_sapling_checked.'>' . $wc_veruspay_global['text_help']['msg_sapling_label'] . '</label><span class="wc_veruspay_sapling_tooltip-text">' . $wc_veruspay_global['text_help']['msg_sapling_tooltip'] . '</span></div></div>';
        }
        else if ( is_checkout() && $wc_veruspay_payment_method == $wc_veruspay_global['id'] && $this->chains['daemon'][$_chain_up]['ST'] == 1 && $this->chains['daemon'][$_chain_up]['ZC'] == 1 && $this->chains['daemon'][$_chain_up]['SP'] == 'yes' ) {
            echo '<input id="veruspay_enforce_sapling" type="hidden" name="wc_veruspay_sapling" value="yes">';
        }
        require_once( $wc_veruspay_global['paths']['chkt_path'] );
        // Include optional description set by store owner
        if ( $this->description ) {
            echo wpautop( wp_kses_post( $this->description ) );
        }
    }
    /**
     * Output for the order received page.
     * @param int $order_id
     */
    public function thankyou_page( $order_id ) {
        $order = wc_get_order( $order_id );
    }

    /**
     * Process the payment and return the result
     * 
     * @param int $order_id
     * @param global $wc_veruspay_global
     * @return array
     */
    public function process_payment( $order_id ) {
        global $wc_veruspay_global;
        $order = wc_get_order( $order_id );
        // On process set the order to on-hold and reduce stock
        $order->update_status( 'on-hold', __( $wc_veruspay_global['text_help']['awaiting_payment'], 'veruspay-verus-gateway' ) );
        $order->reduce_order_stock();
        WC()->cart->empty_cart();
        return array(
            'result' 	=> 'success',
            'redirect'	=> $this->get_return_url( $order )
        );
    }
}