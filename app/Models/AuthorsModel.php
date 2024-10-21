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

        // Count total
        $total = $this->selectCount('username')
            ->first();

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

}