<?php

namespace LEWP;

// Exit if WP_CLI is not installed
if ( ! defined( 'WP_CLI' ) || false === WP_CLI ) {
	return;
}

class WP_CLI extends \WP_CLI_Command {

	/**
	 * Prints a greeting.
	 *
	 * ## OPTIONS
	 *
	 * <name>
	 * : The name of the person to greet.
	 *
	 * ## EXAMPLES
	 *
	 *     wp example hello Newman
	 *
	 * @synopsis <name>
	 */
	function register( $args, $assoc_args ) {
		list( $name ) = $args;

		// Print a success message
		\WP_CLI::success( "Hello, $name!" );
	}

	function authorize( $args, $assoc_args ) {

	}

	function issue_certificate( $args, $assoc_args ) {

	}
}

\WP_CLI::add_command( 'lewp', 'LEWP\WP_CLI' );