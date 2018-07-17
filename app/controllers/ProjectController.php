<?php
namespace App\Controllers;

use Lib\Core\Controller;
use Lib\Core\View;

use App\Models\Project;

class ProjectController extends Controller {
	
	public $middleware = 'AuthController@check';
	
	public function create() {
		return View::get( 'todo/project/create', 'main' );
	}
	
	public function store() {
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
				'project_name_err', 'project_color_err', 'project_name', 'project_color',
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
				'project_name_err', 'project_color_err', 'project_name', 'project_color',
			]) );
		}
	}

}