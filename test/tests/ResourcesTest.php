<?php

class ResourcesTest extends PHPUnit_Framework_TestCase {
	public function test_sanitize_urls_removes_bad_keys() {
		$bad_list = array(
			'bad-thing' => 'http://acme.org/bad-thing',
			'old-reg'   => 'http://acme.org/old-reg',
		);

		$good_list = array(
			'new-reg'  => 'http://acme.org/new-reg',
			'new-cert' => 'http://acme.org/new-cert',
		);

		$test_list = array_merge( $bad_list, $good_list );

		$resources = new LEWP\Resources\Resources( json_encode( $test_list ) );

		$this->assertEquals( $good_list, $resources->get_resource_urls() );
	}

	public function test_resources_are_correctly_extracted() {
		$directory = $this->get_directory_object();
		$resources = new LEWP\Resources\Resources( $directory->get_body() );

		$data = MockData::get_directory_response();
		$data = json_decode( $data['body'], true );

		$this->assertEquals( $data, $resources->get_resource_urls() );
	}

	private function get_directory_object() {
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
		$directory->send();

		\WP_Mock::tearDown();

		return $directory;
	}
}