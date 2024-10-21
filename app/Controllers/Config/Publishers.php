<?php

namespace App\Controllers\Config;

use App\Controllers\BaseController;

class Publishers extends BaseController
{

    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        // Check access
        $user = auth()->user();
        if (!$user->can('config.publisher')) {
            return redirect()->to('config');
        }

        // View params
        $params = [
            'title' => 'App.config.publishers.title',
            'return' => 'config',
            'api' => 'api/publishers',
            'mode' => 'edit',
            'detail' => 'none',
            'edit' => 'App.config.publishers.edit',
            'fields' => [
                'id' => [
                    'search' => false,
                    'col' => true,
                    'disabled' => true,
                    'helper' => 'App.config.publishers.helper.id',
                    'lib' => 'App.config.publishers.id',
                    'type' => 'number'
                ],
                'name' => [
                    'search' => true,
                    'col' => true,
                    'lib' => 'App.config.publishers.name',
                    'type' => 'text'
                ]
            ]
        ];

        return view('layout/crud', $params);
    }

}