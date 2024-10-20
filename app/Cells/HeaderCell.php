<?php

namespace App\Cells;

use CodeIgniter\View\Cells\Cell;

class HeaderCell extends Cell
{
    public string $redirect = '#';
    public ?string $title = null;
    public ?string $return = null;

    protected string $view = 'Views/header';
}