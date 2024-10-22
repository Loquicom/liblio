<?php

namespace App\Controllers\Config;

use App\Controllers\BaseController;

class Menu extends BaseController
{

    public function index(): string
    {
        $params = [
            'title' => 'App.config.title',
            'return' => 'manage',
            'menus' => [
                [
                    'icon' => 'account-cog',
                    'name' => 'App.config.preference.title',
                    'link' => url_to('preference')
                ]
            ]
        ];

        // Get current user
        $user = auth()->user();
        // Can manage publisher
        if ($user->can('config.publisher')) {
            $params['menus'][] = [
                'icon' => 'briefcase-variant',
                'name' => 'App.config.publishers.card',
                'link' => url_to('config/publishers')
            ];
        }
        // Can manage accounts
        if ($user->can('config.users.view')) {
            $params['menus'][] = [
                'icon' => 'account-group',
                'name' => 'App.config.users.card',
                'link' => url_to('config/users')
            ];
        }
        // Can manage website conf
        if ($user->can('config.website', 'beta.access')) {
            $params['menus'][] = [
                'icon' => 'web',
                'name' => 'App.config.website',
                'link' => url_to('config/website')
            ];
        }

        return view('layout/menu', $params);
    }

}