<?php

namespace App\Models;

use CodeIgniter\Model;

class WriteModel extends Model
{

    protected $table = 'write';
    protected $primaryKey = ['author', 'book'];

    protected $allowedFields = ['author', 'book', 'main', 'role'];

    public function findMainForBook($book): object|array|null
    {
        $data = $this->where('book', $book)
            ->where('main', true)
            ->first();

        return $data;
    }

}