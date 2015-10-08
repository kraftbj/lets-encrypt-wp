<?php

class KeyPairTest extends PHPUnit_Framework_TestCase {
	public function testTemp() {
		$keyPair = new LEWP\Keys\KeyPair( 'test' );
		$resource = $keyPair->generate();

		var_dump( $resource );
		echo $keyPair->extract_private_key( $resource );
	}
}