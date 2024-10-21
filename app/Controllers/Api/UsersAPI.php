<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\UsersModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Validation\ValidationRules;

class UsersAPI extends BaseController
{

    use ResponseTrait;

    public function __construct()
    {
        helper('api');
    }

    public function search(): \CodeIgniter\HTTP\ResponseInterface
    {
        // Check authorization
        $user = auth()->user();
        if (!$user->can('config.users.view')) {
            return $this->respond(respond_error(lang('Api.common.forbidden')),$this->codes['forbidden']);
        }

        // Check pagination info
        $get = $this->request->getGet();
        $page = $get['page'] ?? 1;
        $number = $get['number'] ?? 10;

        // Adapt search with auth
        if (!$user->can('config.users.admin')) {
            $get['limitRole'] = 'manager';
        }

        // Remove empty search
        $params = [];
        foreach ($get as $key => $val) {
            if (trim($val) !== '') {
                $params[$key] = $val;
            }
        }

        // Get data
        $model = model(UsersModel::class);
        $data = $model->search($params, $page, $number);
        $data['page'] = $page;

        // Respond
        return $this->respond(respond_page($data));
    }

    public function create(): \CodeIgniter\HTTP\ResponseInterface
    {
        // Check authorization
        $user = auth()->user();
        if (!$user->can('config.users.edit')) {
            return $this->respond(respond_error(lang('Api.common.forbidden')),$this->codes['forbidden']);
        }

        // Get the validation rules
        $rules = $this->getValidationRules();

        // Get data
        $json = $this->request->getJSON(true) ?? [];
        unset($json['id']);

        // Validate data
        if (! $this->validateData($json, $rules, [], config('Auth')->DBGroup)) {
            return $this->respond(respond_error(implode('<br/>', $this->validator->getErrors())),$this->codes['invalid_data']);
        }

        // Check if role exist
        $role = array_keys(config('AuthGroups')->groups);
        if (!in_array($json['role'], $role)) {
            return $this->respond(respond_error(lang('Api.users.invalidRole')),$this->codes['invalid_data']);
        }

        // Create user
        $group = $json['role'];
        unset($json['role']);
        $user = new User($json);

        // Save
        $users = auth()->getProvider();
        $users->save($user);
        $user = $users->findById($users->getInsertID());
        $user->syncGroups($group);

        return $this->respond(respond_success());
    }

    public function update(int $id): \CodeIgniter\HTTP\ResponseInterface
    {
        // Check authorization
        $actualUser = auth()->user();
        if (!$actualUser->can('config.users.edit')) {
            return $this->respond(respond_error(lang('Api.common.forbidden')),$this->codes['forbidden']);
        }

        // Get the validation rules
        $rules = $this->getValidationRules();

        // Get data
        $json = $this->request->getJSON(true) ?? [];
        unset($json['id']);

        // Get user
        $user = auth()->getProvider()->findById($id);
        if ($user == null) {
            return $this->respond(respond_error(lang('Api.users.notFound')),$this->codes['invalid_data']);
        }

        // Check change
        $checkRole = true;
        if ($user->username === trim($json['username'])) {
            unset($json['username']);
            unset($rules['username']);
        }
        if ($user->getEmail() === trim($json['email'])) {
            unset($json['email']);
            unset($rules['email']);
        }
        if ($user->getGroups()[0] === trim($json['role'])) {
            unset($json['role']);
            $checkRole = false;
        }
        if (trim($json['password']) === '') {
            unset($json['password']);
            unset($rules['password']);
        }

        // Any change ?
        if (!$checkRole && count($rules) === 0) {
            // Stop success
            return $this->respond(respond_success());
        }

        // Validate data
        if (count($rules) > 0 && !$this->validateData($json, $rules, [], config('Auth')->DBGroup)) {
            return $this->respond(respond_error(implode('<br/>', $this->validator->getErrors())),$this->codes['invalid_data']);
        }

        // Check if role exist
        if (isset($json['role'])) {
            $role = array_keys(config('AuthGroups')->groups);
            if (!in_array($json['role'], $role)) {
                return $this->respond(respond_error(lang('Api.users.invalidRole')),$this->codes['invalid_data']);
            }
        }

        // Save
        $users = auth()->getProvider();
        if (isset($json['username'])) {
            $user->username = $json['username'];
        }
        if (isset($json['email'])) {
            $user->setEmail($json['email']);
        }
        if (isset($json['password'])) {
            $user->setPassword($json['password']);
        }
        $users->save($user);
        if (isset($json['role'])) {
            $user->syncGroups($json['role']);
        }

        return $this->respond(respond_success());
    }

    public function delete(int $id): \CodeIgniter\HTTP\ResponseInterface
    {
        // Check authorization
        $actualUser = auth()->user();
        if (!$actualUser->can('config.users.edit')) {
            return $this->respond(respond_error(lang('Api.common.forbidden')),$this->codes['forbidden']);
        }

        // Get user
        $user = auth()->getProvider()->findById($id);
        if ($user == null) {
            return $this->respond(respond_error(lang('Api.users.notFound')),$this->codes['invalid_data']);
        }

        // Delete
        $users = auth()->getProvider();
        $users->delete($user->id, true);

        return $this->respond(respond_success());
    }

    /**
     * Returns the rules that should be used for registration.
     *
     * @return array<string, array<string, array<string>|string>>
     * @phpstan-return array<string, array<string, string|list<string>>>
     */
    protected function getValidationRules(): array
    {
        $rules = new ValidationRules();
        $rules = $rules->getRegistrationRules();
        unset($rules['password_confirm']);
        return $rules;
    }
}