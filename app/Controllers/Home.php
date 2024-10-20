<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if(auth()->loggedIn()) {
            return redirect()->to('manage');
        } else {
            return view('home');
        }
    }
}
