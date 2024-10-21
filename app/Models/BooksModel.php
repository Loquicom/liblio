<?php

namespace App\Models;

use CodeIgniter\Model;

class BooksModel extends Model
{

    protected $table = 'book';
    protected $primaryKey = 'isbn';

    protected $allowedFields = ['isbn', 'title', 'description', 'publisher', 'collection'];

    public function search($parameter, $page, $number): array
    {
        // Adapt page to limit
        $offset = $number * ($page - 1);

        // Count total
        $total = $this->selectCount('isbn')
            ->first();

        // Set where clause
        $where = '1=1';
        if (isset($parameter['search'])) { // Simple
            $search = $parameter['search'];
            $where = "(isbn like '%$search%' OR title like '%$search%' OR author.username like '%$search%' OR publisher.name like '%$search%' OR collection like '%$search%')";
        } else { // Advanced
            if (isset($parameter['isbn'])) {
                $search = $parameter['isbn'];
                $where .= " And isbn like '%$search%'";
            }
            if (isset($parameter['title'])) {
                $search = $parameter['title'];
                $where .= " And title like '%$search%'";
            }
            if (isset($parameter['author'])) {
                $search = $parameter['author'];
                $where .= " And author.id = $search";
            }
            if (isset($parameter['publisher'])) {
                $search = $parameter['publisher'];
                $where .= " And publisher = $search";
            }
            if (isset($parameter['collection'])) {
                $search = $parameter['collection'];
                $where .= " And collection like '%$search%'";
            }
        }

        // Order by
        $orderBy = '1';
        if (isset($parameter['sort'])) {
            if($parameter['sort'] === 'isbn') {
                $orderBy = 'isbn';
            }
            if($parameter['sort'] === 'title') {
                $orderBy = 'title';
            }
            if($parameter['sort'] === 'author') {
                $orderBy = 'author.username';
            }
            if($parameter['sort'] === 'publisher') {
                $orderBy = 'publisher.name';
            }
            if($parameter['sort'] === 'collection') {
                $orderBy = 'collection';
            }
        }

        // Get data
        $result = $this->select('isbn, title, author.id as author_id, author.username as author_username, publisher.id as publisher_id, publisher.name as publisher_name, collection')
            ->join('publisher', 'book.publisher = publisher.id')
            ->join('write', '(book.isbn = write.book and write.main = true)')
            ->join('author', 'write.author = author.id')
            ->where($where)
            ->orderBy($orderBy)
            ->findAll($number, $offset);

        // Add id value
        $data = [];
        foreach ($result as $val) {
            $val['id'] = $val['isbn'];
            $val['author'] = [
                'code' => $val['author_id'],
                'lib' => $val['author_username']
            ];
            $val['publisher'] = [
                'code' => $val['publisher_id'],
                'lib' => $val['publisher_name']
            ];
            unset($val['publisher_id']);
            unset($val['publisher_name']);
            $data[] = $val;
        }

        return [
            'data' => $data,
            'total' => $total['isbn']
        ];
    }

}