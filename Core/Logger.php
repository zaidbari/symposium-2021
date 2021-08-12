<?php namespace Core;

use Monolog\ErrorHandler;
use Monolog\Formatter\HtmlFormatter;
use Monolog\Formatter\JsonFormatter;
use Monolog\Formatter\NormalizerFormatter;
use Monolog\Handler\StreamHandler;


class Logger extends \Monolog\Logger
{
	private static array $loggers = [];

	/**
	 * Logger constructor.
	 *
	 * @param string $key
	 * @param null   $config
	 */
	public function __construct( $key = "app", $config = null )
	{
		parent::__construct($key);
		if ( empty($config) ) {
			$LOG_PATH = './logs';
			$config = [
				'logFile' => "{$LOG_PATH}/{$key}.log",
				'logLevel' => \Monolog\Logger::DEBUG
			];
		}
		try {
			$this->pushHandler(new StreamHandler($config['logFile'], $config['logLevel']));
		} catch (\Exception $e) {
			echo $e;
		}
	}

	/**
	 * @param string $key
	 * @param null   $config
	 *
	 * @return Logger
	 */
	public static function getInstance( $key = "app", $config = null ) : Logger
	{
		if ( empty(self::$loggers[ $key ]) ) self::$loggers[ $key ] = new Logger($key, $config);
		return self::$loggers[ $key ];
	}

	public static function enableSystemLogs()
	{

		$LOG_PATH = './logs';

		// Error Log
		self::$loggers['error'] = new Logger('errors');
		self::$loggers['error']->pushHandler((new StreamHandler("{$LOG_PATH}/errors.log"))->setFormatter(new HtmlFormatter()));
		ErrorHandler::register(self::$loggers['error']);

		// Request Log
		$data = [
			"Requested url: " . $_SERVER["REQUEST_URI"],
			$_REQUEST,
			trim(file_get_contents("php://input"))
		];
		self::$loggers['request'] = new Logger('request');
		self::$loggers['request']->pushHandler((new StreamHandler("{$LOG_PATH}/request.log"))->setFormatter(new HtmlFormatter()));
		self::$loggers['request']->debug("REQUEST", $data);
	}

	public static function crit(string $msg,array $data = []) {
		self::getInstance()->critical($msg, $data);
	}
}
