<?php
/**
 * Admin Operational Functions
 */
function wc_veruspay_array_splice( &$input, $offset, $length, $replacement ) {
	$replacement = ( array ) $replacement;
	$key_indices = array_flip(array_keys( $input ) );
	if ( isset( $input[$offset] ) && is_string( $offset ) ) {
		$offset = $key_indices[$offset];
	}
	if ( isset( $input[$length] ) && is_string( $length ) ) {
		$length = $key_indices[$length] - $offset;
	}
	$input = array_slice( $input, 0, $offset, TRUE )
					+ $replacement
					+ array_slice( $input, $offset + $length, NULL, TRUE );
}