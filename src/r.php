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
define( 'RCLEARALL', 1400 );
define( 'RCALLER', 1500 );
define( 'RTRACE', 1600 );
define( 'RCOUNT', 1700 );
define( 'RONCE', 1800 );
define( 'RCLASSNAME', 1900 );
define( 'RMEASURE', 2000 );

/**
 * ray()->r( OPTS, ...$vars )
 */
Spatie\Ray\Ray::macro( 'r', function( ...$vars ) {

	/**
	 * Plug out opts
	 *
	 * Options should be passed in the first item as an
	 * array or string.
	 */

	$opts = $vars[0] ?? [];

	unset( $vars[0] );

	if ( ! is_array( $opts ) && ! is_string( $opts ) ) {
		throw new \InvalidArgumentException( '$opts must be a string or an array.' );
	}

	/**
	 * String vs. Array
	 *
	 * You can call ->r using a string or an array, e.g.
	 *
	 *     ray()->r( 'RLABEL=Something,RSHOW,RRED,RTRACE,RPAUSE', $vars );
	 *
	 * OR, as an array:
	 *
	 *     ray()->r( [
	 *         RLABEL => 'Something',
	 *         RSHOW,
	 *         RRED,
	 *         RTRACE,
	 *         RPAUSE,
	 *     ], $vars );
	 *
	 * This makes sure it works.
	 */

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

	/**
	 * Get ready!
	 */

	$ray = null;

	/**
	 * Screen operations before output.
	 */

	// Clear everything.
	if ( in_array( RCLEARALL, $opts ) ) {
		ray()->clearAll();
	}

	// Create a new screen.
	if ( in_array( RNSCREEN, $opts ) ) {

		ray()->newScreen();
	} elseif (

		in_array( RNSCREEN, array_keys( $opts ) ) &&
		is_string( $opts[ RNSCREEN ] )
	) {

		ray()->newScreen( $opts[ RNSCREEN ] );
	}

	// Clear current screen.
	if ( in_array( RCLEAR, $opts ) ) {
		ray()->clearScreen();
	}

	/**
	 * Outputting Vars in Specific Formats
	 *
	 * The items here usually precent output later,
	 * so might create a $ray instance that may skip
	 * output later.
	 */

	// ->once()
	if ( in_array( RONCE, $opts ) ) {

		if ( ! is_null( $ray ) ) {
			$ray->once( ...$vars );
		} else {
			$ray = ray()->once( ...$vars );
		}
	}

	// ->table()
	if ( in_array( RTABLE, $opts ) && isset( $vars[0] ) ) {

		if ( ! is_null( $ray ) ) {
			$ray->table( $vars[0] );
		} else {
			$ray = ray()->table( $vars[0] );
		}
	}

	// ->className()
	if ( in_array( RCLASSNAME, $opts ) ) {

		if ( ! is_null( $ray ) ) {
			$ray->className( ...$vars );
		} else {
			$ray = ray()->className( ...$vars );
		}
	}

	/**
	 * Output Agnostic
	 *
	 * These things output stuff but they aren't about
	 * examinging variables.
	 */

	// ->measure()
	if ( in_array( RMEASURE, $opts ) ) {
		ray()->measure();
	}

	// ->count()
	if ( in_array( RCOUNT, $opts ) ) {
		ray()->count();
	}

	/**
	 * Normal Output
	 *
	 * Note, you might have already ouput vars above in specific formats,
	 * and if you did we won't do that again here but instead add
	 * properties to the output.
	 */

	$ray = ! is_null( $ray ) ? $ray : ray( ...$vars );

	/**
	 * Colors of Output
	 */

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

	if ( ! empty( $color ) ) {
		$ray->$color();
	}

	/**
	 * Size of Output
	 */

	if ( ! empty( $size ) ) {
		$ray->$size();
	}

	/**
	 * Label of Output
	 */

	if ( in_array( RLABEL, array_keys( $opts ) ) && is_string( $opts[ RLABEL ] ) ) {
		$ray->label( $opts[ RLABEL ] );
	}

	/**
	 * Execution
	 *
	 * The items here make the app do things AFTER output
	 * of vars have been ouput and properties too.
	 */

	// Show the app.
	if ( in_array( RSHOW, $opts ) ) {
		ray()->showApp();
	}

	// Add a Separator.
	if ( in_array( RSEP, $opts ) ) {
		ray()->separator();
	}

	// Pause execution.
	if ( in_array( RPAUSE, $opts ) ) {
		ray()->pause();
	}

	/**
	 * Stuff that won't work.
	 */

	// ->trace()
	if ( in_array( RTRACE, $opts ) ) {
		throw new Exception( 'Sorry but this requires scope, use ray()->trace() intead.' );
	}

	// ->caller()
	if ( in_array( RCALLER, $opts ) ) {
		throw new Exception( 'Sorry but this requires scope, use ray()->caller() intead.' );
	}

	return $this;

} );
