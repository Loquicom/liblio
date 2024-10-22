<?php

namespace App\Controllers;

use App\Models\AuthorsModel;
use App\Models\PublishersModel;

class Books extends BaseController
{

    public function index(): string
    {
        // Defined parameter
        $publisher = [];
        $author = [];

        // Load publisher
        $publisherModel = model(PublishersModel::class);
        $publisherData = $publisherModel->findAll();
        foreach ($publisherData as $data) {
            $publisher[$data['id']] = $data['name'];
        }

        // Load author
        $authorModel = model(AuthorsModel::class);
        $authorData = $authorModel->findAll();
        foreach ($authorData as $data) {
            $author[$data['id']] = $data['username'];
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
                'author' => [
                    'search' => true,
                    'col' => true,
                    'lib' => 'App.manage.books.author',
                    'type' => $author
                ],
                'publisher' => [
                    'search' => true,
                    'col' => true,
                    'lib' => 'App.manage.books.publisher',
                    'type' => $publisher
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

        return view('books', $params);
    }

}