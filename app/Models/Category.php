<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;
use Dimsav\Translatable\Translatable;

class Category extends Model
{
    use NodeTrait, Translatable;

    public $translatedAttributes = ['name'];

    protected $with = ['translations'];


    protected $fillable = [
        'name', 'description',
    ];
}
