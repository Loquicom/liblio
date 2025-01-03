<?php

namespace App\Models;

use CodeIgniter\Model;

class AuthorsModel extends Model
{

    protected $table = 'author';
    protected $primaryKey = 'id';

    protected $allowedFields = ['username'];

    public function search($parameter, $page, $number): array
    {
        // Adapt page to limit
        $offset = $number * ($page - 1);

        // Set where clause
        $where = '1=1';
        if (isset($parameter['search'])) { // Simple
            $search = $parameter['search'];
            $where = "(username like '%$search%')";
        } else { // Advanced
            if (isset($parameter['username'])) {
                $search = $parameter['username'];
                $where .= " And username like '%$search%'";
            }
        }

        // Order by
        $orderBy = '1';
        if (isset($parameter['sort'])) {
            if($parameter['sort'] === 'username') {
                $orderBy = 'username';
            }
        }

        // Count total
        $total = $this->selectCount('username')
            ->where($where)
            ->first();

        // Get data
        $result = $this->select('id, username')
            ->where($where)
            ->orderBy($orderBy)
            ->findAll($number, $offset);

        return [
            'data' => $result,
            'total' => $total['username']
        ];
    }

    public function getMainAuthor($isbn): object|array|null
    {
        return $this->select($this->allowedFields)
            ->join('write', 'author.id = write.author')
            ->where('write.book', $isbn)
            ->where('write.main', true)
            ->first();
    }

    public function getAuthors($book): array
    {
        // Retrieves all authors from a book
        $data = $this->select('author.id, author.username, write.role, write.main')
            ->join('write', 'author.id = write.author')
            ->where('write.book', $book)
            ->findAll();

        return $data;
    }

}