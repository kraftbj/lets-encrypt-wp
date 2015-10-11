<?php

namespace LEWP\Keys\Storage;
use \LEWP\Keys\Storage;
use \LEWP\Keys\KeyPair;

class Memory extends Storage {

	private $keypairs = [];

	public function save( KeyPair $keypair ) {

		$public  = $keypair->get_public_key();
		$private = $keypair->get_private_key();

		$this->keypairs[ $keypair->get_id() ] = (object) compact( 'public', 'private' );

		return true;

	}

	public function get( $id ) {

		if ( ! array_key_exists( $id, $this->keypairs ) ) {
			return false;
		}

		$keypair = new KeyPair( $id );
		$keypair->read( $this->keypairs[ $id ] );

		return $keypair;

	}

	public function delete( $id ) {
		unset( $this->keypairs[ $id ] );
		return true;
	}

}
