<?php namespace App\Lib;

use \Core\Logger;
use Dotenv\Dotenv;
use PDO;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class App
{
	public static function run($dir)
	{
		/**
		 * Environment setup
		 * Import .env variables and add them the environment
		 */

		$dotenv = Dotenv::createImmutable($dir."/");
		$dotenv->load();

		/**
		 * Enable System wide error / routing / request logs
		 */
		Logger::enableSystemLogs();

		/**
		 * Error and Exception handling
		 * et Whoops as the default error and exception handler used by PHP
		 */

		$whoops = new Run();
		$whoops->register();
		$whoops->pushHandler(new PrettyPageHandler());
////
//		error_reporting(E_ALL);
//		set_error_handler('Core\Error::errorHandler');
//		set_exception_handler('Core\Error::exceptionHandler');



	}

}
