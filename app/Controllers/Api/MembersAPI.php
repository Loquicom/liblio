<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\MembersModel;
use CodeIgniter\API\ResponseTrait;

class MembersAPI extends BaseController
{

    use ResponseTrait;

    protected $model;
    protected $rules = [
        'id' => 'required|max_length[20]',
        'firstname' => 'required|max_length[255]',
        'lastname' => 'required|max_length[255]',
        'email' => 'required|max_length[512]',
    ];

    public function __construct()
    {
        helper('api');
        $this->model = model(MembersModel::class);
    }

    public function search(): \CodeIgniter\HTTP\ResponseInterface
    {
        // Check authorization
        $user = auth()->user();
        if (!$user->can('manage.members')) {
            return $this->respond(respond_error(lang('Api.common.forbidden')),$this->codes['forbidden']);
        }

        // Check pagination info
        $get = $this->request->getGet();
        $page = $get['page'] ?? 1;
        $number = $get['number'] ?? 10;

        // Remove empty search
        $params = [];
        foreach ($get as $key => $val) {
            if (trim($val) !== '') {
                $params[$key] = $val;
            }
        }

        // Get data
        $data = $this->model->search($params, $page, $number);
        $data['page'] = $page;

        // Respond
        return $this->respond(respond_page($data));
    }

    public function create(): \CodeIgniter\HTTP\ResponseInterface
    {
        // Check authorization
        $user = auth()->user();
        if (!$user->can('manage.members')) {
            return $this->respond(respond_error(lang('Api.common.forbidden')),$this->codes['forbidden']);
        }

        // Get data
        $json = $this->request->getJSON(true) ?? [];

        // If no ID generate one
        if (!isset($json['id']) || trim($json['id']) === '') {
            $json['id'] = uniqid();
        }
        // Upper id
        $json['id'] = strtoupper($json['id']);

        // Validate data
        if (! $this->validateData($json, $this->rules, [])) {
            return $this->respond(respond_error(implode('<br/>', $this->validator->getErrors())),$this->codes['invalid_data']);
        }

        // Check if ID exist in db
        $otherMember = $this->model->find($json['id']);
        if ($otherMember !== null) {
            return $this->respond(respond_error(lang('Api.members.idUnique')),$this->codes['invalid_data']);
        }

        // Add created date
        $json['created_at'] = date('Y-m-d');

        try {
            $this->model->insert($json);
        } catch (\ReflectionException $e) {
            return $this->respond(respond_error(lang('Api.common.serverError')),$this->codes['server_error']);
        }

        return $this->respond(respond_success());
    }

    public function update($id): \CodeIgniter\HTTP\ResponseInterface
    {
        // Check authorization
        $user = auth()->user();
        if (!$user->can('manage.members')) {
            return $this->respond(respond_error(lang('Api.common.forbidden')),$this->codes['forbidden']);
        }

        // Check data exist
        $entity = $this->model->find($id);
        if ($entity == null) {
            return $this->respond(respond_error(lang('Api.members.notFound')),$this->codes['invalid_data']);
        }

        // Get data
        $json = $this->request->getJSON(true) ?? [];

        // Check change
        $rules = $this->rules;
        if ($entity['id'] === $json['id']) {
            unset($json['id']);
            unset($rules['id']);
        }
        if ($entity['firstname'] === $json['firstname']) {
            unset($json['firstname']);
            unset($rules['firstname']);
        }
        if ($entity['lastname'] === $json['lastname']) {
            unset($json['lastname']);
            unset($rules['lastname']);
        }
        if ($entity['email'] === $json['email']) {
            unset($json['email']);
            unset($rules['email']);
        }

        // Any change ?
        if (count($rules) === 0) {
            // Stop success
            return $this->respond(respond_success());
        }

        // Validate data
        if (! $this->validateData($json, $rules, [])) {
            return $this->respond(respond_error(implode('<br/>', $this->validator->getErrors())),$this->codes['invalid_data']);
        }

        // Check if ID exist in db
        if (isset($json['id'])) {
            $otherMember = $this->model->find($json['id']);
            if ($otherMember !== null) {
                return $this->respond(respond_error(lang('Api.members.idUnique')),$this->codes['invalid_data']);
            }
        }

        // Unset created date
        unset($json['created_at']);

        try {
            $this->model->update($id, $json);
        } catch (\ReflectionException $e) {
            return $this->respond(respond_error(lang('Api.common.serverError')),$this->codes['server_error']);
        }

        return $this->respond(respond_success());
    }

    public function delete($id): \CodeIgniter\HTTP\ResponseInterface
    {
        // Check authorization
        $user = auth()->user();
        if (!$user->can('manage.members')) {
            return $this->respond(respond_error(lang('Api.common.forbidden')),$this->codes['forbidden']);
        }

        // Check data exist
        $entity = $this->model->find($id);
        if ($entity == null) {
            return $this->respond(respond_error(lang('Api.members.notFound')),$this->codes['invalid_data']);
        }

        $this->model->delete($id);
        return $this->respond(respond_success());
    }

}