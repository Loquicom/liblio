<?php

namespace App\Controllers\Manage;

use App\Controllers\BaseController;
use App\Models\BorrowModel;
use App\Models\MembersModel;

class Members extends BaseController
{

    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        // Check access
        $user = auth()->user();
        if (!$user->can('manage.members')) {
            return redirect()->to('manage');
        }

        // View params
        $params = [
            'title' => 'App.manage.members.title',
            'return' => 'manage',
            'api' => 'api/members',
            'mode' => 'edit',
            'detail' => 'page',
            'edit' => 'App.manage.members.edit',
            'fields' => [
                'id' => [
                    'search' => true,
                    'col' => true,
                    'helper' => 'App.helper.id?',
                    'lib' => 'App.manage.members.id',
                    'type' => 'text'
                ],
                'firstname' => [
                    'search' => true,
                    'col' => true,
                    'lib' => 'App.manage.members.firstname',
                    'type' => 'text'
                ],
                'lastname' => [
                    'search' => true,
                    'col' => true,
                    'lib' => 'App.manage.members.lastname',
                    'type' => 'text'
                ],
                'email' => [
                    'search' => true,
                    'col' => true,
                    'lib' => 'App.manage.members.email',
                    'type' => 'email'
                ],
                'created_at' => [
                    'search' => true,
                    'col' => true,
                    'disabled' => true,
                    'helper' => 'App.helper.info',
                    'lib' => 'App.manage.members.createdAt',
                    'type' => 'date'
                ]
            ]
        ];

        return view('layout/crud', $params);
    }

    public function detail($id)
    {
        // Check access
        $user = auth()->user();
        if (!$user->can('manage.mambers')) {
            return redirect()->to('manage');
        }

        // Get member info
        $memberModel = model(MembersModel::class);
        $member = $memberModel->find($id);
        if ($member == null) {
            return redirect()->to('404');
        }

        // Get borrows list
        $borrowModel = model(BorrowModel::class);
        $borrows = $borrowModel->getBorrowsFromMember($id);
        $oldBorrows = $borrowModel->getOldBorrowsFromMember($id);

        // Set parameters
        $get = $this->request->getGet();
        $params = [
            'id' => $id,
            'return' => $get['return'] ?? 'manage/books',
            'member' => $member,
            'borrows' => $borrows,
            'oldBorrows' => $oldBorrows
        ];

        return view('manage/detail/member', $params);
    }

}