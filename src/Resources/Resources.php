<?php

namespace LEWP\Resources;

/**
 * Gets resources from an ACME server via the directory resource
 */

class Resources {
	private $directory_url = '';

	public function __construct( $directory_url ) {
		$this->set_directory_url( $directory_url );
	}

	public function set_directory_url( $url ) {
		$this->directory_url = $url;
	}

	public function get_directory_url() {
		return $this->directory_url;
	}
}