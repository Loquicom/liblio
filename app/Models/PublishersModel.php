<?php

namespace App\Models;

use CodeIgniter\Model;

class PublishersModel extends Model
{

    protected $table = 'publisher';
    protected $primaryKey = 'id';

    protected $allowedFields = ['name'];

    public function search($parameter, $page, $number): array
    {
        // Adapt page to limit
        $offset = $number * ($page - 1);

        // Set where clause
        $where = '1=1';
        if (isset($parameter['search'])) { // Simple
            $search = $parameter['search'];
            $where = "(name like '%$search%')";
        } else { // Advanced
            if (isset($parameter['name'])) {
                $search = $parameter['name'];
                $where .= " And name like '%$search%'";
            }
        }

        // Order by
        $orderBy = '1';
        if (isset($parameter['sort'])) {
            if($parameter['sort'] === 'name') {
                $orderBy = 'name';
            }
        }

        // Count total
        $total = $this->selectCount('name')
            ->where($where)
            ->first();

        // Get data
        $result = $this->select('id, name')
            ->where($where)
            ->orderBy($orderBy)
            ->findAll($number, $offset);

        return [
            'data' => $result,
            'total' => $total['name']
        ];
    }

}