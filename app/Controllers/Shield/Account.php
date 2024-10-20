<?php

namespace App\Controllers\Shield;

use App\Controllers\BaseController;
use CodeIgniter\Shield\Validation\ValidationRules;



class Account extends BaseController
{

    public const ACCOUNT_INFORMATIONS = 'info';
    public const ACCOUNT_PASSWORD = 'pass';

    public function index(): string
    {
        $user = auth()->user();
        return view('shield/account', ['username' => $user->username, 'email' => $user->getEmail()]);
    }

    public function update() {
        $post = $this->request->getPost();
        if (isset($post['username']) && isset($post['email'])) {
            return $this->updateInformations();
        } else if (isset($post['password_old']) && isset($post['password']) && isset($post['password_confirm'])) {
            return $this->updatePassword();
        } else {

        }
    }

    protected function updateInformations() {
        // Get validation rules
        $rules = $this->getValidationRules(self::ACCOUNT_INFORMATIONS);

        //Check if infos have changed
        $post = $this->request->getPost();
        $user = auth()->user();
        if ($user->username === $post['username']) {
            unset($rules['username']);
        }
        if ($user->getEmail() === $post['email']) {
            unset($rules['email']);
        }

        // Validate data
        if (count($rules) < 1) {
            return redirect()->back()->withInput()->with('info-warn', lang('App.account.noChange'));
        } else if (!$this->validateData($post, $rules, [], config('Auth')->DBGroup)) {
            return redirect()->back()->withInput()->with('info-errors', $this->validator->getErrors());
        }

        // Update user
        $user->username = $post['username'];
        $user->setEmail($post['email']);
        $users = auth()->getProvider();
        $users->save($user);

        return redirect()->back()->with('success', lang('App.account.success'));
    }

    protected function updatePassword() {
        // Get validation rules
        $rules = $this->getValidationRules(self::ACCOUNT_PASSWORD);

        // Validate data
        $user = auth()->user();
        $post = $this->request->getPost();
        if (!password_verify($post['password_old'], $user->getPasswordHash())) {
            return redirect()->back()->withInput()->with('pass-errors', lang('App.account.wrongPass'));
        } else if (!$this->validateData($post, $rules, [], config('Auth')->DBGroup)) {
            return redirect()->back()->withInput()->with('pass-errors', $this->validator->getErrors());
        }

        // Update user
        $user->setPassword($post['password']);
        $users = auth()->getProvider();
        $users->save($user);

        return redirect()->back()->with('success', lang('App.account.success'));
    }

    protected function getValidationRules(?string $filter = null): array
    {
        $rules = new ValidationRules();
        $rules = $rules->getRegistrationRules();

        return match ($filter) {
            self::ACCOUNT_INFORMATIONS => ['username' => $rules['username'], 'email' => $rules['email']],
            self::ACCOUNT_PASSWORD => ['password' => $rules['password'], 'password_confirm' => $rules['password_confirm']],
            default => $rules,
        };
    }
}