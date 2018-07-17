<?php

namespace App\Models;

use Lib\Core\Model;
use App\Models\Project;

class Task extends Model {
	public $fields = ['name' ,'project_id', 'priority', 'end_date', 'user_id' ];
	
	
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
	
	public function state_color() {
		$color = '';
		switch ( $this->priority ){
			case '1' : 
				$color = 'white';
			break;
			case '2' : 
				$color = 'orange';
			break;
			case '3' : 
				$color = 'red';
			break;
		}
		return $color;
	}
	
	public function belongs_to_one() {
		return new Project();
	}
	
	public function get_belong_field(){
		return 'project_id';
	}
}
