<?php

namespace App\Controllers\Manage;

use App\Controllers\BaseController;
use App\Models\AuthorsModel;
use App\Models\BooksModel;

class Authors extends BaseController
{

    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        // Check access
        $user = auth()->user();
        if (!$user->can('manage.authors')) {
            return redirect()->to('manage');
        }

        // View params
        $params = [
            'title' => 'App.manage.authors.title',
            'return' => 'manage',
            'api' => 'api/authors',
            'mode' => 'edit',
            'detail' => 'page',
            'edit' => 'App.manage.authors.edit',
            'fields' => [
                'id' => [
                    'search' => false,
                    'col' => true,
                    'disabled' => true,
                    'helper' => 'App.helper.id',
                    'lib' => 'App.manage.authors.id',
                    'type' => 'number'
                ],
                'username' => [
                    'search' => true,
                    'col' => true,
                    'lib' => 'App.manage.authors.username',
                    'type' => 'text'
                ]
            ]
        ];

        return view('layout/crud', $params);
    }

    public function detail($id): string|\CodeIgniter\HTTP\RedirectResponse
    {
        // Check access
        $user = auth()->user();
        if (!$user->can('manage.authors')) {
            return redirect()->to('manage');
        }

        // Get author info
        $authorModel = model(AuthorsModel::class);
        $author = $authorModel->find($id);
        if ($author == null) {
            return redirect()->to('404');
        }

        // Set parameters
        $get = $this->request->getGet();
        $booksModel = model(BooksModel::class);
        $params = [
            'id' => $id,
            'return' => $get['return'] ?? 'manage/authors',
            'author' => $author['username'],
            'books' => $booksModel->getFromAuthor($id),
        ];

        return view('manage/detail/author', $params);
    }

}