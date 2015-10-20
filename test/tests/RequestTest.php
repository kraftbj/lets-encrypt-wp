<?php

class RequestTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		\WP_Mock::setUp();
	}

	public function tearDown() {
		\WP_Mock::tearDown();
	}

	public function test_request_signing() {
		$url = 'https://acme.example.org/stub';
		$request = new StubRequest( $url );

		$request->set_request_body( [
			'hello' => 'world',
		] );
		$request->set_request_nonce( 'nonce' );

		$keypair = new \LEWP\Keys\KeyPair( 'test' );
		$keypair->generate( 'foo' );

		$request->sign( $keypair, 'foo' );
	}

}

class StubRequest extends LEWP\Request\Request {}
