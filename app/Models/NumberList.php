<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NumberList extends Model
{
    protected $guarded = [];

    public function numbers()
    {
        return $this->hasMany(Number::class);
    }
}
