<?php
/**
 * r
 *
 * r is a function that interfaces with spatie/ray's ray() function
 * and offers an easier syntax to call ray with.
 */

error_reporting( E_ALL & ~E_NOTICE );

if ( file_exists( __DIR__ . '/../vendor/autoload.php' ) ) {

	require_once __DIR__ . '/../vendor/autoload.php';

	Kint\Renderer\RichRenderer::$theme = 'solarized-dark.css';
}

define( 'RSHOW', 100 );
define( 'RCLEAR', 200 );
define( 'RPAUSE', 300 );
define( 'RGREEN', 400 );
define( 'RORANGE', 500 );
define( 'RRED', 600 );
define( 'RBLUE', 700 );
define( 'RPURPLE', 800 );
define( 'RGRAY', 900 );
define( 'RSMALL', 1000 );
define( 'RLARGE', 1100 );
define( 'RTABLE', 1100 );
define( 'RSEP', 1200 );
define( 'RNSCREEN', 1300 );
define( 'RLABEL', 1400 );

if ( ! function_exists( 'r' ) && function_exists( 'ray' ) ) {

	/**
	 * r
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since  1.0.0
	 * @param  array $opts
	 * @param  mixed $vars
	 */
	function r( array $opts = [], ...$vars ) {

		$ray   = null;
		$color = '';
		$size  = '';

		d( $opts, $vars );

		if ( in_array( RNSCREEN, $opts, true ) ) {
			ray()->newScreen();
		} elseif (
			in_array( RNSCREEN, array_keys( $opts ), true ) &&
			is_string( $opts[ RNSCREEN ] )
		) {
			ray()->newScreen( $opts[ RNSCREEN ] );
		}

		if ( in_array( RCLEAR, $opts, true ) ) {
			ray()->clearScreen();
		}

		if ( in_array( RSHOW, $opts, true ) ) {
			ray()->showApp();
		}

		if ( in_array( RLARGE, $opts, true ) ) {
			$size = 'large';
		}

		if ( in_array( RSMALL, $opts, true ) ) {
			$size = 'small';
		}

		if ( in_array( RGREEN, $opts, true ) ) {
			$color = 'green';
		}

		if ( in_array( RORANGE, $opts, true ) ) {
			$color = 'orange';
		}

		if ( in_array( RRED, $opts, true ) ) {
			$color = 'red';
		}

		if ( in_array( RBLUE, $opts, true ) ) {
			$color = 'blue';
		}

		if ( in_array( RPURPLE, $opts, true ) ) {
			$color = 'purple';
		}

		if ( in_array( RGRAY, $opts, true ) ) {
			$color = 'gray';
		}

		if ( in_array( RTABLE, $opts, true ) && isset( $vars[0] ) ) {

			if ( is_null( $ray ) ) {
				$ray = ray()->table( $vars[0] );
			}

			$ray->table( $vars[0] );
		}

		$ray = is_null( $ray ) ? ray( ...$vars ) : $ray;

		if ( ! empty( $color ) ) {
			$ray->$color();
		}

		if ( ! empty( $size ) ) {
			$ray->$size();
		}

		if ( in_array( RLABEL, array_keys( $opts ), true ) && is_string( $opts[ RLABEL ] ) ) {
			ray()->label( $opts[ RLABEL ] );
		}

		if ( in_array( RPAUSE, $opts, true ) ) {
			ray()->pause();
		}

		if ( in_array( RSEP, $opts, true ) ) {
			ray()->separator();
		}

		return $ray;
	}
}
