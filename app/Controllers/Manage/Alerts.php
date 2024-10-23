<?php

namespace App\Controllers\Manage;

use App\Controllers\BaseController;
use App\Models\BorrowModel;

class Alerts extends BaseController
{

    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        // Check access
        $user = auth()->user();
        if (!$user->can('manage.alerts')) {
            return redirect()->to('config');
        }

        $alerts = [];

        $borrowModel = model(BorrowModel::class);
        $alerts['overdue'] = $borrowModel->getOverdue();

        return view('manage/alerts', ['alerts' => $alerts]);
    }

}