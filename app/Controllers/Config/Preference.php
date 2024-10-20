<?php

namespace App\Controllers\Config;

use App\Controllers\BaseController;

class Preference extends BaseController
{

    public function index() {
        return view('config/preference');
    }

}