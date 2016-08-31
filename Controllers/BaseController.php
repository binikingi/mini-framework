<?php
class BaseController{
	private $uri;
	private $method;
	private $get;
	private $post;

	public function __construct(){
		$this->uri = $_SERVER['REQUEST_URI'];
		$this->method = $_SERVER['REQUEST_METHOD'];

		$this->get = [
			'/'					=>	'PagesController@index',
		];

		$this->post = [
				
		];
	}

	public function invoke()
	{
		$uriPath = array_values(array_filter(explode('/', $this->uri)));
		$controllerPath = $this->getMethodFunction($uriPath, strtolower($this->method));
		return $this->runControllerFunction($controllerPath, $uriPath);
	}

	private function getMethodFunction($uri, $method){
		foreach($this->$method as $key=>$val)
		{
			$keyPath = array_values(array_filter(explode('/', $key)));
			if(count($keyPath) != count($uri))
				continue;
			if($this->checkPath($keyPath, $uri))
				return $val;
		}
		return $method=='get'?21:20;
	}

	private function getPostFunction($uri){
		foreach($this->post as $key=>$val)
		{
			$keyPath = array_values(array_filter(explode('/', $key)));
			if(count($keyPath) != count($uri))
				continue;
			if($this->checkPath($keyPath, $uri))
				return $val;
		}
		return 20;
	}

	private function getGetFunction($uri){
		foreach($this->get as $key=>$val)
		{
			$keyPath = array_values(array_filter(explode('/', $key)));
			if(count($keyPath) != count($uri))
				continue;
			if($this->checkPath($keyPath, $uri))
				return $val;
		}
		return 21;
	}

	private function runControllerFunction($controllerPath, $uriPath){
		if(is_int($controllerPath))
			getError($controllerPath);
		$controllerPath = explode('@', $controllerPath);
		if(file_exists('../Controllers/' . $controllerPath[0] . '.php')){
			include '../Controllers/' . $controllerPath[0] . '.php';
			$controller = new $controllerPath[0]();
			$function = $controllerPath[1];
			$controller->$function($uriPath);
			return true;
		}
		getError(12);
	}

	private function checkPath($keyPath, $uriPath)
	{
		foreach($keyPath as $key=>$val)
		{
			if($val != '*' && $val != $uriPath[$key])
				return false;
		}
		return true;
	}
}