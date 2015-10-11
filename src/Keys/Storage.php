<?php

namespace LEWP\Keys;

abstract class Storage {

	abstract public function save( KeyPair $keypair );

	abstract public function get( $id );

	abstract public function delete( $id );

	final private function __construct() {
	}

	final public static function get_instance() {
		static $instance;

		if ( ! isset( $instance ) ) {
			$instance = new static;
		}

		return $instance;
	}

}
