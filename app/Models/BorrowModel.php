<?php

namespace App\Models;

use CodeIgniter\Model;

class BorrowModel extends Model
{

    protected $table = 'borrow';
    protected $primaryKey = 'id';

    protected $allowedFields = ['member', 'book', 'out_date', 'return_date', 'delay'];

    public function getBorrowsFromBook($book): array
    {
        return $this->select('member.id, member.firstname, member.lastname, member.email, borrow.out_date, borrow.return_date, borrow.delay')
            ->join('member', 'borrow.member = member.id')
            ->where('borrow.book', $book)
            ->where('borrow.return_date is null')
            ->findAll();
    }

    public function getOldBorrowsFromBook($book, $limit = 20): array
    {
        return $this->select('member.id, member.firstname, member.lastname, member.email, borrow.out_date, borrow.return_date, borrow.delay')
            ->join('member', 'borrow.member = member.id')
            ->where('borrow.book', $book)
            ->where('borrow.return_date is not null')
            ->findAll($limit);
    }

    public function getBorrowsFromMember($member): array
    {
        return $this->select('book.isbn, book.title, author.username as author, author.id as author_id, publisher.name as publisher, borrow.out_date, borrow.return_date, borrow.delay')
            ->join('book', 'borrow.book = book.isbn')
            ->join('write', 'book.isbn = write.book')
            ->join('author', 'write.author = author.id')
            ->join('publisher', 'book.publisher = publisher.id')
            ->where('write.main', true) // Main author only
            ->where('borrow.return_date is null')
            ->where('borrow.member', $member)
            ->findAll();
    }

    public function getOldBorrowsFromMember($member, $limit = 20): array
    {
        return $this->select('book.isbn, book.title, author.username as author, author.id as author_id, publisher.name as publisher, borrow.out_date, borrow.return_date, borrow.delay')
            ->join('book', 'borrow.book = book.isbn')
            ->join('write', 'book.isbn = write.book')
            ->join('author', 'write.author = author.id')
            ->join('publisher', 'book.publisher = publisher.id')
            ->where('write.main', true) // Main author only
            ->where('borrow.return_date is not null')
            ->where('borrow.member', $member)
            ->findAll($limit);
    }

    public function getOverdue(): array
    {
        return $this->select('book.isbn, book.title, member.id as member, member.firstname, member.lastname, member.email, borrow.out_date, borrow.delay')
            ->join('book', 'borrow.book = book.isbn')
            ->join('member', 'borrow.member = member.id')
            ->where('sysdate() >= DATE_ADD(out_date, INTERVAL delay day)')
            ->where('return_date is null')
            ->findAll();
    }

}
