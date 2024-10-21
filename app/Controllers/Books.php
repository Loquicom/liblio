<?php

namespace App\Controllers;

use App\Models\PublishersModel;

class Books extends BaseController
{

    public function index()
    {
        // Defined parameter
        $publisher = [];

        // Load publisher
        $publisherModel = model(PublishersModel::class);
        $publisherData = $publisherModel->findAll();
        foreach ($publisherData as $data) {
            $publisher[$data['id']] = $data['name'];
        }

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

        return view('books', $params);
    }

}