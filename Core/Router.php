<?php namespace Core;

use \Klein\Klein;

class Router extends Klein {

	public function res($path, $controllerAction, $method = ['GET', 'POST'])
	{

		$controller = $controllerAction[0];
		$action = $controllerAction[1];

		if (class_exists($controller)) {
			$callback = [new $controller(), $action];
			parent::respond($method, $path, $callback);
		} else {
			try {
				throw new \Exception("Controller class '$controller' not found");
			} catch (\Exception $e) {
				echo '<pre>' . $e . '</pre>';
			}
		}
	}

	public static function currentRoute() {
		return $_SERVER['REQUEST_URI'];
	}
}
