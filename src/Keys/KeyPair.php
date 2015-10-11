<?php

namespace LEWP\Keys;

class KeyPair {
	private $id = '';

	private $public_key = '';

	private $private_key = '';

	/**
	 * Construct the object.
	 *
	 * @param  string     $id    The key pair ID.
	 */
	public function __construct( $id ) {
		$this->set_id( $id );
	}

	/**
	 * Set the key pair ID.
	 *
	 * @param string $id The key pair ID.
	 */
	public function set_id( $id ) {
		$this->id = $id;
	}

	/**
	 * Get the key pair ID.
	 *
	 * @return string The key pair ID.
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Generates a new public and private key pair.
	 *
	 * @param  string $passphrase The passphrase to encrypt the private key.
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

	/**
	 * Reads a keypair from elsewhere (eg. persistent storage) and places it into the KeyPair object.
	 *
	 * @param array $keypair {
	 *     An array representing the key pair.
	 *
	 *     @type string $public  The public key.
	 *     @type string $private The private key.
	 * }
	 */
	public function read( array $keypair ) {

		$this->private_key = $keypair['private'];
		$this->public_key  = $keypair['public'];

	}

	/**
	 * Get the public key.
	 *
	 * @return string The public key.
	 */
	public function get_public_key() {
		return $this->public_key;
	}

	/**
	 * Get the encrypted private key.
	 *
	 * @return string The encrypted private key in PEM format.
	 */
	public function get_private_key() {
		return $this->private_key;
	}

	/**
	 * Export the decrypted private key resource.
	 *
	 * @param  string $passphrase The passphrase to decrypt the private key.
	 * @return resource|bool The decrypted private key resource, or false on failure.
	 */
	public function export_private_key( $passphrase ) {
		return openssl_pkey_get_private( $this->private_key, $passphrase );
	}

}