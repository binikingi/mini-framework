<?php
	session_start();
	require '../app/config.php';
	require '../Controllers/BaseController.php';
	require '../Models/BaseModel.php';
	require '../app/Db.php';
	require '../app/Parser.php';
	require '../app/helpers.php';
	require '../app/csrf.php';

	spl_autoload_register(function ($class_name) {
		$included = false;
	    foreach($GLOBALS['autoload'] as $path)
	    	if(file_exists('../' . $path . '/' . $class_name . '.php') && !$included){
	    		include '../' . $path . '/' . $class_name . '.php';
	    		$included = true;
	    	}
	});

	$baseController = new BaseController();
	$baseController->invoke();