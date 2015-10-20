<?php

namespace LEWP\Keys;
use \WP_Mock;

class KeyPairStorageTest extends \PHPUnit_Framework_TestCase {

	public function setUp() {
		WP_Mock::setUp();
	}

	public function tearDown() {
		WP_Mock::tearDown();
	}

	/**
	 * @dataProvider data_storage_controllers
	 *
	 * @param string $class Storage controller class name
	 */
	public function test_keypair_storage_and_retrieval( $class ) {

		// When using the option version, always have update option return true
		if ( 'Option' === $class ) {
			// Mock the remote request
			WP_Mock::wpFunction( 'update_site_option', array(
				'args'   => array(
					WP_Mock\Functions::type( 'string' ),
					WP_Mock\Functions::type( 'string' ),
				),
				'times'  => 2,
				'return' => true,
			) );
		}

		$storage = call_user_func( __NAMESPACE__ . "\\Storage\\{$class}::get_instance" );

		$id = __FUNCTION__;

		// Create
		$keypair = new KeyPair( $id );
		$keypair->generate( 'foo' );

		$save = $storage->save( $keypair );

		$this->assertTrue( $save );

		// Read
		if ( 'Option' === $class ) {
			// Mock the remote request
			WP_Mock::wpFunction( 'get_site_option', array(
				'args'   => array(
					WP_Mock\Functions::type( 'string' ),
				),
				'times'  => 1,
				'return' => \json_encode( array( 'public' => $keypair->get_public_key(), 'private' => $keypair->get_private_key() ), JSON_UNESCAPED_SLASHES ),
			) );
		}

		$test = $storage->get( $id );

		$this->assertInstanceOf( __NAMESPACE__ . '\KeyPair', $test );
		$this->assertSame( $id, $test->get_id() );

		$private = $test->export_private_key( 'foo' );

		$this->assertNotFalse( $private );
		$this->assertInternalType( 'resource', $private );

		// Update
		$keypair = new KeyPair( $id );
		$keypair->generate( 'bar' );

		$save = $storage->save( $keypair );

		$this->assertTrue( $save );

		if ( 'Option' === $class ) {
			// Mock the remote request
			WP_Mock::wpFunction( 'get_site_option', array(
				'args'   => array(
					WP_Mock\Functions::type( 'string' ),
				),
				'times'  => 1,
				'return' => \json_encode( array( 'public' => $keypair->get_public_key(), 'private' => $keypair->get_private_key() ), JSON_UNESCAPED_SLASHES ),
			) );
		}

		$test = $storage->get( $id );

		$this->assertInstanceOf( __NAMESPACE__ . '\KeyPair', $test );
		$this->assertSame( $id, $test->get_id() );

		$private = $test->export_private_key( 'foo' );

		$this->assertFalse( $private );

		$private = $test->export_private_key( 'bar' );

		$this->assertNotFalse( $private );
		$this->assertInternalType( 'resource', $private );

		// Delete
		if ( 'Option' === $class ) {
			// Mock the remote request
			WP_Mock::wpFunction( 'delete_site_option', array(
				'args'   => array(
					WP_Mock\Functions::type( 'string' ),
				),
				'times'  => 1,
				'return' => true,
			) );
		}

		$delete = $storage->delete( $id );

		$this->assertTrue( $delete );

		if ( 'Option' === $class ) {
			// Mock the remote request
			WP_Mock::wpFunction( 'get_site_option', array(
				'args'   => array(
					WP_Mock\Functions::type( 'string' ),
				),
				'times'  => 1,
				'return' => false,
			) );
		}

		$test = $storage->get( $id );

		$this->assertFalse( $test );

	}

	public function test_storage_option_get_when_pair_is_not_found() {
		// Mock the remote request
		WP_Mock::wpFunction( 'update_site_option', array(
			'args'   => array(
				WP_Mock\Functions::type( 'string' ),
				WP_Mock\Functions::type( 'string' ),
			),
			'times'  => 1,
			'return' => true,
		) );

		$id = __FUNCTION__;

		// Create
		$keypair = new KeyPair( $id );
		$keypair->generate( 'foo' );

		$storage = Storage\Option::get_instance();
		$save    = $storage->save( $keypair );

		$this->assertTrue( $save );

		// Read
		WP_Mock::wpFunction( 'get_site_option', array(
			'args'   => array(
				WP_Mock\Functions::type( 'string' ),
			),
			'times'  => 1,
			'return' => '{',
		) );

		$test = $storage->get( $id );
		$this->assertFalse( $test );
	}

	public function data_storage_controllers() {
		$data = array(
			array( 'Memory' ),
			array( 'Option' ),
		);

		return $data;

	}

}
