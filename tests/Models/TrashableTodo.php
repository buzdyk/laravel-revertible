<?php

namespace Buzdyk\Revertible\Testing\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class TrashableTodo extends Todo {
    use SoftDeletes;

    public $table = 'todos';

    protected $fillable = [];
}