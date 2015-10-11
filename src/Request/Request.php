<?php

namespace LEWP\Request;
use \Namshi\JOSE\SimpleJWS;
use \Namshi\JOSE\Base64\Base64UrlSafeEncoder;

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
	 * @var array The body content to send with the request.
	 */
	protected $request_body = [];

	/**
	 * The body content from the response.
	 *
	 * @var array The body content received in the response.
	 */
	protected $response_body = [];

	/**
	 * Array of headers for the request.
	 *
	 * @var array Array of headers keyed by their name.
	 */
	protected $request_headers = [];

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
		$this->encoder = new Base64UrlSafeEncoder;
	}

	/**
	 * Send the HTTP request.
	 *
	 * @return array|\WP_Error    An array of response information on success; \WP_Error on failure.
	 */
	public function send() {
		$args = [];
		$body = $this->get_request_body();
		if ( ! empty( $body ) ) {
			$args['body'] = json_encode( $body, JSON_UNESCAPED_SLASHES );
		}
		$headers = $this->get_request_headers();
		if ( ! empty( $headers ) ) {
			$args['headers'] = $headers;
		}
		$result = wp_remote_request( $this->get_resource(), $args );

		$this->set_response( $result );

		if ( is_array( $result ) && isset( $result['body'] ) ) {
			$decoded = json_decode( $result['body'], true );
			if ( ! $decoded ) {
				$decoded = array();
			}
			$this->set_response_body( $decoded );
		}

		if ( isset( $result['headers']['replay-nonce'] ) ) {
			$this->set_response_nonce( $result['headers']['replay-nonce'] );
		}

		return $result;
	}

	/**
	 * Sign the request using a passphrase-protected public/private key pair.
	 *
	 * @param  KeyPair $keypair    A KeyPair object.
	 * @param  string  $passphrase The passphrase for the KeyPair's private key.
	 */
	public function sign( \LEWP\Keys\KeyPair $keypair, $passphrase ) {

		$signed = $this->generate_request_signature( $keypair, $passphrase );

		$this->set_request_body( $signed );

	}

	/**
	 * Generate a signature used to sign the request.
	 *
	 * @param  KeyPair $keypair    A KeyPair object.
	 * @param  string  $passphrase The passphrase for the KeyPair's private key.
	 * @return array Signature used to sign the request.
	 */
	protected function generate_request_signature( \LEWP\Keys\KeyPair $keypair, $passphrase ) {

		$nonce = $this->get_request_nonce();

		if ( ! $nonce ) {
			return $this->get_request_body();
		}

		$alg = 'RS256';
		$jws = new SimpleJWS( [
			'alg' => $alg,
		] );

		$protected_header = [
			'alg'   => $alg,
			'nonce' => $nonce,
		];

		$jws->setHeader( $protected_header );
		$jws->setPayload( $this->get_request_body() );
		$jws->sign( $keypair->export_private_key( $passphrase ) );
		$sig = $jws->getTokenString();

		return [
			'header'    => [
				'alg' => $alg,
				'jwk' => $keypair->get_public_key(),
			],
			'protected' => $this->encoder->encode( json_encode( $protected_header, JSON_UNESCAPED_SLASHES ) ),
			'payload'   => $this->encoder->encode( json_encode( $this->get_request_body(), JSON_UNESCAPED_SLASHES ) ),
			'signature' => $this->encoder->encode( $sig ),
		];


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
	 * @return array The body content to send with the request.
	 */
	public function get_request_body() {
		return $this->request_body;
	}

	/**
	 * Set the request body.
	 *
	 * @param array $body    The body content to send with the request.
	 */
	public function set_request_body( array $body ) {
		$this->request_body = $body;
	}

	/**
	 * Get the response body.
	 *
	 * @return array The body content received in the response.
	 */
	public function get_response_body() {
		return $this->response_body;
	}

	/**
	 * Set the response body.
	 *
	 * @param array $body The body content received in the response.
	 */
	public function set_response_body( array $body ) {
		$this->response_body = $body;
	}

	/**
	 * Get a request header by its key.
	 *
	 * @param  string $name   The header name.
	 * @return string The header value.
	 */
	public function get_request_header( $name ) {
		if ( array_key_exists( $name, $this->reqest_headers ) ) {
			return $this->request_headers[ $name ];
		}
		return false;
	}

	/**
	 * Set a request header.
	 *
	 * @param string $name   The header name.
	 * @param string $header The header value.
	 */
	public function set_request_header( $name, $value ) {
		$this->request_headers[ $name ] = $value;
	}

	/**
	 * Get all of the request headers.
	 *
	 * @return array Array of request headers keyed by name.
	 */
	public function get_request_headers() {
		$headers = $this->request_headers;

		$nonce = $this->get_request_nonce();

		if ( $nonce ) {
			$headers['Replay-Nonce'] = $nonce;
		}

		return $headers;
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