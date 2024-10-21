<?php

namespace App\Controllers\Manage;

use App\Controllers\BaseController;
use App\Models\PublishersModel;

class Books extends BaseController
{

    public function index(): string
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
        $publisherModel = model(PublishersModel::class);
        $publisherData = $publisherModel->findAll();
        foreach ($publisherData as $data) {
            $publisher[$data['id']] = $data['name'];
        }

        // View params
        $params = [
            'title' => 'App.manage.books.title',
            'return' => 'manage',
            'api' => 'api/books',
            'mode' => $mode,
            'detail' => 'page',
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
                'publisher' => [
                    'search' => true,
                    'col' => true,
                    'lib' => 'App.manage.books.publisher',
                    'type' => $publisher
                ],
                'collection' => [
                    'search' => true,
                    'col' => true,
                    'lib' => 'App.manage.books.collection',
                    'type' => 'text'
                ],
            ]
        ];

        return view('layout/crud', $params);
    }

}