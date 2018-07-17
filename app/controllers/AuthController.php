<?php

namespace App\Controllers;

use Lib\Core\Controller;
use Lib\Core\View;
use App\Models\User;

class AuthController extends Controller {
	
	public function check() {
		$user = new User();		
		if( $user->get_auth_user() ){
			return true;
		}
		$this->redirect( '/login' );
	}
	
	public function login(){
		$username = $username_err =
		$password = $password_err = '';
		$page_title = 'Login';
		return View::get( 'auth/login', 'auth', compact([
			'page_title',
			'username' , 'username_err',
			'password', 'password_err' ,
		]) );
	}
	
	public function auth(){
		$page_title = 'Login';
		$input = $this->post_input( ['username', 'password'] );
		$user_input = $input->get_input();
		$validate = $this->validate( $input );
		$validate->add_rule( array(
			'username' => 'required|string'
		) );
		$validate->add_rule( array(
			'password' => 'required|string'
		) );
		$validate->validate();
		if( $validate->is_valid() ){
			$username_err = $password_err = $confirm_password_err = $login_err = '';			
			$user = new User( $user_input );
			if( $user->check_credentials() ){
				$result = $user->auth_credentials();
				if( $result ){
					setcookie("id", $user->get_id(), time()+60*60*24*30);
					setcookie("hash", $user->get_user_hash(), time()+60*60*24*30);

					$this->redirect( '/' );									
				} else {
					$login_err = 'Sorry, failed to authorize';
				}				
			} else {
				$login_err = 'Credentials are incorect!';
			}
			$username = !empty( $user_input['username'] )? $user_input['username'] : '';
			$password = !empty( $user_input['password'] )? $user_input['password'] : '';
			
			return View::get( 'auth/login', 'auth', compact( [
				'page_title', 'login_err',
				'username' , 'username_err',
				'password', 'password_err' ,
			]));
		} else {
			$username = !empty( $user_input['username'] )? $user_input['username'] : '';
			$password = !empty( $user_input['password'] )? $user_input['password'] : '';
			
			$username_err =
			$password_err = '';
			
			$validation_errors = $validate->get_errors();
			
			foreach ( $validation_errors as $input => $error ){
				switch ( $input ){
					case 'username': 
						$username_err = 'User name is requeired!';
					break;
					case 'password': 
						$password_err = 'Password is required!';
					break;							
				}
			}
			return View::get( 'auth/login', 'auth', compact([
				'page_title',
				'username' , 'username_err',
				'password', 'password_err' ,
			]) );
		}
	}	
	
	public function sign_up() {
		$page_title = 'Sign Up';
		$username = $username_err =
		$password = $password_err =
		$confirm_password = $confirm_password_err = '';
		return View::get( 'auth/sign-up', 'auth', compact( [
			'page_title',
			'username' , 'username_err',
			'password', 'password_err' ,
			'confirm_password', 'confirm_password_err' 
		]));
	}
	
	public function register() {
		$page_title = 'Sign Up';
		$input = $this->post_input( ['username', 'password', 'confirm_password'] );
		$user_input = $input->get_input();
		$validate = $this->validate( $input );
		$validate->add_rule( array(
			'username' => 'required|string'
		) );
		$validate->add_rule( array(
			'password' => 'required|string|length:6'
		) );
		$validate->add_rule( array(
			'confirm_password' => 'required|string|length:6|eq:password'
		) );
		$validate->validate();
		if( $validate->is_valid() ){
			$username_err = $password_err = $confirm_password_err = $sign_up_err = '';			
			$user = new User( $user_input );
			if( !$user->exists() ){
				$result = $user->save();
				if( $result ){
					$this->redirect( '/login' );										
				} else {
					$sign_up_err = 'Sorry, failed to create account, please try later';
				}				
			} else {
				$username_err = 'User with such username already exists!';
			}
			$username = !empty( $user_input['username'] )? $user_input['username'] : '';
			$password = !empty( $user_input['password'] )? $user_input['password'] : '';
			$confirm_password = !empty( $user_input['confirm_password'] )? $user_input['confirm_password'] : '';
			return View::get( 'auth/sign-up', 'auth', compact( [
				'page_title', 'sign_up_err',
				'username' , 'username_err',
				'password', 'password_err' ,
				'confirm_password', 'confirm_password_err' 
			]));
		} else {
			
			$username = !empty( $user_input['username'] )? $user_input['username'] : '';
			$password = !empty( $user_input['password'] )? $user_input['password'] : '';
			$confirm_password = !empty( $user_input['confirm_password'] )? $user_input['confirm_password'] : '';
			
			$username_err = $password_err = $confirm_password_err = '';
			$validation_errors = $validate->get_errors();
			
			foreach ( $validation_errors as $input => $error ){
				switch ( $input ){
					case 'username': 
						$username_err = 'User name is requeired!';
					break;
					case 'password': 
						$password_err_arr = array();
						foreach ( $error as $error_rule => $is_valid ){
							switch ( $error_rule ){
								case 'required':
									$password_err_arr[] = ' is required';
								break;
								case 'length:6':
									$password_err_arr[] = ' length of the password has to be at least 6';
								break;
							}
						}
						$password_err = 'Password';
						$password_err .= implode( ' ,', $password_err_arr );						
						$password_err .= '!';
					break;
					case 'confirm_password': 						
						$confirm_password_arr = array();
						foreach ( $error as $error_rule => $is_valid ){
							switch ( $error_rule ){
								case 'required':
									$confirm_password_arr[] = ' is required';
								break;
								case 'eq:password':
									$confirm_password_arr[] = ' not equal to the password';
								break;
								case 'length:6':
									$confirm_password_arr[] = ' length has to be at least 6';
								break;
							}
						}
						$confirm_password_err = 'Password Confirmation';
						$confirm_password_err .= implode( ' ,', $confirm_password_arr );
						
						$confirm_password_err .= '!';
					break;
				}				
			}
			
			return View::get( 'auth/sign-up', 'auth', compact( [
				'page_title',
				'username' , 'username_err',
				'password', 'password_err' ,
				'confirm_password', 'confirm_password_err' 
			]));
		}
	}
}