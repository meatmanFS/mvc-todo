<?php

namespace Lib\Core;

class Validate {
	/**
	 *
	 * @var Input 
	 */
	private $input;
	/**
	 * [required] (check the input existance)
	 * [email] (check for email)
	 * [number] (check for number)
	 * [string] (check for string)
	 * 
	 * @var array
	 */
	private $rules = array();
	private $is_valid = false;
	private $validation = array();
	
	public function __construct( Input $input ) {
		$this->input = $input->get_input();
	}
	
	public function add_rule( $rule ){
		$this->rules = array_merge( $this->rules, $rule );
	}
	
	public function validate() {
		$validation = array();
		foreach ( $this->rules as $input => $rules ){
			if( empty( $rules ) ){
				continue;
			}
			$input_validation = array();
			foreach ( (array)explode( '|' , $rules ) as $rule ){
				switch ( $rule ){
					case 'required':
						if( !empty( $this->input[ $input ] ) ){
							$input_validation[ $rule ] = true;
						} else {
							$input_validation[ $rule ] = false;						
						}
					break;
					case 'email':
						if( !empty( $this->input[ $input ] ) ){
							$input_validation[ $rule ] = filter_var( $this->input[ $input ], FILTER_VALIDATE_EMAIL );
						} 
					break;
					case 'number':
						if( !empty( $this->input[ $input ] ) ){
							$input_validation[ $rule ] = filter_var( $this->input[ $input ], FILTER_VALIDATE_INT );
						}
					break;					
					case 'string':
						if( !empty( $this->input[ $input ] ) ){
							$input_string = filter_var( $this->input[ $input ], FILTER_SANITIZE_STRING );
							if( $input_string !== $this->input[ $input ] ){
								$input_validation[ $rule ] = false;
							} 
						} 
					break;					
					default :
						$parts = explode( ':', $rule );
						if( count( $parts ) == 2 ){
							switch ( $parts[0] ){
								case 'eq':
									if( 
										!empty( $this->input[ $input ] )
										&& !empty( $this->input[ $parts[1] ] )
										&& $this->input[ $input ] !== $this->input[ $parts[1] ] 
									){
										$input_validation[ $rule ] = false;
									}
								break;
								case 'length':
									if( 
										!empty( $this->input[ $input ] )	
										&& strlen( $this->input[ $input]  ) < $parts[1] 
									){
										$input_validation[ $rule ] = false;
									}
								break;
								case 'date':
									if( !empty( $this->input[ $input ] ) ){
										$format = $parts[1];
										$d = \DateTime::createFromFormat( $format, $this->input[ $input] );
										if( $d && $d->format($format) !== $date ){
											$input_validation[ $rule ] = false;											
										}
									}
								break;
							}
						}
						
					break;					
				}				
			}
			
			$validation[ $input ] = $input_validation;
		}
		$this->validation = $validation;
		$this->check_validation();
	}
	
	private function check_validation(){
		$is_valid = true;
		foreach ( $this->validation as $input => $validata ){
			foreach ( $validata as $rule => $valid ){
				if( false === $valid ){
					$is_valid = false;
					break;
				}			
			}				
		}
		$this->is_valid = $is_valid;
	}
	
	public function get_errors() {
		$errors = array();
		foreach ( $this->validation as $input => $validata ){
			foreach ( $validata as $rule => $valid ){
				if( false === $valid ){
					$errors[$input][$rule] = $valid;
				}			
			}				
		}
		return $errors;
	}
	
	public function is_valid(){
		return $this->is_valid;
	}
}
