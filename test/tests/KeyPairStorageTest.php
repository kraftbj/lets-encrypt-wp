<?php

namespace LEWP\Keys;

class KeyPairStorageTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider data_storage_controllers
	 *
	 * @param string $class Storage controller class name
	 */
	public function test_keypair_storage_and_retrieval( $class ) {

		$storage = call_user_func( __NAMESPACE__ . "\\Storage\\{$class}::get_instance" );

		$id = __FUNCTION__;

		// Create
		$keypair = new KeyPair( $id );
		$keypair->generate( 'foo' );

		$save = $storage->save( $keypair );

		$this->assertTrue( $save );

		// Read
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

		$test = $storage->get( $id );

		$this->assertInstanceOf( __NAMESPACE__ . '\KeyPair', $test );
		$this->assertSame( $id, $test->get_id() );

		$private = $test->export_private_key( 'foo' );

		$this->assertFalse( $private );

		$private = $test->export_private_key( 'bar' );

		$this->assertNotFalse( $private );
		$this->assertInternalType( 'resource', $private );

		// Delete
		$delete = $storage->delete( $id );

		$this->assertTrue( $delete );

		$test = $storage->get( $id );

		$this->assertFalse( $test );

	}

	public function data_storage_controllers() {

		$data = array();

		// foreach ( glob( dirname( dirname( __DIR__ ) ) . '/src/Keys/Storage/*.php' ) as $file ) {
		// 	$class = substr( basename( $file ), 0, -4 );
		// 	$data[] = array(
		// 		$class,
		// 	);
		// }

		$data[] = array(
			'Memory',
		);

		return $data;

	}

}
