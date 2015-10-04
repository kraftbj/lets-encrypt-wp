<?php

require_once __DIR__ . '/../vendor/autoload.php';

class MockData {
	static function get_directory_response() {
		return array(
			'headers'  => array(
				'server'                      => 'nginx',
				'content-type'                => 'application/json',
				'content-length'              => '279',
				'access-control-allow-origin' => array(
					'*',
					'*',
				),
				'replay-nonce'                => 'Cee2LkvwXwSoMBC4nSRDQfeOzIGHb6WOEbtfpSwv4FI',
				'x-frame-options'             => 'DENY',
				'strict-transport-security'   => 'max-age=604800',
				'expires'                     => 'Sun, 04 Oct 2015 14:38:51 GMT',
				'cache-control'               => 'max-age=0, no-cache, no-store',
				'pragma'                      => 'no-cache',
				'date'                        => 'Sun, 04 Oct 2015 14:38:51 GMT',
				'connection'                  => 'close',
			),
			'body'     => '{"new-authz":"https://acme.org/new-authz","new-cert":"https://acme.org/new-cert","new-reg":"https://acme.org/new-reg","revoke-cert":"https://acme.org/revoke-cert"}',
			'response' => array(
				'code'    => 200,
				'message' => 'OK',
			),
			'cookies'  => array(),
			'filename' => null,
		);
	}

	static function get_head_response() {
		return array(
			'headers'  => array(
				'server'                      => 'nginx',
				'content-type'                => 'application/json',
				'content-length'              => '279',
				'access-control-allow-origin' => '*',
				'allow'                       => 'POST',
				'replay-nonce'                => 'Cee2LkvwXwSoMBC4nSRDQfeOzIGHb6WOEbtfpSwv4FI',
				'expires'                     => 'Sun, 04 Oct 2015 14:38:51 GMT',
				'cache-control'               => 'max-age=0, no-cache, no-store',
				'pragma'                      => 'no-cache',
				'date'                        => 'Sun, 04 Oct 2015 14:38:51 GMT',
				'connection'                  => 'close',
			),
			'body'     => '',
			'response' => array(
				'code'    => 405,
				'message' => 'Method Not Allowed',
			),
			'cookies'  => array(),
			'filename' => null,
		);
	}
}