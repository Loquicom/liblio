<?php

namespace App\Controllers\Config;

use App\Controllers\BaseController;

class Users extends BaseController
{

    public function index(): string
    {
        // TODO gérer les droits

        $params = [
            'title' => 'App.config.users.title',
            'return' => 'config',
            'api' => 'api/users',
            'mode' => 'edit',
            'edit' => 'App.config.users.edit',
            'fields' => [
                'username' => [
                    'search' => true,
                    'col' => true,
                    'lib' => 'App.config.users.username',
                    'type' => 'text'
                ],
                'email' => [
                    'search' => true,
                    'col' => true,
                    'lib' => 'App.config.users.email',
                    'type' => 'email'
                ],
                'role' => [
                    'search' => true,
                    'col' => true,
                    'lib' => 'App.config.users.role',
                    'type' => [
                        'superadmin' => 'App.role.superAdmin',
                        'admin' => 'App.role.admin',
                        'manager' => 'App.role.manager',
                    ]
                ],
                'password' => [
                    'search' => false,
                    'col' => false,
                    'lib' => 'App.config.users.password',
                    'type' => 'password'
                ]
            ]
        ];

        return view('layout/crud', $params);
    }

}