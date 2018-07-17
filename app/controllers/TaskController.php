<?php
namespace App\Controllers;

use Lib\Core\Controller;
use Lib\Core\View;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;

class TaskController extends Controller {
	
	public $middleware = 'AuthController@check';
	
	public function create() {
		$project = new Project();
		$projects = $project->get_many();
		return View::get( 'todo/task/create', 'main', compact([
			'projects'
		]));
	}
		
	public function store() {
		$page_title = 'Add Task';
		$input = $this->post_input( ['task_name', 'task_end_date', 'task_project', 'task_priority'] );
		$user_input = $input->get_input();
		$validate = $this->validate( $input );
		$validate->add_rule( array(
			'task_name' => 'required|string'
		) );
		$validate->add_rule( array(
			'task_end_date' => 'required|string|date:yy-mm-dd'
		) );
		$validate->add_rule( array(
			'task_project' => 'required|number'
		) );
		$validate->add_rule( array(
			'task_priority' => 'required|number'
		) );
		$validate->validate();
		if( $validate->is_valid() ){	
			$user = new User();
			$_user = $user->get_auth_user();
			$task = new Task( array(
				'name' => $user_input['task_name'],
				'project_id' => $user_input['task_project'],
				'priority' => $user_input['task_priority'],
				'end_date' => $user_input['task_end_date'],
				'user_id'	=> $_user->get_id(),
			) );
			$error = '';
			if( $task->save() ){
				$this->redirect( '/' );													
			} else {
				$error = 'Sorry, failed to add task';
			}
			$task_name = !empty( $user_input['task_name'] )? $user_input['task_name'] : '';
			$task_end_date = !empty( $user_input['task_end_date'] )? $user_input['task_end_date'] : '';
			$task_project = !empty( $user_input['task_project'] )? $user_input['task_project'] : '';
			$task_priority = !empty( $user_input['task_priority'] )? $user_input['task_priority'] : '';
			
			$task_name_err =
			$task_end_date_err = 
			$task_project_err = 
			$task_priority_err = '';
			
			$project = new Project();
			$projects = $project->get_many();
			return View::get( 'todo/task/store', 'main', compact( [
				'task_name', 'task_end_date', 'task_project', 'task_priority',
				'task_name_err', 'task_end_date_err', 'task_project_err',
				'task_priority_err', 'error', 'projects', 'page_title'
			]));
		} else {
			$task_name = !empty( $user_input['task_name'] )? $user_input['task_name'] : '';
			$task_end_date = !empty( $user_input['task_end_date'] )? $user_input['task_end_date'] : '';
			$task_project = !empty( $user_input['task_project'] )? $user_input['task_project'] : '';
			$task_priority = !empty( $user_input['task_priority'] )? $user_input['task_priority'] : '';
			
			$task_name_err =
			$task_end_date_err = 
			$task_project_err = 
			$task_priority_err = '';
			
			$validation_errors = $validate->get_errors();
			
			foreach ( $validation_errors as $input => $error ){
				switch ( $input ){
					case 'task_name': 
						$task_name_err = 'Task name is requeired!';
					break;
					case 'task_project': 
						$task_project_err = 'Task project is requeired!';
					break;
					case 'task_priority': 
						$task_priority_err = 'Task priority is requeired!';
					break;
					case 'task_end_date': 
						$task_end_date_err_arr = array();
						foreach ( $error as $error_rule => $is_valid ){
							switch ( $error_rule ){
								case 'required':
									$task_end_date_err_arr[] = ' is required';
								break;
								case 'date:yy-mm-dd':
									$task_end_date_err_arr[] = ' date has wrong format';
								break;
							}
						}
						$task_end_date_err = 'Task end date';
						$task_end_date_err .= implode( ' ,', $task_end_date_err_arr );						
						$task_end_date_err .= '!';
					break;							
				}
			}
			
			$project = new Project();
			$projects = $project->get_many();
			return View::get( 'todo/task/store', 'main', compact([
				'task_name', 'task_end_date', 'task_project', 'task_priority',
				'task_name_err', 'task_end_date_err', 'task_project_err',
				'task_priority_err', 'projects', 'page_title'
			]) );
		}
	}
	
	public function edit( $id ) {
		$page_title = 'Edit Task';
		$_task = new Task();
		$result = $_task->get( array(
			'id'	=> $id
		), array(
			'id', 'name' ,'project_id', 'priority', 'end_date', 'user_id'
		) );	
		if( $result ){
			$project = new Project();
			$projects = $project->get_many();
			$task_name = $_task->name;
			$task_end_date = $_task->end_date;
			$task_project = $_task->project_id;
			$task_priority = $_task->priority;
			$task_id = $_task->get_id();
			
			$task_name_err =
			$task_end_date_err = 
			$task_project_err = 
			$task_priority_err = '';

			return View::get( 'todo/task/edit', 'main', compact([
				'task_name', 'task_end_date', 'task_project', 'task_priority',
				'task_name_err', 'task_end_date_err', 'task_project_err', 'task_priority_err',
				'projects', 'task_id', 'page_title'
			]));			
		} 
		View::page_404();
	}
	
	public function update( $id ) {
		$page_title = 'Edit Task';
		$input = $this->post_input( ['task_name', 'task_end_date', 'task_project', 'task_priority'] );
		$user_input = $input->get_input();
		$validate = $this->validate( $input );
		$task_id = $id;
		$validate->add_rule( array(
			'task_name' => 'required|string'
		) );
		$validate->add_rule( array(
			'task_end_date' => 'required|string|date:yy-mm-dd'
		) );
		$validate->add_rule( array(
			'task_project' => 'required|number'
		) );
		$validate->add_rule( array(
			'task_priority' => 'required|number'
		) );
		$validate->validate();
		if( $validate->is_valid() ){	
			$user = new User();
			$_user = $user->get_auth_user();
			$task = new Task();
			$result = $task->get( array(
				'id'	=> $id
			), array(
				'id' , 'name' ,'project_id', 'priority', 'end_date', 'user_id'
			) );
			$task->name = $user_input['task_name'];
			$task->project_id = $user_input['task_project'];
			$task->priority = $user_input['task_priority'];
			$task->end_date = $user_input['task_end_date'];
			$task->user_id	= $_user->get_id();
			
			$error = '';
			
			if( $task->save() ){
				$this->redirect( "/tasks/$id/edit" );													
			} else {
				$error = 'Sorry, failed to edit task';
			}
			$task_name = !empty( $user_input['task_name'] )? $user_input['task_name'] : '';
			$task_end_date = !empty( $user_input['task_end_date'] )? $user_input['task_end_date'] : '';
			$task_project = !empty( $user_input['task_project'] )? $user_input['task_project'] : '';
			$task_priority = !empty( $user_input['task_priority'] )? $user_input['task_priority'] : '';
			
			$task_name_err =
			$task_end_date_err = 
			$task_project_err = 
			$task_priority_err = '';
			
			$project = new Project();
			$projects = $project->get_many();
			return View::get( 'todo/task/edit', 'main', compact( [
				'task_name', 'task_end_date', 'task_project', 'task_priority',
				'task_name_err', 'task_end_date_err', 'task_project_err',
				'task_priority_err', 'error', 'projects', 'page_title', 'task_id',
			]));
		} else {
			$task_name = !empty( $user_input['task_name'] )? $user_input['task_name'] : '';
			$task_end_date = !empty( $user_input['task_end_date'] )? $user_input['task_end_date'] : '';
			$task_project = !empty( $user_input['task_project'] )? $user_input['task_project'] : '';
			$task_priority = !empty( $user_input['task_priority'] )? $user_input['task_priority'] : '';
			
			$task_name_err =
			$task_end_date_err = 
			$task_project_err = 
			$task_priority_err = '';
			
			$validation_errors = $validate->get_errors();
			
			foreach ( $validation_errors as $input => $error ){
				switch ( $input ){
					case 'task_name': 
						$task_name_err = 'Task name is requeired!';
					break;
					case 'task_project': 
						$task_project_err = 'Task project is requeired!';
					break;
					case 'task_priority': 
						$task_priority_err = 'Task priority is requeired!';
					break;
					case 'task_end_date': 
						$task_end_date_err_arr = array();
						foreach ( $error as $error_rule => $is_valid ){
							switch ( $error_rule ){
								case 'required':
									$task_end_date_err_arr[] = ' is required';
								break;
								case 'date:yy-mm-dd':
									$task_end_date_err_arr[] = ' date has wrong format';
								break;
							}
						}
						$task_end_date_err = 'Task end date';
						$task_end_date_err .= implode( ' ,', $task_end_date_err_arr );						
						$task_end_date_err .= '!';
					break;							
				}
			}
			
			$project = new Project();
			$projects = $project->get_many();
			return View::get( 'todo/task/edit', 'main', compact([
				'task_name', 'task_end_date', 'task_project', 'task_priority',
				'task_name_err', 'task_end_date_err', 'task_project_err',
				'task_priority_err', 'projects', 'page_title', 'task_id',
			]) );
		}
	}
	
	public function delete( $id ) {
		$task = new Task();
		$result = $task->get( array(
			'id'	=> $id
		), array( 'id' ) );
		if( $result ){
			$task->delete();
		}
		$this->redirect( '/' );			
	}

}