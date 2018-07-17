<?php

namespace App\Models;

use Lib\Core\Model;
use App\Models\Project;

class Task extends Model {
	public $fields = ['name' ,'project_id', 'priority', 'end_date', 'user_id', 'state' ];
	
	
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
			return $this->sort_data( $_data );
		} else {
			return false;
		}
	}
	
	public function sort_data( $_data ){
		$_data = array_filter( (array)$_data );
		if( empty( $_data ) ){
			return array();
		}
		uasort( $_data, array( $this, 'sort_by_priority' ));
		return $_data;
	}
	
	public function sort_by_priority( $a, $b ) {
		if ($a->priority == $b->priority) {
			return 0;
		}
		return ($a->priority > $b->priority) ? -1 : 1;
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
	
	public function format_field( $field, $data ) {
		if( 'end_date' == $field ){
			return date( 'Y-m-d\TH:i', strtotime( $data ) );
		}
		if( 'priority' == $field ){
			if( $this->state == 1 ){
				return $data;
			}
			if( strtotime( $this->end_date ) < time() ){
				return 3;				
			}
		}
		return $data;
	}
	
	public function belongs_to_one() {
		return new Project();
	}
	
	public function get_belong_field(){
		return 'project_id';
	}
}
