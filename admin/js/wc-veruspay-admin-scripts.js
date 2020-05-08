storecurrency = veruspay_admin_params.storecurrency;
    
var lastPrice = function() {
    var bkcolor = "transparent";
    var newval = "";
    var lastval = jQuery( '.wc_veruspay_fiat_rate' ).text();
    jQuery.ajax({
        type: 'post',
        url: 'https://veruspay.io/api/',
        data: {currency: storecurrency},
        success: function(response){
            newval = response;
            if ( lastval > newval ) {
                bkcolor = "red";
            }
            if ( lastval < newval ) {
                bkcolor = "green";
            }
            jQuery( '.wc_veruspay_fiat_rate' ).hide();
            jQuery( '.wc_veruspay_fiat_rate' ).text(newval).css('background-color',bkcolor);
            jQuery('.wc_veruspay_fiat_rate').fadeIn(800).queue( function(next){ 
                jQuery(this).css('background-color','transparent'); 
                next(); 
              });
        }
    });
    
}
setInterval( function() {
	lastPrice();
	var nowtime = jQuery.now();
}, (60000));

jQuery( function( $ ) {
	$( '.wc_veruspay_set_css' ).closest( 'div' ).addClass( 'wc_veruspay_wrapper' );
		// Conditional fields for discount/fee section
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
		// Conditional fields for RPC Settings
	$(document).ready(function() {
		if ( $('.wc_veruspay_togglerpc').hasClass( 'rpc_updated' ) ) {
			location.reload();
		}
		$( '.wc_veruspay_rpcsettings-toggle' ).closest('tr').addClass('wc_veruspay_set_css');
		$( '.wc_veruspay_addresses-toggle' ).closest('tr').toggleClass('wc_veruspay_set_css');
		$( '.wc_veruspay_customization-toggle' ).closest('tr').toggleClass('wc_veruspay_set_css');
		$( '.wc_veruspay_options-toggle' ).closest('tr').toggleClass('wc_veruspay_set_css');
		
		$( '.wc_veruspay_toggleaddr' ).click(function(e) {
			$( '.wc_veruspay_addresses-toggle' ).closest('tr').toggleClass('wc_veruspay_set_css');
		});
		$('.wc_veruspay_togglerpc').click(function(e) {
			$( '.wc_veruspay_rpcsettings-toggle' ).closest('tr').toggleClass('wc_veruspay_set_css');
		});
		$('.wc_veruspay_togglecust').click(function(e) {
			$( '.wc_veruspay_customization-toggle' ).closest('tr').toggleClass('wc_veruspay_set_css');
		});
		$('.wc_veruspay_toggleoptions').click(function(e) {
			$( '.wc_veruspay_options-toggle' ).closest('tr').toggleClass('wc_veruspay_set_css');
		});		
	});
});