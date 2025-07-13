<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $guarded = [];

    public function call_records()
    {
        return $this->hasMany(CallRecord::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
