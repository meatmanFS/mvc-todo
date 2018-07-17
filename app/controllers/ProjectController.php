<?php
namespace App\Controllers;

use Lib\Core\Controller;
use Lib\Core\View;

use App\Models\Project;
use App\Models\Task;

class ProjectController extends Controller {
	
	public $middleware = 'AuthController@check';
	
	public function create() {
		return View::get( 'todo/project/create', 'main' );
	}
	
	public function show( $id ) {
		$this->check_user($id);
		$task = new Task();
		$_project = new Project();
		$_project->set_join_params( $task, array(
			'user_id'	=> $this->middleware_data->get_id(),
			'state'		=> 0,
		));		
		$result = $_project->get( array(
			'id'	=> $id
		), array( 'id', 'name', 'color', 'user_id' ) );
		$tasks = $task->sort_data( $_project->tasks );
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
		return View::get( 'todo/project/project-single', 'main', compact([
			'tasks', 'projects', '_project'
		]) );
	}
	
	public function store() {
		$page_title = 'Edit Project';
		$input = $this->post_input( ['project_name', 'project_color'] );
		$user_input = $input->get_input();
		$validate = $this->validate( $input );
		$validate->add_rule( array(
			'project_name' => 'required|string'
		) );
		$validate->add_rule( array(
			'project_color' => 'required|string'
		) );
		$validate->validate();
		if( $validate->is_valid() ){			
			$project = new Project( array(
				'name' => $user_input['project_name'],
				'color' => $user_input['project_color'],	
				'user_id'	=> $this->middleware_data->get_id(),
			) );
			$error = '';
			if( $project->save() ){
				$this->redirect( '/' );													
			} else {
				$error = 'Sorry, failed to add project';
			}
			$project_name = !empty( $user_input['project_name'] )? $user_input['project_name'] : '';
			$project_color = !empty( $user_input['project_color'] )? $user_input['project_color'] : '';
			
			$project_name_err =
			$project_color_err = '';
			
			return View::get( 'todo/project/store', 'main', compact( [
				'project_name_err', 'project_color_err', 'project_name', 'project_color', 'page_title'
			]));
		} else {
			$project_name = !empty( $user_input['project_name'] )? $user_input['project_name'] : '';
			$project_color = !empty( $user_input['project_color'] )? $user_input['project_color'] : '';
			
			$project_name_err =
			$project_color_err = '';
			
			$validation_errors = $validate->get_errors();
			
			foreach ( $validation_errors as $input => $error ){
				switch ( $input ){
					case 'project_name': 
						$project_name_err = 'Project name is requeired!';
					break;
					case 'project_color': 
						$project_color_err = 'Project color is requeired!';
					break;											
				}
			}
			return View::get( 'todo/project/store', 'main', compact([
				'project_name_err', 'project_color_err', 'project_name', 'project_color', 'page_title'
			]) );
		}
	}
	
	
	public function edit( $id ) {
		$page_title = 'Edit Project';
		$this->check_user($id);
		$_project = new Project();
		$result = $_project->get( array(
			'id'	=> $id
		), array(
			'id', 'name', 'color'
		) );	
		if( $result ){
			$project_name = $_project->name;
			$project_color = $_project->color;
			$project_id = $_project->get_id();
			
			$project_name_err =
			$project_color_err = '';

			return View::get( 'todo/project/edit', 'main', compact([
				'project_name_err', 'project_color_err', 'project_name', 'project_color', 'page_title', 'project_id'
			]));			
		} 
		View::page_404();
	}
	
	public function update( $id ) {
		$page_title = 'Edit Project';
		$this->check_user($id);
		$input = $this->post_input( ['project_name', 'project_color'] );
		$user_input = $input->get_input();
		$validate = $this->validate( $input );
		$project_id = $id;
		$validate->add_rule( array(
			'project_name' => 'required|string'
		) );
		$validate->add_rule( array(
			'project_color' => 'required|string'
		) );
		$validate->validate();
		if( $validate->is_valid() ){	
			$_project = new Project();
			$result = $_project->get( array(
				'id'	=> $id
			), array(
				'id', 'name', 'color'
			) );	
			$_project->name = $user_input['project_name'];
			$_project->color = $user_input['project_color'];
			
			$error = '';
			
			if( $_project->save() ){
				$this->redirect( "/projects/$id/edit" );													
			} else {
				$error = 'Sorry, failed to edit project';
			}
			$project_name = $_project->name;
			$project_color = $_project->color;			
			
			$project_name_err =
			$project_color_err = '';
			return View::get( 'todo/project/edit', 'main', compact( [
				'project_name_err', 'project_color_err', 'project_name', 'project_color', 'error', 'page_title', 'project_id'
			]));
		} else {
			$project_name = !empty( $user_input['project_name'] )? $user_input['project_name'] : '';
			$project_color = !empty( $user_input['project_color'] )? $user_input['project_color'] : '';
			
			$project_name_err =
			$project_color_err = '';
			
			$validation_errors = $validate->get_errors();
			
			foreach ( $validation_errors as $input => $error ){
				switch ( $input ){
					case 'project_name': 
						$project_name_err = 'Project name is requeired!';
					break;
					case 'project_color': 
						$project_color_err = 'Project color is requeired!';
					break;											
				}
			}
			return View::get( 'todo/project/edit', 'main', compact([
				'project_name_err', 'project_color_err', 'project_name', 'project_color', 'page_title', 'project_id'
			]) );
		}
	}
	
	public function delete( $id ) {
		$this->check_user($id);	
		$task = new Task();
		$project = new Project();
		$project->set_join_params( $task, array(
			'user_id'	=> $this->middleware_data->get_id(),
			'state'		=> 0,
		));		
		$result = $project->get( array(
			'id'	=> $id
		), array( 'id' ) );
		$unfinished_tasks = false;
		if( !empty( $project->tasks ) ){
			foreach ( $project->tasks as $task ){
				if( 0 == $task->state  ){
					$unfinished_tasks = true;
					break;
				}
			}
		}
		if( $result && !$unfinished_tasks ){
			$project->delete();
			$this->redirect( '/' );			
		}
		$delete_error = 'All tasks for the project has to be finished!';
		return View::get( 'todo/project/delete-error', 'main', compact([
			'delete_error'
		]) );
	}
	
	public function check_user( $id ) {
		$project = new Project();
		$result = $project->get( array(
			'id'	=> $id
		), array(
			'id', 'name', 'color', 'user_id'
		) );
		if( $result && $project->user_id != $this->middleware_data->get_id() ){
			View::page_401();
		}		
		return false;
	}

}