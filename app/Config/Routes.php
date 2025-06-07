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

$routes->get('/', [App\Controllers\Home\Home::class, 'index']);
$routes->get('books', [App\Controllers\Home\Books::class, 'index']);
$routes->get('members/auth', [App\Controllers\Home\Members::class, 'auth']);
$routes->post('members/auth', [App\Controllers\Home\Members::class, 'authAction'], ['filter' => 'csrf']);
$routes->get('members/detail', [App\Controllers\Home\Members::class, 'detail']);
$routes->view('legal', 'home/legal');

$routes->get('preference', [\App\Controllers\Config\Preference::class, 'index']);
$routes->get('account', [\App\Controllers\Shield\Account::class, 'index'],  ['filter' => 'session']);
$routes->post('account', [\App\Controllers\Shield\Account::class, 'update'],  ['filter' => ['session', 'csrf']]);

$routes->group('config', ['filter' => 'session'], function ($routes) {
    $routes->get('/', [\App\Controllers\Config\Menu::class, 'index']);
    $routes->get('users', [\App\Controllers\Config\Users::class, 'index']);
    $routes->get('publishers', [\App\Controllers\Config\Publishers::class, 'index']);
    $routes->get('website', 'WIP::index');
    $routes->get('export', 'WIP::index');
});

$routes->group('manage', ['filter' => 'session'], function ($routes) {
    $routes->get('/', [\App\Controllers\Manage\Menu::class, 'index']);
    $routes->get('books', [\App\Controllers\Manage\Books::class, 'index']);
    $routes->get('books/(:any)', [\App\Controllers\Manage\Books::class, 'detail']);
    $routes->get('members', [\App\Controllers\Manage\Members::class, 'index']);
    $routes->get('members/(:alphanum)', [\App\Controllers\Manage\Members::class, 'detail']);
    $routes->get('borrow', [\App\Controllers\Manage\Borrow::class, 'out']);
    $routes->get('return', [\App\Controllers\Manage\Borrow::class, 'in']);
    $routes->get('authors', [\App\Controllers\Manage\Authors::class, 'index']);
    $routes->get('authors/(:num)', [\App\Controllers\Manage\Authors::class, 'detail']);
    $routes->get('alerts', [\App\Controllers\Manage\Alerts::class, 'index']);
});

// API without authentication
$routes->group('api', static function ($routes) {
    $routes->get('login', [\App\Controllers\Api\LoginAPI::class, 'sessionLogin'],  ['filter' => 'session']);
    $routes->post('login', [\App\Controllers\Api\LoginAPI::class, 'credentialsLogin']);

    $routes->get('books', [\App\Controllers\Api\BooksAPI::class, 'search']);
    $routes->get('books/(:any)/authors', [\App\Controllers\Api\BooksAPI::class, 'getAuthors']);

    $routes->get('authors/(:num)', [\App\Controllers\Api\AuthorsAPI::class, 'read']);
    $routes->get('authors/search', [\App\Controllers\Api\AuthorsAPI::class, 'search']);

    $routes->get('publishers/(:num)', [\App\Controllers\Api\PublishersAPI::class, 'read']);
    $routes->get('publishers/search', [\App\Controllers\Api\PublishersAPI::class, 'search']);
});
// API with authentication
$routes->group('api', ['filter' => 'jwt'], static function ($routes) {
    $routes->get('users', [\App\Controllers\Api\UsersAPI::class, 'search']);
    $routes->post('users', [\App\Controllers\Api\UsersAPI::class, 'create']);
    $routes->put('users/(:num)', [\App\Controllers\Api\UsersAPI::class, 'update']);
    $routes->delete('users/(:num)', [\App\Controllers\Api\UsersAPI::class, 'delete']);

    $routes->get('publishers', [\App\Controllers\Api\PublishersAPI::class, 'pagedSearch']);
    $routes->post('publishers', [\App\Controllers\Api\PublishersAPI::class, 'create']);
    $routes->put('publishers/(:num)', [\App\Controllers\Api\PublishersAPI::class, 'update']);
    $routes->delete('publishers/(:num)', [\App\Controllers\Api\PublishersAPI::class, 'delete']);

    $routes->get('books/(:any)', [\App\Controllers\Api\BooksAPI::class, 'read']);
    $routes->post('books/(:any)/authors/(:num)', [\App\Controllers\Api\BooksAPI::class, 'addAuthor']);
    $routes->post('books', [\App\Controllers\Api\BooksAPI::class, 'create']);
    $routes->put('books/(:any)', [\App\Controllers\Api\BooksAPI::class, 'update']);
    $routes->delete('books/(:any)/authors/(:num)', [\App\Controllers\Api\BooksAPI::class, 'deleteAuthor']);
    $routes->delete('books/(:any)', [\App\Controllers\Api\BooksAPI::class, 'delete']);

    $routes->get('authors', [\App\Controllers\Api\AuthorsAPI::class, 'pagedSearch']);
    $routes->post('authors', [\App\Controllers\Api\AuthorsAPI::class, 'create']);
    $routes->put('authors/(:num)', [\App\Controllers\Api\AuthorsAPI::class, 'update']);
    $routes->delete('authors/(:num)', [\App\Controllers\Api\AuthorsAPI::class, 'delete']);

    $routes->get('members/(:alphanum)', [\App\Controllers\Api\MembersAPI::class, 'read']);
    $routes->get('members', [\App\Controllers\Api\MembersAPI::class, 'search']);
    $routes->post('members', [\App\Controllers\Api\MembersAPI::class, 'create']);
    $routes->put('members/(:alphanum)', [\App\Controllers\Api\MembersAPI::class, 'update']);
    $routes->delete('members/(:alphanum)', [\App\Controllers\Api\MembersAPI::class, 'delete']);
    $routes->get('members/(:alphanum)/borrow', [\App\Controllers\Api\MembersAPI::class, 'listBorrowBooks']);
    $routes->post('members/(:alphanum)/borrow', [\App\Controllers\Api\MembersAPI::class, 'borrowBooks']);
    $routes->post('members/(:alphanum)/return', [\App\Controllers\Api\MembersAPI::class, 'returnBooks']);
});