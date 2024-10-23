<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\BooksModel;
use App\Models\BorrowModel;
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

    public function read(string $id): \CodeIgniter\HTTP\ResponseInterface
    {
        // Check authorization
        $user = auth()->user();
        if (!$user->can('manage.members')) {
            return $this->respond(respond_error(lang('Api.common.forbidden')),$this->codes['forbidden']);
        }

        // Search by ID
        $member = $this->model->find($id);
        if ($member == null) {
            return $this->respond(respond_error(lang('Api.members.notFound')),$this->codes['invalid_data']);
        }

        return $this->respond(respond_success($member));
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
        $adapt = adapt_rules_and_data_for_update($entity, $json, $this->rules);
        $json = $adapt['data'];
        $rules = $adapt['rules'];

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

    public function listBorrowBooks($id) {
        // Check authorization
        $user = auth()->user();
        if (!$user->can('manage.members')) {
            return $this->respond(respond_error(lang('Api.common.forbidden')),$this->codes['forbidden']);
        }

        // Check member exist
        $entity = $this->model->find($id);
        if ($entity == null) {
            return $this->respond(respond_error(lang('Api.members.notFound')),$this->codes['invalid_data']);
        }

        // Find borrowed books
        $data = $this->model->select('book.isbn, book.title, author.username as author, publisher.name as publisher, borrow.out_date')
            ->join('borrow', 'member.id = borrow.member')
            ->join('book', 'borrow.book = book.isbn')
            ->join('write', 'book.isbn = write.book')
            ->join('author', 'write.author = author.id')
            ->join('publisher', 'book.publisher = publisher.id')
            ->where('write.main', true) // Main author only
            ->where('borrow.return_date is null')
            ->findAll();

        return $this->respond(respond_success($data));
    }

    public function borrowBooks($id): \CodeIgniter\HTTP\ResponseInterface
    {
        // Check authorization
        $user = auth()->user();
        if (!$user->can('manage.borrow')) {
            return $this->respond(respond_error(lang('Api.common.forbidden')),$this->codes['forbidden']);
        }

        // Check member exist
        $entity = $this->model->find($id);
        if ($entity == null) {
            return $this->respond(respond_error(lang('Api.members.notFound')),$this->codes['invalid_data']);
        }

        // Get data
        $json = $this->request->getJSON(true) ?? [];

        // Check data
        if (!isset($json['books']) || count($json['books']) === 0) {
            return $this->respond(respond_error(lang('Api.common.invalidData')),$this->codes['invalid_data']);
        }

        // Check book exist and it's not already borrowed by member
        $booksModel = model(BooksModel::class);
        $borrowModel = model(BorrowModel::class);
        foreach ($json['books'] as $book) {
            // Check book
            if (!is_string($book['isbn']) || !is_numeric($book['delay'])) {
                return $this->respond(respond_error(lang('Api.common.invalidData')),$this->codes['invalid_data']);
            }
            $bookEntity = $booksModel->find($book['isbn']);
            if ($bookEntity == null) {
                return $this->respond(respond_error(lang('Api.books.notFound')),$this->codes['invalid_data']);
            }
            // Check borrow
            $borrowEntity = $borrowModel->where('member', $id)
                ->where('book', $book['isbn'])
                ->find();
            if ($borrowEntity != null) {
                return $this->respond(respond_error(lang('Api.members.alreadyBorrow', [$book['isbn']])),$this->codes['invalid_data']);
            }
        }

        // Save
        foreach ($json['books'] as $book) {
            try {
                $borrowModel->insert([
                    'member' => $id,
                    'book' => $book['isbn'],
                    'delay' => $book['delay'],
                    'out_date' => date('Y-m-d')
                ]);
            } catch (\ReflectionException $e) {
                return $this->respond(respond_error(lang('Api.common.serverError')),$this->codes['server_error']);
            }
        }

        return $this->respond(respond_success());
    }

    public function returnBooks($id) {
        // Check authorization
        $user = auth()->user();
        if (!$user->can('manage.borrow')) {
            return $this->respond(respond_error(lang('Api.common.forbidden')),$this->codes['forbidden']);
        }

        // Check member exist
        $entity = $this->model->find($id);
        if ($entity == null) {
            return $this->respond(respond_error(lang('Api.members.notFound')),$this->codes['invalid_data']);
        }

        // Get data
        $json = $this->request->getJSON(true) ?? [];

        // Check data
        if (!isset($json['books']) || count($json['books']) === 0) {
            return $this->respond(respond_error(lang('Api.common.invalidData')),$this->codes['invalid_data']);
        }

        // Set return date
        $borrowModel = model(BorrowModel::class);
        try {
            $borrowModel->where('member', $id)
                ->whereIn('book', $json['books'])
                ->set('return_date', date('Y-m-d'))
                ->update();
        } catch (\ReflectionException $e) {
            return $this->respond(respond_error(lang('Api.common.serverError')),$this->codes['server_error']);
        }

        return $this->respond(respond_success());
    }

}