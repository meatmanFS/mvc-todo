<?php

namespace Lib\Core;

abstract class Core {
	
	/**
	 *
	 * @var \App_Config 
	 */
	protected $app_config;
	
	public function __construct() {
		$this->app_config = \App_Config::instance();
		$this->setup();
	}
	
	abstract protected function setup();
}