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
	protected $join_params = [];
	protected $date_query = [];
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
		return $this->_get($name);
	}
	
	private function _get( $name ) {
		if( in_array( $name, $this->fields )){
			return $this->get_field( $name );
		}	
		if( $this->has_join() && $this->get_belongs_name() == $name ){
			return $this->get_field( $name );
		}
		if( $this->has_join() && $this->get_have_many_name() == $name ){
			return $this->get_field( $name );
		}
	}
	
	public function __isset( $name ) {
		$isset = $this->_get($name);
		if( empty( $isset ) ){
			return false;
		}
		return true;
	}
	
	private function get_field( $field ) {
		if( !empty( $this->current_data[ $field ] ) ){
			return $this->format_field( $field, $this->current_data[ $field ] );			
		}
		return '';
	}
	
	protected function format_field( $field, $data ){
		return $data;
	}
		
	public function get_many( $params = false ) {
		$where = '';
		if( !empty( $params ) ){
			$where = "WHERE {$this->get_where_fields( $params )}";
		}
		$query = "SELECT {$this->get_all_colums()} FROM {$this->get_query_table_name()} {$this->get_join()} {$where}";
		
		$args = $this->get_where_args( $params );
		$data = $this->request( $query, $args );
		if( !empty( $data ) ){
			if( $this->have_many() ){
				return $this->filter_has_many( $data );
			}
			return $data;
		} else {
			return false;
		}
	}
	
	protected function setup_data( $data ){
		$this->setup_current_data( $data );
	}
	
	protected function setup_plain_data( $data ){
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
					$query = "SELECT
							{$this->get_id_column()}, {$this->get_where_colums()}
							FROM {$this->get_table_name()} 
							{$this->get_join()}
							WHERE {$this->get_where_fields()}";
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
			
			$query = "SELECT
					$select 
					FROM {$this->get_query_table_name()} 
					{$this->get_join()} 
					WHERE {$this->get_where_fields( $fields )}";
			$args = $this->get_where_args( $fields );
			$data = $this->request( $query, $args );
			if( !empty( $data ) ){
				if( $this->has_join() 
					&& !empty( $data[0] )
					&& is_array( $data[0] )
				){			
					$data = $this->filter_has_many( $data );	
					if( count( $data ) == 1 ){
						$data = array_shift( $data );
					}
				}
				$this->setup_current_data( $data );
				return true;
			} else {
				return false;
			}
		}
		return false;
	}
	
	private function setup_current_data( $data ){
		if( $this->has_join() ){
			$current_data = array();
			$join_data = array();			
			foreach ( $data as $field => $data_item ){
				if( 
					in_array(  $field, $this->fields ) 
					|| $field == $this->get_id_column()
				){
					$current_data[ $field ] = $data_item;
				} else {
					$join_data[ $field ] = $data_item;
				}
				
			}
			if( !empty( $join_data ) && $this->have_belongs() ){
				$belongs_name = $this->get_belongs_name();
				if( !empty($belongs_name )){
					$belongs_data = $this->parse_belongs_data( $join_data );
					if( !empty( $belongs_data ) ){
						$current_data[ $belongs_name ] = $belongs_data;					
					}
				}
				
			}
			if( !empty( $join_data ) && $this->have_many() ){
				$have_many_name = $this->get_have_many_name();				
				if( !empty( $have_many_name ) && !empty( $join_data[ $have_many_name] ) ){
					$have_many_data = $this->parse_have_many_data( $join_data[ $have_many_name] );
					if( !empty( $have_many_data ) ){
						$current_data[ $have_many_name ] = $have_many_data;					
					}					
				} elseif( !empty( $join_data ) ) {
					$have_many_data = $this->parse_have_many_data( $join_data );
					if( !empty( $have_many_data ) ){
						$current_data[ $have_many_name ] = $have_many_data;					
					}	
				}
			}
			$this->current_data = $current_data;
		} else {
			$this->current_data = $data;			
		}
	}
	
	private function parse_belongs_data( $data ){
		$belongs = $this->get_belongs();
		if( !empty( $belongs ) ){
			$belongs_data = array();
			foreach ( $data as $field => $value ){
				$_field = $field;
				$alias = $belongs->get_table_name_alias();
				if( !empty( $alias )) {
					$_field = str_replace( "{$alias}_", '', $_field );				
				}

				$belongs_data[ $_field ] = $value;
			}
			
			if( !empty( $belongs ) ){
				$belongs->setup_data( $belongs_data );
				return $belongs;
			}
		}
		
		return false;
	}
	
	protected function filter_has_many( $data ){
		$main_ids = array();
		$main_data = array();
		foreach ( $data as $key => $data_item ){
			foreach ( $data_item as $field => $value ){
				if( 
					in_array(  $field, $this->fields ) 
					|| $field == $this->get_id_column()
				){
					$main_data[ $key ][ $field ] = $value;
				} else {
					$join_data[ $data_item[$this->get_id_column()] ][$key][ $field ] = $value;
				}

			}
		}
		$_main_data = array();
		$have_many_name = $this->get_have_many_name();
		foreach ( $main_data as $data_item ){
			$data_id = $data_item[$this->get_id_column()];
			if( !in_array( $data_id, $main_ids ) ){
				$current_data = $data_item;
				$qw = array_filter( $join_data[$data_id] );
				if( !empty( $join_data[$data_id] ) ){
					$has_many_data = $this->filter_has_many_data( $join_data[$data_id] );
					if( !empty( $has_many_data ) ){
						$current_data[ $have_many_name ] = $has_many_data;											
					}
				}
				$_main_data[] = $current_data;
			}
			$main_ids[] = $data_id;
		}
		return $_main_data;
	}
	
	protected function filter_has_many_data( $many_data ){
		$_many_data = array();
		foreach ( $many_data as $item ){
			if( array_filter( $item ) ){
				$_many_data[] = $item;
			}
		}
		return $_many_data;
	} 
	
	private function parse_have_many_data( $data ){
		$have_many = $this->get_have_many();
		if( !empty( $have_many ) ){
			$have_many_data = array();
			$one_have_many_data = array();
			foreach ( $data as $key => $data_item ){
				$single_have_many_data = array();
				if( is_array( $data_item ) ){
					foreach ( $data_item as $field => $value ){
						$_field = $field;
						$alias = $have_many->get_table_name_alias();
						if( !empty( $alias )) {
							$_field = str_replace( "{$alias}_", '', $_field );				
						}

						$single_have_many_data[ $_field ] = $value;
					}	
					if( !empty( $single_have_many_data ) ){
						$have_many->setup_plain_data( $single_have_many_data );
						$have_many_data[] = clone( $have_many );
					}
				} else {
					$_field = $key;
					$alias = $have_many->get_table_name_alias();
					if( !empty( $alias )) {
						$_field = str_replace( "{$alias}_", '', $_field );				
					}

					$one_have_many_data[ $_field ] = $data_item;
				}
				
			}
			if( !empty( $one_have_many_data ) ){
				$have_many->setup_plain_data( $one_have_many_data );
				$have_many_data[] = clone( $have_many );
			}
			return $have_many_data;
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
	
	
	private function has_join(){
		if( method_exists(  $this, 'belongs_to_one' ) ){
			return true;
		}
		if( method_exists(  $this, 'has_many' ) ){
			return true;
		}
		return false;
	}
	
	private function have_belongs(){
		if( method_exists(  $this, 'belongs_to_one' ) ){
			return true;
		}
		return false;
	}
	
	private function have_many(){
		if( method_exists(  $this, 'has_many' ) ){
			return true;
		}
		return false;
	}
	
	protected function get_table_name_alias(){
		return substr( $this->get_table_name(), 0, 1 );
	}
	
	public function set_join_params( Model $model, $param ) {
		if( !is_array( $param ) ){
			return false;
		}
		$this->join_params[] = array( 
			'model' => $model,
			'param' => $param,
		);
	}
	
	protected function get_join(){
		if( $this->have_belongs() ){
			$belongs = $this->belongs_to_one();
			return 
			"LEFT JOIN {$belongs->get_table_name()} AS {$belongs->get_table_name_alias()}
			ON {$this->get_table_name_alias()}.{$this->get_belong_field()} = {$belongs->get_table_name_alias()}.{$belongs->get_id_column()}
			{$this->get_join_params()}" ;
		}
		if( $this->have_many() ){
			$has_many = $this->has_many();
			return 
			"LEFT JOIN {$has_many->get_table_name()} AS {$has_many->get_table_name_alias()}
			ON {$this->get_table_name_alias()}.{$this->get_id_column()} = {$has_many->get_table_name_alias()}.{$this->get_has_many_field()} 
			{$this->get_join_params()}";
		}
		
		return '';
	}
	
	protected function get_join_params(){
		if( !empty( $this->join_params ) ){
			foreach ( $this->join_params as $join_param ){
				$keys = array_keys( $join_param['param'] );
				$join_model = $join_param['model'];
				$fields = array();
				foreach ( $keys as $key ){
					$fields[] = sprintf( 
						$join_model->get_table_name_alias() . ".{$key} = {$join_model->get_field_type_value($key)}"
						, $join_param['param'][ $key ]
					);	
				}
				$fields = array_merge( $fields, $join_model->get_join_date_query() );
				if( !empty( $fields )  ){
					return 'AND ' . implode( ' AND ', $fields );					
				}
			}
			
		}
		return '';
	}
	
	protected function get_belongs_columns(){
		if( $this->have_belongs() ){
			$belongs = $this->belongs_to_one();
			$all_colimns = $belongs->get_all_columns();
			$fields = array();
			foreach ( $all_colimns as $field ){				
				$fields[] = "{$belongs->get_table_name_alias()}.{$field} AS {$belongs->get_table_name_alias()}_{$field}" ;				
			}
			return $fields;
		}
		
		return array();
	}
	
	protected function get_many_columns(){
		if( $this->have_many() ){
			$many = $this->has_many();
			$all_colimns = $many->get_all_columns();
			$fields = array();
			foreach ( $all_colimns as $field ){				
				$fields[] = "{$many->get_table_name_alias()}.{$field} AS {$many->get_table_name_alias()}_{$field}" ;				
			}
			return $fields;
		}
		
		return '';
	}
	
	protected function get_belongs(){
		if( method_exists(  $this, 'belongs_to_one' ) ){
			$belongs = $this->belongs_to_one();
			return $belongs;
		}
		
		return false;
	}
	
	protected function get_belongs_name(){
		if( $this->have_belongs() ){
			$belongs = $this->belongs_to_one();
			return $belongs->get_table_name();
		}
		
		return false;
	}
	
	protected function get_have_many(){
		if( $this->have_many() ){
			$many = $this->has_many();
			return $many;
		}
		
		return false;
	}
	
	protected function get_have_many_name(){
		if( $this->have_many() ){
			$many = $this->has_many();
			return $many->get_table_name();
		}
		
		return false;
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
				if( !empty( $param['type'] ) && isset($param['value']) ){
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
		$table_name = strtolower( array_pop($path) ) . 's';
		return $table_name;
	}
	
	public function get_query_table_name(){
		$table_name = $this->get_table_name();
		if( $this->has_join() ){
			return "$table_name AS {$this->get_table_name_alias()}";
		}
		return $table_name;
	}
	
	public function get_fields(){
		$current_data = array();
		foreach ( $this->current_data as $data ){
			$current_data[] = $data;
		}
		$string = implode( "','", $current_data );
		return "'{$string}'";
	}
	
	public function get_all_columns() {
		return array_merge( array( $this->get_id_column()), $this->fields );
	}
	
	public function get_where_colums( $fields = false ) {
		if( empty( $fields ) ){
			$fields = $this->save_fields;
		}
		if( $this->has_join() ){
			$_fields = array_keys( $fields );
			$fields = array();
			foreach ( $_fields as $field ){
				$fields[] = $this->get_table_name_alias() . '.' . $field;
			}
			if( $this->have_belongs() ){
				$fields = array_merge( $fields, $this->get_belongs_columns() );
			}
			if( $this->have_many() ){
				$fields = array_merge( $fields, $this->get_many_columns() );
			}
			return implode( ',', $fields );
		}
		return implode( ',', array_keys( $fields ) );
	}
	
	public function get_all_colums( ) {		
		$fields = $this->get_all_columns();
		if( $this->has_join() ){
			$_fields = array();
			foreach ( $fields as $field ){
				$_fields[] = $this->get_table_name_alias() . '.' . $field;
			}
			if( $this->have_belongs() ){
				$_fields = array_merge( $_fields, $this->get_belongs_columns() );
			}
			if( $this->have_many() ){
				$_fields = array_merge( $_fields, $this->get_many_columns() );
			}
				
			return implode( ',', $_fields );
		}
		return implode( ',', $fields );
	}
	
	public function get_select_colums( $select_colums ) {	
		if( $this->has_join() ){
			$_fields = array();
			foreach ( $select_colums as $field ){
				$_fields[] = $this->get_table_name_alias() . '.' . $field;
			}			
			if( $this->have_belongs() ){
				$_fields = array_merge( $_fields, $this->get_belongs_columns() );
			}
			if( $this->have_many() ){
				$_fields = array_merge( $_fields, $this->get_many_columns() );
			}
			return implode( ',', $_fields );
		}
		return implode( ',', $select_colums );
	}
	
	public function date_query( $param ) {
		$this->date_query = array_merge( $this->date_query, $param );
	}
	
	private function get_date_query(){
		if( !empty( $this->date_query ) ){
			$date_query = array();
			foreach ( $this->date_query as $field => $param ){
				switch ( $field ){
					case 'today':
						$date_query[] = "DATE($param) = DATE(NOW())";
					break;
					case 'between':
						$date_query[] = "DATE({$param['field']}) BETWEEN {$param['from']} AND {$param['to']}";
					break;
				}
			}
			return $date_query;
		}
		return array();
	}
	
	private function get_join_date_query(){
		if( !empty( $this->date_query ) ){
			$date_query = array();
			foreach ( $this->date_query as $field => $param ){
			$_field = "{$this->get_table_name_alias()}.{$field}";
				switch ( $param ){
					case 'today':
						$date_query[] = "DATE($_field) = DATE(NOW())";
					break;
					case 'between':
						$date_query[] = "DATE($_field) BETWEEN {$param['from']} AND {$param['to']}";
					break;
				}
			}
			return $date_query;
		}
		return array();
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
			if( $this->has_join() ){
				$where_fields[] = $this->get_table_name_alias() . '.' .$keys . ' = ? ';				
			} else {
				$where_fields[] = $keys . ' = ? ';				
			}
		}
		$where_data = $where_fields;
		$where_data = array_merge( $where_data , $this->get_date_query() );
		return implode( 'AND ', array_filter( $where_data ) );
	}
	
	public function get_update_fields() {
		$fields = array();
		foreach ( $this->current_data as $field => $value ){
			if( $this->has_join() 
				&& ( 
					$this->get_belongs_name() == $field 
					|| $this->get_have_many_name() == $field
				) 					
			){
				continue;
			}
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
	
	public function get_field_type_value( $field ){
		$field_type_value = "'%s'";
		switch ( $this->get_field_type($field) ){
			case 's': 
				$field_type_value = "'%s'";
			break;
			case 'd': 
				$field_type_value = "%d";
			break;
		}
		return $field_type_value;
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
