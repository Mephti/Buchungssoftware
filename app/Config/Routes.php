<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'HomeController::index');
$routes->get('db-test', 'DevTestController::dbTest');
$routes->get('kunden-test', 'DevTestController::kundenTest');

$routes->get('login', 'AuthController::loginForm');
$routes->post('login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');

$routes->get('register', 'AuthController::registerForm');
$routes->post('register', 'AuthController::register');
