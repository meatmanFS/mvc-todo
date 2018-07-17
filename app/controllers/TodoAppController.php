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
		$tasks = $task->get_many();
		$project = new Project();
		$projects = $project->get_many();
		return View::get( 'todo/index', 'main', compact([
			'tasks', 'projects'
		]) );
	}
	
}
