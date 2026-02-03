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

$routes->get('mein-konto', 'AccountController::index');
$routes->get('meine-buchungen', 'AccountController::bookings');
$routes->post('meine-buchungen/storno', 'AccountController::cancelBooking');

$routes->get('mitarbeiter', 'MitarbeiterController::index');
$routes->get('mitarbeiter/buchung', 'MitarbeiterController::bookingForm');
$routes->post('mitarbeiter/buchung', 'MitarbeiterController::createBooking');
$routes->post('mitarbeiter/buchungen/storno', 'MitarbeiterController::cancelBooking');
$routes->post('mitarbeiter/status', 'MitarbeiterController::updateStatus');
$routes->post('mitarbeiter/boote/anlegen', 'MitarbeiterController::createBoat');

$routes->post('buchung/auswahl', 'BookingController::select');
$routes->get('buchung/weiter', 'BookingController::next');
$routes->post('buchung/reset', 'BookingController::reset');
$routes->post('buchung/filter', 'BookingController::filter');
$routes->post('buchung/liegeplatz-toggle', 'BookingController::toggleLiegeplatz');
$routes->get('buchung/zusammenfassung', 'BookingController::summary');
$routes->post('buchung/abschliessen', 'BookingController::finish');

$routes->post('buchung/boot-toggle', 'BookingController::toggleBoot');
$routes->post('buchung/boot-filter', 'BookingController::bootFilter');
