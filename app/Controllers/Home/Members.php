<?php

namespace App\Controllers\Home;

use App\Controllers\BaseController;
use App\Models\BorrowModel;
use App\Models\MembersModel;

class Members extends BaseController
{

    protected $rules = [
        'id' => 'required',
        'email' => 'required|max_length[254]|valid_email'
    ];

    public function auth(): string
    {
        return view('home/member-auth');
    }

    public function authAction() {
        // Check data
        $post = $this->request->getPost();
        if (!$this->validateData($post, $this->rules, [])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Check if member match
        $membersModel = model(MembersModel::class);
        $member = $membersModel->where($post)->first();
        if ($member == null) {
            return redirect()->back()->withInput()->with('error', lang('Api.members.notFound'));
        }

        // Authorize
        $session = service('session');
        $session->setFlashdata('authorize', 'members/detail');
        $session->setFlashdata('authorize-id', $member['id']);

        // Redirect
        return redirect()->to('members/detail');
    }

    public function detail() {
        // Check authorize
        $session = service('session');
        $authorize = $session->get('authorize');
        if ($authorize !== 'members/detail') {
            return redirect()->to('members/auth');
        }

        // Get ID
        $id = $session->get('authorize-id');

        // Get member info
        $memberModel = model(MembersModel::class);
        $member = $memberModel->find($id);

        // Get borrows list
        $borrowModel = model(BorrowModel::class);
        $borrows = $borrowModel->getBorrowsFromMember($id);
        $oldBorrows = $borrowModel->getOldBorrowsFromMember($id);

        // Set parameters
        $get = $this->request->getGet();
        $params = [
            'id' => $id,
            'member' => $member,
            'borrows' => $borrows,
            'oldBorrows' => $oldBorrows
        ];

        return view('home/member', $params);
    }

}