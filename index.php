<?php
/**
 * Composer
 */
require  'vendor/autoload.php';
session_start();

/**
 * Instantiating Application
 * Start logging
 * */

\App\Lib\App::run(__DIR__);

// Requiring our routes
 require_once('Routes/web.php');
