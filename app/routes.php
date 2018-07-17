<?php

return array(
	array(
		'route'		=> '/',
		'method'	=> 'GET',
		'handler'	=> 'TodoAppController@index',
	),
	array(
		'route'		=> '/next-7-days',
		'method'	=> 'GET',
		'handler'	=> 'TodoAppController@next7d',
	),
	array(
		'route'		=> '/archive',
		'method'	=> 'GET',
		'handler'	=> 'TodoAppController@archive',
	),
	// tasks
	array(
		'route'		=> '/tasks/create',
		'method'	=> 'GET',
		'handler'	=> 'TaskController@create',
	),
	array(
		'route'		=> '/tasks',
		'method'	=> 'POST',
		'handler'	=> 'TaskController@store',
	),
	array(
		'route'		=> '/tasks/{id}/edit',
		'method'	=> 'GET',
		'handler'	=> 'TaskController@edit',
	),
	array(
		'route'		=> '/tasks/{id}/delete',
		'method'	=> 'GET',
		'handler'	=> 'TaskController@delete',
	),
	array(
		'route'		=> '/tasks/{id}/done',
		'method'	=> 'GET',
		'handler'	=> 'TaskController@done',
	),
	array(
		'route'		=> '/tasks/{id}',
		'method'	=> 'POST',
		'handler'	=> 'TaskController@update',
	),
	// projects
	array(
		'route'		=> '/projects/create',
		'method'	=> 'GET',
		'handler'	=> 'ProjectController@create',
	),
	array(
		'route'		=> '/projects/{id}',
		'method'	=> 'GET',
		'handler'	=> 'ProjectController@show',
	),
	array(
		'route'		=> '/projects',
		'method'	=> 'POST',
		'handler'	=> 'ProjectController@store',
	),
	array(
		'route'		=> '/projects/{id}/edit',
		'method'	=> 'GET',
		'handler'	=> 'ProjectController@edit',
	),
	array(
		'route'		=> '/projects/{id}/delete',
		'method'	=> 'GET',
		'handler'	=> 'ProjectController@delete',
	),
	array(
		'route'		=> '/projects/{id}',
		'method'	=> 'POST',
		'handler'	=> 'ProjectController@update',
	),
	// login
	array(
		'route'		=> '/login',
		'method'	=> 'GET',
		'handler'	=> 'AuthController@login',
	),
	array(
		'route'		=> '/logout',
		'method'	=> 'GET',
		'handler'	=> 'AuthController@logout',
	),
	array(
		'route'		=> '/login',
		'method'	=> 'POST',
		'handler'	=> 'AuthController@auth',
	),
	array(
		'route'		=> '/sign-up',
		'method'	=> 'GET',
		'handler'	=> 'AuthController@sign_up',
	),
	array(
		'route'		=> '/sign-up',
		'method'	=> 'POST',
		'handler'	=> 'AuthController@register',
	),
);