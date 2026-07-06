<?php

use App\Controllers\CreateAccount;
use App\Controllers\Product2;
use App\Controllers\Products;
use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// --- Account creation ---
$routes->get('create-account', 'CreateAccount::user_page');          // show form
$routes->post('create-account', 'CreateAccount::user_registration'); // handle form submission

// --- Login ---
$routes->get('userlogin', 'AuthController::loginpage');   // show login form
$routes->post('userlogin', 'AuthController::userlogin');
$routes->get('DashBoard/index', 'DashBoard::index'); // prevent 404 after login

$routes->get('Productcontroller','Productcontroller::producthome');
