<?php

namespace LEWP\Keys;

class KeyPairTest extends \PHPUnit_Framework_TestCase {

	public function test_keypair_generates_correctly() {
		$keypair = new KeyPair( __FUNCTION__ );
		$keypair->generate( 'hunter2' );

		$public  = $keypair->get_public_key();
		$private = $keypair->get_private_key();

		$this->assertInternalType( 'string', $public );
		$this->assertInternalType( 'string', $private );
		$this->assertNotEmpty( $public );
		$this->assertNotEmpty( $private );
		$this->assertSame( __FUNCTION__, $keypair->get_id() );
	}

	public function test_generated_private_key_exports_with_correct_passphrase() {
		$keypair = new KeyPair( __FUNCTION__ );
		$keypair->generate( 'hunter2' );

		$private = $keypair->export_private_key( 'hunter2' );

		$this->assertNotFalse( $private );
		$this->assertInternalType( 'resource', $private );
	}

	public function test_generated_private_key_does_not_export_with_incorrect_passphrase() {
		$keypair = new KeyPair( __FUNCTION__ );
		$keypair->generate( 'hunter2' );

		$private = $keypair->export_private_key( 'hunter1' );

		$this->assertFalse( $private );
	}

	public function test_keypair_reads_correctly() {
		$keypair = new KeyPair( __FUNCTION__ );
		$keypair->read( array(
			'public'  => $this->data_public_key(),
			'private' => $this->data_private_key(),
		) );

		$public  = $keypair->get_public_key();
		$private = $keypair->get_private_key();

		$this->assertInternalType( 'string', $public );
		$this->assertInternalType( 'string', $private );
		$this->assertSame( $this->data_public_key(), $public );
		$this->assertSame( $this->data_private_key(), $private );
	}

	public function test_read_private_key_exports_with_correct_passphrase() {
		$keypair = new KeyPair( __FUNCTION__ );
		$keypair->read( array(
			'public'  => $this->data_public_key(),
			'private' => $this->data_private_key(),
		) );

		$private = $keypair->export_private_key( $this->data_private_key_passphrase() );

		$this->assertNotFalse( $private );
		$this->assertInternalType( 'resource', $private );
	}

	public function test_read_private_key_does_not_export_with_incorrect_passphrase() {
		$keypair = new KeyPair( __FUNCTION__ );
		$keypair->read( array(
			'public'  => $this->data_public_key(),
			'private' => $this->data_private_key(),
		) );

		$private = $keypair->export_private_key( $this->data_private_key_passphrase() . 'incorrect' );

		$this->assertFalse( $private );
	}

	public function data_private_key_passphrase() {
		return 'foo';
	}

	public function data_private_key() {
		return "-----BEGIN RSA PRIVATE KEY-----
Proc-Type: 4,ENCRYPTED
DEK-Info: DES-EDE3-CBC,02619DA8FFB230AD

iXPGwZCRbctemYJP6mybn9vlILdyzhr8dO0Wq83S7lMKAb8L5T2E3yTJXNdNqDWs
aQwuBg4K1cZT+q67Hp7qn720V0nQQFj3gJoAEnl/0qWOojJWiv/2U/xduav6lDAt
o0Mf1+BMiMN+/PUh8pLfqSE6tQEbqz2Ohh7R77CRd1pm2UbhnTR7dHpeQz9xUxkB
Grxcd8xdzl0/GlXbXYBLvf35wkBQeDxKS0QJTGk+DWgnTZ8fxnsAULyPX0riEr2c
Hl2ZEJqqYknL+lOa/N3skg6hr9WOdi1vXRWxBfj5eeUqjD20RrQNZC4jxhNMAACL
4i/AeWb7N8l5S7vdjUbhczGtdaUUCCvWdSZA5f0/tw7U/FlQQb9XVJZQ0iN92SeY
8vyAggrbVHx4vsY3VaKUr8qTmyDXyntFfryRNnb5Qkw20mnKuIGZUAcM/ft+vPMd
lKkmcdiC5E1n+VSwMsGWmaZSTtxOXqW2zMnWxG3b47URavLFYbLEBm1ez0QMsPxe
zN3Xg3wSdYrXf+B2NCbv3DKdq0yQKeRfUBKFAhpsjqRxhMMysn5y/lXm+9/s5ihB
+IMDJXk2ZJoaGhNuLE5KMpeRkweEqQaWrTiYulUlX+zlzt6d2DVqn8PijjxxNkfs
3NDd4UmLUDPtO6q1bHSoGFmdRz1qKHJRaSD2+OE64TUs2FG3UWzJJy7781Q8LeHg
3P9/IZYlXccrOfJivE9vtu+gYb1UxAlowx9VBsYWBRusupdqja02cdFmyhZMEK0B
Q3yG9SVylY9OHcSWJMUStrQGBAzy1IlImSUmhdfQWrGMeWMDLfinbA==
-----END RSA PRIVATE KEY-----
";
	}

	public function data_public_key() {
		return "-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDVtJAgSzHoCAN7Nc3tAeXG5/7p
i1KHbSJzowMazqvaMJmv5NwlOJcg4nGwDDL/sjk478mAUg2TBbJcxtLvnIBvElrm
UW28EgKHsVDWBN3jKW3Mui9yUO8R4rRldn/wRLGAUn7PuJKJKsdqocpUw4VZBc0o
GxQNOcuif4YmZrYfZwIDAQAB
-----END PUBLIC KEY-----
";
	}

}