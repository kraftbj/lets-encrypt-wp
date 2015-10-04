<?php

namespace LEWP\Request;

class Nonce extends Request {
	/**
	 * Construct the nonce.
	 *
	 * @param  string    $resource    The REST URL to get the nonce from.
	 * @return Nonce
	 */
	public function __construct( $resource ) {
		$method = 'HEAD';
		parent::__construct( $resource, $method );
	}
}