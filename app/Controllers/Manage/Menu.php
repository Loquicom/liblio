<?php

namespace App\Controllers\Manage;

use App\Controllers\BaseController;

class Menu extends BaseController
{

    public function index(): string
    {
        $params = [
            'title' => 'App.manage.title',
            'menus' => []
        ];

        // Get current user
        $user = auth()->user();
        // Can manage books
        if ($user->can('manage.books')) {
            $params['menus'][] = [
                'icon' => 'book-open-variant',
                'name' => 'App.manage.books.card',
                'link' => url_to('manage/books')
            ];
        }
        // Can manage members
        if ($user->can('manage.members')) {
            $params['menus'][] = [
                'icon' => 'book-account',
                'name' => 'App.manage.members.card',
                'link' => url_to('manage/members')
            ];
        }
        // Can manage borrow
        if ($user->can('manage.borrow')) {
            $params['menus'][] = [
                'icon' => 'inbox-arrow-up',
                'name' => 'App.manage.borrow.card',
                'link' => url_to('manage/borrow')
            ];
        }
        // Can manage return
        if ($user->can('manage.return')) {
            $params['menus'][] = [
                'icon' => 'inbox-arrow-down',
                'name' => 'App.manage.return.card',
                'link' => url_to('manage/return')
            ];
        }
        // Can manage authors
        if ($user->can('manage.authors')) {
            $params['menus'][] = [
                'icon' => 'account-edit',
                'name' => 'App.manage.authors.card',
                'link' => url_to('manage/authors')
            ];
        }

        return view('layout/menu', $params);
    }

}