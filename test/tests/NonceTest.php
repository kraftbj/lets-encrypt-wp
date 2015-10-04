<?php

class NonceTest extends PHPUnit_Framework_TestCase {
	public function test_nonce_request_get_response_nonce() {
		$nonce = $this->make_request();
		$this->assertEquals( MockData::get_head_response(), $nonce->get_response() );
		$this->assertEquals( MockData::get_head_response()['headers']['replay-nonce'], $nonce->get_response_nonce() );
	}

	public function test_nonce_request_generates_405() {
		$nonce = $this->make_request();
		$this->assertEquals( 405, $nonce->get_response()['response']['code'] );
		$this->assertEquals( 'Method Not Allowed', $nonce->get_response()['response']['message'] );
	}

	private function make_request() {
		$url  = 'http://acme.org/new-authz';
		$response = MockData::get_head_response();

		\WP_Mock::setUp();

		// Mock the remote request
		\WP_Mock::wpFunction( 'wp_remote_request', array(
			'args'   => array(
				$url,
				array(
					'body' => '',
				),
			),
			'times'  => 1,
			'return' => $response,
		) );

		$nonce = new \LEWP\Request\Nonce( $url );
		$nonce->send();

		\WP_Mock::tearDown();

		return $nonce;
	}
}