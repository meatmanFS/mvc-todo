<?php
use Lib\Core\Router;

include_once 'app-config.php';
include_once 'autoload.php';

$app_config = App_Config::instance();
new Auto_Load( $app_config );

Router::run( $app_config );