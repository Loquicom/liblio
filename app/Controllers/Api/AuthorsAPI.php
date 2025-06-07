<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\AuthorsModel;
use App\Models\WriteModel;
use CodeIgniter\API\ResponseTrait;

class AuthorsAPI extends BaseController
{

    use ResponseTrait;

    protected $model;
    protected $rules = [
        'username' => 'required|max_length[512]'
    ];

    public function __construct()
    {
        helper('api');
        $this->model = model(AuthorsModel::class);
    }

    public function read(string $id): \CodeIgniter\HTTP\ResponseInterface
    {
        // Search by ID
        $author = $this->model->find($id);
        if ($author == null) {
            return $this->respond(respond_error(lang('Api.authors.notFound')),$this->codes['invalid_data']);
        }

        return $this->respond(respond_success($author));
    }

    public function search($search): \CodeIgniter\HTTP\ResponseInterface
    {
        $get = $this->request->getGet();
        $number = $get['number'] ?? 200;

        $params['search'] = $search;
        $data = $this->model->search($params, 1, $number);
        return $this->respond(respond_success($data['data']));
    }

    public function pagedSearch(): \CodeIgniter\HTTP\ResponseInterface
    {
        // Check authorization
        $user = auth()->user();
        if (!$user->can('manage.authors')) {
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
        if (!$user->can('manage.authors')) {
            return $this->respond(respond_error(lang('Api.common.forbidden')),$this->codes['forbidden']);
        }

        // Get data
        $json = $this->request->getJSON(true) ?? [];
        unset($json['id']);

        // Validate data
        if (! $this->validateData($json, $this->rules, [])) {
            return $this->respond(respond_error(implode('<br/>', $this->validator->getErrors())),$this->codes['invalid_data']);
        }

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
        if (!$user->can('manage.authors')) {
            return $this->respond(respond_error(lang('Api.common.forbidden')),$this->codes['forbidden']);
        }

        // Check data exist
        $entity = $this->model->find($id);
        if ($entity == null) {
            return $this->respond(respond_error(lang('Api.authors.notFound')),$this->codes['invalid_data']);
        }

        // Get data
        $json = $this->request->getJSON(true) ?? [];
        unset($json['id']);

        // Check change
        $adapt = adapt_rules_and_data_for_update($entity, $json, $this->rules);
        $json = $adapt['data'];
        $rules = $adapt['rules'];

        // Any change left
        if (count($json) === 0) {
            // Stop success
            return $this->respond(respond_success());
        }

        // Validate data
        if (count($rules) > 0 && !$this->validateData($json, $rules, [])) {
            return $this->respond(respond_error(implode('<br/>', $this->validator->getErrors())),$this->codes['invalid_data']);
        }

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
        if (!$user->can('manage.authors')) {
            return $this->respond(respond_error(lang('Api.common.forbidden')),$this->codes['forbidden']);
        }

        // Can't delete default
        if ($id == 1) {
            return $this->respond(respond_error(lang('Api.authors.defaultDelete')),$this->codes['invalid_data']);
        }

        // Check data exist
        $entity = $this->model->find($id);
        if ($entity == null) {
            return $this->respond(respond_error(lang('Api.authors.notFound')),$this->codes['invalid_data']);
        }

        try {
            // If author is use as main replace with default
            $writeModel = model(WriteModel::class);
            $writeModel->where('author', $id)
                ->where('main', true)
                ->set('author', 1)
                ->update();
        } catch (\ReflectionException $e) {
            return $this->respond(respond_error(lang('Api.common.serverError')),$this->codes['server_error']);
        }

        $this->model->delete($id);
        return $this->respond(respond_success());
    }

}