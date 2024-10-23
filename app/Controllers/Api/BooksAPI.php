<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\AuthorsModel;
use App\Models\BooksModel;
use App\Models\PublishersModel;
use App\Models\WriteModel;
use CodeIgniter\API\ResponseTrait;

class BooksAPI extends BaseController
{

    use ResponseTrait;

    protected BooksModel|null $model;
    protected WriteModel|null $writeModel;
    protected array $rules = [
        'isbn' => 'required|min_length[10]|max_length[13]',
        'title' => 'required|max_length[1024]',
        'author' => 'required|integer',
        'publisher' => 'required|integer',
        'theme' => 'max_length[512]',
        'year' => 'integer',
        'copy' => 'required|integer',
        'reference' => 'max_length[512]'
    ];

    public function __construct()
    {
        helper('api');
        $this->model = model(BooksModel::class);
        $this->writeModel = model(WriteModel::class);
    }

    public function read(string $isbn): \CodeIgniter\HTTP\ResponseInterface
    {
        // Clean ISBN
        $isbn = str_replace([' ', '-'], '', $isbn);

        // Search by ID
        $book = $this->model->find($isbn);
        if ($book == null) {
            return $this->respond(respond_error(lang('Api.books.notFound')),$this->codes['invalid_data']);
        }

        // Get publisher info
        $publisherModel = model(PublishersModel::class);
        $publisher = $publisherModel->find($book['publisher']);
        $book['publisher'] = [
            'code' => $publisher['id'],
            'lib' => $publisher['name'],
        ];

        // Get main author
        $write = $this->writeModel->findMainForBook($isbn);
        if ($write != null) {
            $authorModel = model(AuthorsModel::class);
            $author = $authorModel->find($write['author']);
            $book['author'] = [
                'code' => $author['id'],
                'lib' => $author['username'],
            ];
        } else {
            $book['author'] = [
                'code' => -1,
                'lib' => '',
            ];
        }


        return $this->respond(respond_success($book));
    }

    public function search(): \CodeIgniter\HTTP\ResponseInterface
    {
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
        if (!$user->can('manage.books')) {
            return $this->respond(respond_error(lang('Api.common.forbidden')),$this->codes['forbidden']);
        }

        // Get data
        $json = $this->request->getJSON(true) ?? [];
        unset($json['id']);

        // Clean ISBN
        if (isset($json['isbn'])) {
            $json['isbn'] = str_replace([' ', '-'], '', $json['isbn']);
        }

        // Remove copy if empty
        $rules = $this->rules;
        if (isset($json['copy']) && trim($json['copy']) === '') {
            unset($json['copy']);
            unset($rules['copy']);
        }

        // Remove year if empty
        if (isset($json['year']) && trim($json['year']) === '') {
            unset($json['year']);
            unset($rules['year']);
        }

        // Validate data
        if (! $this->validateData($json, $rules, [])) {
            return $this->respond(respond_error(implode('<br/>', $this->validator->getErrors())),$this->codes['invalid_data']);
        }

        // Check if ISBN exist in db
        $otherBook = $this->model->find($json['isbn']);
        if ($otherBook !== null) {
            return $this->respond(respond_error(lang('Api.books.isbnUnique')),$this->codes['invalid_data']);
        }

        // Extract author
        $author = $json['author'];
        unset($json['author']);

        try {
            // Book insert
            $this->model->insert($json);
            // Write insert (author)
            $this->writeModel->insert(['author' => $author, 'book' => $json['isbn'], 'main' => true, 'role' => lang('App.manage.books.author')]);
        } catch (\ReflectionException $e) {
            return $this->respond(respond_error(lang('Api.common.serverError')),$this->codes['server_error']);
        }

        return $this->respond(respond_success());
    }

    public function update($id): \CodeIgniter\HTTP\ResponseInterface
    {
        // Check authorization
        $user = auth()->user();
        if (!$user->can('manage.books')) {
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

        // Default value if copy if empty
        if (isset($json['copy']) && trim($json['copy']) === '') {
            $json['copy'] = 1;
        }

        // Check change from book table field
        $adapt = adapt_rules_and_data_for_update($entity, $json, $this->rules, ['author']);
        $json = $adapt['data'];
        $rules = $adapt['rules'];

        // Remove year if is empty
        if (isset($json['year']) && trim($json['year']) === '') {
            unset($json['year']);
            unset($rules['year']);
        }

        // Extract author
        $author = $json['author'];

        // Check author change
        $write = $this->writeModel->findMainForBook($id);
        if ($write['author'] === $author) {
            unset($rules['author']);
            $author = null;
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

        // If isbn update check if exist in db
        if (isset($json['isbn'])) {
            $otherBook = $this->model->find($json['isbn']);
            if ($otherBook !== null) {
                return $this->respond(respond_error(lang('Api.books.isbnUnique')),$this->codes['invalid_data']);
            }
        }

        // Unset author infos
        unset($json['author']);
        unset($rules['author']);

        try {
            // Book update
            if (count($rules) > 0) {
                $this->model->update($id, $json);
            }
            // Write update (author)
            if ($author !== null) {
                $this->writeModel->where('author', $write['author'])
                    ->where('book', $json['isbn'] ?? $id)
                    ->set('author', $author)
                    ->update();
            }
        } catch (\ReflectionException $e) {
            return $this->respond(respond_error(lang('Api.common.serverError')),$this->codes['server_error']);
        }

        return $this->respond(respond_success());
    }

    public function delete($id): \CodeIgniter\HTTP\ResponseInterface
    {
        // Check authorization
        $user = auth()->user();
        if (!$user->can('manage.books')) {
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