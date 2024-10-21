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

$routes->get('config', [\App\Controllers\Config\Menu::class, 'index'],  ['filter' => 'session']);
$routes->get('config/users', [\App\Controllers\Config\Users::class, 'index'],  ['filter' => 'session']);
$routes->get('config/website', 'Home::index',  ['filter' => 'session']);

$routes->get('manage', [\App\Controllers\Manage\Menu::class, 'index'],  ['filter' => 'session']);
$routes->get('manage/books', 'Home::index');
$routes->get('manage/members', 'Home::index',  ['filter' => 'session']);
$routes->get('manage/borrow', 'Home::index',  ['filter' => 'session']);
$routes->get('manage/return', 'Home::index',  ['filter' => 'session']);

$routes->get('account', [\App\Controllers\Shield\Account::class, 'index'],  ['filter' => 'session']);
$routes->post('account', [\App\Controllers\Shield\Account::class, 'update'],  ['filter' => ['session', 'csrf']]);

$routes->get('member/history', 'Home::index');

$routes->get('api/login', [\App\Controllers\Api\Login::class, 'sessionLogin'],  ['filter' => 'session']);
$routes->post('api/login', [\App\Controllers\Api\Login::class, 'credentialsLogin']);
$routes->group('api', ['filter' => 'jwt'], static function ($routes) {
    $routes->get('users', [\App\Controllers\Api\Users::class, 'search']);
    $routes->post('users', [\App\Controllers\Api\Users::class, 'create']);
    $routes->put('users/(:num)', [\App\Controllers\Api\Users::class, 'update']);
    $routes->delete('users/(:num)', [\App\Controllers\Api\Users::class, 'delete']);
});