<?php

namespace Lib\Core;

class Config {
	public $config_file;
	
	protected $config;
	protected $app_path;
	
	protected $base_url;
	protected $db;
	
	static $instance;
	
	public function __construct( $app_path ) {
		$this->setup( $app_path );
		$this->load_config();
	}
	
	protected function setup( $app_path ){
		$this->app_path = $app_path;		
		$this->config_file = 'config.php';
	}
	
	protected function load_config(){
		if( file_exists( "{$this->app_path}{$this->config_file}" ) ){
			$config = require ( "{$this->app_path}{$this->config_file}" );
			if( !empty( $config ) && is_array( $config ) ){
				if( !empty( $config['base_url'] ) ){
					$this->base_url = $config['base_url'];
				}
				if( !empty( $config['db'] ) ){
					$this->db = $config['db'];
				}
			}
		}
	}
	
	public function get_base_url() {
		return $this->base_url;
	}
	
	public function get_db() {
		return $this->db;
	}

}
