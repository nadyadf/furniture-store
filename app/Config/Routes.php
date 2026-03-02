<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');
$routes->get('signin', 'Home::signin');
$routes->post('signin', 'Home::signin');

$routes->get('signup', 'Home::signup');
$routes->post('signup', 'Home::signup');
$routes->post('signup/(:segment)', 'Home::signup/$1');

$routes->post('signout', 'Home::signout');

$routes->set404Override('Home::_404');

$routes->get('kategori/(:segment)', 'Kategori::index/$1');
$routes->get('produk/(:segment)', 'Produk::index/$1');
$routes->get('page/(:segment)', 'Page::index/$1');
$routes->get('blog/(:segment)', 'Blog::single/$1');
