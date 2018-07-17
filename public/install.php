<?php

if( 
	empty( $_GET['pass'] )  
	|| (
		!empty( $_GET['pass'] )
		&& 'AEZAKMI' != $_GET['pass']
	)
){
	die();
}

