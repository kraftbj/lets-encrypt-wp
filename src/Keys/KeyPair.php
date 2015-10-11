<?php

namespace LEWP\Keys;

class KeyPair {
	private $id = '';

	private $public_key = '';

	private $private_key = '';

	/**
	 * Construct the object.
	 *
	 * @param  string     $id    The key ID.
	 */
	public function __construct( $id ) {
		$this->set_id( $id );
	}

	public function set_id( $id ) {
		$this->id = $id;
	}

	public function get_id() {
		return $this->id;
	}

	/**
	 * Generates a new public and private key pair.
	 *
	 */
	public function generate( $passphrase ) {
		$config = array(
			'digest_alg'       => 'sha256',
			'private_key_bits' => '2048',
			'private_key_type' => 'OPENSSL_KEYTYPE_RSA',
		);

		$resource = openssl_pkey_new( $config );

		openssl_pkey_export( $resource, $private_key, $passphrase );

		$details = openssl_pkey_get_details( $resource );

		$this->private_key = $private_key;
		$this->public_key  = $details['key'];

	}

	public function read( $keypair ) {

		$this->private_key = $keypair->private;
		$this->public_key  = $keypair->public;

	}

	/**
	 * Export the encrypted private key.
	 *
	 * @return string The encrypted private key in PEM format.
	 */
	public function export_private_key() {
		return $this->private_key;
	}

	/**
	 * Get the decrypted private key resource.
	 *
	 * @param  string $passphrase The passphrase to decrypt the private key.
	 * @return resource The decrypted private key resource.
	 */
	public function get_private_key( $passphrase ) {
		return openssl_pkey_get_private( $this->private_key, $passphrase );
	}

	public function export_public_key() {
		return $this->public_key;
	}

}