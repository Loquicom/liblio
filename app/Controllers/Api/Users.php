<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class Users extends BaseController
{

    use ResponseTrait;

    public function get()
    {
        return $this->respond([
            'success' => true,
            'data' => [
                'total' => 18,
                'values' => [
                    [
                        'id' => 1,
                        'username' => 'test',
                        'email' => 'test@email.fr',
                        'role' => [
                            'code' => 'admin',
                            'lib' => lang('App.role.admin')
                        ],
                    ]
                ]
            ]
        ]);
    }

}