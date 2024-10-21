<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\BooksModel;
use App\Models\PublishersModel;
use CodeIgniter\API\ResponseTrait;

class BooksAPI extends BaseController
{

    use ResponseTrait;

    protected $model;
    protected $rules = [
        'isbn' => 'required|min_length[10]|max_length[13]',
        'title' => 'required|max_length[1024]',
        'publisher' => 'required|integer',
        'collection' => 'max_length[512]'
    ];

    public function __construct()
    {
        helper('api');
        $this->model = model(BooksModel::class);
    }

    public function search(): \CodeIgniter\HTTP\ResponseInterface
    {
        // Check authorization
        $user = auth()->user();
        if (!$user->can('manage.books.view')) {
            return $this->respond(respond_error(lang('Api.common.forbidden')),$this->codes['forbidden']);
        }

        // Check pagination info
        $get = $this->request->getGet();
        $page = $get['page'] ?? 1;
        $number = $get['number'] ?? 10;

        // Clean ISBN
        if (isset($get['isbn'])) {
            $get['isbn'] = str_replace([' ', '-'], '', $get['isbn']);
        }

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
        if (!$user->can('manage.books.edit')) {
            return $this->respond(respond_error(lang('Api.common.forbidden')),$this->codes['forbidden']);
        }

        // Get data
        $json = $this->request->getJSON(true) ?? [];
        unset($json['id']);

        // Clean ISBN
        if (isset($json['isbn'])) {
            $json['isbn'] = str_replace([' ', '-'], '', $json['isbn']);
        }

        // Validate data
        if (! $this->validateData($json, $this->rules, [])) {
            return $this->respond(respond_error(implode('<br/>', $this->validator->getErrors())),$this->codes['invalid_data']);
        }

        try {
            $this->model->insert($json);
        } catch (\ReflectionException $e) {
            return $this->respond(respond_error('Api.common.serverError'),$this->codes['server_error']);
        }

        return $this->respond(respond_success());
    }

    public function update($id): \CodeIgniter\HTTP\ResponseInterface
    {
        // Check authorization
        $user = auth()->user();
        if (!$user->can('manage.books.edit')) {
            return $this->respond(respond_error(lang('Api.common.forbidden')),$this->codes['forbidden']);
        }

        // Clean ID (ISBN)
        $id = str_replace([' ', '-'], '', $id);

        // Check data exist
        $entity = $this->model->find($id);
        if ($entity == null) {
            return $this->respond(respond_error(lang('Api.books.notFound')),$this->codes['invalid_data']);
        }

        // Get data
        $json = $this->request->getJSON(true) ?? [];
        unset($json['id']);

        // Clean ISBN
        if (isset($json['isbn'])) {
            $json['isbn'] = str_replace([' ', '-'], '', $json['isbn']);
        }

        // Check change
        $rules = $this->rules;
        if ($entity['isbn'] === $json['isbn']) {
            unset($json['isbn']);
            unset($rules['isbn']);
        }
        if ($entity['title'] === $json['title']) {
            unset($json['title']);
            unset($rules['title']);
        }
        if ($entity['publisher'] === $json['publisher']) {
            unset($json['publisher']);
            unset($rules['publisher']);
        }
        if ($entity['collection'] === $json['collection']) {
            unset($json['collection']);
            unset($rules['collection']);
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

        try {
            $this->model->update($id, $json);
        } catch (\ReflectionException $e) {
            return $this->respond(respond_error('Api.common.serverError'),$this->codes['server_error']);
        }

        return $this->respond(respond_success());
    }

    public function delete($id): \CodeIgniter\HTTP\ResponseInterface
    {
        // Check authorization
        $user = auth()->user();
        if (!$user->can('manage.books.edit')) {
            return $this->respond(respond_error(lang('Api.common.forbidden')),$this->codes['forbidden']);
        }

        // Clean ID (ISBN)
        $id = str_replace([' ', '-'], '', $id);

        // Check data exist
        $entity = $this->model->find($id);
        if ($entity == null) {
            return $this->respond(respond_error(lang('Api.books.notFound')),$this->codes['invalid_data']);
        }

        $this->model->delete($id);
        return $this->respond(respond_success());
    }

}