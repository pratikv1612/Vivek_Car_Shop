<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'home';
$route['404_override'] = 'errors/page_missing';
$route['translate_uri_dashes'] = TRUE;

// Admin routes
$route['admin'] = 'admin/auth/login';
$route['admin/login'] = 'admin/auth/login';
$route['admin/logout'] = 'admin/auth/logout';
$route['admin/dashboard'] = 'admin/dashboard/index';
$route['admin/banners/create'] = 'admin/banners/create';
$route['admin/banners/store'] = 'admin/banners/store';
$route['admin/banners/edit/(:num)'] = 'admin/banners/edit/$1';
$route['admin/banners/update/(:num)'] = 'admin/banners/update/$1';
$route['admin/banners/delete/(:num)'] = 'admin/banners/delete/$1';
$route['admin/banners'] = 'admin/banners/index';
$route['admin/products/create'] = 'admin/products/create';
$route['admin/products/store'] = 'admin/products/store';
$route['admin/products/edit/(:num)'] = 'admin/products/edit/$1';
$route['admin/products/update/(:num)'] = 'admin/products/update/$1';
$route['admin/products/delete/(:num)'] = 'admin/products/delete/$1';
$route['admin/products/delete-image/(:num)'] = 'admin/products/delete_image/$1';
$route['admin/products/index/(:num)'] = 'admin/products/index/$1';
$route['admin/products'] = 'admin/products/index';

// User auth
$route['login'] = 'auth/login';
$route['register'] = 'auth/register';
$route['logout'] = 'auth/logout';
$route['account'] = 'auth/account';
$route['about'] = 'pages/about';
$route['contact'] = 'pages/contact';
$route['contact/send'] = 'pages/send_contact';
$route['cart'] = 'shop/cart';
$route['wishlist'] = 'shop/wishlist';
$route['compare'] = 'shop/compare';
$route['checkout'] = 'shop/checkout';
$route['shop/add/(:any)'] = 'shop/add/$1';
$route['shop/remove/(:any)'] = 'shop/remove/$1';
$route['shop/quantity'] = 'shop/quantity';
$route['shop/summary'] = 'shop/summary';

// Accessories
$route['accessories'] = 'accessories/index';
$route['accessories/(:num)'] = 'accessories/index/$1';
$route['new-arrivals'] = 'accessories/new_arrivals';
$route['new-arrivals/(:num)'] = 'accessories/new_arrivals/$1';
$route['accessory/(:num)/(:any)'] = 'accessories/detail/$1/$2';

// Cars
$route['cars'] = 'cars/index';
$route['cars/(:num)'] = 'cars/index/$1';
$route['car/(:num)/(:any)'] = 'cars/detail/$1/$2';


// ================= ADMIN (MUST BE ABOVE GENERIC) =================
$route['admin/cars/models-by-brand'] = 'admin/cars/models_by_brand';

$route['admin/cars/create'] = 'admin/cars/create';
$route['admin/cars/store'] = 'admin/cars/store';

$route['admin/cars/edit/(:num)'] = 'admin/cars/edit/$1';
$route['admin/cars/update/(:num)'] = 'admin/cars/update/$1';

$route['admin/cars/delete/(:num)'] = 'admin/cars/delete/$1';
$route['admin/cars/delete-image/(:num)'] = 'admin/cars/delete_image/$1';

$route['admin/cars/index/(:num)'] = 'admin/cars/index/$1';
$route['admin/cars'] = 'admin/cars/index';

$route['admin/accessories/create'] = 'admin/accessories/create';
$route['admin/accessories/store'] = 'admin/accessories/store';
$route['admin/accessories/edit/(:num)'] = 'admin/accessories/edit/$1';
$route['admin/accessories/update/(:num)'] = 'admin/accessories/update/$1';
$route['admin/accessories/delete/(:num)'] = 'admin/accessories/delete/$1';
$route['admin/accessories/delete-image/(:num)'] = 'admin/accessories/delete_image/$1';

$route['admin/accessories/index/(:num)'] = 'admin/accessories/index/$1';
$route['admin/accessories'] = 'admin/accessories/index';
        
