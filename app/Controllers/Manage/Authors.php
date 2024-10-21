<?php

namespace App\Controllers\Manage;

use App\Controllers\BaseController;

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

}