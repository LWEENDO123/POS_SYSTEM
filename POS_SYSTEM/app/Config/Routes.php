<?php

use App\Controllers\Product2;
use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$routes->get('/products', 'Productpage::index');
$routes->get('/products/allproducts', 'Productpage::allproducts'); 
$routes->get('/products/oneproduct', 'Productpage::oneproduct'); 
$routes->get('/products/search/(:any)', 'Productpage::search/$1'); 
$routes->get('/products/lowstock', 'Productpage::lowstock');


$routes->get('/product2', 'Product2::product2');

$routes->get('/hello', 'hellocontroller::index');

$routes->get('/studentregister', 'Studentregister::index'); 
$routes->post('/studentregister', 'Studentregister::register'); 

$routes->get('/studentlogin', 'Studentregister::loginForm'); 
$routes->post('/studentlogin', 'Studentregister::login'); 
//$routes->get('/homepage', 'Studentregister::homepage');
//$routes->get('/homepage', 'Studentregister::homeview');
//$routes->get('/homepage', 'Studentregister::showoneuser');
