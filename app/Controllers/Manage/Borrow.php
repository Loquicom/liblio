<?php

namespace App\Controllers\Manage;

use App\Controllers\BaseController;

class Borrow extends BaseController
{

    public function in(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        // Check access
        $user = auth()->user();
        if (!$user->can('manage.return')) {
            return redirect()->to('manage');
        }
        return 'Salut';
    }

    public function out(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        // Check access
        $user = auth()->user();
        if (!$user->can('manage.borrow')) {
            return redirect()->to('manage');
        }
        return view('manage/borrow');
    }

}