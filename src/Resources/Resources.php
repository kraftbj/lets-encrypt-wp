<?php

namespace LEWP\Resources;

class Resources {
	/**
	 * List of RESTful resources for an ACME server.
	 *
	 * @var array
	 */
	private $resource_urls = array();

	/**
	 * Construct the object.
	 *
	 * @param  string    $directory_request_body    The response from a Directory object request response.
	 */
	public function __construct( $directory_request_body = '' ) {
		if ( ! empty( $directory_request_body ) ) {
			$resources = $this->extract_resources( $directory_request_body );
			$this->set_resource_urls( $resources );
		}
	}

	/**
	 * Get the resources out of a request body.
	 *
	 * @param  string    $directory_request_body   JSON string with resources.
	 * @return array                               Array of sanitized resources.
	 */
	private function extract_resources( $directory_request_body ) {
		$body      = json_decode( $directory_request_body, true );
		$resources = $this->sanitize_resource_list( $body );

		return $resources;
	}

	/**
	 * Checks a list of resources against a whitelist of resource names.
	 *
	 * @param  array    $resources    The list of resources with resource name as a key.
	 * @return array                  The cleaned list of resources.
	 */
	private function sanitize_resource_list( $resources ) {
		$clean_resources       = array();
		$whitelisted_resources = $this->resource_whitelist();

		foreach ( $resources as $resource_name => $resource ) {
			if ( in_array( $resource_name, $whitelisted_resources ) ) {
				$clean_resources[ $resource_name ] = $resource;
			}
		}

		return $clean_resources;
	}

	/**
	 * Acceptable resource names.
	 *
	 * These represent the resource "names" that the ACME protocol supports. The directory resource should return a list
	 * of resources keyed with these values.
	 *
	 * @return array    List of acceptable ACME resources.
	 */
	private function resource_whitelist() {
		return array(
			'new-reg',
			'recover-reg',
			'new-authz',
			'new-cert',
			'revoke-cert',
			'reg',
			'authz',
			'challenge',
			'cert',
		);
	}

	/**
	 * Set the list of resources URLs.
	 *
	 * @param  array    $urls    A list of resource URLs.
	 * @return void
	 */
	public function set_resource_urls( $urls ) {
		$this->resource_urls = $urls;
	}

	/**
	 * Return the list of resources.
	 *
	 * @return array    The list of resources.
	 */
	public function get_resource_urls() {
		return $this->resource_urls;
	}
}