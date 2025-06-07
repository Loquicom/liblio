<?php

namespace App\Controllers\Home;

use App\Controllers\BaseController;
use App\Models\AuthorsModel;
use App\Models\PublishersModel;

class Books extends BaseController
{

    public function index(): string
    {
        // Defined parameter
        $publisher = [];
        $author = [];

        // View params
        $params = [
            'title' => 'App.common.books',
            'return' => '/',
            'api' => 'api/books',
            'mode' => 'view',
            'detail' => 'popup',
            'edit' => 'App.manage.books.edit',
            'fields' => [
                'isbn' => [
                    'search' => true,
                    'col' => true,
                    'lib' => 'App.manage.books.isbn',
                    'type' => 'text'
                ],
                'title' => [
                    'search' => true,
                    'col' => true,
                    'lib' => 'App.common.title',
                    'type' => 'text'
                ],
                'author' => [
                    'search' => true,
                    'col' => true,
                    'lib' => 'App.manage.books.author',
                    'type' => 'autocomplete:username:api/authors'
                ],
                'publisher' => [
                    'search' => true,
                    'col' => true,
                    'lib' => 'App.manage.books.publisher',
                    'type' => 'autocomplete:name:api/publishers'
                ],
                'theme' => [
                    'search' => true,
                    'col' => true,
                    'lib' => 'App.manage.books.theme',
                    'type' => 'text'
                ],
                'year' => [
                    'search' => true,
                    'col' => true,
                    'lib' => 'App.manage.books.year',
                    'type' => 'number'
                ]
            ]
        ];

        return view('home/books', $params);
    }

}