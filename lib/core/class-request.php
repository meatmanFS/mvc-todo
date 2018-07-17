<?php

namespace Lib\Core;

class Request {
	use Check_Controller;
	
	/**
	 *
	 * @var \App_Config
	 */
	protected $app_config;
	protected $routes;
	protected $has_route = false;
	
	protected $current_request;
	protected $current_request_method;
	protected $request_method;
	protected $request_controller;
	protected $default_request_method = 'GET';
	protected $current_route;
	protected $routes_file;
	
	public $controller;
	public $controller_method;
	public $route_parts = [];
	
	public function __construct( \App_Config $app_config  ) {
		$this->app_config = $app_config;
		$this->setup();		
		$this->load_routes();
	}
	
	public function setup() {
		$this->current_request = $this->get_request();
		$this->current_request_method = $_SERVER['REQUEST_METHOD'];
		$this->app_path = $this->app_config->app_path;		
		$this->routes_file = 'routes.php';
	}
	
	private function get_request(){
		$request = $_SERVER['REQUEST_URI'];
		$request_parts = explode( '?', $request );
		return $request_parts[0];
	}
	
	protected function load_routes(){
		if( file_exists( "{$this->app_path}{$this->routes_file}" ) ){
			$this->routes = require ( "{$this->app_path}{$this->routes_file}" );			
		}
	}
	
	public function dispatch(){
		$this->find_route();
		$this->check_route();
	}
	
	public function has_route() {
		return $this->has_route;
	}
	
	protected function find_route(){
		foreach ( (array)$this->routes as $route => $handler ){
			if( 
				!empty( $handler['route'] ) 
				&& (
					$handler['route'] == $this->current_request 
					|| $this->check_preg_route( $handler['route'] )
				) 
						
			){
				if( isset( $handler['handler'] ) ){
					if( 
						isset( $handler['method'] )
						&& $handler['method'] == $this->current_request_method
					){
						$this->request_method		= $handler['method'];
						$this->current_route		= $handler['route'];
						$this->request_controller	= $handler['handler'];
						break;
					}
				}
			}
		}
	}
	
	protected function check_preg_route( $route ) {
		if( preg_match( '/({(.+?)})/i', $route, $matches ) ){
			$route_pattern = preg_replace( '/{(.+?)}/i', '(.+)', $route );
			$route_pattern = str_replace( '/', '\/', $route_pattern );
			$route_pattern = "/^$route_pattern/";
			if( preg_match( $route_pattern, $this->current_request, $route_matches ) ){
				$this->route_parts[ $matches[2] ] = $route_matches[1];
				return true;
			}
		
		}
		return false;
	}
	
	protected function check_route(){
		if( !empty( $this->current_route ) && $this->check_controller() ){
			$this->has_route = true;
		}
	}
	
	protected function check_controller(){
		$controller = $this->check_controller_code( $this->request_controller );
		
		if( !$controller ){
			return false;
		}
		$this->controller			= $controller->controller;
		$this->controller_method	= $controller->controller_method;
		return true;
	}	
}

