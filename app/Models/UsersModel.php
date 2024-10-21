<?php

namespace App\Models;

class UsersModel
{

    public function search($parameter, $page, $number) {
        // Adapt page to limit
        $offset = $number * ($page - 1);

        // Get provider
        $users = auth()->getProvider();

        // Count total
        $total = $users->selectCount('username')
            ->join('auth_identities', 'users.id = auth_identities.user_id')
            ->join('auth_groups_users', 'users.id = auth_groups_users.user_id')
            ->first();

        // Set where clause
        $where = '1=1';
        if (isset($parameter['search'])) { // Simple
            $search = $parameter['search'];
            $where = "(username like '%$search%' or secret like '%$search%')";
        } else { // Advanced
            if (isset($parameter['username'])) {
                $search = $parameter['username'];
                $where .= " And username like '%$search%'";
            }
            if (isset($parameter['email'])) {
                $search = $parameter['email'];
                $where .= " And secret like '%$search%'";
            }
            if (isset($parameter['role'])) {
                $search = $parameter['role'];
                $where .= " And group like '%$search%'";
            }
        }

        // Limit role
        if (isset($parameter['limitRole'])) {
            $limitRole = $parameter['limitRole'];
            if (is_string($limitRole)) {
                $where .= " And group = '$limitRole'";
            } else {
                $where .= " And group in (";
                foreach ($limitRole as $lr) {
                    $where .= "'$lr'";
                }
                $where .= ")";
            }
        }

        // Order by
        $orderBy = '1';
        if (isset($parameter['sort'])) {
            if($parameter['sort'] === 'username') {
                $orderBy = 'username';
            } else if($parameter['sort'] === 'email') {
                $orderBy = 'secret';
            } else if($parameter['sort'] === 'role') {
                $orderBy = 'group';
            }
        }

        // Get data
        $result = $users->select('users.id, username, secret, group')
            ->join('auth_identities', 'users.id = auth_identities.user_id')
            ->join('auth_groups_users', 'users.id = auth_groups_users.user_id')
            ->where($where)
            ->orderBy($orderBy)
            ->findAll($number, $offset);

        // Mapping result
        $data = [];
        foreach (json_decode(json_encode($result), true) as $val) {
            $data[] = [
                'id' => $val['id'],
                'username' => $val['username'],
                'email' => $val['secret'],
                'role' => [
                    'code' => $val['group'],
                    'lib' => lang('App.role.' . $val['group'])
                ]
            ];
        }

        return [
            'data' => $data,
            'total' => $total->username
        ];
    }

}