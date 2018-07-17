<?php

namespace App\Models;

use Lib\Core\Model;
use App\Models\Task;

class Project extends Model {
	public $fields = ['name' ,'color', 'user_id'];
	
	
	public function get_many( $params = false ) {
		$data = parent::get_many( $params );
		if( !empty( $data ) ){
			$_data = array();
			foreach ( $data as $item ){
				if( is_array( $item ) ){ 
					$class = new self();
					$class->setup_data($item);
					$_data[] = $class;
				} else {
					$class = new self();
					$class->setup_data($data);
					$_data[] = $class;
					break;
				}
			}
			return $_data;
		} else {
			return false;
		}
	}
	
	public function has_many() {
		return new Task();
	}
		
	public function get_has_many_field(){
		return 'project_id';
	}
	
}
