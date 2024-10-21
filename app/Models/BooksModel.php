<?php

namespace App\Models;

use CodeIgniter\Model;

class BooksModel extends Model
{

    protected $table = 'book';
    protected $primaryKey = 'isbn';

    protected $allowedFields = ['isbn', 'title', 'description', 'publisher', 'collection'];

    public function search($parameter, $page, $number) {
        // Adapt page to limit
        $offset = $number * ($page - 1);

        // Count total
        $total = $this->selectCount('isbn')
            ->first();

        // Set where clause
        $where = '1=1';
        if (isset($parameter['search'])) { // Simple
            $search = $parameter['search'];
            $where = "(isbn like '%$search%' Or title like '%$search%' Or publisher.name like '%$search%' Or collection like '%$search%')";
        } else { // Advanced
            if (isset($parameter['isbn'])) {
                $search = $parameter['isbn'];
                $where .= " And isbn like '%$search%'";
            }
            if (isset($parameter['title'])) {
                $search = $parameter['title'];
                $where .= " And title like '%$search%'";
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
            if($parameter['sort'] === 'publisher') {
                $orderBy = 'publisher.name';
            }
            if($parameter['sort'] === 'collection') {
                $orderBy = 'collection';
            }
        }

        // Get data
        $result = $this->select('isbn, title, publisher.id as publisher_id, publisher.name as publisher_name, collection')
            ->join('publisher', 'book.publisher = publisher.id')
            ->where($where)
            ->orderBy($orderBy)
            ->findAll($number, $offset);

        // Add id value
        $data = [];
        foreach ($result as $val) {
            $val['id'] = $val['isbn'];
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