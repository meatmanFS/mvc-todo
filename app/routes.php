<?php

return array(
	array(
		'route'		=> '/',
		'method'	=> 'GET',
		'handler'	=> 'TodoAppController@index',
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
		'route'		=> '/projects',
		'method'	=> 'POST',
		'handler'	=> 'ProjectController@store',
	),
	// login
	array(
		'route'		=> '/login',
		'method'	=> 'GET',
		'handler'	=> 'AuthController@login',
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