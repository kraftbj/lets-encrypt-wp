<?php

namespace LEWP\Keys\Storage;
use \LEWP\Keys\Storage;
use \LEWP\Keys\KeyPair;

class Option extends Storage {

	private static $option_name = 'lewp_keypair_%s';

	public function save( KeyPair $keypair ) {

		$public  = $keypair->export_public_key();
		$private = $keypair->export_private_key();
		$option  = \wp_json_encode( (object) compact( 'public', 'private' ) );

		return \update_site_option( sprintf( self::$option_name, $keypair->get_id() ), $option );

	}

	public function get( $id ) {

		$value = \get_site_option( sprintf( self::$option_name, $id ) );

		if ( ! $value ) {
			return false;
		}

		$pair = \json_decode( $value, false );

		if ( ! $pair ) {
			return false;
		}

		$keypair = new KeyPair( $id );
		$keypair->read( $pair );

		return $keypair;

	}

	public function delete( $id ) {
		return \delete_site_option( sprintf( self::$option_name, $id ) );
	}

}
