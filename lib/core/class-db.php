<?php

namespace Lib\Core;

use Lib\Core\Model;

class DB {
	static $instance;
	
	private $server;
	private $user_name;
	private $password;
	private $db_name;
	
	private $table;
	private $fields;
	private $connection;
	
	private $is_db_ok = false;
		
	public function __construct( $config ) {
		$this->setup( $config );
	}
	
	protected function setup( $config ){
		if( 
			isset( $config['server'] )
			&& isset( $config['user_name'] )
			&& isset( $config['password'] )
			&& isset( $config['db_name'] )
		){
			$this->server = $config['server'];
			$this->user_name = $config['user_name'];
			$this->password = $config['password'];
			$this->db_name = $config['db_name'];
			
			$this->connect_db();
		}
	}
		
	public function connect_db() {
		$connection = new \mysqli(
			$this->server
			, $this->user_name
			, $this->password
			, $this->db_name
		);
		
		if( !mysqli_connect_errno() ){
			$this->is_db_ok = true;
			$this->connection = $connection;
			register_shutdown_function(array( $this, 'shutdown' ));
		}
	}
	
	public function shutdown() {
		$this->connection->close();
	}
	
	public function prepare( $query ) {
		return $this->connection->prepare( $query );
	}
	
	public function sanitizy( $data ){
		return $this->connection->escape_string( $data );
	}
	
	public function insert( Model $model ) {
		$query = 
		"INSERT INTO {$model->get_table_name()} ({$model->get_colums()})
		VALUES ({$model->get_fields()})";
		$result = $this->connection->query($query);
		if( !$result ){
			return (object)array(
				'error' => $this->connection->error,
				'errno' => $this->connection->errno,
			);
		}
	
		return $this->connection->insert_id;
	}
	
	public function update( Model $model ) {
		$query = 
		"UPDATE {$model->get_table_name()}
		SET {$model->get_update_set()}
		WHERE ID = {$model->get_id()}";
		$result = $this->connection->query($query);
		if( !$result ){
			return (object)array(
				'error' => $this->connection->error,
				'errno' => $this->connection->errno,
			);
		}
		return true;
	}
	
	public function query( $query ) {
		$result = $this->connection->query( $query );
		if( !$result ){
			return (object)array(
				'error' => $this->connection->error,
				'errno' => $this->connection->errno,
			);
		}
		return true;
	}
	
	public function is_db_ok(){
		return $this->is_db_ok;
	}
	
	public static function instance(){
		if( empty( self::$instance ) ){
			self::$instance = new self();
		}
		
		return self::$instance;
	}
}

