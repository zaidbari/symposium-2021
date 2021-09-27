<?php
/**
 * Composer
 */
require  'vendor/autoload.php';
session_start();

date_default_timezone_set('Asia/Dubai');

/**
 * Instantiating Application
 * Start logging
 * */

\App\Lib\App::run(__DIR__);

// Requiring our routes
 require_once('Routes/web.php');
