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
$routes->get('DashBoard/sales', 'DashBoard::Newsaledashboard');
$routes->post('DashBoard/add_to_cart', 'DashBoard::add_to_cart');
$routes->post('DashBoard/checkout', 'DashBoard::checkout');
$routes->post('DashBoard/clear_cart', 'DashBoard::clear_cart');

// --- Product controller ---
$routes->get('productcontroller', 'ProductController::showProducts');
$routes->get('product/search_product', 'ProductController::searchProducts');
$routes->get('product/categories_navbar', 'ProductController::filterByCategory');

// --- Sales (Sale history) ---
$routes->get('sales', 'Sales::saleshomepage');
$routes->get('sales/status', 'Sales::salesstatus');
$routes->get('sales/search', 'Sales::search');
$routes->get('sales/searchfilter', 'Sales::searchfilter');

// --- Sidebar placeholder routes (prevent 404s) ---
$routes->get('products', 'DashBoard::index');
$routes->get('cashiers', 'DashBoard::index');

// --- Newsales (New Sale dashboard) ---
$routes->get('newsales', 'NewSalesController::showNewSale');
$routes->get('newsales/display_products', 'NewSalesController::displayProducts');
$routes->get('newsales/search_product', 'NewSalesController::searchProducts');
$routes->post('newsales/cart', 'NewSalesController::addToCart');
$routes->post('newsales/update_qty', 'NewSalesController::updateQuantity');
$routes->post('newsales/remove/(:num)', 'NewSalesController::removeFromCart/$1');
$routes->post('newsales/remove', 'NewSalesController::removeFromCart');
$routes->post('newsales/clear', 'NewSalesController::clearCart');
$routes->post('newsales/checkout', 'NewSalesController::checkout');
$routes->post('newsales/payment', 'NewSalesController::processPayment');
$routes->get('newsales/receipt/(:num)', 'NewSalesController::showReceipt/$1');
$routes->get('newsales/receipts', 'NewSalesController::listReceipts');
$routes->get('newsales/force_clear_pending', 'NewSalesController::forceClearPending');

// --- Receipts (Receipt controller - index + search by receipt number) ---
$routes->get('receipts', 'Receipt::index');
$routes->get('receipts/search', 'Receipt::search');
$routes->get('receipts/view/(:num)', 'Receipt::view_receipt/$1');   // the View buttons
$routes->post('receipts/view_receipt', 'Receipt::view_receipt');    // POST compat (was pointing at the MODEL - fixed)
// FIX (404): the Dashboard's old "View" links pointed at sales/view/{id},

// --- Cron Job ---
$routes->get('cron/check', 'Cron::checkPaymentTransactionPending');

// CLI cron routes so the job can be run from the command line, e.g.:
//   php public/index.php cron checkPayments
//   php public/index.php cron checkPaymentTransactionPending
//   php public/index.php cron markbypending
$routes->cli('cron/checkPayments', 'Cron::checkPaymentTransactionPending');
$routes->cli('cron/checkPaymentTransactionPending', 'Cron::checkPaymentTransactionPending');
$routes->cli('cron/markbypending', 'Cron::markbypending');

// FIX (404): the Dashboard's old "View" links pointed at sales/view/{id},
// which had NO route - every click was a 404. This fallback renders the
// same receipt; the Dashboard now opens it in a popup instead.
$routes->get('sales/view/(:num)', 'Receipt::view_receipt/$1');
