<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\BooksModel;
use App\Models\PublishersModel;
use CodeIgniter\API\ResponseTrait;

class PublishersAPI extends BaseController
{

    use ResponseTrait;

    protected $model;
    protected $rules = [
        'name' => 'required|max_length[512]'
    ];

    public function __construct()
    {
        helper('api');
        $this->model = model(PublishersModel::class);
    }

    public function read(string $id): \CodeIgniter\HTTP\ResponseInterface
    {
        // Search by ID
        $publisher = $this->model->find($id);
        if ($publisher == null) {
            return $this->respond(respond_error(lang('Api.publishers.notFound')),$this->codes['invalid_data']);
        }

        return $this->respond(respond_success($publisher));
    }

    public function search(): \CodeIgniter\HTTP\ResponseInterface
    {
        // Read GET parameters
        $get = $this->request->getGet();
        $number = $get['number'] ?? 100;
        $search = $get['search'] ?? '';

        // Limit number to 500
        if ($number > 500) {
            $number = 500;
        }

        // Search
        $params['search'] = $search;
        $data = $this->model->search($params, 1, $number);
        return $this->respond(respond_success($data['data']));
    }

    public function pagedSearch(): \CodeIgniter\HTTP\ResponseInterface
    {
        // Check authorization
        $user = auth()->user();
        if (!$user->can('config.publisher')) {
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
        if (!$user->can('config.publisher')) {
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
        if (!$user->can('config.publisher')) {
            return $this->respond(respond_error(lang('Api.common.forbidden')),$this->codes['forbidden']);
        }

        // Check data exist
        $entity = $this->model->find($id);
        if ($entity == null) {
            return $this->respond(respond_error(lang('Api.publishers.notFound')),$this->codes['invalid_data']);
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
        if (!$user->can('config.publisher')) {
            return $this->respond(respond_error(lang('Api.common.forbidden')),$this->codes['forbidden']);
        }

        // Can't delete default
        if ($id == 1) {
            return $this->respond(respond_error(lang('Api.publishers.defaultDelete')),$this->codes['invalid_data']);
        }

        // Check data exist
        $entity = $this->model->find($id);
        if ($entity == null) {
            return $this->respond(respond_error(lang('Api.publishers.notFound')),$this->codes['invalid_data']);
        }

        try {
            // If publisher is use replace with default
            $bookModel = model(BooksModel::class);
            $bookModel->where('publisher', $id)
                ->set('publisher', 1)
                ->update();
        } catch (\ReflectionException $e) {
            return $this->respond(respond_error(lang('Api.common.serverError')),$this->codes['server_error']);
        }

        $this->model->delete($id);
        return $this->respond(respond_success());
    }

}