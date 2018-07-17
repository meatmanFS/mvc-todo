<?php

use Lib\Core\Config;

class App_Config {
	static $instance;
	public $app_root;
	public $app_path;
	public $dir_sep;
	
	public $controllers_path;
	public $models_path;
	public $views_path;
	
	public function __construct() {
		$this->setup();
	}
	
	protected function setup(){
		$this->dir_sep	= DIRECTORY_SEPARATOR; 
		$this->app_root = dirname( dirname( __FILE__ ) ) . $this->dir_sep;
		$this->app_path = "{$this->app_root}app{$this->dir_sep}";
		
		$this->controllers_path = "{$this->app_path}controllers{$this->dir_sep}";
		$this->models_path		= "{$this->app_path}models{$this->dir_sep}";
		$this->views_path		= "{$this->app_path}views{$this->dir_sep}";
	}
	
	public function __get( $name ) {
		if( 'config' == $name ){
			$this->config = new Config( $this->app_path );
			return $this->config;
		}
	}
		
	public static function instance(){
		if( empty( self::$instance ) ){
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
}
