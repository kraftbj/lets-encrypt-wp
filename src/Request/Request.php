<?php

namespace LEWP\Request;
use \Namshi\JOSE\SimpleJWS;

abstract class Request {
	/**
	 * The response returned from the HTTP request.
	 *
	 * @var array|\WP_Error    Either a WP_Error or an array with response data.
	 */
	protected $response = array();

	/**
	 * The RESTful resource used in the current request.
	 *
	 * @var string    URL for the REST resource.
	 */
	protected $resource = '';

	/**
	 * The body content for the request.
	 *
	 * @var string    The body content to send with the request.
	 */
	protected $body = '';

	/**
	 * CSRF token for the request.
	 *
	 * @var string    The nonce to protect the request against CSRF.
	 */
	protected $request_nonce = '';

	/**
	 * CSRF token received from the request.
	 *
	 * @var string    The nonce to protect the next request against CSRF.
	 */
	protected $response_nonce = '';

	/**
	 * The named ACME resource type to fetch.
	 *
	 * @var string    The ACME request type (e.g., new-reg, new-cert).
	 */
	protected $type = '';

	/**
	 * The private key used to sign the request.
	 *
	 * @var string    The private key used to sign the request to create the JSW object.
	 */
	protected $private_key = '';

	/**
	 * The request method.
	 *
	 * @var string    The method used for the request.
	 */
	protected $method = 'GET';

	/**
	 * Construct the request object.
	 *
	 * @param  string     $resource    The REST resource URL.
	 * @param  string     $method      The request method.
	 * @return Request
	 */
	public function __construct( $resource = '', $method = 'GET' ) {
		$this->set_resource( $resource );
		$this->set_method( $method );
	}

	/**
	 * Send the HTTP request.
	 *
	 * @return array|\WP_Error    An array of response information on success; \WP_Error on failure.
	 */
	public function send() {
		$result = wp_remote_request( $this->get_resource(), array(
			'body' => $this->get_body(),
		) );

		$this->set_response( $result );

		if ( is_array( $result ) && isset( $result['body'] ) ) {
			$this->set_body( $result['body'] );
		}

		if ( isset( $result['headers']['replay-nonce'] ) ) {
			$this->set_response_nonce( $result['headers']['replay-nonce'] );
		}

		return $result;
	}

	public function sign() {
		$jws  = new SimpleJWS( array(
			'alg' => 'RS256'
		) );

		$jws->setPayload( $this->get_body() );

		$privateKey = openssl_pkey_get_private("file://path/to/private.key", self::SSL_KEY_PASSPHRASE);
		$jws->sign($privateKey);
	}

	/**
	 * Get the response object from the request.
	 *
	 * @return array|\WP_Error    Either a WP_Error or an array with response data.
	 */
	public function get_response() {
		return $this->response;
	}

	/**
	 * Set the response object from the request.
	 *
	 * @param array|\WP_Error    $response    Either a WP_Error or an array with response data.
	 */
	public function set_response( $response ) {
		$this->response = $response;
	}

	/**
	 * Get the resource URL.
	 *
	 * @return string    URL for the REST resource.
	 */
	public function get_resource() {
		return $this->resource;
	}

	/**
	 * Set the resource URL.
	 *
	 * @param string    $resource    URL for the REST resource.
	 */
	public function set_resource( $resource ) {
		$this->resource = $resource;
	}

	/**
	 * Get the request body.
	 *
	 * @return string    The body content to send with the request.
	 */
	public function get_body() {
		return $this->body;
	}

	/**
	 * Set the request body.
	 *
	 * @param string    $body    The body content to send with the request.
	 */
	public function set_body( $body ) {
		$this->body = $body;
	}

	/**
	 * Get the nonce for the request.
	 *
	 * @return string    The nonce to protect the request against CSRF.
	 */
	public function get_request_nonce() {
		return $this->request_nonce;
	}

	/**
	 * Set the nonce for the request.
	 *
	 * @param string    $request_nonce    The nonce to protect the request against CSRF.
	 */
	public function set_request_nonce( $request_nonce ) {
		$this->request_nonce = $request_nonce;
	}

	/**
	 * Get the nonce for the response.
	 *
	 * @return string    The nonce to protect the next request against CSRF.
	 */
	public function get_response_nonce() {
		return $this->response_nonce;
	}

	/**
	 * Set the nonce for the response.
	 *
	 * @param string    $response_nonce    The nonce to protect the next request against CSRF.
	 */
	public function set_response_nonce( $response_nonce ) {
		$this->response_nonce = $response_nonce;
	}

	/**
	 * Get the type of ACME request.
	 *
	 * @return string    The ACME request type (e.g., new-reg, new-cert).
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Set the type of ACME request.
	 *
	 * @param string    $type    The ACME request type (e.g., new-reg, new-cert).
	 */
	public function set_type( $type ) {
		$this->type = $type;
	}

	/**
	 * Get the private key for signing the request.
	 *
	 * @return string    The private key used to sign the request to create the JSW object.
	 */
	public function get_private_key() {
		return $this->private_key;
	}

	/**
	 * Set the private key for signing the request.
	 *
	 * @param string    $private_key    The private key used to sign the request to create the JSW object.
	 */
	public function set_private_key( $private_key ) {
		$this->private_key = $private_key;
	}

	/**
	 * Get the request method.
	 *
	 * @return string    The method used for the request.
	 */
	public function get_method() {
		return $this->method;
	}

	/**
	 * Set the request method.
	 *
	 * @param string    $method    The method used for the request.
	 */
	public function set_method( $method ) {
		$this->method = $method;
	}
}