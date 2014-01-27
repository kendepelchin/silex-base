<?php
/**
 * Bootstrapping
 *
 */
require_once __DIR__ . '/../app/bootstrap.php';

/**
 * Namespaces
 *
 */
use Controllers\HomeController;
use Controllers\UserController;

/**
 * Routing
 *
 */

// home
$app->mount('/', new HomeController());

// user
$app->mount('/user', new UserController());

/**
 * Run
 *
 */
$app->run();
