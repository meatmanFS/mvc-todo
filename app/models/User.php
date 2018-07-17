<?php

namespace App\Models;

use Lib\Core\Model;

class User extends Model {
	public $fields = ['username' ,'password', 'user_hash' ];
	
	private $password_trimed = false;
	
	public function exists() {
		$query = "SELECT ID FROM {$this->get_table_name()} WHERE username = ?";
		$user = $this->request( $query, array(
			array(
				'type' => 's',
				'value'	=> $this->save_fields['username']
			)
		) );
		if( !empty( $user ) ){
			return true;
		}
		return false;
	}
	
	public function check_credentials( $fields = false ) {
		$user = $this->get( $fields );		
		if( !empty( $user ) ){
			return true;
		}
		return false;
	}
	
	public function get_auth_user(){
		if (
			!empty($_COOKIE['id'])
			&& !empty($_COOKIE['hash'])
		){
			$user = new User();
			$result = $user->check_credentials(array(
				'id'		=> $_COOKIE['id'],
				'user_hash'	=> $_COOKIE['hash'],
			));
			if( $result ){
				return $user;
			}
		}
		return false;
	}
	
	public function auth_credentials() {
		$this->user_hash = $this->get_hash();
		$result = $this->save();
		if( true == $result ){
			return true;
		}
		return false;
	}
	
	public function get_user_hash() {
		return $this->user_hash;
	}
	
	public function get_hash() {
		return md5( $this->get_rundom_code() );
	}
	
	public function get_rundom_code( $length = 6 ) {
		$chars = range( "a", "z");
		$chars = array_merge( $chars, range( "A", "Z") );
		$chars = array_merge( $chars, range( "0", "9") );

		$code = array_rand( $chars, $length );
		$code_values = array();
		foreach ( $code as $keys ){
			$code_values[] = $chars[ $keys ];
		}

		return implode( '', $code_values );
	}
	
	public function save() {
		$this->save_fields['password'] = $this->get_password();
		return parent::save();
	}
	
	private function get_password(){
		if( $this->password_trimed ){
			return $this->save_fields['password'];
		}
		$this->password_trimed = true;
		return md5(trim($this->save_fields['password']));
	}
	
	public function before_get(){
		$this->save_fields['password'] = $this->get_password();
	}
}