<?php

namespace Lib\Core;

class Model extends Core {
	
	protected $app_config;
	/**
	 *
	 * @var DB 
	 */
	private $db;
	
	protected $table_name;
	protected $fields = [];
	protected $save_fields;
	protected $current_data;
	protected $is_db_ok = false;
	
	public function __construct( $save_fields = false ) {
		parent::__construct();
		$this->save_fields = $save_fields;
	}
	
	protected function setup() {
		$db_config = $this->app_config->config->get_db();
		$db = new DB( $db_config );
		if( $db->is_db_ok() ){
			$this->db = $db;
			$this->is_db_ok = true;
		}
		unset( $this->app_config );
	}
	
	public function __get( $name ){
		if( in_array( $name, $this->fields )){
			return $this->get_field( $name );
		}		
	}
	
	private function get_field( $field ) {
		if( !empty( $this->current_data[ $field ] ) ){
			return $this->current_data[ $field ];			
		}
		return '';
	}
		
	public function get_many( $params = false ) {
		$where = '';
		if( !empty( $params ) ){
			$where = "WHERE {$this->get_where_fields( $params )}";
		}
		$query = "SELECT * FROM {$this->get_table_name()} {$where}";
		$args = $this->get_where_args( $params );
		$data = $this->request( $query, $args );
		if( !empty( $data ) ){
			return $data;
		} else {
			return false;
		}
	}
	
	protected function setup_data( $data ){
		$this->current_data = $data;
	}
	
	public function get( $fields = false, $get_fields = false ) {
		if( false == $fields ){
			if( method_exists( $this , 'before_get') ){
				$this->before_get();
			}
			if( !empty( $this->save_fields ) ){
				$save_fields = array();
				foreach ( $this->fields as $field ){
					if( !empty( $this->save_fields[ $field ] ) ){
						$save_fields[ $field ] = $this->db->sanitizy( $this->save_fields[ $field ] );					
					}
				}
				$this->save_fields = $save_fields;
				if( !empty( array_filter( $this->save_fields ) ) ){
					$query = "SELECT {$this->get_id_column()}, {$this->get_where_colums()} FROM {$this->get_table_name()} WHERE {$this->get_where_fields()}";
					$args = $this->get_where_args();
					$data = $this->request( $query, $args );
					if( !empty( $data ) ){
						$this->current_data = $data;
						return $this->current_data[ $this->get_id_column() ];
					} else {
						return false;
					}
				}
				
			}
		} else {
			$select = '*';
			if( !empty( $get_fields ) ){
				$select = $this->get_select_colums( $get_fields );
			} else {
				$select = $this->get_where_colums( $fields );
			}
			
			$query = "SELECT $select FROM {$this->get_table_name()} {$this->get_join()} WHERE {$this->get_where_fields( $fields )}";
			$args = $this->get_where_args( $fields );
			$data = $this->request( $query, $args );
			if( !empty( $data ) ){
				$this->current_data = $data;
				return true;
			} else {
				return false;
			}
		}
		return false;
	}
	
	public function request( $query, $params = false ) {
		if( !$this->is_db_ok() ){
			return false;
		}
		try {
			return $this->_request( $query, $params );
		} catch (\Exception $exc) {
			return $exc->getTraceAsString();
		}
	}
	
	private function _request( $query, $params = false ){
		$request = $this->db->prepare( $query );
		if ( $request ) {
			$params = $this->_check_params( $params );
			if( !empty( $params ) ){
				$types = '';
				$data = array();
				foreach ( $params as $param ){
					$types .= $param->type;
					$data[] = $param->value;
				}
				
				$bind_names[] = &$types;
				for ($i=0; $i<count( $data );$i++) 
				{
					$bind_name = 'bind' . $i;
					$$bind_name = $data[$i];
					$bind_names[] = &$$bind_name;
				}
				$return = call_user_func_array(
					array(
						$request,'bind_param'
					),
					$bind_names
				);			
			}			
			$request->execute();
			$result = $request->get_result();
			if( is_bool( $result ) ){
				return $result;
			}
			$data = array();
			while( $result_data = $result->fetch_assoc() ) {
				$data[] = $result_data;
				$result_data = false;
			}
			if( count( $data ) <= 1 ){
				return array_shift( $data );
			}
			return $data;
		}
		return false;
	}
	
	protected function get_join(){
//		if( method_exists(  $this, ) ){
//			
//		}
		
		return '';
	}
	
	public function __set( $name, $value ) {
		if( in_array( $name , $this->fields ) ){
			$this->current_data[ $name ] = $value;
		}
	}
	
	private function _check_params( $params ){
		$_params = array();
		if( !empty( $params ) ){
			foreach ( $params as $param ){
				if( !empty( $param['type'] ) && $param['value'] ){
					$param_data = new \stdClass;
					$param_data->type = $param['type'];
					$param_data->value = $param['value'];
					$_params[] = $param_data;					
				}
			}
		}
		return $_params;
	}
	
	public function save() {
		if( !$this->is_db_ok() ){
			return false;
		}
		return $this->_save();
	}
	
	public function is_db_ok(){
		return $this->is_db_ok;
	}
	
	private function _save(){
		if( empty( $this->current_data ) && !empty( $this->save_fields ) ){
			$save_fields = array();
			foreach ( $this->fields as $field ){
				if( !empty( $this->save_fields[ $field ] ) ){
					$save_fields[ $field ] = $this->db->sanitizy( $this->save_fields[ $field ] );					
				}
			}
			if( !empty( array_filter( $save_fields ) ) ){
				$this->current_data = $save_fields;
				$insert_id = $this->db->insert( $this );
				if( is_numeric( $insert_id ) ){
					$this->current_data = array_merge( $this->current_data, [ $this->get_id_column() => $insert_id] );									
					return $insert_id;
				} else {
					return false;
				}
			}
		}
		if( !empty( $this->current_data ) ){
			$result = $this->db->update( $this );
			if( true == $result ){
				return true;
			} else {
				return $result;
			}
		}
		return false;
	}
	
	public function delete() {
		if( !empty( $this->current_data ) ){
			$query = "DELETE FROM {$this->get_table_name()} WHERE {$this->get_id_column()} = {$this->get_id()}";
			$result = $this->db->query( $query );
			if( true == $result ){
				return true;
			} else {
				return $result;
			}
		}
		return false;
	}
	
	public function get_table_name(){
		$path = explode('\\', get_class($this));
		return strtolower( array_pop($path) ) . 's';
	}
	
	public function get_fields(){
		$current_data = array();
		foreach ( $this->current_data as $data ){
			$current_data[] = $data;
		}
		$string = implode( "','", $current_data );
		return "'{$string}'";
	}
	
	public function get_where_colums( $fields = false ) {
		if( empty( $fields ) ){
			$fields = $this->save_fields;
		}
		return implode( ',', array_keys( $fields ) );
	}
	
	public function get_select_colums( $select_colums ) {		
		return implode( ',', $select_colums );
	}
	
	public function get_where_fields( $fields = false ){
		if( empty( $fields ) ){
			$fields = $this->save_fields;
		}
		if( empty( $fields ) ){
			return;
		}
		$keys = array_keys( $fields );
		$where_fields = array();
		foreach ( $keys as $keys ){
			$where_fields[] = $keys . ' = ? ';
		}
		return implode( 'AND ', $where_fields );
	}
	
	public function get_update_fields() {
		$fields = array();
		foreach ( $this->current_data as $field => $value ){
			$fields[ $field ] = array(
				'type' => $this->get_field_type( $field ),
				'value'	=> $value,
			);
		}
		return $fields;
	}
	
	public function get_where_args( $fields = false) {
		if( empty( $fields ) ){
			$fields = $this->save_fields;
		}
		if( empty( $fields ) ){
			return;
		}
		$where_args = array();
		foreach ( $fields as $field => $value ){
			$where_args[] = array(
				'type' => $this->get_field_type( $field ),
				'value'	=> $value,
			);
		}
		return $where_args;
	}
	
	public function get_field_type( $field ){
		return 's';
	}
	
	public function get_id_column() {
		return 'id';
	}
	
	public function get_id(){
		return $this->current_data[ $this->get_id_column() ];
	}
	
	public function get_update_set() {
		$update_set = array();
		$fields = $this->get_update_fields();
		foreach ( $fields as $field => $data ){
			if( $field == $this->get_id_column() ){
				continue;
			}
			$field_type = "'%s'";
			if( 'd' == $data['type'] ){
				$field_type = '%d';
			}
			$update_set[] = sprintf( "%s = $field_type", $field, $data['value'] );
		}
		return implode( ' ,', $update_set );
	}
	
	public function get_colums() {
		return implode( ',', array_keys( $this->current_data ) );
	}
		
}
