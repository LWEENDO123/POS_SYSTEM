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
$routes->get('logout','AuthController::logout');

// --- Dashboard ---
$routes->get('DashBoard/index', 'DashBoard::index');
$routes->get('DashBoard/sales', 'DashBoard::newsaledashboard');
$routes->post('DashBoard/add_to_cart', 'DashBoard::add_to_cart');
$routes->post('DashBoard/checkout', 'DashBoard::checkout');
$routes->post('DashBoard/clear_cart', 'DashBoard::clear_cart');

// --- Product controller ---
$routes->get('productcontroller', 'Productcontroller::search');

// --- Sales (Sale history) ---
$routes->get('sales', 'Sales::saleshomepage');
$routes->get('sales/status', 'Sales::salesstatus');
$routes->get('sales/search', 'Sales::search');
$routes->get('sales/searchfilter', 'Sales::searchfilter');

// --- Sidebar placeholder routes (prevent 404s) ---
$routes->get('products', 'DashBoard::index');
$routes->get('customers', 'DashBoard::index');
$routes->get('cashiers', 'DashBoard::index');

// --- Newsales (New Sale dashboard) ---
$routes->get('newsales', 'Newsales::Newsale');
$routes->get('newsales/display_products', 'Newsales::display_products');
$routes->get('newsales/change_customer', 'Newsales::change_customer');
$routes->get('newsales/search_product', 'Newsales::search_product');
$routes->get('newsales/view', 'Newsales::view');
$routes->get('newsales/cart', 'Newsales::cart');
$routes->post('newsales/cart', 'Newsales::cart');
$routes->post('newsales/update_qty', 'Newsales::update_qty');
$routes->post('newsales/remove/(:num)', 'Newsales::remove/$1');
$routes->post('newsales/remove', 'Newsales::remove');
$routes->post('newsales/clear', 'Newsales::clear');
$routes->post('newsales/checkout', 'Newsales::checkout');

