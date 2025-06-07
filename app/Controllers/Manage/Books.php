<?php

namespace App\Controllers\Manage;

use App\Controllers\BaseController;
use App\Models\AuthorsModel;
use App\Models\BooksModel;
use App\Models\BorrowModel;
use App\Models\PublishersModel;

class Books extends BaseController
{

    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        // Check access
        $user = auth()->user();
        if (!$user->can('manage.books')) {
            return redirect()->to('manage');
        }

        // Defined parameter
        $mode = 'view';
        $author = [];
        $publisher = [];

        // Adapt parameter bases on auth
        $user = auth()->user();
        if ($user->can('manage.book.edit')) {
            $mode = 'edit';
        }

        // Load publisher
        $publisherModel = model(PublishersModel::class);
        $publisherData = $publisherModel->findAll();
        foreach ($publisherData as $data) {
            $publisher[$data['id']] = $data['name'];
        }

        // View params
        $params = [
            'title' => 'App.manage.books.title',
            'return' => 'manage',
            'api' => 'api/books',
            'mode' => $mode,
            'detail' => 'page',
            'edit' => 'App.manage.books.edit',
            'fields' => [
                'isbn' => [
                    'search' => true,
                    'col' => true,
                    'lib' => 'App.manage.books.isbn',
                    'type' => 'text'
                ],
                'title' => [
                    'search' => true,
                    'col' => true,
                    'lib' => 'App.common.title',
                    'type' => 'text'
                ],
                'author' => [
                    'search' => true,
                    'col' => true,
                    'lib' => 'App.manage.books.author',
                    'type' => 'autocomplete:username:api/authors'
                ],
                'publisher' => [
                    'search' => true,
                    'col' => true,
                    'lib' => 'App.manage.books.publisher',
                    'type' => $publisher
                ],
                'theme' => [
                    'search' => true,
                    'col' => true,
                    'helper' => 'App.common.optional',
                    'lib' => 'App.manage.books.theme',
                    'type' => 'text'
                ],
                'year' => [
                    'search' => true,
                    'col' => true,
                    'helper' => 'App.common.optional',
                    'lib' => 'App.manage.books.year',
                    'type' => 'number'
                ],
                'reference' => [
                    'search' => true,
                    'col' => false,
                    'helper' => 'App.common.optional',
                    'lib' => 'App.manage.books.reference',
                    'type' => 'text'
                ],
                'copy' => [
                    'search' => true,
                    'col' => true,
                    'helper' => [
                        'lib' => 'App.helper.default',
                        'val' => [1]
                    ],
                    'lib' => 'App.manage.books.copy',
                    'type' => 'number'
                ],
            ]
        ];

        return view('layout/crud', $params);
    }

    public function detail($id): string|\CodeIgniter\HTTP\RedirectResponse
    {
        // Check access
        $user = auth()->user();
        if (!$user->can('manage.books')) {
            return redirect()->to('manage');
        }

        // Get book info
        $booksModel = model(BooksModel::class);
        $book = $booksModel->find($id);
        if ($book == null) {
            return redirect()->to('404');
        }

        // Get list of book author
        $authorsModel = model(AuthorsModel::class);
        $bookAuthors = $authorsModel->getAuthors($id);

        // Get list of all author
        $exludeAuthors = [];
        foreach ($bookAuthors as $author) {
            $exludeAuthors[] = $author['id'];
        }
        $authors = $authorsModel->whereNotIn('id', $exludeAuthors)->findAll();

        // Get borrows list
        $borrowModel = model(BorrowModel::class);
        $borrows = $borrowModel->getBorrowsFromBook($id);
        $oldBorrows = $borrowModel->getOldBorrowsFromBook($id);

        // Set parameters
        $get = $this->request->getGet();
        $params = [
            'id' => $id,
            'return' => $get['return'] ?? 'manage/books',
            'book' => $book,
            'authors' => $authors,
            'bookAuthors' => $bookAuthors,
            'borrows' => $borrows,
            'oldBorrows' => $oldBorrows
        ];

        return view('manage/detail/book', $params);
    }

}