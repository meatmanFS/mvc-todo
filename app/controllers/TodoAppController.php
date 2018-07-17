<?php

namespace App\Controllers;

use Lib\Core\Controller;
use Lib\Core\View;

use App\Models\Task;
use App\Models\Project;

class TodoAppController extends Controller {
	
	public $middleware = 'AuthController@check';
	
	public function index() {
		$task = new Task();
		$task->date_query( array(
			'today'	=> 'end_date'
		) );
		$tasks = $task->get_many(array(
			'user_id'	=> $this->middleware_data->get_id(),
			'state'		=> 0,
		));
		$project = new Project();
		$project->set_join_params( $task, array(
			'user_id'	=> $this->middleware_data->get_id(),
			'state'		=> 0,
		));
		$projects = $project->get_many(array(
			'user_id'	=> $this->middleware_data->get_id()
		));
		if( empty( $tasks ) ){
			$tasks = array();
		}
		if( empty( $projects ) ){
			$projects = array();
		}
		return View::get( 'todo/index', 'main', compact([
			'tasks', 'projects'
		]) );
	}
	
	public function next7d() {
		$task = new Task();
		$date = new \DateTimeImmutable();
		$next_day = new \DateInterval("P1D");
		$date_from = $date->add( $next_day );
		$next_7days = new \DateInterval("P7D");
		$date_to = $date->add( $next_7days );
		$task->date_query( array(
			'between'	=> array(
				'field'	=> 'end_date',
				'from' => "'{$date_from->format('Y-m-d')}'",
				'to' => "'{$date_to->format('Y-m-d')}'",
			)
		) );
		$tasks = $task->get_many(array(
			'user_id'	=> $this->middleware_data->get_id(),
			'state'		=> 0,
		));
		$project = new Project();
		$project->set_join_params( $task, array(
			'user_id'	=> $this->middleware_data->get_id(),
			'state'		=> 0,
		));
		$projects = $project->get_many(array(
			'user_id'	=> $this->middleware_data->get_id()
		));
		if( empty( $tasks ) ){
			$tasks = array();
		}
		if( empty( $projects ) ){
			$projects = array();
		}
		return View::get( 'todo/next7d-tasks', 'main', compact([
			'tasks', 'projects'
		]) );
	}
	
	public function archive() {
		$task = new Task();
		$tasks = $task->get_many(array(
			'user_id'	=> $this->middleware_data->get_id(),
			'state'		=> 1,
		));
		$project = new Project();
		$project->set_join_params( $task, array(
			'user_id'	=> $this->middleware_data->get_id(),
			'state'		=> 0,
		));
		$projects = $project->get_many(array(
			'user_id'	=> $this->middleware_data->get_id()
		));
		if( empty( $tasks ) ){
			$tasks = array();
		}
		if( empty( $projects ) ){
			$projects = array();
		}
		return View::get( 'todo/archive', 'main', compact([
			'tasks', 'projects'
		]) );
	}
}
