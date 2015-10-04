<?php

class DirectoryTest extends PHPUnit_Framework_TestCase {
	public function test_directory_resource_url_is_set() {
		$url = 'http://acme.org/directory';
		$directory = new \LEWP\Request\Directory( $url );

		$this->assertEquals( $url, $directory->get_resource() );
	}

	public function test_send_generates_response_and_sets_properties() {
		$url  = 'http://acme.org/directory';
		$args = array(
			'body' => '',
		);

		$response = array(
			'headers'  => array(
				'server'                      => 'nginx',
				'content-type'                => 'application/json',
				'content-length'              => '279',
				'access-control-allow-origin' => array(
					'*',
					'*',
				),
				'replace-nonce'               => 'Cee2LkvwXwSoMBC4nSRDQfeOzIGHb6WOEbtfpSwv4FI',
				'x-frame-options'             => 'DENY',
				'strict-transport-security'   => 'max-age=604800',
				'expires'                     => 'Sun, 04 Oct 2015 14:38:51 GMT',
				'cache-control'               => 'max-age=0, no-cache, no-store',
				'pragma'                      => 'no-cache',
				'date'                        => 'Sun, 04 Oct 2015 14:38:51 GMT',
				'connection'                  => 'close',
			),
			'body'     => '{"new-authz":"https://acme-staging.api.letsencrypt.org/acme/new-authz","new-cert":"https://acme-staging.api.letsencrypt.org/acme/new-cert","new-reg":"https://acme-staging.api.letsencrypt.org/acme/new-reg","revoke-cert":"https://acme-staging.api.letsencrypt.org/acme/revoke-cert"}',
			'response' => array(
				'code'    => 200,
				'message' => 'OK',
			),
			'cookies'  => array(),
			'filename' => null,
		);

		\WP_Mock::setUp();

		// Mock the remote request
		\WP_Mock::wpFunction( 'wp_remote_request', array(
			'args'   => array(
				$url,
				$args,
			),
			'times'  => 1,
			'return' => $response,
		) );

		$directory = new \LEWP\Request\Directory( $url );
		$this->assertEquals( $response, $directory->send() );
		$this->assertEquals( $response, $directory->get_response() );
		$this->assertEquals( $response['body'], $directory->get_body() );

		\WP_Mock::tearDown();
	}
}