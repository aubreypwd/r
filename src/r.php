<?php
/**
 * r
 *
 * r is a function that interfaces with spatie/ray's ray() function
 * and offers an easier syntax to call ray with.
 */
if ( ! function_exists( 'ray' ) ) {
	function r( ...$var ) {
		ray( $var );
	}
}
