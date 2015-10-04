<?php

namespace LEWP\Request;

class Directory extends Request {
	/**
	 * Construct the object.
	 *
	 * Primary purpose of this method is to set the ACME resource type.
	 *
	 * @param  string    $resource    The REST resource URL.
	 * @return Directory
	 */
	public function __construct( $resource ) {
		parent::__construct( $resource );
		$this->set_type( 'directory' );
	}
}