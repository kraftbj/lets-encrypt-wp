<?php

namespace LEWP\Keys;

class KeyPair {
	/**
	 * The OpenSSL key resource.
	 *
	 * @var null|resource    The OpenSSL key resource.
	 */
	private $resource = null;

	/**
	 * The ID for the key.
	 *
	 * @var string    The key ID.
	 */
	private $id = '';

	private $public_key = '';

	private $private_key = '';

	private $key_pair = '';

	/**
	 * Construct the object.
	 *
	 * @param  string     $id    The key ID.
	 * @return KeyPair
	 */
	public function __construct( $id ) {
		$this->set_id( $id );
	}

	public function generate() {
		$config = array(
			'digest_alg' => 'sha256',
			'private_key_bits' => '2048',
			'private_key_type' => 'OPENSSL_KEYTYPE_RSA',
		);

		$resource = openssl_pkey_new( $config );
		$this->set_resource( $resource );

		return $resource;
	}

	public function retrieve_key_pair( $id ) {

	}

	public function read( $key ) {

	}

	public function extract_private_key( $resource ) {
		openssl_pkey_export( $resource, $private_key );
		return $private_key;
	}

	public function extract_public_key( $resource ) {
		$public_key = openssl_pkey_get_details( $resource );
		return $public_key['key'];
	}

	/**
	 * Get the OpenSSL key resource.
	 *
	 * @return null|resource    The OpenSSL key resource.
	 */
	public function get_resource() {
		return $this->resource;
	}

	/**
	 * Set the OpenSSL key resource.
	 *
	 * @param  null|resource    $resource    The OpenSSL key resource.
	 * @return void
	 */
	public function set_resource( $resource ) {
		$this->resource = $resource;
	}

	/**
	 * Get the key ID.
	 *
	 * @return string    The ID for the key.
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Set the key ID.
	 *
	 * @param  string    $id    The ID for the key.
	 * @return void
	 */
	public function set_id( $id ) {
		$this->id = $id;
	}
}