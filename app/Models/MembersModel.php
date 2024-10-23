<?php

namespace App\Models;

use CodeIgniter\Model;

class MembersModel extends Model
{

    protected $table = 'member';
    protected $primaryKey = 'id';

    protected $allowedFields = ['id', 'firstname', 'lastname', 'email', 'created_at', 'comment'];

    public function search($parameter, $page, $number): array
    {
        // Adapt page to limit
        $offset = $number * ($page - 1);

        // Set where clause
        $where = '1=1';
        if (isset($parameter['search'])) { // Simple
            $search = $parameter['search'];
            $where = "(id like '%$search%' OR firstname like '%$search%' OR lastname like '%$search%' OR email like '%$search%')";
        } else { // Advanced
            if (isset($parameter['id'])) {
                $search = $parameter['id'];
                $where .= " And id like '%$search%'";
            }
            if (isset($parameter['firstname'])) {
                $search = $parameter['firstname'];
                $where .= " And firstname like '%$search%'";
            }
            if (isset($parameter['lastname'])) {
                $search = $parameter['lastname'];
                $where .= " And lastname like '%$search%'";
            }
            if (isset($parameter['email'])) {
                $search = $parameter['email'];
                $where .= " And email like '%$search%'";
            }
            if (isset($parameter['created_at'])) {
                $search = $parameter['created_at'];
                $where .= " And created_at = '$search'";
            }
        }

        // Order by
        $orderBy = '1';
        if (isset($parameter['sort'])) {
            if($parameter['sort'] === 'id') {
                $orderBy = 'id';
            }
            if($parameter['sort'] === 'firstname') {
                $orderBy = 'firstname';
            }
            if($parameter['sort'] === 'lastname') {
                $orderBy = 'lastname';
            }
            if($parameter['sort'] === 'email') {
                $orderBy = 'email';
            }
            if($parameter['sort'] === 'created_at') {
                $orderBy = 'created_at';
            }
        }

        // Count total
        $total = $this->selectCount('id')
            ->where($where)
            ->first();

        // Get data
        $result = $this->select('id, firstname, lastname, email, created_at')
            ->where($where)
            ->orderBy($orderBy)
            ->findAll($number, $offset);

        return [
            'data' => $result,
            'total' => $total['id']
        ];
    }

}