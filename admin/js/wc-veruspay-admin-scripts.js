var storecurrency = veruspay_admin_params.storecurrency;
var ajax_url = veruspay_admin_params.ajax_url;
jQuery( function( $ ) {
	$(document).ready(function() {
		/**
		 * On Load Section
		 * Functions and changes occurring on load only
		 */
		// Format and adjust classes on load
		$( '#verus_chain_tools_version' ).insertAfter('#woocommerce_veruspay_verus_gateway_access_code').css('display','inline-block');
		$( '.wc_veruspay_checkbox_option' ).closest( 'label' ).addClass( 'wc_veruspay_control wc_veruspay_control--checkbox' );
		$( '<div class="wc_veruspay_control_container"><div class="wc_veruspay_control__indicator"></div></div>' ).insertAfter( '.wc_veruspay_checkbox_option' );
		$( '.wc_veruspay_section_heading' ).next().find( 'tbody' ).addClass( 'wc_veruspay_section_body' );
		$( '.wc_veruspay_tab-container' ).next( 'table' ).remove();
		if ( $('.wc_veruspay_toggledaemon').length == 0 && $('.wc_veruspay_togglecoins').length == 0 ) {
			//alert('setup');
			$( '#wc_veruspay_admin_menu' ).remove();
			$( '.woocommerce-save-button' ).text( 'Continue' );
		}
		if ( $('.wc_veruspay_toggledaemon').length == 1 ) {
			//alert('daemons');
			$( '.wc_veruspay_coinsettings-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
			$( '.wc_veruspay_coinsettings-toggle' ).addClass('wc_veruspay_set_css');
			$( '#wc_veruspay_admin_menu' ).removeClass( 'wc_veruspay_noheight' ).insertBefore( '.wc_veruspay_toggledaemon' );
			$( '.wc_veruspay_tab-container' ).appendTo( '#wc_veruspay_admin_menu' );
		}
		if ( $('.wc_veruspay_toggledaemon').length == 0 && $('.wc_veruspay_togglecoins').length == 1 ) {
			//alert('coin only');
			$( '#wc_veruspay_admin_menu' ).removeClass( 'wc_veruspay_noheight' ).insertBefore( '.wc_veruspay_togglecoins' );
			$( '.wc_veruspay_tab-container' ).appendTo( '#wc_veruspay_admin_menu' );
		}
		$( '.wc_veruspay_noinput' ).closest( 'tr' ).addClass( 'wc_veruspay_titleonly_row' );
		$( '.wc_veruspay_noinput' ).closest( 'tr' ).find( 'td' ).find( 'legend' ).remove();
		$( '.wc_veruspay_title-sub_normal' ).closest( 'tr' ).addClass ( 'wc_veruspay_title-sub_normal' );
		$( '.wc_veruspay_set_css' ).closest( 'div' ).addClass( 'wc_veruspay_wrapper' );
		$( '.wc_veruspay_hide_all' ).closest( 'tr' ).hide();
		// Daemon Adds
		$( '.wc_veruspay_daemon_add-button').closest('td').prev().hide();
		$( '.wc_veruspay_daemon_add-title' ).hide();
		$( '.wc_veruspay_daemon_add-status').hide();
		$( '.wc_veruspay_daemon_add-fn,.wc_veruspay_daemon_add-ip,.wc_veruspay_daemon_add-ssl,.wc_veruspay_daemon_add-code' ).closest('tbody').hide();
		// Coin Adds
		$( '.wc_veruspay_coin_add-button').closest('td').prev().hide();
		$( '.wc_veruspay_coin_add-title' ).hide();
		$( '.wc_veruspay_coin_add-status').hide();
		$( '.wc_veruspay_coin_add-fn,.wc_veruspay_coin_add-ip,.wc_veruspay_coin_add-ssl,.wc_veruspay_coin_add-code' ).closest('tbody').hide();
		// Check boxes
		$( '.wc_veruspay_is_checked' ).prop( 'checked', true );
		$( '.wc_veruspay_is_unchecked' ).prop( 'checked', false );
		$( '.wc_veruspay_is_inactive option' ).prop( 'selected', false );
		$( '.wc_veruspay_is_inactive option[value="Inactive (Select Threads to Begin)"]').prop( 'selected', true );
		$( '.wc_veruspay_is_active option[value="Active"]' ).prop( 'selected', true );
		if ( $( '.wc_veruspay_setdiscount' ).is( ':checked' ) ) {
			$( '.wc_veruspay_discount-toggle' ).closest('tr').show();
		}
		else { $( '.wc_veruspay_discount-toggle' ).closest('tr').hide();
		}
		$( '.wc_veruspay_setdiscount' ).change( function( event ) {
			if ( $( '.wc_veruspay_setdiscount' ).is( ':checked' ) ) {
				$( '.wc_veruspay_discount-toggle' ).closest('tr').show();
			}
			if ( ! $( '.wc_veruspay_setdiscount' ).is( ':checked' ) ) {
				$( '.wc_veruspay_discount-toggle' ).closest('tr').hide();
			}
		});
		if ( $('.wc_veruspay_toggledaemon').length == 0 && $('.wc_veruspay_togglecoins').length == 1 ) {
			$( '.wc_veruspay_togglecoins' ).addClass( 'wc_veruspay_active_tab' );
		}
		else {
			$( '.wc_veruspay_toggledaemon' ).addClass( 'wc_veruspay_active_tab' );
		}
		$( '.wc_veruspay_walletsettings-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
		$( '.wc_veruspay_walletsettings-toggle' ).addClass('wc_veruspay_set_css');
		$( '.wc_veruspay_addresses-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
		$( '.wc_veruspay_addresses-toggle' ).addClass('wc_veruspay_set_css');
		$( '.wc_veruspay_customization-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
		$( '.wc_veruspay_customization-toggle' ).addClass('wc_veruspay_set_css');
		$( '.wc_veruspay_options-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
		$( '.wc_veruspay_options-toggle' ).addClass('wc_veruspay_set_css');
		$( '.wc_veruspay_hostedsettings-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
		$( '.wc_veruspay_hostedsettings-toggle' ).addClass('wc_veruspay_set_css');
		// After Loaded, Fade-Out Loading Screen
		$( '#wc_veruspay_loading' ).delay(4000).queue( function( next ){
			$( this ).fadeOut(800);
			$( '#mainform' ).addClass( 'wc_veruspay_fadein' ).css('opacity','');
			next();
		});

		/**
		 * Interactive Section
		 * Click, Select, Intervals, Change events, etc
		 */
		// Setup Page
		$( '.wc_veruspay_mode_select' ).on( 'change', function(e) {
			var valueSelected = this.value;
			if ( valueSelected == 'daemon' ) {
				$( '#wc_veruspay_setup_modal' ).fadeIn();
				$( '#wc_veruspay_mode-full' ).fadeIn().delay(1000).queue( function( next ) {
					$( '.wc_veruspay_daemonsettings-toggle' ).closest( 'tr' ).show();
					$( '#wc_veruspay_setup_modal' ).fadeOut();
					$( this ).fadeOut();
					next();
				});
			}
			if ( valueSelected == 'manual' ) {
				$( '#wc_veruspay_setup_modal' ).fadeIn();
				$( '#wc_veruspay_mode-manual' ).fadeIn().delay(1000).queue( function( next ) {
					$( '.wc_veruspay_daemonsettings-toggle' ).closest( 'tr' ).hide();
					$( '#wc_veruspay_setup_modal' ).fadeOut();
					$( this ).fadeOut();
					next();
				});
			}
			// Not Yet Implemented:
			/*if ( valueSelected == '2' ) {
				$( '#wc_veruspay_setup_modal' ).fadeIn();
				$( '#wc_veruspay_mode-hosted' ).fadeIn().delay(3000).queue( function( next ) {
					$( '#wc_veruspay_setup_modal' ).fadeOut();
					$( this ).fadeOut();
					next();
				});
			}*/
		});

		// Click actions  wc_veruspay_customization-toggle
		// Update Modal
		$('.wc_veruspay_edit_daemon').click(function(e) {
			var l = 'localhost';
			var i = '127.0.0.1';
			var url = $(this).attr('data-url');
			var root = $(this).attr('data-root');
			root = root.replace('https://', '');
			var lcheck = url.startsWith(l);
			var icheck = url.startsWith(i);
			if ( lcheck === true ) {
				url = url.replace(l, root);
			}
			else if ( icheck === true ) {
				url = url.replace(i, root);
			}
			url = 'https://'+url;
			$( '#wc_veruspay_update_modal-go' ).attr( 'data-url', url );
			$( '#wc_veruspay_update_modal' ).fadeIn();
		});
		$('#wc_veruspay_update_modal-go').click(function(e) {
			var url = $(this).data('url');
			var code = $('#wc_veruspay_update_code').val();
			if ( $.trim( code ) != '' && $.trim( code ).length == 72 ){
				$('#wc_veruspay_update_iframe').attr('src', url+'?update=1&code='+code);
			}
		});
		$('#wc_veruspay_update_modal-container_close').on( 'click', function(e) {
			var urllen = $('#wc_veruspay_update_iframe').attr('src').length;
			$( '#wc_veruspay_update_modal' ).fadeOut();
			$( '#wc_veruspay_update_code').val('');
			$( '#wc_veruspay_update_modal-go' ).attr( 'data-url', '' );
			if ( urllen != '7' ) {
				$('#wc_veruspay_update_iframe').attr('src', 'http://').delay(1000).queue( function( next ) {
					location.reload();
				});
			}
		});

		$('.wc_veruspay_toggledaemon').click(function(e) {
			if ( ! ( $( this ).hasClass( 'wc_veruspay_active_tab' ) ) ) {
				$( '.wc_veruspay_active_tab' ).removeClass( 'wc_veruspay_active_tab' );
				// hide all
				$( '.wc_veruspay_coinsettings-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_coinsettings-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_walletsettings-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_walletsettings-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_addresses-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_addresses-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_customization-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_customization-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_options-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_options-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_hostedsettings-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_hostedsettings-toggle' ).addClass('wc_veruspay_set_css');
				//
				$( this ).addClass( 'wc_veruspay_active_tab' );
				$( '.wc_veruspay_daemonsettings-toggle' ).closest('tbody').removeClass('wc_veruspay_set_css');
				$( '.wc_veruspay_daemonsettings-toggle' ).removeClass('wc_veruspay_set_css');
			}
		});
		$('.wc_veruspay_togglecoins').click(function(e) {
			if ( ! ( $( this ).hasClass( 'wc_veruspay_active_tab' ) ) ) {
				$( '.wc_veruspay_active_tab' ).removeClass( 'wc_veruspay_active_tab' );
				// hide all
				$( '.wc_veruspay_daemonsettings-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_daemonsettings-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_walletsettings-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_walletsettings-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_addresses-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_addresses-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_customization-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_customization-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_options-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_options-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_hostedsettings-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_hostedsettings-toggle' ).addClass('wc_veruspay_set_css');
				//
				$( this ).addClass( 'wc_veruspay_active_tab' );
				$( '.wc_veruspay_coinsettings-toggle' ).closest('tbody').removeClass('wc_veruspay_set_css');
				$( '.wc_veruspay_coinsettings-toggle' ).removeClass('wc_veruspay_set_css');
			}
		});
		$('.wc_veruspay_togglewallet').click(function(e) {
			if ( ! ( $( this ).hasClass( 'wc_veruspay_active_tab' ) ) ) {
				$( '.wc_veruspay_active_tab' ).removeClass( 'wc_veruspay_active_tab' );
				// hide all
				$( '.wc_veruspay_daemonsettings-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_daemonsettings-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_coinsettings-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_coinsettings-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_addresses-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_addresses-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_customization-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_customization-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_options-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_options-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_hostedsettings-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_hostedsettings-toggle' ).addClass('wc_veruspay_set_css');
				//
				$( this ).addClass( 'wc_veruspay_active_tab' );
				$( '.wc_veruspay_walletsettings-toggle' ).closest('tbody').removeClass('wc_veruspay_set_css');
				$( '.wc_veruspay_walletsettings-toggle' ).removeClass('wc_veruspay_set_css');
			}
		});
		$( '.wc_veruspay_toggleaddr' ).click(function(e) {
			if ( ! ( $( this ).hasClass( 'wc_veruspay_active_tab' ) ) ) {
				$( '.wc_veruspay_active_tab' ).removeClass( 'wc_veruspay_active_tab' );
				// hide all
				$( '.wc_veruspay_daemonsettings-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_daemonsettings-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_coinsettings-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_coinsettings-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_walletsettings-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_walletsettings-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_customization-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_customization-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_options-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_options-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_hostedsettings-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_hostedsettings-toggle' ).addClass('wc_veruspay_set_css');
				//
				$( this ).addClass( 'wc_veruspay_active_tab' );
				$( '.wc_veruspay_addresses-toggle' ).closest('tbody').removeClass('wc_veruspay_set_css');
				$( '.wc_veruspay_addresses-toggle' ).removeClass('wc_veruspay_set_css');
			}
		});
		$('.wc_veruspay_togglecust').click(function(e) {
			if ( ! ( $( this ).hasClass( 'wc_veruspay_active_tab' ) ) ) {
				$( '.wc_veruspay_active_tab' ).removeClass( 'wc_veruspay_active_tab' );
				// hide all
				$( '.wc_veruspay_daemonsettings-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_daemonsettings-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_coinsettings-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_coinsettings-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_walletsettings-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_walletsettings-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_addresses-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_addresses-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_options-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_options-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_hostedsettings-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_hostedsettings-toggle' ).addClass('wc_veruspay_set_css');
				//
				$( this ).addClass( 'wc_veruspay_active_tab' );
				$( '.wc_veruspay_customization-toggle' ).closest('tbody').removeClass('wc_veruspay_set_css');
				$( '.wc_veruspay_customization-toggle' ).removeClass('wc_veruspay_set_css');
			}
		});
		$('.wc_veruspay_toggleoptions').click(function(e) {
			if ( ! ( $( this ).hasClass( 'wc_veruspay_active_tab' ) ) ) {
				$( '.wc_veruspay_active_tab' ).removeClass( 'wc_veruspay_active_tab' );
				// hide all
				$( '.wc_veruspay_daemonsettings-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_daemonsettings-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_coinsettings-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_coinsettings-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_walletsettings-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_walletsettings-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_addresses-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_addresses-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_customization-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_customization-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_hostedsettings-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_hostedsettings-toggle' ).addClass('wc_veruspay_set_css');
				//
				$( this ).addClass( 'wc_veruspay_active_tab' );
				$( '.wc_veruspay_options-toggle' ).closest('tbody').removeClass('wc_veruspay_set_css');
				$( '.wc_veruspay_options-toggle' ).removeClass('wc_veruspay_set_css');
			}
		});
		$('.wc_veruspay_togglehosted').click(function(e) {
			if ( ! ( $( this ).hasClass( 'wc_veruspay_active_tab' ) ) ) {
				$( '.wc_veruspay_active_tab' ).removeClass( 'wc_veruspay_active_tab' );
				// hide all
				$( '.wc_veruspay_daemonsettings-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_daemonsettings-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_coinsettings-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_coinsettings-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_walletsettings-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_walletsettings-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_addresses-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_addresses-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_customization-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_customization-toggle' ).addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_options-toggle' ).closest('tbody').addClass('wc_veruspay_set_css');
				$( '.wc_veruspay_options-toggle' ).addClass('wc_veruspay_set_css');
				//
				$( this ).addClass( 'wc_veruspay_active_tab' );
				$( '.wc_veruspay_hostedsettings-toggle' ).closest('tbody').removeClass('wc_veruspay_set_css');
				$( '.wc_veruspay_hostedsettings-toggle' ).removeClass('wc_veruspay_set_css');
			}
		});
		// Refresh prices
		var lastPrice = function() {
			$( '.wc_veruspay_fiat_rate' ).each( function() {
				var newval = '';
				var coin = $( this ).attr( 'data-coin' );
				var lastval = $( this ).text();
				var div = this;
				$.ajax({
					url: ajax_url,
					type: "POST",
					data: {
						action:"wc_veruspay_price_refresh",
						"coin":coin
					},
					success: function(response){
						$(div).hide();
						newval = response;
						if ( lastval > newval ) {
							$( div ).html( newval ).css('background-color','red');
							$( div ).fadeIn(800).queue( function(next){
								$( div ).css( 'background-color', 'transparent' ); 
								next();
							});
						}
						if ( lastval < newval ) {
							$( div ).html( newval ).css('background-color','#12ee12');
							$( div ).fadeIn(800).queue( function(next){
								$( div ).css( 'background-color', 'transparent' ); 
								next();
							});					
						}
						else {
							$( div ).html( newval ).css('background-color','#12ee12');
							$( div ).fadeIn(800).queue( function(next){
								$( div ).css( 'background-color', 'transparent' ); 
								next();
							});				
						}						
					}
				});
			});
		}
		// Refresh balances
		var refreshBalance = function() {
			$( ".wc_veruspay_bal_admin" ).each(function() {
				var ccoin = $(this).attr("data-coin");
				var ctype = $(this).attr("data-type");
				var update = $(this).attr('id');
				var div = this;
				if ( $('#wc_veruspay_'+ccoin+'_nostat').length == 0 ) {
					$.ajax({
						url: ajax_url,
						type: "POST",
						data: {
							action:"wc_veruspay_balance_refresh",
							"coin":ccoin,
							"type":ctype
						},
						success: function(response){
							if ( response == 0 ) {
								$("#"+update).removeClass("wc_veruspay_red");
								$('#wc_veruspay_cashout_text-'+ccoin+'-'+ctype).hide();
								$("#"+update).text(response);
								$("#"+update+"-button").attr("data-amount",response);
							}
							if ( response > 0 ) {
								$("#"+update).removeClass("wc_veruspay_red");
								$('#wc_veruspay_cashout_text-'+ccoin+'-'+ctype).fadeIn();
								$("#"+update).text(response);
								$("#"+update+"-button").attr("data-amount",response);
							}
							if ( response.indexOf( "Err:" ) >= 0 ) {
								$("#"+update).addClass("wc_veruspay_red");
								$('#wc_veruspay_cashout_text-'+ccoin+'-'+ctype).hide();
								$("#"+update).text(response);
								$("#"+update+"-button").attr("data-amount",response);
							}
						}
					});
				}
			});
		}
		// Run process intervals
		setInterval( function() {
			lastPrice();
			var nowtime = $.now();
		}, (60000));

		setInterval( function() {
			refreshBalance();
			var nowtime = $.now();
		}, (20000));
		// - //
		// Add Daemons
		$('.wc_veruspay_daemon_add-button').click(function() {
			$('.wc_veruspay_daemon_add-title:first').removeClass('wc_veruspay_daemon_add-title').show();
			$('.wc_veruspay_daemon_add-status:first').removeClass('wc_veruspay_daemon_add-status').show();
			$('.wc_veruspay_daemon_add-fn:first').removeClass('wc_veruspay_daemon_add-fn').closest('tbody').show();
			$('.wc_veruspay_daemon_add-ip:first').removeClass('wc_veruspay_daemon_add-ip').closest('tbody').show();
			$('.wc_veruspay_daemon_add-ssl:first').removeClass('wc_veruspay_daemon_add-ssl').closest('tbody').show();
			$('.wc_veruspay_daemon_add-code:first').removeClass('wc_veruspay_daemon_add-code').closest('tbody').show();
		});
		// Add Manual Coins
		$('.wc_veruspay_coin_add-button').click(function() {
			$('.wc_veruspay_coin_add-title:first').removeClass('wc_veruspay_coin_add-title').show();
			$('.wc_veruspay_coin_add-status:first').removeClass('wc_veruspay_coin_add-status').show();
			$('.wc_veruspay_coin_add-fn:first').removeClass('wc_veruspay_coin_add-fn').closest('tbody').show();
			$('.wc_veruspay_coin_add-ip:first').removeClass('wc_veruspay_coin_add-ip').closest('tbody').show();
			$('.wc_veruspay_coin_add-ssl:first').removeClass('wc_veruspay_coin_add-ssl').closest('tbody').show();
			$('.wc_veruspay_coin_add-code:first').removeClass('wc_veruspay_coin_add-code').closest('tbody').show();
		});
		// 1-Click Cashout Initiated
		$('.wc_veruspay_cashout').click(function() {
			var icoin = $(this).attr("data-coin");
			var itype = $(this).attr("data-addrtype");
			var icomm = $(this).attr("data-type");
			var iamount = $(this).attr("data-amount");
			var iaddress = $(this).attr("data-address");
			$('#wc_veruspay_modal_button-cashout').attr("data-coin",icoin);
			$('#wc_veruspay_modal_button-cashout').attr("data-type",icomm);
			$('.wc_veruspay_modal_coin').text(icoin);
			$('.wc_veruspay_modal_type').text(itype);
			$('.wc_veruspay_modal_amount').text(iamount);
			$('.wc_veruspay_modal_address').text(iaddress);
			$('.wc_veruspay_cashout-modalback').fadeIn();
		});
		// - //

		// 1-Click Cashout Cancelled
		$('#wc_veruspay_modal_button-cancel').click(function() {
			$('.wc_veruspay_cashout-modalback').fadeOut();
			$('.wc_veruspay_modal_coin').text('');
			$('.wc_veruspay_modal_type').text('');
			$('.wc_veruspay_modal_amount').text('');
			$('.wc_veruspay_modal_address').text('');
		});
		// - //

		// 1-Click Cashout Confirmed
		$('#wc_veruspay_modal_button-cashout').click(function() {
			var vcoin = $(this).attr("data-coin");
			var vtype = $(this).attr("data-type");
			$('.wc_veruspay_cashout-modalback').fadeOut();
			$('.wc_veruspay_modal_coin').text('');
			$('.wc_veruspay_modal_type').text('');
			$('.wc_veruspay_modal_amount').text('');
			$('.wc_veruspay_modal_address').text('');
			$('.wc_veruspay_cashout_processing-modalback').fadeIn();
			$.ajax({
				url: ajax_url,
				type: "POST",
				data: {
					action:"wc_veruspay_cashout_do",
					"coin":vcoin,
					"type":vtype
				}
			}).done(function(data) {
				$('.wc_veruspay_cashout_processing-modalback').hide();
				$('.wc_veruspay_cashout_complete-modalcontent').html(data);
				$('.wc_veruspay_cashout_complete-modalback').fadeIn();
			});
			refreshBalance();
		});
		// - //
		// Cashout Confirm Close Button
		$('#wc_veruspay_modal_complete_button-close').click(function() {
			$('.wc_veruspay_cashout_complete-modalback').fadeOut();
			$('.wc_veruspay_cashout_complete-modalcontent').text('');
		});
		/**
		 * Generate Section
		 * Staking and mining control functionality with realtime (ajax) commands and status
		 */
		// Staking Change
		$( '.wc_veruspay_setstake' ).on( 'change', function(e) {
			var stakeid = $( this ).attr( 'id' );
			coin = stakeid.replace( 'woocommerce_veruspay_verus_gateway_stake_enable_', '' );
			var return_title = $( '.wc_veruspay_stake_'+coin ).text();
			if ( $( this ).is( ':checked' ) ) {
				var modal_text = 'Activating Staking';
				if ( $( '#woocommerce_veruspay_verus_gateway_generate_threads_'+coin ).hasClass( 'wc_veruspay_is_active' ) ) {
					var threads = $( '.wc_veruspay_mine_'+coin ).data( 'threads' );
					var gentype = 'generateOn';
				}
				else {
					var threads = '0';
					var gentype = 'stakeOn';
				}
				$( '#wc_veruspay_gen_modal' ).fadeIn();
				$( '#wc_veruspay-activatingstake' ).fadeIn();
				$.ajax({
					url: ajax_url,
					type: "POST",
					data: {
						action:"wc_veruspay_generate_ctrl",
						"coin":coin,
						"gentype":gentype,
						"threads":threads
					},
					success: function(response){
						if ( response != '1' ) {
							var return_msg = 'Error Encountered! Check Daemon Server';
							var return_class = 'wc_veruspay_gen_error';
							var add_class = 'wc_veruspay_is_unchecked';
							var del_class = 'wc_veruspay_is_checked';
						}
						else {
							var return_msg = 'Staking Successfully Activated';
							var return_class = 'wc_veruspay_green';
							return_title = 'Staking Activated';
							var add_class = 'wc_veruspay_is_checked';
							var del_class = 'wc_veruspay_is_unchecked';
						}
						$( '#wc_veruspay-activatingstake' ).delay(2000).queue( function( next ){
							$( this ).text( return_msg );
							next();
						});
						$( '#wc_veruspay_gen_modal' ).delay(3000).queue( function( next ){
							$( '#wc_veruspay_gen_modal' ).fadeOut().delay(1000).queue( function( next ){
								$( '#wc_veruspay-activatingstake' ).text( modal_text );
								next();
							});
							$( '#wc_veruspay-activatingstake' ).fadeOut();
							$( '.wc_veruspay_stake_'+coin ).addClass( return_class ).text( return_title );
							$( '#woocommerce_veruspay_verus_gateway_stake_enable_'+coin ).addClass( add_class ).removeClass( del_class );
							next();
						});
					}
				});
			}
			else {
				var modal_text = 'Deactivating Staking';
				if ( $( '#woocommerce_veruspay_verus_gateway_generate_threads_'+coin ).hasClass( 'wc_veruspay_is_active' ) ) {
					var threads = $( '.wc_veruspay_mine_'+coin ).data( 'threads' );
					var gentype = 'mineOn';
				}
				else {
					var threads = '0';
					var gentype = 'generateOff';
				}
				$( '#wc_veruspay_gen_modal' ).fadeIn();
				$( '#wc_veruspay-deactivatingstake' ).fadeIn();
				$.ajax({
					url: ajax_url,
					type: "POST",
					data: {
						action:"wc_veruspay_generate_ctrl",
						"coin":coin,
						"gentype":gentype,
						"threads":threads
					},
					success: function(response){
						if ( response != '1' ) {
							var return_msg = 'Error Encountered! Check Daemon Server';
							var return_class = 'wc_veruspay_gen_error';
							var add_class = 'wc_veruspay_is_checked';
							var del_class = 'wc_veruspay_is_unchecked';
						}
						else {
							var return_msg = 'Staking Successfully Deactivated';
							var return_class = 'wc_veruspay_green';
							return_title = 'Activate Staking';
							var add_class = 'wc_veruspay_is_unchecked';
							var del_class = 'wc_veruspay_is_checked';
						}
						$( '#wc_veruspay-deactivatingstake' ).delay(2000).queue( function( next ){
							$( this ).text( return_msg );
							next();
						});
						$( '#wc_veruspay_gen_modal' ).delay(3000).queue( function( next ){
							$( '#wc_veruspay_gen_modal' ).fadeOut().delay(1000).queue( function( next ){
								$( '#wc_veruspay-deactivatingstake' ).text( modal_text );
								next();
							});
							$( '#wc_veruspay-deactivatingstake' ).fadeOut();
							$( '.wc_veruspay_stake_'+coin ).removeClass( return_class ).text( return_title );
							$( '#woocommerce_veruspay_verus_gateway_stake_enable_'+coin ).addClass( add_class ).removeClass( del_class );
							next();
						});
					}
				});
			}
		});
		// Mining Change
		$( '.wc_veruspay_setgenerate' ).on( 'change', function(e) {
			var valueSelected = this.value;
			var mineid = $( this ).attr( 'id' );
			coin = mineid.replace( 'woocommerce_veruspay_verus_gateway_generate_threads_', '' );
			var return_title = $( '.wc_veruspay_mine_'+coin ).text();
			// If Stopping Miner
			if ( valueSelected == 'Stop Mining' ) {
				if ( $( this ).hasClass( 'wc_veruspay_is_active' ) ) {
					var modal_text = 'Deactivating Mining';
					var thread_data = $( '.wc_veruspay_mine_'+coin ).data( 'threads' );
					// If staking is still active
					if ( $( '#woocommerce_veruspay_verus_gateway_stake_enable_'+coin ).hasClass( 'wc_veruspay_is_checked' ) ) {
						var threads = '0';
						var gentype = 'stakeOn';
					}
					else {
						var threads = '0';
						var gentype = 'generateOff';
					}
					$( '#wc_veruspay_gen_modal' ).fadeIn();
					$( '#wc_veruspay-deactivatingmine' ).fadeIn();
					$.ajax({
						url: ajax_url,
						type: "POST",
						data: {
							action:"wc_veruspay_generate_ctrl",
							"coin":coin,
							"gentype":gentype,
							"threads":threads
						},
						success: function(response){
							if ( response != '1' ) {
								var return_msg = 'Error Encountered! Check Daemon Server';
								var return_class = 'wc_veruspay_gen_error';
								var add_class = 'wc_veruspay_is_active';
								var del_class = 'wc_veruspay_is_inactive';
							}
							else {
								var return_msg = 'Mining Successfully Deactivated';
								var return_class = 'wc_veruspay_green';
								return_title = 'Activate Mining';
								var add_class = 'wc_veruspay_is_inactive';
								var del_class = 'wc_veruspay_is_active';
								thread_data = '0';
							}
							$( '#wc_veruspay-deactivatingmine' ).delay(2000).queue( function( next ){
								$( this ).text( return_msg );
								next();
							});
							$( '#wc_veruspay_gen_modal' ).delay(3000).queue( function( next ){
								$( '#wc_veruspay_gen_modal' ).fadeOut().delay(1000).queue( function( next){
									$( '#wc_veruspay-deactivatingmine' ).text( modal_text );
									next();
								});
								$( '#wc_veruspay-deactivatingmine' ).fadeOut();
								$( '.wc_veruspay_mine_'+coin ).removeClass( return_class ).text( return_title );
								$( '#woocommerce_veruspay_verus_gateway_generate_threads_'+coin ).addClass( add_class ).removeClass( del_class );
								$( '.wc_veruspay_mine_'+coin ).data( 'threads', thread_data );
								next();
							});
						}
					});
				}
				else {
					return;
				}
			}
			else {
				if ( valueSelected == '0' || valueSelected == 'Inactive (Select Threads to Begin)' || valueSelected == 'Active' ) {
					return;
				}
				else {
					var modal_text = 'Activating Mining';
					// Start Mining w Conditions Provided
					var threads = valueSelected;
					if ( $( '#woocommerce_veruspay_verus_gateway_stake_enable_'+coin ).hasClass( 'wc_veruspay_is_checked' ) ) {
						var gentype = 'generateOn';
					}
					else {
						var gentype = 'mineOn';
					}
					$( '#wc_veruspay_gen_modal' ).fadeIn();
					$( '#wc_veruspay-activatingmine' ).fadeIn();
					$.ajax({
						url: ajax_url,
						type: "POST",
						data: {
							action:"wc_veruspay_generate_ctrl",
							"coin":coin,
							"gentype":gentype,
							"threads":threads
						},
						success: function(response){
							if ( response != '1' ) {
								var return_msg = 'Error Encountered! Check Daemon Server';
								var return_class = 'wc_veruspay_gen_error';
								var add_class = 'wc_veruspay_is_inactive';
								var del_class = 'wc_veruspay_is_active';
								var thread_data = '0';
							}
							else {
								var add_class = 'wc_veruspay_is_active';
								var del_class = 'wc_veruspay_is_inactive';
								var return_msg = 'Mining Successfully Activated on ' + threads + ' threads';
								var return_class = 'wc_veruspay_green';
								var thread_data = threads;
								return_title = 'Mining on ' + threads + ' threads';
							}
							$( '#wc_veruspay-activatingmine' ).delay(2000).queue( function( next ){
								$( this ).text( return_msg );
								next();
							});
							$( '#wc_veruspay_gen_modal' ).delay(3000).queue( function( next ){
								$( '#wc_veruspay_gen_modal' ).fadeOut().delay(1000).queue( function( next ){
									$( '#wc_veruspay-activatingmine' ).text( modal_text );
									next();
								});
								$( '#wc_veruspay-activatingmine' ).fadeOut();
								$( '.wc_veruspay_mine_'+coin ).addClass( return_class ).text( return_title );
								$( '#woocommerce_veruspay_verus_gateway_generate_threads_'+coin ).addClass( add_class ).removeClass( del_class );
								$( '.wc_veruspay_mine_'+coin ).data( 'threads', thread_data );
								next();
							});
						}
					});
				}
			}
		});
	});
});