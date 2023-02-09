<?php

namespace App\Models\Filters;

use EloquentFilter\ModelFilter;

class ReplyFilter extends ModelFilter
{
    public $relations = [];

    public function replyer($id)
    {
        return $this->where('user_id', $id);
    }
}