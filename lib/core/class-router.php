<?php

namespace Lib\Core;

use Lib\Core\Request;
use Lib\Core\View;

class Router {
		
	public static function run( \App_Config $config ){		
		$request = new Request( $config );
		$request->dispatch();
		if( $request->has_route() ){
			$view = call_user_func( array( $request->controller, 'request' ), $request );
			$view->display();
		} else {
			View::page_404();			
		}	
	}
}

