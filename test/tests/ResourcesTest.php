<?php

class ResourcesTest extends PHPUnit_Framework_TestCase {
	public function test_set_directory_url_sets_value() {
		$Resources = new LEWP\Resources\Resources( '' );
		$Resources->set_directory_url( 'http://acme.org' );

		$this->assertEquals( 'http://acme.org', $Resources->get_directory_url() );
	}

	public function test_get_directory_url_gets_value() {
		$Resources = new LEWP\Resources\Resources( '' );
		$this->assertEquals( '', $Resources->get_directory_url() );

		$Resources->set_directory_url( 'http://acme.org' );
		$this->assertEquals( 'http://acme.org', $Resources->get_directory_url() );

		$Resources2 = new LEWP\Resources\Resources( 'http://acme.org' );
		$this->assertEquals( 'http://acme.org', $Resources2->get_directory_url() );
	}
}