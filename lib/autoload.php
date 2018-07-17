<?php

class Auto_Load {
	protected $include_path;
	protected $dir_sep;
	
	public function __construct( App_Config $config ) {
		$this->setup( $config );
		$this->auto_load();
	}
	
	protected function setup( $config ){
		$this->include_path = $config->app_root;
		$this->dir_sep		= $config->dir_sep;
	}
	
	public function get_module_path( $path ){
		array_pop( $path );// class name
		$_path = array();
		foreach ( $path as $path_item ){
			$_path[] = strtolower( $path_item );
		}
		$module_path = implode( $this->dir_sep, $_path );
		return "{$this->include_path}$module_path{$this->dir_sep}";
	}
	
	private function auto_load(){
		spl_autoload_register( array( $this, 'load' ) );		
	}
	
	public function load( $load_name ) {
		$load_name = explode( '\\', $load_name );
		$class_name = end( $load_name );
		// load the class
		$class_file = 'class-' . str_replace( '_', '-', strtolower( $class_name ) ) . '.php';
		$module_path = $this->get_module_path( $load_name );
		if ( file_exists( $module_path . $class_file ) ) {
			include_once( $module_path . $class_file );
		}	
		// load trait
		$trait_file = 'trait-' . str_replace( '_', '-', strtolower( $class_name ) ) . '.php';
		if ( file_exists( $module_path . $trait_file ) ) {
			include_once( $module_path . $trait_file );
		}	
		// try to load the module (with name == class name)
		if ( file_exists( $module_path . $class_name .'.php' ) ) {
			include_once( $module_path . $class_name .'.php' );
		}	
	}
}

