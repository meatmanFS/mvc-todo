<?php

namespace Lib\Core;

class Input {
	private $input = array();
	
	public function get( $elements ){
		if( !empty( $_GET ) ){
			foreach ( $elements as $element ){
				foreach ( $_GET as $key => $item ){
					if( $element == $key ){
						$this->input[ $key ] = $item;
						break;
					}
				}
			}
		}
	}
	
	public function post( $elements ){
		if( !empty( $_POST ) ){
			foreach ( $elements as $element ){
				foreach ( $_POST as $key => $item ){
					if( $element == $key ){
						$this->input[ $key ] = $item;
						break;
					}
				}
			}
		}
	}
	
	public function get_input(){
		return $this->input;
	}
}

