<?php

use App\Controllers\News;
use App\Controllers\Pages;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
service('auth')->routes($routes, ['except' => ['login']]);
$routes->get('login', [\CodeIgniter\Shield\Controllers\LoginController::class, 'loginView']);
$routes->post('login', [\CodeIgniter\Shield\Controllers\LoginController::class, 'loginAction'], ['filter' => 'csrf']);

$routes->get('/', 'Home::index');

$routes->get('preference', [\App\Controllers\Config\Preference::class, 'index']);

$routes->group('config', ['filter' => 'session'], function ($routes) {
    $routes->get('/', [\App\Controllers\Config\Menu::class, 'index']);
    $routes->get('users', [\App\Controllers\Config\Users::class, 'index']);
    $routes->get('website', 'Home::index');
    $routes->get('publishers', [\App\Controllers\Config\Publishers::class, 'index']);
});

$routes->group('manage', ['filter' => 'session'], function ($routes) {
    $routes->get('/', [\App\Controllers\Manage\Menu::class, 'index']);
    $routes->get('books', [\App\Controllers\Manage\Books::class, 'index']);
    $routes->get('members', 'Home::index');
    $routes->get('borrow', 'Home::index');
    $routes->get('return', 'Home::index');
});

$routes->get('account', [\App\Controllers\Shield\Account::class, 'index'],  ['filter' => 'session']);
$routes->post('account', [\App\Controllers\Shield\Account::class, 'update'],  ['filter' => ['session', 'csrf']]);

$routes->get('member/history', 'Home::index');

$routes->get('api/login', [\App\Controllers\Api\LoginAPI::class, 'sessionLogin'],  ['filter' => 'session']);
$routes->post('api/login', [\App\Controllers\Api\LoginAPI::class, 'credentialsLogin']);
$routes->group('api', ['filter' => 'jwt'], static function ($routes) {
    $routes->get('users', [\App\Controllers\Api\UsersAPI::class, 'search']);
    $routes->post('users', [\App\Controllers\Api\UsersAPI::class, 'create']);
    $routes->put('users/(:num)', [\App\Controllers\Api\UsersAPI::class, 'update']);
    $routes->delete('users/(:num)', [\App\Controllers\Api\UsersAPI::class, 'delete']);

    $routes->get('publishers', [\App\Controllers\Api\PublishersAPI::class, 'search']);
    $routes->post('publishers', [\App\Controllers\Api\PublishersAPI::class, 'create']);
    $routes->put('publishers/(:num)', [\App\Controllers\Api\PublishersAPI::class, 'update']);
    $routes->delete('publishers/(:num)', [\App\Controllers\Api\PublishersAPI::class, 'delete']);

    $routes->get('books', [\App\Controllers\Api\BooksAPI::class, 'search']);
    $routes->post('books', [\App\Controllers\Api\BooksAPI::class, 'create']);
    $routes->put('books/(:any)', [\App\Controllers\Api\BooksAPI::class, 'update']);
    $routes->delete('books/(:any)', [\App\Controllers\Api\BooksAPI::class, 'delete']);
});