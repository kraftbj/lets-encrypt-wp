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

		$response = MockData::get_directory_response();

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
		$this->assertEquals( $response['body'], $directory->get_response_body() );
		$this->assertEquals( $response['headers']['replay-nonce'], $directory->get_response_nonce() );

		\WP_Mock::tearDown();
	}
}