<?php

namespace Lib\Core;

class View extends Core {
	private $default_templates;
	protected  $app_config;
	private $views_dir;
	private $template_404;
	private $view;
	private $template;
	private $view_located;
	private $template_located;
	private $template_data;
	
	public  $views_lauout_dir;
	
	private static $instance;
	
	
	protected function setup(){
		$ds = $this->app_config->dir_sep;
		$this->default_templates =
			"{$this->app_config->app_root}lib{$ds}templates{$ds}error_pages{$ds}";
		
		$this->views_dir = $this->app_config->views_path;
		$this->views_lauout_dir = "{$this->app_config->views_path}layouts{$ds}";
			
		$this->template_404 = "{$this->default_templates}404.html";
	}
	
	public function get_template_404(){
		return $this->template_404;
	}
	
	public static function instance(){
		if( empty( self::$instance ) ){
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	public function locate_view( $view, $template = false, $data = false ){
		$this->view_located = false;
		$this->template_located = false;
		$this->template_data = $data;
		if( file_exists( "{$this->views_dir}{$view}.php"  ) ) {
			$this->view = "{$this->views_dir}{$view}.php";
			$this->view_located = true;
		}
		if(!empty( $template )) {
			if( file_exists( "{$this->views_dir}{$template}.php"  )  ){
				$this->template = "{$this->views_dir}{$template}.php";
				$this->template_located = true;
			} elseif( file_exists( "{$this->views_lauout_dir}{$template}.php"  )  ){				
				$this->template = "{$this->views_lauout_dir}{$template}.php";
				$this->template_located = true;
			}
		}
		return $this;
	}
	
	public function display() {
		if( $this->view_located ){
			if( !empty( $this->template_data ) && is_array( $this->template_data ) ){
				extract( $this->template_data );
			}
			if( $this->template_located  ){
				ob_start();
				include( $this->view );							
				$content = ob_get_clean();
				ob_start();
				include ( $this->template );			
				echo ob_get_clean();
			} else {
				ob_start();
				include( $this->view );							
				echo ob_get_clean();
			}
		}
	}
	
	public function load_layout( $layout_template ){
		if(!empty( $layout_template )) {
			if( file_exists( "{$this->views_dir}{$layout_template}.php"  )  ){
				ob_start();
				include ( "{$this->views_dir}{$layout_template}.php" );			
				return ob_get_clean();				
			} elseif( file_exists( "{$this->views_lauout_dir}{$layout_template}.php"  )  ){				
				ob_start();
				include ( "{$this->views_lauout_dir}{$layout_template}.php" );			
				return ob_get_clean();	
			}
		}
	}
	
	
	public function layout( $template ){
		echo $this->load_layout( $template );
	}
	
	public static function get( $view, $template = false, $data = false ) {
		return self::instance()->locate_view( $view, $template, $data );
	}
	
	public static function page_404(){
		if( file_exists( self::instance()->get_template_404() ) ) {
			echo file_get_contents( self::instance()->get_template_404() );
		}
	}
}
