<?php

class ResourcesTest extends PHPUnit_Framework_TestCase {
	public function test_sanitize_urls_removes_bad_keys() {
		$bad_list = array(
			'bad-thing' => 'https://acme.example.org/bad-thing',
			'old-reg'   => 'https://acme.example.org/old-reg',
		);

		$good_list = array(
			'new-reg'  => 'https://acme.example.org/new-reg',
			'new-cert' => 'https://acme.example.org/new-cert',
		);

		$test_list = array_merge( $bad_list, $good_list );

		$resources = new LEWP\Resources\Resources( $test_list );

		$this->assertEquals( $good_list, $resources->get_resource_urls() );
	}

	public function test_resources_are_correctly_extracted() {
		$directory = $this->get_directory_object();
		$resources = new LEWP\Resources\Resources( $directory->get_response_body() );

		$data = MockData::get_directory_response();
		$data = json_decode( $data['body'], true );

		$this->assertEquals( $data, $resources->get_resource_urls() );
	}

	private function get_directory_object() {
		$url  = 'https://acme.example.org/directory';
		$args = array(
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