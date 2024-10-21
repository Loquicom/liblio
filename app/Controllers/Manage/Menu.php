<?php

namespace App\Controllers\Manage;

use App\Controllers\BaseController;

class Menu extends BaseController
{

    public function index(): string
    {
        $params = [
            'title' => 'App.manage.title',
            'menus' => [
                [
                    'icon' => 'book-open-variant',
                    'name' => 'App.manage.books.card',
                    'link' => url_to('manage/books')
                ],
                [
                    'icon' => 'book-account',
                    'name' => 'App.manage.member',
                    'link' => url_to('manage/members')
                ],
                [
                    'icon' => 'inbox-arrow-up',
                    'name' => 'App.manage.borrow',
                    'link' => url_to('manage/borrow')
                ],
                [
                    'icon' => 'inbox-arrow-down',
                    'name' => 'App.manage.return',
                    'link' => url_to('manage/return')
                ],
                [
                    'icon' => 'account-edit',
                    'name' => 'App.manage.authors.card',
                    'link' => url_to('manage/authors')
                ],
            ]
        ];

        return view('layout/menu', $params);
    }

}