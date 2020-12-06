<?php
namespace EM_OAuth;
use EM_Exception;

class OAuth_API_Token {

	public $access_token = '';
	public $refresh_token = '';
	public $token_type = '';
	public $expires_in = 0;
	/**
	 * @var int Timestamp when a token will expire at, which can be supplied instead of expires_in and that value will be generated from this one.
	 */
	public $expires_at = 0;
	public $created = 0;

	public $id = '';
	public $email = '';
	public $name = '';
	public $photo = '';

	/**
	 * @param array $token
	 * @throws EM_Exception
	 */
	public function __construct( $token ){
		$this->refresh($token);
		if( empty($token['created']) ) $this->created = time();
	}

	/**
	 * @param array $token
	 * @return boolean $updated
	 * @throws EM_Exception
	 */
	public function refresh( $token, $reset = false ){
		$updated = false;
		// reset values
		if( $reset ){
			$this->expires_in = $this->expires_at = $this->created = 0;
			$this->access_token = $this->refresh_token = $this->token_type = '';
		}
		// add new values
		foreach( $token as $k => $v ){
			if( empty($this->$k) || $this->$k != $token[$k] ){
				$this->$k = $token[$k];
				$updated = true;
			}
		}
		// set values that may not have been added
		if( empty($this->id) && !empty($this->email) ) $this->id = $this->email;
		if( !$this->created ) $this->created = time();
		// set expires_at, which is what we'll use for expiry checking
		if( $this->expires_at ){
			$this->expires_in = $this->expires_at - time();
		}elseif( $this->created && $this->expires_in ){
			$this->expires_at = $this->expires_in + $this->created;
		}else{
			$this->expires_in = $this->expires_at = time();
		}
		$this->verify();
		return $updated;
	}

	/**
	 * @throws EM_Exception
	 */
	public function verify(){
		$missing = array();
		foreach( array('access_token', 'expires_at') as $k ){
			if( empty($this->$k) ) $missing[] = $k;
		}
		if( !empty($missing) ) throw new EM_Exception( sprintf(__('Involid token credentials, the folloiwng are missing: %s.', 'events-manager'), implode(', ', $missing)) );
	}

	public function is_expired(){
		return $this->expires_at < time();
	}

	public function to_array(){
		$array = array();
		$ignore = array('id');
		foreach( get_object_vars($this) as $k => $v ){
			if( !in_array($k, $ignore) && !empty($this->$k) ) $array[$k] = $this->$k;
		}
		return $array;
	}
}