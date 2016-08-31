<?php

function view($viewPath, array $attributes = []){
	$viewPath = str_replace('.', '/', $viewPath);
	$viewPath .= '.php';
	if(file_exists('../views/'.$viewPath)){
		return parseHtml('../views/'.$viewPath, $attributes);
	}
	else die(getError(10));
}

function parseHtml($viewPath, $attributes){
	$htmlParser = new Parser($viewPath, $attributes);
	$htmlParser->printHtml();
}

function redirect($path){
	header("Location: ".$path);
}

function getError($errorNum){
	switch($errorNum){
		case 10:
			$err = 'Couldnt find the specific View.';
			break;
		case 11:
			$err = 'Couldnt find the Model you searched.';
			break;
		case 12:
			$err = 'Couldnt find the specific Controller.';
			break;
		case 20:
			$err = 'No Post function specified for this Route.';
			break;
		case 21:
			$err = 'No Get function specified for this Route.';
			break;
		case 30:
			$err = 'No CSRFName found, probable invalid request.';
			break;
		case 31:
			$err = 'Invalid CSRF token.';
			break;
		default:
			$err = 'error num is: ' . $errorNum;
	}
	die($err);
}

function head(){
	return view('header');
}

function footer(){
	return view('footer');
}