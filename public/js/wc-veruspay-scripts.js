var blockUI = '<div id="processingUIblock" style="z-index: 1000; border: medium none; margin: 0px; padding: 0px; width: 100%; height: 100%; top: 0px; left: 0px; background: rgb(255, 255, 255) none repeat scroll 0% 0%; opacity: 0.6; cursor: default; position: fixed;"></div>',
	pricetime = veruspay_params.pricetime,
	testmode = veruspay_params.testmode,
	testaddr = veruspay_params.testaddr;
// Checkout Update Function & Interval
var updateCheckout = function() {
	var t = { updateTimer: !1,  dirtyInput: !1,
		reset_update_checkout_timer: function() {
			clearTimeout(t.updateTimer)
		},  trigger_update_checkout: function() {
			t.reset_update_checkout_timer(), t.dirtyInput = !1,
			jQuery('#wc_veruspay_updating_price').fadeIn();
			jQuery(document.body).trigger("update_checkout")
		}
	};
	t.trigger_update_checkout();
}
var updatePayment = function() {
	jQuery('#wc_veruspay_updating_price').fadeIn();
}
setInterval( function() {
	updateCheckout();
	var nowtime = jQuery.now();
}, (pricetime * 60000));
// Countdown
function getTimeRemaining(endtime) {
	var t = Date.parse(endtime) - Date.parse(new Date());
	var seconds = Math.floor((t / 1000) % 60);
	var minutes = Math.floor((t / 1000 / 60) % 60);
	return {
	  'total': t,
	  'minutes': minutes,
	  'seconds': seconds
	};
}
function initializeMinCountDown(id, endtime) {
	var timeleft = document.getElementById(id);
	function updateSpan() {
	  	var t = getTimeRemaining(endtime);
	  	timeleft.innerHTML = ('0' + t.minutes).slice(-2)+':'+('0' + t.seconds).slice(-2);
	  	if (t.total <= 0) {
			clearInterval(countdownint);
			location.reload();
	  	}
	}
	updateSpan();
	var countdownint = setInterval(updateSpan, 1000);
}
jQuery(function($){
	var checkout_form = $( 'form.woocommerce-checkout' );
	checkout_form.on( 'checkout_place_order', function() {
		$('#wc_veruspay_generate_order').fadeIn();
		var maxTime = 60000,
    		startTime = Date.now();
		var interval = setInterval(function () {
			if ($('.woocommerce-error').is(':visible')) {
				$('#wc_veruspay_generate_order').fadeOut();
            	clearInterval(interval);
			}
			else {
            	if (Date.now() - startTime > maxTime) {
					clearInterval(interval);
            	}
        	}
    	},
    	1000
		);
	});

	$(document).ready(function(){		
		// For refresh price
		$('#order_review').on('click', '#wc_veruspay_icon-price', function() {
			updateCheckout();
		});
		// For change payment method
		$('form.checkout').on( 'change', 'input[name^="payment_method"]', function() {
			updateCheckout();
		});
		$('form.checkout').on( 'change', '#wc_veruspay_coin', function() {
      		updatePayment();
		});
		if ($('#wc_veruspay_timeleft').length > 0) {
			var a = document.getElementById('wc_veruspay_timeleft');
		  	var a = a.innerHTML.split(':');
		  	var expiretime = ((+a[0]) * 60 + (+a[1]));
		  	var expiretime = new Date(Date.parse(new Date()) + expiretime * 1000);
		  	initializeMinCountDown('wc_veruspay_timeleft', expiretime);
		}
	});
});