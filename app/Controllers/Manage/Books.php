<?php

namespace App\Controllers\Manage;

use App\Controllers\BaseController;

class Books extends BaseController
{

    public function index()
    {
        // Defined parameter
        $mode = 'view';
        $publisher = [];

        // Adapt parameter bases on auth
        $user = auth()->user();
        if ($user->can('manage.book.edit')) {
            $mode = 'edit';
        }

        // Load publisher

        // View params
        $params = [
            'title' => 'App.manage.book.title',
            'return' => 'config',
            'api' => 'api/books',
            'mode' => $mode,
            'detail' => 'page',
            'edit' => 'App.manage.book.edit',
            'fields' => [
                'isbn' => [
                    'search' => true,
                    'col' => true,
                    'lib' => 'App.manage.book.isbn',
                    'type' => 'text'
                ],
                'title' => [
                    'search' => true,
                    'col' => true,
                    'lib' => 'App.manage.book.title',
                    'type' => 'text'
                ],
                'publisher' => [
                    'search' => true,
                    'col' => true,
                    'lib' => 'App.manage.book.publisher',
                    'type' => $publisher
                ],
                'collection' => [
                    'search' => true,
                    'col' => true,
                    'lib' => 'App.manage.book.collection',
                    'type' => 'text'
                ],
            ]
        ];

        return view('layout/crud', $params);
    }

}