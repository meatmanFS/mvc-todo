<?php

namespace Lib\Core;

trait Check_Controller {
	protected function check_controller_code( $controller_code ){	
		$controller_name = $this->get_controller_name( $controller_code );
		if( !$controller_name ){
			return false;
		}
		if( file_exists( $this->app_config->controllers_path . $controller_name->class . '.php' ) ){
			include ( $this->app_config->controllers_path . $controller_name->class . '.php' );
			$controller_class_name = '\\App\\Controllers\\'. $controller_name->class;
			$controller_inst = new $controller_class_name;
			if( method_exists( $controller_inst, $controller_name->method ) ){
				$controller = new \stdClass();
				$controller->controller = $controller_inst;
				$controller->controller_method = $controller_name->method;
				return $controller;
			}
		}
		return false;
	}
	
	protected function get_controller_name( $controller_code ){
		if( !empty( $controller_code ) ){
			$controller = explode( '@' , $controller_code );
			if( count( $controller ) == 2 ){
				return (object)array(
					'class'		=> $controller[0],
					'method'	=> $controller[1],
				);
			}
		}
		return false;
	}
}

