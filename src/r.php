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

	if ( class_exists( 'Kint\Renderer\RichRenderer' ) ) {
		Kint\Renderer\RichRenderer::$theme = 'solarized-dark.css';
	}
}

define( 'RSHOW', 100 );
define( 'RCLEAR', 200 );
define( 'RPAUSE', 300 ); // https://spatie.be/docs/ray/v1/usage/framework-agnostic-php-project#pausing-execution
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
define( 'RCLEARALL', 1400 ); // https://spatie.be/docs/ray/v1/usage/framework-agnostic-php-project#clearing-everything-including-history
define( 'RCALLER', 1500 ); // https://spatie.be/docs/ray/v1/usage/framework-agnostic-php-project#see-the-caller-of-a-function
define( 'RTRACE', 1600 ); // https://spatie.be/docs/ray/v1/usage/framework-agnostic-php-project#see-the-caller-of-a-function
define( 'RCOUNT', 1700 ); // https://spatie.be/docs/ray/v1/usage/framework-agnostic-php-project#counting-execution-times
define( 'RONCE', 1800 ); // https://spatie.be/docs/ray/v1/usage/framework-agnostic-php-project#sending-a-payload-once
define( 'RCLASSNAME', 1900 ); // https://spatie.be/docs/ray/v1/usage/framework-agnostic-php-project#display-the-class-name-of-an-object
define( 'RMEASURE', 2000 ); // https://spatie.be/docs/ray/v1/usage/framework-agnostic-php-project#measuring-performance-and-memory-usage

Spatie\Ray\Ray::macro( 'r', function( ...$vars ) {

	$opts = $vars[0] ?? [];

	unset( $vars[0] );

	if ( ! is_array( $opts ) && ! is_string( $opts ) ) {
		throw new \InvalidArgumentException( '$opts must be a string or an array.' );
	}

	if ( is_string( $opts ) ) {

		$opts = explode( ',', $opts );

		foreach ( $opts as $opt_key => $opt_value ) {

			if ( stristr( $opt_value, '=' ) ) {

				$opt_config = explode( '=', $opt_value );
				$constant   = strtoupper( trim( $opt_config[0] ) );

				if ( ! defined( $constant ) ) {
					continue;
				}

				$opts[ abs( (int) constant( $constant ) ) ] = (string) trim( $opt_config[1] ?? '' );

				unset( $opts[ $opt_key ] );
			}
		}

		foreach ( $opts as $opt_key => $opt_value ) {

			$constant = strtoupper( trim( $opt_value ) );

			if ( ! defined( $constant ) ) {
				continue;
			}

			$opts[ $opt_key ] = abs( (int) constant( $constant ) );
		}
	}

	if ( in_array( RCALLER, $opts ) ) {
		throw new Exception( 'Sorry but this requires scope, use ray()->caller() intead.' );
	}

	if ( in_array( RTRACE, $opts ) ) {
		throw new Exception( 'Sorry but this requires scope, use ray()->trace() intead.' );
	}

	if ( in_array( RCOUNT, $opts ) ) {
		throw new Exception( 'Sorry but this requires scope, use ray()->count() intead.' );
	}

	$ray = null;

	if ( in_array( RCLEARALL, $opts ) ) {
		ray()->clearAll();
	}

	if ( in_array( RNSCREEN, $opts ) ) {
		ray()->newScreen();
	} elseif (
		in_array( RNSCREEN, array_keys( $opts ) ) &&
		is_string( $opts[ RNSCREEN ] )
	) {
		ray()->newScreen( $opts[ RNSCREEN ] );
	}

	if ( in_array( RCLEAR, $opts ) ) {
		ray()->clearScreen();
	}

	if ( in_array( RSHOW, $opts ) ) {
		ray()->showApp();
	}

	if ( in_array( RLARGE, $opts ) ) {
		$size = 'large';
	}

	if ( in_array( RSMALL, $opts ) ) {
		$size = 'small';
	}

	if ( in_array( RGREEN, $opts ) ) {
		$color = 'green';
	}

	if ( in_array( RORANGE, $opts ) ) {
		$color = 'orange';
	}

	if ( in_array( RRED, $opts ) ) {
		$color = 'red';
	}

	if ( in_array( RBLUE, $opts ) ) {
		$color = 'blue';
	}

	if ( in_array( RPURPLE, $opts ) ) {
		$color = 'purple';
	}

	if ( in_array( RGRAY, $opts ) ) {
		$color = 'gray';
	}

	if ( in_array( RONCE, $opts ) ) {
		$ray = ray()->once( ...$vars );
	}

	if ( in_array( RTABLE, $opts ) && isset( $vars[0] ) ) {

		if ( is_null( $ray ) ) {
			$ray = ray()->table( $vars[0] );
		}

		$ray->table( $vars[0] );
	}

	if ( in_array( RCLASSNAME, $opts ) ) {
		$ray = ray()->className( ...$vars );
	}

	if ( in_array( RMEASURE, $opts ) ) {
		ray()->measure();
	}

	$ray = is_null( $ray ) ? ray( ...$vars ) : $ray;

	if ( ! empty( $color ) ) {
		$ray->$color();
	}

	if ( ! empty( $size ) ) {
		$ray->$size();
	}

	if ( in_array( RLABEL, array_keys( $opts ) ) && is_string( $opts[ RLABEL ] ) ) {
		ray()->label( $opts[ RLABEL ] );
	}

	if ( in_array( RPAUSE, $opts ) ) {
		ray()->pause();
	}

	if ( in_array( RSEP, $opts ) ) {
		ray()->separator();
	}

	return $ray;

} );
