<?php

namespace Lib\Core;

class Controller extends Core {	
	use Check_Controller;
	
	protected $app_config;
	protected $base_url;
	
	protected function setup(){
		$this->base_url = $this->app_config->config->get_base_url();
	}
	
	public function request( $request ) {
		$middleware = $this->middleware( $request );
		if( !$middleware ){
			$method = $request->controller_method;
			if( !empty( $request->route_parts ) ){ 
				$route_parts = $request->route_parts;
				$keys = array_keys( $route_parts );
				switch ( count( $route_parts ) ){
					case 1:
						return $this->$method( $route_parts[ $keys[0] ] );
					case 2:
						return $this->$method(
							$route_parts[ $keys[0] ]
							,$route_parts[ $keys[1] ]
						);
					case 3:
						return $this->$method(
							$route_parts[ $keys[0] ]
							,$route_parts[ $keys[1] ]
							,$route_parts[ $keys[2] ]
						);
					case 4:
						return $this->$method(
							$route_parts[ $keys[0] ]
							,$route_parts[ $keys[1] ]
							,$route_parts[ $keys[2] ]							
							,$route_parts[ $keys[3] ]							
						);						
				}
				
			} 
			return $this->$method();
		} else {
			return $middleware;
		}
	}
	
	public function middleware(){
		if( isset( $this->middleware ) ){
			$controller = $this->check_controller_code( $this->middleware );
			if( $controller ){
				$result = call_user_func( array( $controller->controller, $controller->controller_method ) );
				if( true !== $result ){
					return $result;
				}
			}
		}
		return false;
	}
	
	public function redirect( $location ){
		header("HTTP/1.1 301 Moved Permanently"); 
		header("Location: {$this->base_url}$location"); 
		die();
	}
	
	public function get_input( $elements ){
		$input = new Input();
		$input->get($elements);
		return $input;
	}
	
	public function post_input( $elements ){
		$input = new Input();		
		$input->post($elements);
		return $input;
	}
	
	public function validate( Input $input ) {
		return new Validate( $input );
	}
}
