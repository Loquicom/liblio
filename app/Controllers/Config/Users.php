<?php

namespace App\Controllers\Config;

use App\Controllers\BaseController;

class Users extends BaseController
{

    public function index(): string
    {
        // Defined parameter
        $mode = 'view';
        $typeRole = [
            'manager' => 'App.role.manager',
        ];

        // Adapt parameter bases on auth
        $user = auth()->user();
        if ($user->can('config.users.edit')) {
            $mode = 'edit';
        }
        if ($user->can('config.users.admin')) {
            $typeRole['admin'] = 'App.role.admin';
            $typeRole['superadmin'] = 'App.role.superadmin';
        }

        $params = [
            'title' => 'App.config.users.title',
            'return' => 'config',
            'api' => 'api/users',
            'mode' => $mode,
            'detail' => 'none',
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
                    'type' => $typeRole
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