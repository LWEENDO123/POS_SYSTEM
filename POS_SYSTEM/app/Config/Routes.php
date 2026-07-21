<?php

use App\Controllers\CreateAccount;
use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// --- Account creation ---
$routes->get('create-account', 'CreateAccount::user_page');
$routes->post('create-account', 'CreateAccount::user_registration');

// --- Login ---
$routes->get('userlogin', 'AuthController::loginpage');
$routes->post('userlogin', 'AuthController::userlogin');

// --- Dashboard ---
$routes->get('DashBoard/index', 'DashBoard::index');
$routes->get('DashBoard/sales', 'DashBoard::newsaledashboard');
$routes->post('DashBoard/add_to_cart', 'DashBoard::add_to_cart');
$routes->post('DashBoard/checkout', 'DashBoard::checkout');
$routes->post('DashBoard/clear_cart', 'DashBoard::clear_cart');

// --- Product controller ---
$routes->get('productcontroller', 'Productcontroller::search');

// --- Sidebar placeholder routes (prevent 404s) ---
$routes->get('sales', 'DashBoard::index');
$routes->get('products', 'DashBoard::index');
$routes->get('customers', 'DashBoard::index');
$routes->get('cashiers', 'DashBoard::index');
