<?php

namespace App\Models;

use CodeIgniter\Model;

class BorrowModel extends Model
{

    protected $table = 'borrow';
    protected $primaryKey = ['member', 'book'];

    protected $allowedFields = ['member', 'book', 'out_date', 'return_date', 'delay'];

}